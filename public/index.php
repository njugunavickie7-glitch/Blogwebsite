<?php
// public/index.php
$page_title = 'Ismano Portfolio — Creative Digital Solutions';
$page_description = 'Explore our creative portfolio of web development, design, and digital solutions.';
$use_home_navbar = true; // Tell template to use home navbar with hero effects

ob_start();

require_once __DIR__ . '/../app/config/db_connect.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

$stmt = $pdo->prepare("
    SELECT p.*, c.category_name
    FROM projects p
    LEFT JOIN project_categories c ON p.category_id = c.id
    WHERE p.status = 'published'
    ORDER BY p.created_at DESC
");
$stmt->execute();
$allProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

$featuredProjects = [];
if (!empty($allProjects)) {
    $keys = array_rand($allProjects, min(3, count($allProjects)));
    foreach ((array) $keys as $k) { $featuredProjects[] = $allProjects[$k]; }
}

$stmt = $pdo->prepare("SELECT * FROM services WHERE status = 'published' ORDER BY created_at DESC LIMIT 4");
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT b.*, c.name as category_name
    FROM blogs b
    LEFT JOIN blog_categories c ON b.category_id = c.id
    WHERE b.status = 'published'
    ORDER BY b.published_at DESC
    LIMIT 3
");
$stmt->execute();
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM project_categories");
$stmt->execute();
$totalCategories = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
$totalProjects   = count($allProjects);

$serviceIcons = [
    'default' => 'fa-solid fa-layer-group',
    'web'     => 'fa-solid fa-globe',
    'design'  => 'fa-solid fa-pen-nib',
    'seo'     => 'fa-solid fa-chart-line',
    'brand'   => 'fa-solid fa-star',
    'content' => 'fa-solid fa-pen-to-square',
    'social'  => 'fa-solid fa-share-nodes',
    'ads'     => 'fa-solid fa-rectangle-ad',
    'mobile'  => 'fa-solid fa-mobile-screen',
    'app'     => 'fa-solid fa-mobile-screen',
];
function serviceIcon(string $title, array $map): string {
    $t = strtolower($title);
    foreach ($map as $k => $v) { if ($k !== 'default' && str_contains($t, $k)) return $v; }
    return $map['default'];
}

// Hero slideshow images
$heroSlides = [
    [
        'url'     => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1920&q=80',
        'credit'  => 'Photo: Unsplash',
        'caption' => 'Modern creative workspace',
    ],
    [
        'url'     => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1920&q=80',
        'credit'  => 'Photo: Unsplash',
        'caption' => 'Team collaboration',
    ],
    [
        'url'     => 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=1920&q=80',
        'credit'  => 'Photo: Unsplash',
        'caption' => 'Strategy & planning',
    ],
    [
        'url'     => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=1920&q=80',
        'credit'  => 'Photo: Unsplash',
        'caption' => 'Creative brainstorming',
    ],
    [
        'url'     => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=1920&q=80',
        'credit'  => 'Photo: Unsplash',
        'caption' => 'Digital innovation',
    ],
];

$serviceImages = [
    'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&q=75',
    'https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?w=600&q=75',
    'https://images.unsplash.com/photo-1499951360447-b19be8fe80f5?w=600&q=75',
    'https://images.unsplash.com/photo-1432888498266-38ffec3eaf0a?w=600&q=75',
];
?>

<style>
/* Additional homepage styles that extend the template */
.page-body { padding-top: var(--navbar-height); }

/* Hero Section */
.site-hero { cursor: none; position: relative; height: calc(100svh - var(--navbar-height)); min-height: 580px; overflow: hidden; display: flex; align-items: center; }
.hero-slides { position: absolute; inset: 0; z-index: 0; }
.hero-slide { position: absolute; inset: 0; opacity: 0; transition: opacity 1.5s ease; }
.hero-slide.is-active { opacity: 1; }
.hero-slide img { width: 100%; height: 100%; object-fit: cover; object-position: center; transform: scale(1.08); transition: transform 9s ease; }
.hero-slide.is-active img { transform: scale(1); }
.hero-overlay { position: absolute; inset: 0; z-index: 1; background: linear-gradient(to right, rgba(4,4,8,0.86) 0%, rgba(4,4,8,0.5) 55%, rgba(4,4,8,0.2) 100%), linear-gradient(to top, rgba(4,4,8,0.6) 0%, transparent 45%); }
.hero-cursor { position: absolute; top: 0; left: 0; width: 32px; height: 32px; pointer-events: none; z-index: 10; transform: translate(-50%, -50%); opacity: 0; }
.hero-cursor svg { width: 100%; height: 100%; filter: drop-shadow(0 0 6px rgba(200,242,60,0.9)) drop-shadow(0 0 14px rgba(200,242,60,0.5)); animation: bolt-pulse 2.4s ease infinite; }
@keyframes bolt-pulse { 0%, 100% { filter: drop-shadow(0 0 5px rgba(200,242,60,0.9)) drop-shadow(0 0 12px rgba(200,242,60,0.4)); } 50% { filter: drop-shadow(0 0 10px rgba(200,242,60,1)) drop-shadow(0 0 24px rgba(200,242,60,0.7)); } }
.hero-cursor-light { position: absolute; top: 0; left: 0; width: 320px; height: 320px; pointer-events: none; z-index: 2; border-radius: 50%; transform: translate(-50%, -50%); background: radial-gradient(circle, rgba(200,242,60,0.09) 0%, rgba(200,242,60,0.04) 40%, transparent 70%); opacity: 0; }
.hero-content { position: relative; z-index: 4; width: 100%; }
.hero-heading { font-family: var(--font-display); font-size: clamp(3rem, 6.5vw, 5.5rem); font-weight: 700; line-height: 1.06; letter-spacing: 0.01em; color: #fff; margin-bottom: var(--space-6); font-style: italic; }
.hero-heading strong { font-style: normal; font-weight: 700; display: block; color: var(--brand-accent); letter-spacing: -0.01em; }
.hero-sub { font-family: var(--font-body); font-size: var(--text-base); font-weight: 300; color: rgba(255,255,255,0.6); line-height: 1.85; max-width: 480px; margin-bottom: var(--space-10); letter-spacing: 0.01em; }
.hero-dots { display: flex; gap: var(--space-2); align-items: center; margin-bottom: 0; }
.hero-dot { width: 26px; height: 2px; background: rgba(255,255,255,0.25); border-radius: 2px; cursor: none; transition: all var(--transition-base); border: none; padding: 0; }
.hero-dot.is-active { background: var(--brand-accent); width: 44px; }
.hero-caption { position: absolute; bottom: var(--space-10); right: var(--space-8); z-index: 5; font-family: var(--font-body); font-size: 10px; font-weight: 400; color: rgba(255,255,255,0.28); letter-spacing: 0.1em; text-transform: uppercase; }
.hero-scroll-btn { position: absolute; bottom: var(--space-8); left: 50%; transform: translateX(-50%); z-index: 5; display: flex; flex-direction: column; align-items: center; gap: var(--space-3); background: none; border: none; cursor: none; color: rgba(255,255,255,0.5); font-family: var(--font-body); font-size: 9px; font-weight: 500; letter-spacing: 0.2em; text-transform: uppercase; transition: color var(--transition-base); text-decoration: none; padding: 0; }
.hero-scroll-btn:hover { color: var(--brand-accent); }
.scroll-mouse { width: 26px; height: 40px; border: 1.5px solid rgba(255,255,255,0.35); border-radius: 14px; position: relative; transition: border-color var(--transition-base); }
.hero-scroll-btn:hover .scroll-mouse { border-color: var(--brand-accent); }
.scroll-mouse::before { content: ''; position: absolute; top: 6px; left: 50%; transform: translateX(-50%); width: 3px; height: 8px; background: rgba(255,255,255,0.5); border-radius: 2px; animation: mouse-wheel 1.6s ease infinite; }
.hero-scroll-btn:hover .scroll-mouse::before { background: var(--brand-accent); }
@keyframes mouse-wheel { 0% { opacity: 1; transform: translateX(-50%) translateY(0); } 60% { opacity: 0; transform: translateX(-50%) translateY(10px); } 61% { opacity: 0; transform: translateX(-50%) translateY(0); } 100% { opacity: 1; transform: translateX(-50%) translateY(0); } }

/* Stats */
.stats-section { background: var(--color-surface-alt); border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border); padding-block: var(--space-12); }
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); }
.stat-item { padding: var(--space-6) var(--space-8); border-right: 1px solid var(--color-border); }
.stat-item:last-child { border-right: none; }
.stat-number { font-family: var(--font-display); font-size: clamp(2.5rem, 4vw, 3.5rem); font-weight: 800; color: var(--brand-primary); line-height: 1; letter-spacing: -0.04em; margin-bottom: var(--space-2); }
.stat-number .accent { color: var(--brand-accent); }

