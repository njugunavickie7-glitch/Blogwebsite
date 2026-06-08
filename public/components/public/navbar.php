<?php
// public/components/public/navbar.php
// SINGLE shared navbar for the whole site (replaces nav_home.php).
// Works in two modes automatically:
//   - Transparent-over-hero  : any page that contains a <section class="site-hero">
//                              OR that sets  $navbar_transparent = true;  before include.
//   - Solid (interior pages) : everything else.
// Colours are inherited from theme.css tokens, with safe fallbacks so the
// bar still renders correctly even if a page forgets to load theme.css.

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Active-link helper. Guarded so including this file twice (or alongside an
// older helper) never triggers a fatal "cannot redeclare" error.
if (!function_exists('navIsActive')) {
    function navIsActive(string $segment): string {
        $uri  = $_SERVER['REQUEST_URI'] ?? '';
        $self = basename($_SERVER['PHP_SELF'] ?? '');
        if ($segment === 'home') {
            $active = $self === 'index.php'
                   && str_contains($uri, '/public/')
                   && !str_contains($uri, '/projects/')
                   && !str_contains($uri, '/services/')
                   && !str_contains($uri, '/blogs/')
                   && !str_contains($uri, '/contact');
        } else {
            $active = str_contains($uri, '/' . $segment);
        }
        return $active ? 'is-active' : '';
    }
}

// Logo path (falls back to an inline SVG mark if the file is missing).
$logoPath = '/Ismano/public/assets/images/logo/logo.png';
$logoExists = isset($_SERVER['DOCUMENT_ROOT'])
    && is_file($_SERVER['DOCUMENT_ROOT'] . $logoPath);

// Force transparent hero styling without a .site-hero element if desired.
$navbarTransparent = !empty($navbar_transparent);
?>
<header class="site-header<?php echo $navbarTransparent ? ' is-hero' : ''; ?>" id="siteHeader">
    <div class="header-inner container">

        <!-- Brand -->
        <a class="header-brand" href="/Ismano/public/" aria-label="Ismano — Home">
            <?php if ($logoExists): ?>
                <img src="<?php echo $logoPath; ?>" alt="Ismano logo" class="brand-logo" width="34" height="34">
            <?php else: ?>
                <svg class="brand-mark" width="26" height="26" viewBox="0 0 28 28" fill="none"
                     xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect width="12" height="12" fill="currentColor"/>
                    <rect x="16" width="12" height="12" fill="currentColor" opacity=".45"/>
                    <rect y="16" width="12" height="12" fill="currentColor" opacity=".45"/>
                    <rect x="16" y="16" width="12" height="12" fill="currentColor"/>
                </svg>
            <?php endif; ?>
            <span class="brand-name">Ismano</span>
        </a>

        <!-- Desktop nav -->
        <nav class="header-nav" aria-label="Primary navigation">
            <ul class="nav-list" role="list">
                <li><a class="nav-link <?php echo navIsActive('home'); ?>"     href="/Ismano/public/">Home</a></li>
                <li><a class="nav-link <?php echo navIsActive('projects'); ?>" href="/Ismano/public/projects/">Projects</a></li>
                <li><a class="nav-link <?php echo navIsActive('services'); ?>" href="/Ismano/public/services/">Services</a></li>
                <li><a class="nav-link <?php echo navIsActive('blogs'); ?>"    href="/Ismano/public/blogs/">Blog</a></li>
                <li><a class="nav-link <?php echo navIsActive('contact'); ?>"  href="/Ismano/public/contact/">Contact</a></li>
            </ul>
        </nav>

        <!-- Right-side actions -->
        <div class="header-actions">
            <?php if (!empty($_SESSION['logged_in'])): ?>
                <div class="user-menu" id="userMenu">
                    <button class="user-trigger" id="userMenuTrigger"
                            aria-expanded="false" aria-haspopup="true" aria-controls="userDropdown">
                        <span class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'] ?? '?', 0, 1)); ?></span>
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
                        <i class="fas fa-chevron-down user-caret" aria-hidden="true"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown" role="menu">
                        <a class="dropdown-item" href="/Ismano/public/profile/" role="menuitem">
                            <i class="fas fa-user" aria-hidden="true"></i> My Profile
                        </a>
                        <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] <= 2): ?>
                        <a class="dropdown-item" href="/Ismano/public/admin/dashboard.php" role="menuitem">
                            <i class="fas fa-gauge-high" aria-hidden="true"></i> Admin Panel
                        </a>
                        <?php endif; ?>
                        <hr class="dropdown-divider">
                        <a class="dropdown-item dropdown-item--danger" href="/Ismano/public/auth/logout.php" role="menuitem">
                            <i class="fas fa-arrow-right-from-bracket" aria-hidden="true"></i> Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/Ismano/public/auth/login.php"    class="btn btn--outline desk-login">Log in</a>
                <a href="/Ismano/public/auth/register.php" class="btn btn--primary desk-cta">Let's Talk</a>
            <?php endif; ?>

            <button class="hamburger" id="hamburger"
                    aria-label="Open navigation menu" aria-expanded="false" aria-controls="mobileDrawer">
                <span class="ham-bar ham-bar--top" aria-hidden="true"></span>
                <span class="ham-bar ham-bar--mid" aria-hidden="true"></span>
                <span class="ham-bar ham-bar--bot" aria-hidden="true"></span>
            </button>
        </div>
    </div>
