<?php
// public/components/admin/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$role = $_SESSION['role'] ?? '';
$role_id = $_SESSION['role_id'] ?? 0;

// Determine active menu
function isActive($path, $current_dir, $current_page) {
    if (strpos($current_dir, $path) !== false) return true;
    if ($current_page == $path) return true;
    return false;
}

// Check if we're in a store subdirectory
function isStoreActive($current_dir) {
    return $current_dir == 'store' || $current_dir == 'categories';
}
?>
<div class="admin-sidebar">
    <div class="sidebar-header text-center">
        <h3>
            <i class="fas fa-shield-alt me-2"></i>
            Ismano Admin
        </h3>
        <small>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?></small>
        <small class="d-block mt-1">
            <span class="badge" style="background:#2a2a2a;">
                <?php echo ucfirst($role); ?>
            </span>
        </small>
    </div>
    
    <div class="sidebar-nav">
        <div class="nav flex-column">
            <!-- Dashboard -->
            <a href="/Ismano/public/admin/dashboard.php" 
               class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            
            <!-- Projects -->
            <a href="/Ismano/public/admin/projects/index.php" 
               class="nav-link <?php echo isActive('projects', $current_dir, $current_page) ? 'active' : ''; ?>">
                <i class="fas fa-folder-open"></i>
                <span>Projects</span>
            </a>
            
            <!-- Services -->
            <a href="/Ismano/public/admin/services/index.php" 
               class="nav-link <?php echo isActive('services', $current_dir, $current_page) ? 'active' : ''; ?>">
                <i class="fas fa-cogs"></i>
                <span>Services</span>
            </a>
            
            <!-- Blog Posts -->
            <a href="/Ismano/public/admin/blogs/index.php" 
               class="nav-link <?php echo isActive('blogs', $current_dir, $current_page) ? 'active' : ''; ?>">
                <i class="fas fa-blog"></i>
                <span>Blog Posts</span>
            </a>

            <!-- Gallery Posts -->
            <a href="/Ismano/public/admin/gallery/index.php" 
               class="nav-link <?php echo isActive('gallery', $current_dir, $current_page) ? 'active' : ''; ?>">
                <i class="fas fa-images"></i>
                <span>Gallery</span>
            </a>
            
            <!-- STORE SECTION with Dropdown -->
            <div class="nav-item">
                <a href="#storeMenu" class="nav-link <?php echo isStoreActive($current_dir) ? 'active' : ''; ?>" 
                   data-bs-toggle="collapse" role="button" aria-expanded="<?php echo isStoreActive($current_dir) ? 'true' : 'false'; ?>">
                    <i class="fas fa-store"></i>
                    <span>Store</span>
                    <i class="fas fa-chevron-down ms-auto" style="font-size: 12px;"></i>
                </a>
                <div class="collapse <?php echo isStoreActive($current_dir) ? 'show' : ''; ?>" id="storeMenu">
                    <div class="ps-4 mt-2">
                        <a href="/Ismano/public/admin/store/products/index.php" 
                           class="nav-link <?php echo $current_dir == 'products' ? 'active' : ''; ?>">
                            <i class="fas fa-box"></i>
                            <span>Products</span>
                        </a>
                        <a href="/Ismano/public/admin/store/categories/index.php" 
                           class="nav-link <?php echo $current_dir == 'categories' ? 'active' : ''; ?>">
                            <i class="fas fa-tags"></i>
                            <span>Categories</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <!-- Profile -->
            <a href="/Ismano/public/profile/admin/" 
               class="nav-link <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-circle"></i>
                <span>My Profile</span>
            </a>
            
            <!-- Settings -->
            <a href="/Ismano/public/admin/settings/index.php" 
               class="nav-link <?php echo isActive('settings', $current_dir, $current_page) ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            
            <!-- Logout -->
            <a href="/Ismano/public/auth/logout.php" class="nav-link text-danger">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</div>

<style>
/* Sidebar dropdown styles */
.admin-sidebar .nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
}

.admin-sidebar .nav-link .fa-chevron-down {
    margin-left: auto;
    transition: transform 0.3s ease;
}

.admin-sidebar .nav-link[aria-expanded="true"] .fa-chevron-down {
    transform: rotate(180deg);
}

.admin-sidebar .collapse .nav-link {
    padding-left: 35px;
    font-size: 0.85rem;
}

.admin-sidebar .collapse .nav-link i {
    font-size: 0.8rem;
    width: 20px;
}

.admin-sidebar .nav-link.active {
    background: #2a2a2a;
    color: #fff;
}

.admin-sidebar .nav-link.active i {
    color: #fff;
}

.admin-sidebar .collapse .nav-link.active {
    background: #2a2a2a;
    border-left: 3px solid var(--brand-primary, #00A1F3);
}
</style>

<script>
// Store dropdown state in localStorage
document.addEventListener('DOMContentLoaded', function() {
    const storeLink = document.querySelector('a[href="#storeMenu"]');
    const storeMenu = document.getElementById('storeMenu');
    
    if (storeLink && storeMenu) {
        // Load saved state
        const savedState = localStorage.getItem('storeMenuOpen');
        if (savedState === 'true') {
            storeMenu.classList.add('show');
            storeLink.setAttribute('aria-expanded', 'true');
        }
        
        // Save state when toggled
        storeLink.addEventListener('click', function(e) {
            setTimeout(() => {
                const isOpen = storeMenu.classList.contains('show');
                localStorage.setItem('storeMenuOpen', isOpen);
            }, 100);
        });
    }
});
</script>