/* About Section */
.about-section { background: var(--color-surface); }
.about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-16); align-items: center; }
.about-image-wrap { position: relative; }
.about-image-wrap img { width: 100%; height: 480px; object-fit: cover; border-radius: var(--radius-xl); }
.about-badge { position: absolute; bottom: -24px; right: -24px; background: var(--brand-primary); color: #fff; border-radius: var(--radius-lg); padding: var(--space-5) var(--space-6); box-shadow: var(--shadow-xl); min-width: 160px; }
.about-badge-num { font-family: var(--font-display); font-size: 2.5rem; font-weight: 800; color: var(--brand-accent); line-height: 1; letter-spacing: -0.04em; }
.about-text { padding-left: var(--space-4); }
.check-item { display: flex; align-items: center; gap: var(--space-3); font-size: var(--text-sm); font-weight: 500; color: var(--color-text-body); }
.check-item i { width: 22px; height: 22px; border-radius: 50%; background: var(--brand-accent); color: var(--brand-primary); display: flex; align-items: center; justify-content: center; font-size: 0.65rem; flex-shrink: 0; }

/* Services */
.services-section { background: var(--color-surface-alt); }
.services-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--space-5); }
.service-card { background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); overflow: hidden; display: flex; flex-direction: column; transition: all var(--transition-base); position: relative; }
.service-card::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: var(--brand-accent); transform: scaleX(0); transform-origin: left; transition: transform var(--transition-base); }
.service-card:hover { box-shadow: var(--shadow-xl); transform: translateY(-5px); border-color: transparent; }
.service-card:hover::after { transform: scaleX(1); }
.service-card-image { height: 180px; overflow: hidden; background: var(--color-surface-alt); }
.service-card-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.7s ease; filter: saturate(0.8); }
.service-card:hover .service-card-image img { transform: scale(1.07); filter: saturate(1); }
.service-icon-wrap { width: 48px; height: 48px; border-radius: var(--radius-md); background: var(--color-surface-alt); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; color: var(--brand-primary); transition: background var(--transition-base); }
.service-card:hover .service-icon-wrap { background: var(--brand-accent); }