</header>

<div class="drawer-backdrop" id="drawerBackdrop" aria-hidden="true"></div>

<aside class="mobile-drawer" id="mobileDrawer" aria-hidden="true" aria-label="Mobile navigation">
    <div class="drawer-head">
        <a class="header-brand drawer-brand" href="/Ismano/public/">
            <?php if ($logoExists): ?>
                <img src="<?php echo $logoPath; ?>" alt="Ismano logo" class="brand-logo" width="26" height="26">
            <?php else: ?>
                <svg class="brand-mark" width="22" height="22" viewBox="0 0 28 28" fill="none"
                     xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect width="12" height="12" fill="currentColor"/>
                    <rect x="16" width="12" height="12" fill="currentColor" opacity=".45"/>
                    <rect y="16" width="12" height="12" fill="currentColor" opacity=".45"/>
                    <rect x="16" y="16" width="12" height="12" fill="currentColor"/>
                </svg>
            <?php endif; ?>
            <span class="brand-name">Ismano</span>
        </a>
        <button class="drawer-close" id="drawerClose" aria-label="Close navigation menu">
            <i class="fas fa-xmark" aria-hidden="true"></i>
        </button>
    </div>

    <nav class="drawer-nav" aria-label="Mobile navigation">
        <ul class="drawer-list" role="list">
            <li class="drawer-item" style="--i:0">
                <a class="drawer-link <?php echo navIsActive('home'); ?>" href="/Ismano/public/">
                    <span class="drawer-link-label">Home</span><i class="fas fa-arrow-right drawer-arrow" aria-hidden="true"></i>
                </a>
            </li>
            <li class="drawer-item" style="--i:1">
                <a class="drawer-link <?php echo navIsActive('projects'); ?>" href="/Ismano/public/projects/">
                    <span class="drawer-link-label">Projects</span><i class="fas fa-arrow-right drawer-arrow" aria-hidden="true"></i>
                </a>
            </li>
            <li class="drawer-item" style="--i:2">
                <a class="drawer-link <?php echo navIsActive('services'); ?>" href="/Ismano/public/services/">
                    <span class="drawer-link-label">Services</span><i class="fas fa-arrow-right drawer-arrow" aria-hidden="true"></i>
                </a>
            </li>
            <li class="drawer-item" style="--i:3">
                <a class="drawer-link <?php echo navIsActive('blogs'); ?>" href="/Ismano/public/blogs/">
                    <span class="drawer-link-label">Blog</span><i class="fas fa-arrow-right drawer-arrow" aria-hidden="true"></i>
                </a>
            </li>
            <li class="drawer-item" style="--i:4">
                <a class="drawer-link <?php echo navIsActive('contact'); ?>" href="/Ismano/public/contact/">
                    <span class="drawer-link-label">Contact</span><i class="fas fa-arrow-right drawer-arrow" aria-hidden="true"></i>
                </a>
            </li>
        </ul>
    </nav>

    <div class="drawer-foot">
        <?php if (!empty($_SESSION['logged_in'])): ?>
            <div class="drawer-user">
                <div class="drawer-user-avatar"><?php echo strtoupper(substr($_SESSION['username'] ?? '?', 0, 1)); ?></div>
                <div class="drawer-user-info">
                    <span class="drawer-user-name"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
                    <span class="drawer-user-role"><?php echo (isset($_SESSION['role_id']) && $_SESSION['role_id'] <= 2) ? 'Administrator' : 'Member'; ?></span>
                </div>
            </div>
            <div class="drawer-foot-links">
                <a href="/Ismano/public/profile/" class="drawer-foot-link"><i class="fas fa-user" aria-hidden="true"></i> My Profile</a>
                <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] <= 2): ?>
                <a href="/Ismano/public/admin/dashboard.php" class="drawer-foot-link"><i class="fas fa-gauge-high" aria-hidden="true"></i> Admin Panel</a>
                <?php endif; ?>
                <a href="/Ismano/public/auth/logout.php" class="drawer-foot-link drawer-foot-link--danger"><i class="fas fa-arrow-right-from-bracket" aria-hidden="true"></i> Logout</a>
            </div>
        <?php else: ?>
            <p class="drawer-foot-tagline">Ready to start something great?</p>
            <a href="/Ismano/public/auth/register.php" class="btn btn--primary drawer-cta">Let's Talk <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            <a href="/Ismano/public/auth/login.php" class="drawer-login-link">Already have an account? <strong>Log in</strong></a>
        <?php endif; ?>
    </div>
