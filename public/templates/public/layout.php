<?php
// public/templates/public/layout.php
if (session_status() === PHP_SESSION_NONE) session_start();

// Brand colors
$brandPrimary = '#00A1F3';
$brandSecondary = '#0759F8';
$brandAccent = '#EBA94E';

// Determine which navbar to use (homepage has special hero navbar)
$useHomeNavbar = $use_home_navbar ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo $page_title ?? 'Ismano - Creative Digital Solutions'; ?></title>
    <meta name="description" content="<?php echo $page_description ?? 'Explore our creative portfolio of web development, design, and digital solutions.'; ?>">
    
    <!-- Theme CSS -->
    <link rel="stylesheet" href="/Ismano/public/assets/css/theme.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --brand-primary: <?php echo $brandPrimary; ?>;
            --brand-secondary: <?php echo $brandSecondary; ?>;
            --brand-accent: <?php echo $brandAccent; ?>;
            --brand-white: #FFFFFF;
            --brand-black: #000000;
            
            --font-display: 'Playfair Display', Georgia, serif;
            --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            
            --navbar-height: 80px;
            --transition-fast: 0.2s ease;
            --transition-base: 0.3s ease;
            
            --space-1: 4px;
            --space-2: 8px;
            --space-3: 12px;
            --space-4: 16px;
            --space-5: 20px;
            --space-6: 24px;
            --space-8: 32px;
            --space-10: 40px;
            --space-12: 48px;
            --space-16: 64px;
            --space-20: 80px;
            --space-24: 96px;
            
            --text-xs: 0.75rem;
            --text-sm: 0.875rem;
            --text-base: 1rem;
            --text-md: 1.125rem;
            --text-lg: 1.25rem;
            --text-xl: 1.5rem;
            --text-2xl: 2rem;
            --text-3xl: 2.5rem;
            
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --radius-xl: 20px;
            --radius-full: 9999px;
            
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
            --shadow-md: 0 5px 20px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 30px rgba(0,0,0,0.1);
            --shadow-xl: 0 20px 40px rgba(0,0,0,0.12);
            
            --color-surface: #ffffff;
            --color-surface-alt: #f8f9fa;
            --color-border: #e5e7eb;
            --color-text-heading: #1a1a1a;
            --color-text-body: #4a5568;
            --color-text-muted: #6c757d;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-body);
            font-size: var(--text-base);
            line-height: 1.6;
            color: var(--color-text-body);
            background: var(--color-surface);
            padding-top: var(--navbar-height);
        }
        
        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-display);
            font-weight: 600;
            line-height: 1.2;
            color: var(--color-text-heading);
        }
        
        /* Section styles */
        .section {
            padding: var(--space-16) 0;
        }
        
        .section-alt {
            background: var(--color-surface-alt);
        }
        
        .eyebrow {
            font-family: var(--font-body);
            font-size: var(--text-xs);
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--brand-primary);
            margin-bottom: var(--space-2);
            display: inline-block;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: var(--space-2);
            padding: 12px 28px;
            border-radius: var(--radius-full);
            font-family: var(--font-body);
            font-size: var(--text-sm);
            font-weight: 500;
            text-decoration: none;
            transition: all var(--transition-base);
            cursor: pointer;
            border: none;
        }
        
        .btn--primary {
            background: var(--brand-primary);
            color: white;
        }
        
        .btn--primary:hover {
            background: var(--brand-secondary);
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0,161,243,0.25);
        }
        
        .btn--outline {
            background: transparent;
            border: 1.5px solid var(--brand-primary);
            color: var(--brand-primary);
        }
        
        .btn--outline:hover {
            background: var(--brand-primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .btn--outline-inv {
            background: transparent;
            border: 1.5px solid white;
            color: white;
        }
        
        .btn--outline-inv:hover {
            background: white;
            color: var(--brand-primary);
        }
        
        .btn--dark {
            background: #1a1a1a;
            color: white;
        }
        
        .btn--dark:hover {
            background: #333;
            transform: translateY(-2px);
        }
        
        /* Reveal animations */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }
        
        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }
        
        /* Container */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 var(--space-6);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            :root {
                --navbar-height: 70px;
            }
            .section {
                padding: var(--space-10) 0;
            }
            .container {
                padding: 0 var(--space-4);
            }
        }
        
        @media (max-width: 480px) {
            .btn {
                padding: 10px 20px;
                font-size: var(--text-xs);
            }
        }
        
        /* Tags */
        .tag {
            display: inline-block;
            padding: 4px 12px;
            background: #f0f0f0;
            border-radius: var(--radius-full);
            font-size: var(--text-xs);
            color: #666;
            text-decoration: none;
        }
        
        .tag--muted {
            background: #e9ecef;
            color: #495057;
        }
        
        /* Text utilities */
        .text-muted {
            color: var(--color-text-muted);
        }
    </style>
    
    <?php if (isset($extra_css)): ?>
        <?php echo $extra_css; ?>
    <?php endif; ?>
</head>
<body>

<!-- Include appropriate navbar -->
<!--?php if ($useHomeNavbar): ?-->
    <!--?php include __DIR__ . '/../../components/public/nav_home.php'; ?-->
<!--?php else: ?-->
    <?php include __DIR__ . '/../../components/public/navbar.php'; ?>
<!--?php endif; ?-->

<!-- Main Content -->
<main class="main-content">
    <?php echo $content; ?>
</main>

<!-- Footer -->
<?php include __DIR__ . '/../../components/public/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if (isset($extra_js)): ?>
    <?php echo $extra_js; ?>
<?php endif; ?>
</body>
</html>