/* Projects */
.projects-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-5); }
.project-card { background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); overflow: hidden; display: flex; flex-direction: column; transition: all var(--transition-base); }
.project-card:hover { box-shadow: var(--shadow-xl); transform: translateY(-6px); border-color: transparent; }
.project-thumb { position: relative; aspect-ratio: 4/3; overflow: hidden; background: var(--color-surface-alt); }
.project-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; }
.project-card:hover .project-thumb img { transform: scale(1.06); }
.project-cat-badge { position: absolute; top: var(--space-4); left: var(--space-4); background: rgba(255,255,255,0.92); backdrop-filter: blur(8px); padding: 4px 12px; border-radius: var(--radius-full); font-size: var(--text-xs); font-weight: 600; color: var(--brand-primary); letter-spacing: 0.04em; }

/* Why Us */
.why-section { background: var(--color-surface-alt); }
.why-layout { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-16); align-items: center; }
.why-quote-card { position: absolute; top: 32px; left: -28px; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: var(--space-5); box-shadow: var(--shadow-xl); max-width: 240px; }
.quote-avatar { width: 28px; height: 28px; border-radius: 50%; background: var(--brand-accent); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; color: var(--brand-primary); font-family: var(--font-display); flex-shrink: 0; }

/* Blog */
.blog-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-5); }
.blog-card { background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); overflow: hidden; display: flex; flex-direction: column; transition: all var(--transition-base); }
.blog-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-4px); border-color: transparent; }
.blog-thumb { aspect-ratio: 16/9; overflow: hidden; background: var(--color-surface-alt); }
.blog-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; }
.blog-card:hover .blog-thumb img { transform: scale(1.06); }
.read-more { display: inline-flex; align-items: center; gap: var(--space-2); font-size: var(--text-sm); font-weight: 600; color: var(--brand-primary); transition: gap var(--transition-fast); }
.read-more:hover { gap: var(--space-3); }

