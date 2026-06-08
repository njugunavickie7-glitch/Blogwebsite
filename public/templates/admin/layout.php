<?php
// public/templates/admin/layout.php
if (session_status() === PHP_SESSION_NONE) session_start();

// Check authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id']) || $_SESSION['role_id'] > 2) {
    header('Location: /Ismano/public/auth/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo $page_title ?? 'Ismano'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Black & White Admin Theme */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
        }
        
        /* Admin Wrapper */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .admin-sidebar {
            width: 280px;
            background: #1a1a1a;
            color: #e0e0e0;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        }
        
        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid #333;
            margin-bottom: 20px;
        }
        
        .sidebar-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #fff;
            margin: 0;
        }
        
        .sidebar-header small {
            font-size: 0.75rem;
            color: #888;
            margin-top: 8px;
            display: block;
        }
        
        .sidebar-nav {
            padding: 0 15px;
        }
        
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: #ccc;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 4px;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }
        
        .sidebar-nav .nav-link i {
            width: 20px;
            font-size: 1rem;
            color: #888;
        }
        
        .sidebar-nav .nav-link:hover {
            background: #2a2a2a;
            color: #fff;
        }
        
        .sidebar-nav .nav-link:hover i {
            color: #fff;
        }
        
        .sidebar-nav .nav-link.active {
            background: #2a2a2a;
            color: #fff;
        }
        
        .sidebar-nav .nav-link.active i {
            color: #fff;
        }
        
        .sidebar-nav .nav-link.text-danger {
            color: #dc2626;
        }
        
        .sidebar-nav .nav-link.text-danger:hover {
            background: #dc2626;
            color: #fff;
        }
        
        .sidebar-nav .nav-link.text-danger:hover i {
            color: #fff;
        }
        
        .sidebar-nav hr {
            border-color: #333;
            margin: 15px 0;
        }
        
        /* Main Content Area */
        .admin-content {
            flex: 1;
            margin-left: 280px;
            padding: 25px 30px;
            min-height: 100vh;
            background: #f5f5f5;
        }
        
        /* Top Bar */
        .top-bar {
            background: #fff;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
        }
        
        .breadcrumb {
            margin: 0;
            padding: 0;
            background: transparent;
            font-size: 0.8rem;
        }
        
        .breadcrumb-item a {
            color: #666;
            text-decoration: none;
        }
        
        .breadcrumb-item a:hover {
            color: #1a1a1a;
        }
        
        .breadcrumb-item.active {
            color: #1a1a1a;
            font-weight: 500;
        }
        
        .user-dropdown .btn {
            background: #fff;
            border: 1px solid #e0e0e0;
            color: #1a1a1a;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.85rem;
        }
        
        .user-dropdown .btn:hover {
            background: #f5f5f5;
            border-color: #ccc;
        }
        
        .dropdown-menu {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .dropdown-item {
            padding: 8px 16px;
            font-size: 0.85rem;
            color: #1a1a1a;
        }
        
        .dropdown-item i {
            width: 20px;
            margin-right: 8px;
            color: #666;
        }
        
        .dropdown-item:hover {
            background: #f5f5f5;
        }
        
        /* Alert Styles */
        .alert {
            border-radius: 8px;
            border: none;
            padding: 12px 20px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
        }
        
        .alert-danger {
            background: #ffebee;
            color: #c62828;
        }
        
        /* Cards */
        .card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .card-header {
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 15px 20px;
            font-weight: 600;
        }
        
        /* Tables */
        .table {
            color: #1a1a1a;
        }
        
        .table thead th {
            background: #fafafa;
            border-bottom: 2px solid #e0e0e0;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        /* Buttons */
        .btn-primary {
            background: #1a1a1a;
            border-color: #1a1a1a;
        }
        
        .btn-primary:hover {
            background: #333;
            border-color: #333;
        }
        
        .btn-outline-primary {
            color: #1a1a1a;
            border-color: #ccc;
        }
        
        .btn-outline-primary:hover {
            background: #1a1a1a;
            border-color: #1a1a1a;
            color: #fff;
        }
        
        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #1a1a1a;
            color: #fff;
            border: none;
            cursor: pointer;
            z-index: 1001;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .mobile-menu-toggle:hover {
            background: #333;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .admin-sidebar.active {
                transform: translateX(0);
            }
            
            .admin-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .mobile-menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .top-bar {
                flex-direction: column;
                gap: 15px;
            }
        }
        
        /* Scrollbar */
        .admin-sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .admin-sidebar::-webkit-scrollbar-track {
            background: #1a1a1a;
        }
        
        .admin-sidebar::-webkit-scrollbar-thumb {
            background: #444;
            border-radius: 3px;
        }
    </style>
    
    <?php if (isset($extra_css)): ?>
        <?php echo $extra_css; ?>
    <?php endif; ?>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Include Admin Sidebar -->
        <?php include_once __DIR__ . '/../../components/admin/sidebar.php'; ?>
        
        <!-- Main Content Area -->
        <div class="admin-content">
            <!-- Top Bar -->
            <div class="top-bar d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title"><?php echo $page_title ?? 'Dashboard'; ?></h1>
                    <?php if (isset($breadcrumbs)): ?>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mt-2">
                                <li class="breadcrumb-item"><a href="/Ismano/public/admin/dashboard.php">Dashboard</a></li>
                                <?php foreach ($breadcrumbs as $crumb): ?>
                                    <li class="breadcrumb-item <?php echo isset($crumb['active']) ? 'active' : ''; ?>">
                                        <?php if (isset($crumb['url']) && !isset($crumb['active'])): ?>
                                            <a href="<?php echo $crumb['url']; ?>"><?php echo $crumb['label']; ?></a>
                                        <?php else: ?>
                                            <?php echo $crumb['label']; ?>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </nav>
                    <?php endif; ?>
                </div>
                <div class="user-dropdown dropdown">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>
                        <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/Ismano/public/profile/">
                            <i class="fas fa-user"></i> My Profile
                        </a></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="fas fa-cog"></i> Settings
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/Ismano/public/auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash']['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $_SESSION['flash']['success']; unset($_SESSION['flash']['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['flash']['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $_SESSION['flash']['error']; unset($_SESSION['flash']['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Page Content -->
            <?php echo $content; ?>
        </div>
    </div>
    
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Mobile Menu Script -->
    <script>
        const mobileToggle = document.getElementById('mobileMenuToggle');
        const sidebar = document.querySelector('.admin-sidebar');
        
        if (mobileToggle && sidebar) {
            mobileToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const isMobile = window.innerWidth <= 768;
                if (isMobile && sidebar.classList.contains('active')) {
                    if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                        sidebar.classList.remove('active');
                    }
                }
            });
        }
    </script>
    
    <?php if (isset($extra_js)): ?>
        <?php echo $extra_js; ?>
    <?php endif; ?>
</body>
</html>