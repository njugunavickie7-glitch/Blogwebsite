<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/../../app/config/db_connect.php';
require_once __DIR__ . '/../../app/controllers/GalleryController.php';

$galleryController = new GalleryController($pdo);
$page_title       = 'Gallery - Ismano';
$page_description = 'Explore our creative portfolio of images and videos';

// Filters from the query string
$category = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : null;
$type     = isset($_GET['type']) && in_array($_GET['type'], ['image', 'video'], true) ? $_GET['type'] : null;

// Build the filter set that is ACTUALLY passed to the query.
// (The previous version ignored $category entirely, so filtering never worked.)
$filters = ['status' => 'active', 'limit' => 30, 'offset' => 0];
if ($category) $filters['category']   = $category;
if ($type)     $filters['media_type'] = $type;

$items      = [];
$categories = [];
$loadError  = null;

try {
    if (method_exists($galleryController, 'getFiltered')) {
        // Preferred path (updated controller): filtering happens in SQL.
        $items = $galleryController->getFiltered($filters);
    } else {
        // Fallback for the older controller that only has getAll().
        // Filter by category / type in PHP so the page still works.
        $items = $galleryController->getAll('active', 30, 0);
        if ($category) {
            $items = array_values(array_filter($items, function ($i) use ($category) {
                return ($i['category'] ?? null) === $category;
            }));
        }
        if ($type) {
            $items = array_values(array_filter($items, function ($i) use ($type) {
                return ($i['media_type'] ?? null) === $type;
            }));
        }
    }
    $categories = $galleryController->getCategories();
} catch (Throwable $e) {
    $loadError = $e->getMessage();
    error_log('[public gallery] ' . $e->getMessage());
}

ob_start();
?>

<?php if (isset($_GET['debug'])): ?>
<div class="container mt-3">
    <div class="alert alert-info">
        <h5 class="mb-2">Debug Information</h5>
        <p class="mb-1">Items returned: <strong><?php echo count($items); ?></strong></p>
        <p class="mb-1">Categories found: <strong><?php echo count($categories); ?></strong></p>
        <p class="mb-1">Active filters: <code><?php echo htmlspecialchars(json_encode(array_diff_key($filters, ['status' => 1]))); ?></code></p>
        <?php if ($loadError): ?>
            <p class="text-danger mb-0">Load error: <?php echo htmlspecialchars($loadError); ?></p>
        <?php elseif (empty($items)): ?>
            <p class="text-danger mb-0">Query ran but returned no rows. Check that the <code>gallery</code> table has rows with <code>status = 'active'</code> and a non-empty <code>file_path</code>/<code>video_url</code>.</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<section class="section">
    <div class="container">

        <div class="text-center mb-5 reveal">
            <span class="eyebrow">Visual Journey</span>
            <h1 class="display-4 fw-bold mb-3">Our Gallery</h1>
            <p class="lead text-muted">Explore our creative work through images and videos</p>
        </div>

        <?php if ($loadError && !isset($_GET['debug'])): ?>
            <div class="alert alert-warning text-center reveal">
                We couldn't load the gallery right now. Please try again shortly.
            </div>
        <?php endif; ?>

        <?php if (!empty($categories)): ?>
        <div class="mb-5 text-center reveal reveal-delay-1">
            <div class="btn-group flex-wrap gap-2">
                <a href="?" class="btn <?php echo empty($category) ? 'btn--primary' : 'btn--outline'; ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="?category=<?php echo urlencode($cat); ?>"
                       class="btn <?php echo $category === $cat ? 'btn--primary' : 'btn--outline'; ?>">
                        <?php echo htmlspecialchars($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (empty($items)): ?>
            <div class="text-center py-5 reveal">
                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                <p class="mb-0">No gallery items available yet. Please check back soon.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($items as $index => $item): ?>
                    <?php
                    // Skip items with nothing to show.
                    if (empty($item['file_path']) && empty($item['video_url']) && empty($item['video_embed_code'])) {
                        continue;
                    }
                    ?>
                    <div class="col-md-6 col-lg-4 reveal reveal-delay-<?php echo min(4, ($index % 4) + 1); ?>">
                        <div class="gallery-card h-100" data-bs-toggle="modal" data-bs-target="#galleryModal-<?php echo (int) $item['id']; ?>">
                            <?php if ($item['media_type'] === 'image' && !empty($item['file_path'])): ?>
                                <div class="gallery-image-wrapper">
                                    <img src="<?php echo htmlspecialchars($item['thumbnail_path'] ?? $item['file_path']); ?>"
                                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                                         class="gallery-image" loading="lazy"
                                         onerror="this.onerror=null;this.src='https://placehold.co/600x400?text=Image+Unavailable'">
                                    <div class="gallery-overlay"><i class="fas fa-search-plus"></i></div>
                                </div>
                            <?php elseif ($item['media_type'] === 'video'): ?>
                                <div class="gallery-video-wrapper">
                                    <?php echo gallery_video_html($item, 'card'); ?>
                                </div>
                            <?php endif; ?>
                            <div class="gallery-caption p-3">
                                <h3 class="h5 mb-1"><?php echo htmlspecialchars($item['title']); ?></h3>
                                <?php if (!empty($item['description'])): ?>
                                    <p class="text-muted small mb-0"><?php echo htmlspecialchars(mb_strimwidth($item['description'], 0, 100, '…')); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($item['category'])): ?>
                                    <span class="badge bg-light text-dark mt-2"><?php echo htmlspecialchars($item['category']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="galleryModal-<?php echo (int) $item['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content bg-dark text-white">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center p-4">
                                    <?php if ($item['media_type'] === 'image' && !empty($item['file_path'])): ?>
                                        <img src="<?php echo htmlspecialchars($item['file_path']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                    <?php elseif ($item['media_type'] === 'video'): ?>
                                        <?php echo gallery_video_html($item, 'modal'); ?>
                                    <?php endif; ?>
                                    <?php if (!empty($item['description'])): ?>
                                        <p class="mt-3"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
/**
 * Render the right markup for a video item.
 * Embed codes come from trusted admins only — still scoped to the modal/card.
 */
function gallery_video_html(array $item, string $context): string {
    $height = $context === 'modal' ? 'height="500"' : '';

    if (!empty($item['video_embed_code'])) {
        return $item['video_embed_code']; // admin-provided raw embed
    }

    if (!empty($item['video_url'])) {
        $url = $item['video_url'];

        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            if (strpos($url, 'youtu.be') !== false) {
                $vid = trim(parse_url($url, PHP_URL_PATH) ?? '', '/');
            } else {
                parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $q);
                $vid = $q['v'] ?? '';
            }
            $vid = preg_replace('/[^A-Za-z0-9_\-]/', '', $vid);
            return '<iframe src="https://www.youtube.com/embed/' . $vid . '" width="100%" ' . $height . ' frameborder="0" allowfullscreen></iframe>';
        }

        if (strpos($url, 'vimeo.com') !== false) {
            $vid = preg_replace('/[^0-9]/', '', basename(parse_url($url, PHP_URL_PATH) ?? ''));
            return '<iframe src="https://player.vimeo.com/video/' . $vid . '" width="100%" ' . $height . ' frameborder="0" allowfullscreen></iframe>';
        }

        return '<video controls class="w-100"><source src="' . htmlspecialchars($url) . '" type="video/mp4"></video>';
    }

    if (!empty($item['file_path'])) {
        return '<video controls class="w-100"><source src="' . htmlspecialchars($item['file_path']) . '" type="video/mp4">Your browser does not support the video tag.</video>';
    }

    return '';
}
?>