/* CTA */
.cta-section { position: relative; overflow: hidden; text-align: center; padding-block: var(--space-24); }
.cta-bg { position: absolute; inset: 0; z-index: 0; }
.cta-bg img { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.25) saturate(0.6); }
.cta-content { position: relative; z-index: 1; }
.cta-heading { font-family: var(--font-display); font-size: clamp(2rem, 4vw, 3.25rem); font-weight: 800; color: #fff; letter-spacing: -0.03em; margin-bottom: var(--space-4); }

/* Responsive */
@media (max-width: 1024px) {
    .about-grid, .why-layout { grid-template-columns: 1fr; }
    .projects-grid, .blog-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .stats-grid { grid-template-columns: 1fr; }
    .stat-item { border-right: none; border-bottom: 1px solid var(--color-border); }
    .services-grid { grid-template-columns: 1fr; }
    .projects-grid, .blog-grid { grid-template-columns: 1fr; }
    .hero-caption { display: none; }
}
</style>

<!-- Include Homepage Navbar -->
<!--?php include __DIR__ . '/components/public/nav_home.php'; ?-->

<!-- Hero Section -->
<section class="site-hero" id="hero">
  <div class="hero-slides" aria-hidden="true">
    <?php foreach ($heroSlides as $i => $slide): ?>
      <div class="hero-slide <?php echo $i === 0 ? 'is-active' : ''; ?>" data-index="<?php echo $i; ?>">
        <img src="<?php echo $slide['url']; ?>" alt="<?php echo htmlspecialchars($slide['caption']); ?>" loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>">
      </div>
    <?php endforeach; ?>
  </div>
  <div class="hero-overlay" aria-hidden="true"></div>
  <div class="hero-cursor-light" id="heroCursorLight" aria-hidden="true"></div>
  <div class="hero-cursor" id="heroCursor" aria-hidden="true">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" fill="#C8F23C" stroke="#C8F23C" stroke-width="0.5" stroke-linejoin="round"/>
    </svg>
  </div>
  <div class="hero-content">
    <div class="container">
      <h1 class="hero-heading">Strategy. Craft.<br><strong>Real Results.</strong></h1>
      <p class="hero-sub">From brand positioning to full digital builds — we deliver creative solutions designed to increase visibility, engagement, and long-term growth.</p>
      <div class="hero-dots" role="tablist" aria-label="Slideshow navigation">
        <?php foreach ($heroSlides as $i => $s): ?>
          <button class="hero-dot <?php echo $i === 0 ? 'is-active' : ''; ?>" data-slide="<?php echo $i; ?>" role="tab" aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>" aria-label="Slide <?php echo $i + 1; ?>"></button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="hero-caption" id="heroCaption" aria-live="polite"><?php echo htmlspecialchars($heroSlides[0]['caption']); ?></div>
  <a href="#stats-anchor" class="hero-scroll-btn" id="heroScrollBtn" aria-label="Scroll to next section">
    <div class="scroll-mouse" aria-hidden="true"></div>
    <span>Scroll</span>
  </a>
</section>
<span id="stats-anchor" style="display:block;position:relative;top:0;"></span>

<!-- Stats Section -->
<section class="stats-section" id="stats-section">
  <div class="container">
    <div class="stats-grid reveal">
      <div class="stat-item"><div class="stat-number" data-target="<?php echo $totalProjects; ?>"><?php echo $totalProjects; ?><span class="accent">+</span></div><div class="stat-label">Completed Projects</div><div class="stat-sub">Successfully delivered</div></div>
      <div class="stat-item"><div class="stat-number" data-target="<?php echo $totalCategories; ?>"><?php echo $totalCategories; ?><span class="accent">+</span></div><div class="stat-label">Expertise Areas</div><div class="stat-sub">Across key categories</div></div>
      <div class="stat-item"><div class="stat-number">100<span class="accent">%</span></div><div class="stat-label">Client Satisfaction</div><div class="stat-sub">Happy clients worldwide</div></div>
    </div>
  </div>
</section>

<!-- About Section -->
<section class="section about-section">
  <div class="container">
    <div class="about-grid">
      <div class="about-image-wrap reveal">
        <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=900&q=80" alt="Our team at work" loading="lazy">
        <div class="about-badge"><div class="about-badge-num"><?php echo $totalProjects; ?>+</div><div class="about-badge-label">Projects delivered</div></div>
      </div>
      <div class="about-text reveal reveal-delay-2">
        <p class="eyebrow">Who we are</p>
        <h2>A team built around<br>outcomes, not output</h2>
        <p>We're a team of strategists, designers, and developers who believe great digital work starts with a deep understanding of your goals — not a template. Every project we take on is treated as if it were our own.</p>
        <div class="about-checks">
          <div class="check-item"><i class="fa-solid fa-check"></i> Strategy-first approach to every engagement</div>
          <div class="check-item"><i class="fa-solid fa-check"></i> Transparent process with no surprise costs</div>
          <div class="check-item"><i class="fa-solid fa-check"></i> Measurable results, not just deliverables</div>
          <div class="check-item"><i class="fa-solid fa-check"></i> Long-term partnerships over one-off projects</div>
        </div>
        <a href="/Ismano/public/projects/" class="btn btn--dark">See Our Work <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- Services Section -->
<?php if (!empty($services)): ?>
<section class="section services-section">
  <div class="container">
    <div class="services-grid">
      <?php foreach ($services as $i => $svc): ?>
        <div class="service-card reveal reveal-delay-<?php echo ($i % 4) + 1; ?>">
          <div class="service-card-image"><img src="<?php echo $serviceImages[$i % count($serviceImages)]; ?>" alt="<?php echo htmlspecialchars($svc['title']); ?>" loading="lazy"></div>
          <div class="service-card-body">
            <div class="service-icon-wrap"><i class="<?php echo serviceIcon($svc['title'], $serviceIcons); ?>"></i></div>
            <h3 class="service-title"><?php echo htmlspecialchars($svc['title']); ?></h3>
            <p class="service-desc"><?php echo htmlspecialchars(mb_strimwidth($svc['short_description'] ?? '', 0, 120, '…')); ?></p>
            <a href="/Ismano/public/services/readmore.php?slug=<?php echo urlencode($svc['slug']); ?>" class="service-link-arrow">Learn more <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Featured Projects Section -->
<section class="section projects-section" id="featured">
  <div class="container">
    <div class="projects-header"><div><p class="eyebrow">Our work</p><h2 style="margin:0;">Featured Projects</h2></div><?php if (!empty($featuredProjects)): ?><a href="/Ismano/public/projects/" class="btn btn--outline">All Projects <i class="fa-solid fa-arrow-right"></i></a><?php endif; ?></div>
    <div class="projects-grid">
      <?php if (empty($featuredProjects)): ?>
        <div class="empty-state"><i class="fa-regular fa-folder-open"></i><p>No projects published yet. Check back soon!</p></div>
      <?php else: ?>
        <?php foreach ($featuredProjects as $i => $p): ?>
          <div class="project-card reveal reveal-delay-<?php echo ($i % 3) + 1; ?>">
            <div class="project-thumb">
              <?php if (!empty($p['cover_image'])): ?>
                <img src="<?php echo htmlspecialchars($p['cover_image']); ?>" alt="<?php echo htmlspecialchars($p['small_title']); ?>" loading="lazy">
              <?php else: ?>
                <div style="width:100%;height:100%;background:linear-gradient(135deg,#f0f0ec,#e0e0d8);display:flex;align-items:center;justify-content:center;"><i class="fa-regular fa-image" style="font-size:2rem;color:var(--color-border);"></i></div>
              <?php endif; ?>
              <?php if (!empty($p['category_name'])): ?><span class="project-cat-badge"><?php echo htmlspecialchars($p['category_name']); ?></span><?php endif; ?>
            </div>
            <div class="project-body"><div class="project-subtitle"><?php echo htmlspecialchars($p['major_title'] ?? ''); ?></div><h3 class="project-title"><?php echo htmlspecialchars($p['small_title']); ?></h3><p class="project-excerpt"><?php echo htmlspecialchars(mb_strimwidth(strip_tags($p['description'] ?? ''), 0, 100, '…')); ?></p></div>
            <div class="project-footer"><div class="project-footer-meta"><span><i class="fa-regular fa-eye"></i> <?php echo number_format($p['view_count'] ?? 0); ?></span><span><i class="fa-regular fa-calendar"></i> <?php echo date('M Y', strtotime($p['created_at'])); ?></span></div><a href="/Ismano/public/projects/readmore.php?id=<?php echo (int)$p['id']; ?>" class="project-view-btn">View <i class="fa-solid fa-arrow-right"></i></a></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Why Us Section -->
<section class="section why-section">
  <div class="container">
    <div class="why-layout">
      <div class="why-image-col reveal">
        <img src="https://images.unsplash.com/photo-1553877522-43269d4ea984?w=900&q=80" alt="Strategy planning session" loading="lazy">
        <div class="why-quote-card"><p class="quote-text">"The results exceeded everything we expected in year one."</p><div class="quote-author"><div class="quote-avatar">JM</div><div><strong style="display:block;color:var(--color-text-heading);font-size:11px;">James M.</strong><span>Client, 2024</span></div></div></div>
      </div>
      <div class="reveal reveal-delay-2">
        <p class="eyebrow">Why work with us</p>
        <h2>What sets us apart<br>from the rest</h2>
        <div class="why-list">
          <div class="why-item"><div class="why-number">01</div><div class="why-content"><h4>Focused on what truly matters</h4><p>We prioritise performance, growth, and long-term value — every effort contributes to meaningful business results.</p></div></div>
          <div class="why-item"><div class="why-number">02</div><div class="why-content"><h4>Driven by insight, not assumptions</h4><p>Every decision is backed by data and real customer understanding so your strategy is grounded in what works.</p></div></div>
          <div class="why-item"><div class="why-number">03</div><div class="why-content"><h4>Committed to long-term success</h4><p>We don't chase quick wins. We build strategies designed to grow with your business and deliver sustainable results.</p></div></div>
          <div class="why-item"><div class="why-number">04</div><div class="why-content"><h4>Transparent and communicative</h4><p>Clear timelines, honest updates, no surprises — you always know exactly where your project stands.</p></div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Blog Section -->
<?php if (!empty($blogs)): ?>
<section class="section blog-section">
  <div class="container">
    <div class="blog-header"><div><p class="eyebrow">From our blog</p><h2 style="margin:0;">Latest insights</h2></div><a href="/Ismano/public/blogs/" class="btn btn--outline">All Articles <i class="fa-solid fa-arrow-right"></i></a></div>
    <div class="blog-grid">
      <?php foreach ($blogs as $i => $blog): ?>
        <article class="blog-card reveal reveal-delay-<?php echo ($i % 3) + 1; ?>">
          <div class="blog-thumb"><?php if (!empty($blog['featured_image'])): ?><img src="<?php echo htmlspecialchars($blog['featured_image']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" loading="lazy"><?php else: ?><img src="https://images.unsplash.com/photo-1499951360447-b19be8fe80f5?w=600&q=70" alt="Blog post" loading="lazy"><?php endif; ?></div>
          <div class="blog-body"><div class="blog-meta"><?php if (!empty($blog['category_name'])): ?><span class="tag tag--muted"><?php echo htmlspecialchars($blog['category_name']); ?></span><?php endif; ?><span class="blog-date"><i class="fa-regular fa-calendar"></i> <?php echo date('M d, Y', strtotime($blog['published_at'] ?? $blog['created_at'])); ?></span></div><h3 class="blog-title"><a href="/Ismano/public/blogs/readmore.php?slug=<?php echo urlencode($blog['slug']); ?>"><?php echo htmlspecialchars($blog['title']); ?></a></h3><p class="blog-excerpt"><?php echo htmlspecialchars(mb_strimwidth($blog['excerpt'] ?? strip_tags($blog['content'] ?? ''), 0, 120, '…')); ?></p><a href="/Ismano/public/blogs/readmore.php?slug=<?php echo urlencode($blog['slug']); ?>" class="read-more">Read more <i class="fa-solid fa-arrow-right"></i></a></div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="cta-section">
  <div class="cta-bg" aria-hidden="true"><img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=1920&q=80" alt="" loading="lazy"></div>
  <div class="container cta-content">
    <p class="eyebrow cta-eyebrow">Ready to grow?</p>
    <h2 class="cta-heading">Ready to start your project?</h2>
    <p class="cta-sub">Let's create something extraordinary together. No pressure — just an honest conversation.</p>
    <div class="cta-actions"><a href="/Ismano/public/auth/register.php" class="btn btn--primary">Get Started <i class="fa-solid fa-arrow-right"></i></a><a href="/Ismano/public/projects/" class="btn btn--outline-inv">Browse Portfolio</a></div>
    <p class="cta-note">We'll respond within <strong>24 hours</strong>. No commitment required.</p>
  </div>
</section>

<!-- Homepage Scripts -->
<script>
(function () {
  const slides = document.querySelectorAll('.hero-slide');
  const dots = document.querySelectorAll('.hero-dot');
  const caption = document.getElementById('heroCaption');
  const captions = <?php echo json_encode(array_column($heroSlides, 'caption')); ?>;
  let current = 0, timer = null, isPaused = false;
  function goTo(n) { slides[current].classList.remove('is-active'); dots[current].classList.remove('is-active'); dots[current].setAttribute('aria-selected', 'false'); current = (n + slides.length) % slides.length; slides[current].classList.add('is-active'); dots[current].classList.add('is-active'); dots[current].setAttribute('aria-selected', 'true'); if (caption) caption.textContent = captions[current]; }
  function startTimer() { clearInterval(timer); timer = setInterval(() => goTo(current + 1), 5500); }
  dots.forEach((dot, i) => { dot.addEventListener('click', () => { goTo(i); startTimer(); }); });
  const hero = document.getElementById('hero');
  hero.addEventListener('mouseenter', () => { clearInterval(timer); isPaused = true; });
  hero.addEventListener('mouseleave', () => { if (isPaused) { startTimer(); isPaused = false; } });
  startTimer();

  const bolt = document.getElementById('heroCursor'), light = document.getElementById('heroCursorLight');
  if (hero && bolt && light) {
    let mouseX = 0, mouseY = 0, boltX = 0, boltY = 0, lightX = 0, lightY = 0, visible = false, raf = null;
    hero.addEventListener('mouseenter', () => { visible = true; bolt.style.opacity = '1'; light.style.opacity = '1'; });
    hero.addEventListener('mouseleave', () => { visible = false; bolt.style.opacity = '0'; light.style.opacity = '0'; });
    hero.addEventListener('mousemove', e => { const rect = hero.getBoundingClientRect(); mouseX = e.clientX - rect.left; mouseY = e.clientY - rect.top; });
    function tick() { boltX += (mouseX - boltX) * 0.18; boltY += (mouseY - boltY) * 0.18; lightX += (mouseX - lightX) * 0.08; lightY += (mouseY - lightY) * 0.08; bolt.style.transform = `translate(${boltX - 16}px, ${boltY - 16}px)`; light.style.transform = `translate(${lightX - 160}px, ${lightY - 160}px)`; raf = requestAnimationFrame(tick); }
    raf = requestAnimationFrame(tick);
  }

  document.getElementById('heroScrollBtn')?.addEventListener('click', e => { e.preventDefault(); document.getElementById('stats-anchor')?.scrollIntoView({ behavior: 'smooth', block: 'start' }); });

  const ease = t => t < .5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
  const io = new IntersectionObserver(entries => { entries.forEach(entry => { if (!entry.isIntersecting) return; const el = entry.target; const target = parseInt(el.dataset.target, 10); if (isNaN(target)) return; let start = null; function step(ts) { if (!start) start = ts; const p = Math.min((ts - start) / 1400, 1); el.firstChild.textContent = Math.floor(ease(p) * target).toLocaleString(); if (p < 1) requestAnimationFrame(step); else el.firstChild.textContent = target.toLocaleString(); } requestAnimationFrame(step); io.unobserve(el); }); }, { threshold: 0.5 });
  document.querySelectorAll('.stat-number[data-target]').forEach(el => io.observe(el));

  const revealIo = new IntersectionObserver(entries => { entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('is-visible'); revealIo.unobserve(e.target); } }); }, { threshold: 0.1 });
  document.querySelectorAll('.reveal').forEach(el => revealIo.observe(el));
})();
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/templates/public/layout.php';
?>