</aside>

<style>
/* ============================================================
   NAVBAR — uses theme.css tokens; fallbacks keep it safe.
   Brand: #00A1F3 sky · #0759F8 royal · #EBA94E gold
============================================================ */
.site-header{
    position:fixed; top:0; left:0; right:0; z-index:1000;
    height:var(--navbar-height,80px);
    background:rgba(255,255,255,.96);
    backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px);
    border-bottom:1px solid transparent;
    transition:border-color .3s ease, box-shadow .3s ease, background .3s ease;
}
.site-header.is-scrolled{ border-color:rgba(7,89,248,.10); box-shadow:0 1px 22px rgba(7,89,248,.07); }
.site-header.is-hero{ background:transparent; }
.site-header.is-hero.is-scrolled{ background:rgba(255,255,255,.97); }

.header-inner{ display:flex; align-items:center; gap:var(--space-6,24px); height:100%; }

/* Brand */
.header-brand{ display:flex; align-items:center; gap:10px; flex-shrink:0; text-decoration:none; color:var(--brand-primary,#00A1F3); transition:opacity .2s ease; }
.header-brand:hover{ opacity:.82; }
.brand-logo{ width:34px; height:34px; object-fit:contain; display:block; }
.brand-mark{ display:block; color:var(--brand-primary,#00A1F3); }
.site-header.is-hero .brand-mark, .site-header.is-hero .brand-name{ color:#fff; }
.site-header.is-hero.is-scrolled .brand-mark, .site-header.is-hero.is-scrolled .brand-name{ color:var(--brand-primary,#00A1F3); }
.brand-name{ font-family:var(--font-display,'Playfair Display',serif); font-size:1.32rem; font-weight:700; line-height:1; color:var(--brand-primary,#00A1F3); }

/* Desktop nav */
.header-nav{ margin-left:auto; }
.nav-list{ display:flex; align-items:center; gap:2px; list-style:none; margin:0; padding:0; }
.nav-link{ display:block; font-family:var(--font-body,'Inter',sans-serif); font-size:.8125rem; font-weight:500; letter-spacing:.02em; color:var(--color-text-body,#3D4B5C); padding:8px 14px; border-radius:var(--radius-full,9999px); text-decoration:none; white-space:nowrap; position:relative; transition:color .2s ease, background .2s ease; }
.nav-link:hover{ color:var(--brand-primary,#00A1F3); background:rgba(0,161,243,.08); }
.nav-link.is-active{ color:var(--brand-primary,#00A1F3); font-weight:600; }
.nav-link.is-active::after{ content:''; position:absolute; bottom:4px; left:50%; transform:translateX(-50%); width:20px; height:2px; background:var(--brand-accent,#EBA94E); border-radius:2px; }
.site-header.is-hero .nav-link{ color:rgba(255,255,255,.85); }
.site-header.is-hero .nav-link:hover{ color:#fff; background:rgba(255,255,255,.12); }
.site-header.is-hero .nav-link.is-active{ color:#fff; }
.site-header.is-hero.is-scrolled .nav-link{ color:var(--color-text-body,#3D4B5C); }
.site-header.is-hero.is-scrolled .nav-link.is-active{ color:var(--brand-primary,#00A1F3); }

/* Actions + buttons */
.header-actions{ display:flex; align-items:center; gap:var(--space-3,12px); flex-shrink:0; }
.site-header .btn--outline{ display:inline-flex; align-items:center; gap:6px; padding:8px 18px; border-radius:var(--radius-full,9999px); font-size:.8125rem; font-weight:500; text-decoration:none; transition:all .2s ease; background:transparent; border:1.5px solid var(--brand-primary,#00A1F3); color:var(--brand-primary,#00A1F3); }
.site-header .btn--outline:hover{ background:var(--brand-primary,#00A1F3); color:#fff; }
.site-header .btn--primary{ display:inline-flex; align-items:center; gap:6px; padding:8px 20px; border-radius:var(--radius-full,9999px); font-size:.8125rem; font-weight:500; text-decoration:none; transition:all .2s ease; background:var(--brand-primary,#00A1F3); color:#fff; border:1.5px solid var(--brand-primary,#00A1F3); }
.site-header .btn--primary:hover{ background:var(--brand-secondary,#0759F8); border-color:var(--brand-secondary,#0759F8); transform:translateY(-1px); box-shadow:0 4px 12px rgba(7,89,248,.3); }
.site-header.is-hero .btn--outline{ border-color:rgba(255,255,255,.7); color:#fff; }
.site-header.is-hero .btn--outline:hover{ background:#fff; color:var(--brand-primary,#00A1F3); border-color:#fff; }
.site-header.is-hero .btn--primary{ background:#fff; color:var(--brand-primary,#00A1F3); border-color:#fff; }
.site-header.is-hero .btn--primary:hover{ background:var(--brand-accent,#EBA94E); border-color:var(--brand-accent,#EBA94E); color:#fff; }

/* User dropdown */
.user-menu{ position:relative; }
.user-trigger{ display:flex; align-items:center; gap:8px; background:var(--color-surface-alt,#F4F8FC); border:1px solid var(--color-border,#E3E9F0); border-radius:var(--radius-full,9999px); padding:5px 12px 5px 5px; cursor:pointer; font-family:var(--font-body,sans-serif); font-size:.875rem; color:var(--color-text-body,#3D4B5C); transition:border-color .2s ease, box-shadow .2s ease; }
.user-trigger:hover{ border-color:var(--brand-primary,#00A1F3); box-shadow:var(--shadow-sm,0 2px 8px rgba(0,0,0,.04)); }
.user-avatar{ width:28px; height:28px; border-radius:50%; background:var(--brand-primary,#00A1F3); color:#fff; display:flex; align-items:center; justify-content:center; font-size:.7rem; font-weight:700; font-family:var(--font-display,serif); flex-shrink:0; }
.user-name{ font-weight:500; max-width:90px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.user-caret{ font-size:.6rem; color:var(--color-text-muted,#6B7A8D); transition:transform .2s ease; }
.user-trigger[aria-expanded="true"] .user-caret{ transform:rotate(180deg); }
.user-dropdown{ position:absolute; top:calc(100% + 8px); right:0; min-width:200px; background:var(--color-surface,#fff); border:1px solid var(--color-border,#E3E9F0); border-radius:var(--radius-md,10px); box-shadow:var(--shadow-xl,0 20px 40px rgba(0,0,0,.12)); padding:6px; opacity:0; pointer-events:none; transform:translateY(-6px) scale(.97); transform-origin:top right; transition:opacity .2s ease, transform .2s ease; z-index:200; }
.user-dropdown.is-open{ opacity:1; pointer-events:all; transform:translateY(0) scale(1); }
.dropdown-item{ display:flex; align-items:center; gap:10px; padding:9px 12px; border-radius:var(--radius-sm,6px); font-size:.875rem; color:var(--color-text-body,#3D4B5C); text-decoration:none; transition:background .2s ease, color .2s ease; }
.dropdown-item:hover{ background:rgba(0,161,243,.08); color:var(--brand-primary,#00A1F3); }
.dropdown-item--danger{ color:#d9534f; }
.dropdown-item--danger:hover{ background:#fff5f5; color:#c0392b; }
.dropdown-item i{ width:15px; text-align:center; font-size:.85em; color:var(--color-text-muted,#6B7A8D); }
.dropdown-item--danger i{ color:#d9534f; }
.dropdown-divider{ border:none; border-top:1px solid var(--color-border,#E3E9F0); margin:5px 0; }

/* Hamburger */
.hamburger{ display:none; align-items:center; justify-content:center; width:44px; height:44px; background:none; border:1px solid var(--color-border,#E3E9F0); border-radius:var(--radius-md,10px); cursor:pointer; padding:0; flex-direction:column; gap:5px; transition:border-color .2s ease, background .2s ease; flex-shrink:0; }
.hamburger:hover{ border-color:var(--brand-primary,#00A1F3); background:rgba(0,161,243,.05); }
.site-header.is-hero .hamburger{ border-color:rgba(255,255,255,.3); }
.site-header.is-hero .hamburger:hover{ border-color:rgba(255,255,255,.7); background:rgba(255,255,255,.1); }
.ham-bar{ display:block; width:18px; height:1.5px; background:var(--brand-black,#000); border-radius:2px; transition:transform .3s ease, opacity .3s ease, width .3s ease; pointer-events:none; }
.site-header.is-hero .ham-bar{ background:#fff; }
.site-header.is-hero.is-scrolled .ham-bar{ background:var(--brand-black,#000); }
.hamburger.is-open .ham-bar--top{ transform:translateY(6.5px) rotate(45deg); }
.hamburger.is-open .ham-bar--mid{ opacity:0; transform:scaleX(0); }
.hamburger.is-open .ham-bar--bot{ transform:translateY(-6.5px) rotate(-45deg); }

/* Drawer */
.drawer-backdrop{ position:fixed; inset:0; z-index:1099; background:rgba(7,17,33,.55); backdrop-filter:blur(3px); -webkit-backdrop-filter:blur(3px); opacity:0; pointer-events:none; transition:opacity .35s ease; }
.drawer-backdrop.is-open{ opacity:1; pointer-events:all; }
.mobile-drawer{ position:fixed; top:0; right:0; bottom:0; width:min(340px,88vw); z-index:1100; background:var(--color-surface,#fff); display:flex; flex-direction:column; transform:translateX(100%); transition:transform .38s cubic-bezier(.32,0,.15,1); box-shadow:-8px 0 40px rgba(0,0,0,.15); overflow-y:auto; overscroll-behavior:contain; }
.mobile-drawer.is-open{ transform:translateX(0); }
.drawer-head{ display:flex; align-items:center; justify-content:space-between; padding:20px 24px; border-bottom:1px solid var(--color-border,#E3E9F0); flex-shrink:0; }
.drawer-brand, .drawer-brand .brand-name{ color:var(--brand-primary,#00A1F3); }
.drawer-close{ display:flex; align-items:center; justify-content:center; width:40px; height:40px; background:var(--color-surface-alt,#F4F8FC); border:1px solid var(--color-border,#E3E9F0); border-radius:var(--radius-md,10px); cursor:pointer; font-size:1rem; color:var(--color-text-body,#3D4B5C); transition:all .2s ease; flex-shrink:0; }
.drawer-close:hover{ background:var(--brand-primary,#00A1F3); border-color:var(--brand-primary,#00A1F3); color:#fff; }
.drawer-nav{ flex:1; padding:16px 0; overflow-y:auto; }
.drawer-list{ list-style:none; margin:0; padding:0; }
.drawer-item{ opacity:0; transform:translateX(20px); transition:opacity .3s ease calc(var(--i) * 50ms + 80ms), transform .3s ease calc(var(--i) * 50ms + 80ms); }
.mobile-drawer.is-open .drawer-item{ opacity:1; transform:translateX(0); }
.drawer-link{ display:flex; align-items:center; justify-content:space-between; padding:16px 24px; font-family:var(--font-display,serif); font-size:1.5rem; font-weight:600; font-style:italic; color:var(--color-text-heading,#0A1B2A); text-decoration:none; border-bottom:1px solid var(--color-border,#E3E9F0); transition:color .2s ease, background .2s ease; gap:16px; }
.drawer-link:hover, .drawer-link:focus-visible{ color:var(--brand-primary,#00A1F3); background:rgba(0,161,243,.05); }
.drawer-link.is-active{ color:var(--brand-primary,#00A1F3); }
.drawer-link-label{ position:relative; }
.drawer-link.is-active .drawer-link-label::after{ content:''; position:absolute; bottom:-3px; left:0; width:100%; height:2px; background:var(--brand-accent,#EBA94E); border-radius:2px; }
.drawer-arrow{ font-size:.75rem; color:var(--color-text-muted,#6B7A8D); transition:transform .2s ease, color .2s ease; flex-shrink:0; }
.drawer-link:hover .drawer-arrow, .drawer-link:focus-visible .drawer-arrow{ transform:translateX(4px); color:var(--brand-primary,#00A1F3); }
.drawer-foot{ border-top:1px solid var(--color-border,#E3E9F0); padding:24px; background:var(--color-surface-alt,#F4F8FC); flex-shrink:0; }
.drawer-user{ display:flex; align-items:center; gap:12px; margin-bottom:16px; padding:12px; background:var(--color-surface,#fff); border:1px solid var(--color-border,#E3E9F0); border-radius:var(--radius-md,10px); }
.drawer-user-avatar{ width:40px; height:40px; border-radius:50%; background:var(--brand-primary,#00A1F3); color:#fff; display:flex; align-items:center; justify-content:center; font-family:var(--font-display,serif); font-size:1rem; font-weight:700; flex-shrink:0; }
.drawer-user-info{ display:flex; flex-direction:column; }
.drawer-user-name{ font-weight:600; color:var(--color-text-heading,#0A1B2A); }
.drawer-user-role{ font-size:.75rem; color:var(--color-text-muted,#6B7A8D); }
.drawer-foot-links{ display:flex; flex-direction:column; gap:2px; }
.drawer-foot-link{ display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:var(--radius-sm,6px); color:var(--color-text-body,#3D4B5C); text-decoration:none; font-size:.875rem; transition:background .2s ease, color .2s ease; }
.drawer-foot-link i{ width:16px; text-align:center; }
.drawer-foot-link:hover{ background:rgba(0,161,243,.08); color:var(--brand-primary,#00A1F3); }
.drawer-foot-link--danger{ color:#d9534f; }
.drawer-foot-link--danger:hover{ background:#fff5f5; color:#c0392b; }
.drawer-foot-tagline{ font-size:.9rem; color:var(--color-text-muted,#6B7A8D); margin-bottom:14px; }
.drawer-cta{ width:100%; justify-content:center; margin-bottom:14px; font-size:.875rem; padding:12px 20px; border-radius:var(--radius-full,9999px); background:var(--brand-primary,#00A1F3); color:#fff; text-decoration:none; display:inline-flex; align-items:center; gap:8px; transition:all .2s ease; }
.drawer-cta:hover{ background:var(--brand-secondary,#0759F8); }
.drawer-login-link{ display:block; text-align:center; font-size:.85rem; color:var(--color-text-muted,#6B7A8D); text-decoration:none; }
.drawer-login-link strong{ color:var(--brand-primary,#00A1F3); font-weight:600; }

/* Responsive */
@media (max-width:1024px){ .header-inner{ gap:var(--space-4,16px); } .desk-login{ display:none; } }
@media (max-width:768px){ .header-nav{ display:none; } .desk-login,.desk-cta{ display:none; } .user-name,.user-caret{ display:none; } .hamburger{ display:inline-flex; } }
@media (max-width:380px){ .brand-name{ font-size:1.1rem; } .brand-logo{ width:28px; height:28px; } }
@media (min-width:769px){ .mobile-drawer,.drawer-backdrop{ display:none; } .hamburger{ display:none !important; } }

.hamburger:focus-visible, .drawer-close:focus-visible, .drawer-link:focus-visible, .nav-link:focus-visible{ outline:2px solid var(--brand-accent,#EBA94E); outline-offset:2px; }
</style>

<script>
(function () {
    'use strict';
    var header      = document.getElementById('siteHeader');
    var hamburger   = document.getElementById('hamburger');
    var drawer      = document.getElementById('mobileDrawer');
    var backdrop    = document.getElementById('drawerBackdrop');
    var closeBtn    = document.getElementById('drawerClose');
    var userTrigger = document.getElementById('userMenuTrigger');
    var userDropdown= document.getElementById('userDropdown');

    // Auto-enable transparent hero mode when a hero section is present.
    if (header && document.querySelector('.site-hero')) header.classList.add('is-hero');

    var ticking = false;
    function onScroll(){
        if (!ticking){
            requestAnimationFrame(function(){
                if (header) header.classList.toggle('is-scrolled', window.scrollY > 40);
                ticking = false;
            });
            ticking = true;
        }
    }
    window.addEventListener('scroll', onScroll, { passive:true });
    onScroll();

    function openDrawer(){
        drawer.classList.add('is-open'); backdrop.classList.add('is-open'); hamburger.classList.add('is-open');
        hamburger.setAttribute('aria-expanded','true'); hamburger.setAttribute('aria-label','Close navigation menu');
        drawer.setAttribute('aria-hidden','false'); backdrop.setAttribute('aria-hidden','false');
        document.body.style.overflow='hidden';
        setTimeout(function(){ if (closeBtn) closeBtn.focus(); }, 50);
    }
    function closeDrawer(){
        drawer.classList.remove('is-open'); backdrop.classList.remove('is-open'); hamburger.classList.remove('is-open');
        hamburger.setAttribute('aria-expanded','false'); hamburger.setAttribute('aria-label','Open navigation menu');
        drawer.setAttribute('aria-hidden','true'); backdrop.setAttribute('aria-hidden','true');
        document.body.style.overflow=''; hamburger.focus();
    }
    if (hamburger) hamburger.addEventListener('click', openDrawer);
    if (closeBtn)  closeBtn.addEventListener('click', closeDrawer);
    if (backdrop)  backdrop.addEventListener('click', closeDrawer);
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape' && drawer && drawer.classList.contains('is-open')) closeDrawer();
    });
    if (drawer){
        drawer.querySelectorAll('a').forEach(function(link){
            link.addEventListener('click', function(){ setTimeout(closeDrawer, 80); });
        });
    }
    if (userTrigger && userDropdown){
        userTrigger.addEventListener('click', function(){
            var open = userDropdown.classList.toggle('is-open');
            userTrigger.setAttribute('aria-expanded', String(open));
        });
        document.addEventListener('click', function(e){
            if (!userTrigger.contains(e.target) && !userDropdown.contains(e.target)){
                userDropdown.classList.remove('is-open');
                userTrigger.setAttribute('aria-expanded','false');
            }
        });
    }
})();
</script>