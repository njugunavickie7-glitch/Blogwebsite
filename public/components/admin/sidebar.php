<?php
// public/components/admin/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$role = $_SESSION['role'] ?? '';
$role_id = $_SESSION['role_id'] ?? 0;

// Determine active menu
function isActive($path, $current_dir, $current_page) {
    if (strpos($path, $current_dir) !== false) return true;
    if ($current_page == $path) return true;
    return false;
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
            
            <hr>
            
            <!-- Profile -->
            <a href="/Ismano/public/profile/admin/" 
               class="nav-link <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-circle"></i>
                <span>My Profile</span>
            </a>
            
            <!-- Settings -->
            <a href="/Ismano/public/admin/settings/index.php" class="nav-link">
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