<style>
.gallery-card { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 5px 20px rgba(0,0,0,.08); transition:transform .3s ease, box-shadow .3s ease; cursor:pointer; }
.gallery-card:hover { transform:translateY(-5px); box-shadow:0 15px 35px rgba(0,0,0,.12); }
.gallery-image-wrapper { position:relative; overflow:hidden; aspect-ratio:16/9; background:#f8f9fa; }
.gallery-image { width:100%; height:100%; object-fit:cover; transition:transform .4s ease; }
.gallery-card:hover .gallery-image { transform:scale(1.05); }
.gallery-overlay { position:absolute; inset:0; background:rgba(0,0,0,.5); display:flex; align-items:center; justify-content:center; opacity:0; transition:opacity .3s ease; }
.gallery-card:hover .gallery-overlay { opacity:1; }
.gallery-overlay i { color:#fff; font-size:2rem; }
.gallery-video-wrapper { aspect-ratio:16/9; background:#000; }
.gallery-video-wrapper iframe, .gallery-video-wrapper video { width:100%; height:100%; object-fit:cover; }
.gallery-caption { background:#fff; }
.btn-group { display:inline-flex; flex-wrap:wrap; gap:8px; }
@media (max-width:768px){ .modal-dialog { margin:10px; } }
</style>

<?php /*
  Scroll-reveal for this page.
  The theme's .reveal elements start at opacity:0 and only become visible once
  the class .is-visible is added. The shared layout does NOT ship a reveal script,
  so without this the whole page renders invisible (blank). This observer adds
  .is-visible as elements scroll into view (so the fade-in animation plays), and
  a load-time safety net reveals anything still hidden. <noscript> covers JS-off.
*/ ?>
<noscript><style>.reveal{opacity:1 !important;transform:none !important;}</style></noscript>
<script>
(function () {
    var els = document.querySelectorAll('.reveal');
    if (!els.length) return;

    function showAll() {
        els.forEach(function (el) { el.classList.add('is-visible'); });
    }

    if (!('IntersectionObserver' in window)) {
        showAll();
        return;
    }

    var io = new IntersectionObserver(function (entries, obs) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

    els.forEach(function (el) { io.observe(el); });

    // Safety net: reveal anything still hidden shortly after load.
    window.addEventListener('load', function () {
        setTimeout(function () {
            els.forEach(function (el) {
                if (window.getComputedStyle(el).opacity === '0') {
                    el.classList.add('is-visible');
                }
            });
        }, 400);
    });
})();
</script>

<?php
$content = ob_get_clean();
$use_home_navbar = false;
require_once __DIR__ . '/../templates/public/layout.php';