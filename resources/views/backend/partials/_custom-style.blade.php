<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --galaxy-deep: #ffffff;
        --galaxy-prime: #f9fafb;
        --galaxy-mint: #f97316;
        --galaxy-glow: rgba(249, 115, 22, 0.15);
        --galaxy-text: #1f2937;
        --galaxy-sub: #6b7280;
        --galaxy-surface: #ffffff;
        --galaxy-bg: #f9fafb;
    }

    body,
    .app-content,
    .main-content,
    .page,
    .page-main,
    body.dark-mode .app-content,
    body.dark-mode .main-content,
    body.dark-mode .page {
        font-family: 'Outfit', sans-serif !important;
        background-color: var(--galaxy-bg) !important;
        background: var(--galaxy-bg) !important;
    }

    /* ══════════════════════════════
       FULL-WIDTH DEEP DARK SIDEBAR
    ══════════════════════════════ */
    body .app-sidebar.deep-dark-glass,
    .deep-dark-glass {
        width: 280px !important;
        background: #ffffff !important;
        border-right: 1px solid #e5e7eb !important;
        box-shadow: none !important;
        margin: 0 !important;
        border-radius: 0 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh !important;
        z-index: 1050; /* Increased z-index to stay above header */
        display: flex;
        flex-direction: column;
        overflow: hidden !important;
    }

    .sticky-wrapper {
        z-index: 1040 !important; /* Ensure wrapper doesn't overlap sidebar */
    }

    .app-header {
        padding-left: 280px !important; /* Push header content to the right of sidebar */
        z-index: 1040 !important;
    }

    @media (max-width: 991px) {
        .app-header {
            padding-left: 0 !important;
        }
    }

    /* Force transparency and DISABLE GRADIENTS on theme defaults */
    body .side-header,
    body .sidebar-user-header,
    body .app-sidebar,
    body .sidebar-navs,
    body .main-sidemenu,
    body .side-menu {
        background: transparent !important;
        background-color: transparent !important;
        background-image: none !important; /* Critical to remove theme glows */
        border-color: rgba(255, 255, 255, 0.05) !important;
        box-shadow: none !important;
    }

    .side-menu {
        margin-top: 25px !important;
    }

    /* Side Header (Floating Logo Box) */
    body .side-header,
    .side-header {
        height: auto !important;
        background: transparent !important;
        padding: px 5px !important; /* Minimal padding to move it up */
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        position: relative !important;
        z-index: 1060 !important;
        border-bottom: none !important;
    }

    /* The Rounded White Logo Container */
    .header-brand1 {
        background: #ffffff !important;
        padding: 8px 15px !important; /* Bigger internal space for logo */
        border-radius: 6px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08) !important;
        width: 85% !important; /* Slightly wider logo box */
        margin: 0 auto !important;
        min-height: 45px !important; /* Taller box for the bigger logo */
    }

    #sidebar-logo-img {
        max-height: 32px !important; /* Bigger logo image */
        width: auto !important;
        display: block !important;
        filter: none !important;
    }

    /* Push the menu down */
    .main-sidemenu {
        margin-top: 5px !important; /* Minimal gap */
    }

    /* Mac Style Control Dots */
    .sidebar-control-dots {
        padding: 15px 25px 5px;
        display: flex;
        gap: 8px;
    }
    .sidebar-control-dots .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .dot.red { background: #ff5f56; }
    .dot.yellow { background: #ffbd2e; }
    .dot.green { background: #27c93f; }

    /* Top User Profile */
    .sidebar-user-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding: 24px 20px 16px !important;
        margin-bottom: 10px;
        background: transparent !important;
        display: flex !important;
        align-items: center !important;
    }
    .user-avatar img {
        width: 48px !important;
        height: 48px !important;
        border: 2px solid rgba(255, 255, 255, 0.2);
        object-fit: cover;
    }
    .user-name {
        font-family: 'Sora', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: #1f2937 !important;
        margin-bottom: 2px !important;
    }
    .user-email {
        font-size: 12px;
        color: #6b7280 !important;
    }

    /* Scrollable Menu Area */
    .main-sidemenu {
        overflow-y: auto;
        padding: 0 20px !important; /* Consistent section padding */
        margin-top: 16px !important; /* Natural gap below search */
    }
    .main-sidemenu::-webkit-scrollbar { width: 3px; }
    .main-sidemenu::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }

    /* Sidebar Search Box */
    .sidebar-search-container {
        padding: 0 20px !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .search-glass {
        background: rgba(0, 0, 0, 0.2) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 10px !important;
        padding: 8px 15px !important;
        transition: all 0.3s ease !important;
        overflow: hidden;
        display: flex !important;
        align-items: center !important;
    }
    .search-glass:focus-within {
        border-color: #6b7280 !important;
        background: rgba(0, 0, 0, 0.4) !important;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.05) !important;
    }
    .search-glass input {
        background: transparent !important;
        border: none !important;
        color: #1f2937 !important;
        padding: 8px 12px !important;
        font-size: 14px !important;
        width: 100% !important;
        outline: none !important;
    }
    .search-glass input::placeholder {
        color: rgba(255, 255, 255, 0.5) !important;
    }

    /* Sidebar Menu Labels & Icons (Pure White) */
    .side-menu .slide {
        position: relative;
    }

    .side-menu__item {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 12px 20px !important;
        width: calc(100% - 30px) !important;
        margin: 4px 15px !important;
        border-radius: 12px !important;
        transition: all 0.2s ease !important;
        color: #4b5563 !important;
        text-decoration: none !important;
    }

    .side-menu__icon {
        color: #9ca3af !important;
        fill: #9ca3af !important;
        font-size: 18px !important;
        width: 24px !important;
        text-align: center !important;
        margin-right: 0 !important;
        opacity: 0.9 !important;
    }

    svg.side-menu__icon {
        fill: #9ca3af !important;
        color: #9ca3af !important;
    }

    .side-menu__label {
        color: #4b5563 !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        letter-spacing: 0.3px !important;
    }

    .side-menu h3 {
        font-size: 11px !important;
        text-transform: uppercase !important;
        letter-spacing: 1.5px !important;
        color: rgba(255, 255, 255, 0.4) !important;
        margin: 25px 20px 10px !important;
        font-weight: 700 !important;
    }

    .side-menu__item:hover {
        background: #f9fafb !important;
        color: #1f2937 !important;
    }
    .side-menu__item:hover .side-menu__label,
    .side-menu__item:hover .side-menu__icon {
        color: #1f2937 !important;
        opacity: 1 !important;
        transform: translateX(5px);
    }

    /* Active Item (Reference Image Look) */
    .side-menu__item.active {
        background: #ffffff !important;
        color: #1f2937 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 0 0 1px #e5e7eb !important;
        border: none !important;
        border-radius: 12px !important;
        padding: 12px 20px !important;
        width: calc(100% - 30px) !important;
        margin: 4px 15px !important;
    }
    .side-menu__item.active .side-menu__label,
    .side-menu__item.active .side-menu__icon {
        color: #1f2937 !important;
        opacity: 1 !important;
    }

    /* SUBMENU TREE VIEW LINES */
    .slide-menu {
        padding: 5px 0 5px 30px !important;
        list-style: none;
        display: none;
        position: relative;
    }
    .bg-galaxy-glow { background-color: var(--galaxy-glow) !important; }
    .text-galaxy-mint { color: var(--galaxy-mint) !important; }
    .rounded-pill { border-radius: 50rem !important; }

    /* Member List / Collaboration */
    .member-item {
        padding: 12px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }
    .member-item:last-child { border-bottom: none; }
    .member-item:hover { background: rgba(0, 0, 0, 0.01); }

    .status-tag {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: capitalize;
    }
    .status-completed { background: #ecfdf5; color: #10b981; }
    .status-progress { background: #fffbeb; color: #f59e0b; }
    .status-pending { background: #fef2f2; color: #ef4444; }

    /* Time Tracker Card (Deep Dark) */
    .time-tracker-card {
        background: #022c22 !important;
        position: relative;
        overflow: hidden;
        color: #1f2937 !important;
        border: none !important;
    }

    .wave-container {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100px;
        opacity: 0.15;
        z-index: 0;
    }

    .time-display {
        font-family: 'Sora', sans-serif;
        font-size: 42px;
        font-weight: 700;
        letter-spacing: 2px;
        margin: 20px 0;
        position: relative;
        z-index: 1;
    }

    .tracker-controls {
        display: flex;
        gap: 15px;
        justify-content: center;
        position: relative;
        z-index: 1;
    }

    .control-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        transition: all 0.2s ease;
    }
    .control-btn:hover { background: rgba(255, 255, 255, 0.2); transform: scale(1.1); }
    .control-btn.btn-stop { background: #ef4444; border: none; }

    /* Dashboard List Styling */
    .project-list-item {
        display: flex;
        align-items: center;
        padding: 14px 0;
        gap: 12px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    }
    .project-list-item:last-child { border-bottom: none; }
    .icon-square {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    /* Shadow refinements */
    .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important; }
    .shadow { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important; }

    body .side-menu .slide-menu {
        list-style: none !important;
        padding: 0 !important;
        margin: 0 !important;
        position: relative;
    }

    body .side-menu .slide-menu::before {
        content: '';
        position: absolute;
        left: 27px; /* Align with Dashboard icon center */
        top: 0;
        bottom: 10px;
        width: 1px;
        background: rgba(255, 255, 255, 0.2);
    }

    body .side-menu .slide-item {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 8px 16px 8px 45px !important;
        position: relative !important;
        color: #4b5563 !important;
        font-size: 13px !important;
        opacity: 1 !important;
        transition: all 0.2s ease;
    }

    body .side-menu .slide-item::before {
        content: '';
        position: absolute;
        left: 27px !important;
        top: 50%;
        width: 6px;
        height: 6px;
        background: rgba(255, 255, 255, 0.3) !important;
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: all 0.3s ease;
        z-index: 2;
    }

    body .side-menu .slide-item:hover,
    body .side-menu .slide-item.active {
        color: #1f2937 !important;
    }

    body .side-menu .slide-item:hover::before,
    body .side-menu .slide-item.active::before {
        background: #fff !important;
        box-shadow: 0 0 8px #fff;
    }

    /* ══════════════════════════════
       3RD LEVEL SUBMENU STYLES
    ══════════════════════════════ */
    body .side-menu .sub-slide {
        position: relative;
    }

    body .side-menu .sub-side-menu__item {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 8px 16px 8px 45px !important;
        position: relative !important;
        color: #4b5563 !important;
        font-size: 13px !important;
        opacity: 1 !important;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }

    body .side-menu .sub-side-menu__item::before {
        content: '';
        position: absolute;
        left: 27px !important;
        top: 50%;
        width: 6px;
        height: 6px;
        background: rgba(255, 255, 255, 0.3) !important;
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: all 0.3s ease;
        z-index: 2;
    }

    body .side-menu .sub-side-menu__item:hover,
    body .side-menu .sub-side-menu__item.active {
        color: #1f2937 !important;
    }

    body .side-menu .sub-side-menu__item:hover::before,
    body .side-menu .sub-side-menu__item.active::before {
        background: #fff !important;
        box-shadow: 0 0 8px #fff;
    }

    body .side-menu .sub-side-menu__label {
        color: inherit !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        flex-grow: 1;
        margin-left: 0px !important;
    }

    body .side-menu .sub-angle {
        margin-left: auto;
        font-size: 10px !important;
        transition: all 0.3s ease;
        color: inherit !important;
        opacity: 0.7;
    }

    body .side-menu .sub-slide.is-expanded .sub-angle {
        transform: rotate(90deg);
    }

    body .side-menu .sub-slide-menu {
        list-style: none !important;
        padding: 5px 0 5px 0px !important;
        margin: 0 !important;
        display: none;
        position: relative;
    }

    body .side-menu .sub-slide-menu::before {
        content: '';
        position: absolute;
        left: 45px;
        top: 0;
        bottom: 10px;
        width: 1px;
        background: rgba(255, 255, 255, 0.15);
    }

    body .side-menu .sub-slide-item {
        display: flex !important;
        align-items: center !important;
        padding: 6px 16px 6px 65px !important;
        position: relative !important;
        color: #6b7280 !important;
        font-size: 12px !important;
        text-decoration: none !important;
        transition: all 0.2s ease;
        text-indent: 0px !important;
    }

    body .side-menu .sub-slide-item::before {
        content: '';
        position: absolute;
        left: 45px !important;
        top: 50%;
        width: 5px;
        height: 5px;
        background: rgba(255, 255, 255, 0.2) !important;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: all 0.3s ease;
        z-index: 2;
    }

    body .side-menu .sub-slide-item:hover,
    body .side-menu .sub-slide-item.active {
        color: #1f2937 !important;
    }

    body .side-menu .sub-slide-item:hover::before,
    body .side-menu .sub-slide-item.active::before {
        background: #fff !important;
        box-shadow: 0 0 6px #fff;
    }

    /* Floating Profile Card */
    .floating-profile-card {
        margin: auto 15px 20px !important;
        background: rgba(255, 255, 255, 0.1) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 16px !important;
        padding: 12px 15px !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px;
        backdrop-filter: blur(10px);
    }

    .user-avatar-initials {
        width: 36px;
        height: 36px;
        background: #059669;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
    }

    .floating-profile-card .user-info {
        overflow: hidden;
    }

    .floating-profile-card .user-name {
        font-size: 13px !important;
        font-weight: 600 !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .floating-profile-card .user-email {
        font-size: 11px !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ══════════════════════════════
       PREMIUM HEADER & FOOTER (Obsidian Ash)
    ══════════════════════════════ */
    .app-header {
        height: 70px !important;
        padding-left: 280px !important;
        transition: all 0.3s ease;
        background: #ffffff !important;
        backdrop-filter: blur(20px) saturate(180%) !important;
        border-bottom: 1px solid #e5e7eb !important;
        display: flex !important;
        align-items: center !important;
        box-shadow: none !important;
        z-index: 1020 !important;
    }

    body .footer {
        padding-left: 280px !important;
        background: #ffffff !important;
        border-top: 1px solid #e2e8f0 !important;
        color: #022c22 !important;
        padding-top: 20px !important;
        padding-bottom: 20px !important;
        transition: all 0.3s ease;
    }
    /* Header Left Side Fix */
    .app-header .main-container {
        display: flex !important;
        align-items: center !important;
        height: 70px !important;
    }
    .app-header .main-container > .d-flex {
        width: 100% !important;
        align-items: center !important;
    }

    /* Target ALL Header Icons (Left & Right) */
    .app-header .header-link,
    .app-header .nav-link,
    .app-header .app-sidebar__toggle,
    .app-header .main-header-center button {
        color: #4b5563 !important;
        background: #f9fafb !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 50% !important;
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        padding: 0 !important;
        margin: 0 5px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.2s ease !important;
        position: relative !important;
        top: 0 !important;
        bottom: 0 !important;
    }

    /* Correcting the Sidebar Toggle Specifically */
    .app-header .app-sidebar__toggle {
        font-family: inherit !important; /* Don't force font here */
        text-decoration: none !important;
        margin-left: 0 !important;
    }
    .app-sidebar__toggle::before {
        content: "\f0c9" !important; /* FontAwesome bars icon */
        font-family: "Font Awesome 6 Free" !important;
        font-weight: 900 !important;
        font-size: 16px !important;
        color: #4b5563 !important;
    }

    /* Fix the Sync/Globe buttons stacking */
    .main-header-center {
        display: flex !important;
        align-items: center !important;
        margin: 0 !important;
        padding: 0 10px !important;
    }
    .main-header-center button {
        /* Squircle handled above */
    }

    .app-header .header-link svg,
    .app-header .nav-link svg,
    .app-header .main-header-center i {
        width: 18px !important;
        height: 18px !important;
        fill: #4b5563 !important;
        color: #4b5563 !important;
        font-size: 16px !important;
    }

    .app-header .header-link:hover,
    .app-header .nav-link:hover,
    .app-header .app-sidebar__toggle:hover,
    .app-header .main-header-center button:hover {
        background: #f3f4f6 !important;
        border-color: #d1d5db !important;
        box-shadow: none !important;
    }

    /* Avatar specific visibility */
    .app-header .profile-user img {
        border: 2px solid #e5e7eb !important;
        padding: 2px;
        width: 38px !important;
        height: 38px !important;
        box-shadow: none !important;
    }

    .app-header .profile-1 .nav-link {
        border: none !important;
        background: none !important;
        width: auto !important;
    }

    .app-content {
        margin-left: 280px !important;
        transition: all 0.3s ease;
    }

    .app-content {
        margin-left: 280px !important;
        transition: all 0.3s ease;
    }

    /* WHEN TOGGLED (Minimized to Icons) */
    body.sidenav-toggled .deep-dark-glass {
        width: 80px !important;
    }
    body.sidenav-toggled .app-header, body.sidenav-toggled .footer {
        padding-left: 80px !important;
    }
    body.sidenav-toggled .app-content {
        margin-left: 80px !important;
    }

    /* Hide specific elements on toggle */
    body.sidenav-toggled .side-menu__label,
    body.sidenav-toggled .angle,
    body.sidenav-toggled .sidebar-user-header,
    body.sidenav-toggled .sidebar-search-container,
    body.sidenav-toggled .sidebar-action-card,
    body.sidenav-toggled #sidebar-logo-img,
    body.sidenav-toggled .sub-header-title {
        display: none !important;
    }

    body.sidenav-toggled .sidebar-control-dots {
        flex-direction: column;
        align-items: center;
        padding: 20px 0;
    }

    body.sidenav-toggled .side-menu__icon {
        margin-right: 0 !important;
        width: 100%;
    }

    /* Select2 Width & Style Fixes */
    .select2-container {
        width: 100% !important;
        display: block !important;
    }
    .select2-container--default .select2-selection--multiple {
        border: 2px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 4px 8px !important;
        min-height: 48px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #059669 !important;
    }
    /* Select2 Choice (Tags) - Restored Blue/Violet Theme */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #059669 !important; /* Restored previous color */
        border: none !important;
        color: #ffffff !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        margin-top: 8px !important;
        margin-right: 8px !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #ffffff !important;
        margin-right: 8px !important;
        font-size: 16px !important;
    }

    /* Select2 Dropdown / Results Styling - Forced White BG */
    .select2-dropdown,
    .select2-results,
    .select2-results__options {
        background-color: #ffffff !important;
    }

    .select2-dropdown {
        border: 1px solid rgba(0,0,0,0.1) !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        overflow: hidden !important;
        z-index: 9999 !important;
    }

    .select2-container--default .select2-results__options .select2-results__option {
        padding: 12px 16px !important;
        color: #0f172a !important; /* Dark text by default */
        font-size: 14px !important;
        background-color: #ffffff !important; /* Force white item BG */
        border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        font-weight: 600 !important;
        transition: none !important; /* Disable transitions to avoid flickering with Select2 */
    }

    /* ONLY Hover State - Burgundy Color */
    .select2-container--default .select2-results__options .select2-results__option--highlighted {
        background-color: #800020 !important; /* Burgundy */
        color: #ffffff !important; /* White text on hover */
        font-weight: 700 !important;
    }

    /* Reset selection if needed */
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #ffffff !important; /* Keep selected white in list */
        color: #0f172a !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true].select2-results__option--highlighted {
        background-color: #800020 !important; /* Burgundy on hover even if selected */
        color: #ffffff !important;
    }

    /* Search Box in Dropdown Header */
    .select2-search--dropdown {
        background-color: #f8fafc !important;
        padding: 12px !important;
        border-bottom: 1px solid rgba(0,0,0,0.05) !important;
    }

    .select2-search--dropdown .select2-search__field {
        background-color: #ffffff !important;
        border: 1px solid rgba(0,0,0,0.1) !important;
        color: #0f172a !important;
        border-radius: 8px !important;
        padding: 10px !important;
    }

    body.sidenav-toggled .side-menu__item {
        justify-content: center !important;
        margin: 5px 10px !important;
    }

    /* Main Content Background (Ash Theme) */
    .app.sidebar-mini {
        background-color: #f1f5f9 !important; /* Ash / Light Slate */
    }
    .main-container {
        padding: 30px !important;
    }

    /* Mobile Logic */
    @media (max-width: 991px) {
        .deep-dark-glass {
            left: -250px !important;
        }
        .sidenav-toggled .deep-dark-glass {
            left: 0 !important;
            width: 250px !important;
        }
        .app-header, .app-content {
            margin-left: 0 !important;
            padding-left: 0 !important;
        }
        body.sidenav-toggled .side-menu__label,
        body.sidenav-toggled .sidebar-user-header,
        body.sidenav-toggled #sidebar-logo-img {
            display: block !important;
        }
    }

    /* ══════════════════════════════
       DASHBOARD PREMIUM AESTHETICS (v2 Professional)
    ══════════════════════════════ */
    .glass-card {
        background: var(--galaxy-surface) !important;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
        border-radius: 28px !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.02), 0 8px 10px -6px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        padding: 1.5rem !important;
    }

    .glass-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px -12px rgba(16, 185, 129, 0.15) !important;
        border-color: rgba(16, 185, 129, 0.25) !important;
    }

    /* Professional Stat Cards */
    .stat-card-galaxy {
        background: linear-gradient(135deg, var(--galaxy-deep) 0%, var(--galaxy-prime) 100%) !important;
        color: #1f2937 !important;
    }

    .stat-card-galaxy .card-title {
        color: #1f2937 !important;
        font-weight: 800 !important;
    }

    .stat-card-galaxy .card-label {
        color: #4b5563 !important;
    }

    .glass-icon-box {
        width: 54px;
        height: 54px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    /* Action Buttons (Galaxy Style) */
    .btn-galaxy {
        background: linear-gradient(90deg, #10b981, #6952ec) !important;
        color: #1f2937 !important;
        border-radius: 100px !important;
        padding: 0.75rem 1.75rem !important;
        font-weight: 600 !important;
        border: none !important;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3) !important;
        transition: all 0.3s ease !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-galaxy:hover {
        background: linear-gradient(90deg, #4432c2, #5a4bdf) !important;
        color: #1f2937 !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4) !important;
    }

    .btn-outline-galaxy {
        background: transparent !important;
        color: var(--galaxy-mint) !important;
        border: 2px solid var(--galaxy-mint) !important;
        border-radius: 100px !important;
        padding: 0.75rem 1.75rem !important;
        font-weight: 600 !important;
        transition: all 0.3s ease !important;
    }

    .btn-outline-galaxy:hover {
        background: rgba(16, 185, 129, 0.05) !important;
        transform: scale(1.05);
    }

    /* Page Header Overrides */
    .page-title {
        font-family: 'Sora', sans-serif !important;
        font-weight: 800 !important;
        letter-spacing: -1px;
        color: #022c22 !important;
    }

    .page-subtitle {
        color: var(--galaxy-sub) !important;
        font-size: 1rem;
        font-weight: 400;
        margin-top: 4px;
    }

    /* Chart Overrides */
    .apexcharts-canvas {
        font-family: 'Outfit', sans-serif !important;
    }

    /* Final Polish for Obsidian Icons & Footer */
    .app-header .header-link,
    .app-header .nav-link,
    .app-header .app-sidebar__toggle,
    .app-header .main-header-center button {
        border-radius: 12px !important;
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        margin: 0 4px !important;
    }

    body.sidenav-toggled .footer {
        padding-left: 80px !important;
    }

    /* ══════════════════════════════
       AESTHETIC ADMIN LOADER
    ══════════════════════════════ */
    #global-loader {
        position: fixed;
        z-index: 99999;
        inset: 0;
        overflow: hidden;
        display: grid;
        place-items: center;
        background:
            radial-gradient(circle at 14% 20%, rgba(56, 189, 248, 0.22), transparent 42%),
            radial-gradient(circle at 82% 82%, rgba(59, 130, 246, 0.18), transparent 44%),
            linear-gradient(140deg, rgba(2, 6, 23, 0.96), rgba(15, 23, 42, 0.95));
        backdrop-filter: blur(12px) saturate(130%);
        -webkit-backdrop-filter: blur(12px) saturate(130%);
    }

    .admin-loader-bg-glow {
        position: absolute;
        width: min(40vw, 420px);
        height: min(40vw, 420px);
        border-radius: 50%;
        filter: blur(8px);
        pointer-events: none;
        animation: admin-loader-glow-float 7s ease-in-out infinite;
    }

    .admin-loader-bg-glow--left {
        top: -10%;
        left: -8%;
        background: radial-gradient(circle, rgba(34, 211, 238, 0.22), rgba(34, 211, 238, 0));
    }

    .admin-loader-bg-glow--right {
        bottom: -12%;
        right: -6%;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.25), rgba(99, 102, 241, 0));
        animation-delay: -3.5s;
    }

    .admin-loader-card {
        position: relative;
        z-index: 1;
        width: min(92vw, 460px);
        padding: 34px 28px 30px;
        border-radius: 26px;
        border: 1px solid rgba(255, 255, 255, 0.16);
        background: linear-gradient(150deg, rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0.05));
        box-shadow: 0 28px 70px rgba(2, 6, 23, 0.52), inset 0 1px 0 rgba(255, 255, 255, 0.16);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 14px;
    }

    .admin-loader-logo-wrap {
        width: 88px;
        height: 88px;
        border-radius: 22px;
        padding: 14px;
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.2), rgba(99, 102, 241, 0.25));
        border: 1px solid rgba(255, 255, 255, 0.16);
        box-shadow: inset 0 0 28px rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        animation: admin-loader-logo-breathe 2.4s ease-in-out infinite;
    }

    .admin-loader-logo {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 0 16px rgba(125, 211, 252, 0.3));
    }

    .admin-loader-orbit {
        position: relative;
        width: 112px;
        height: 112px;
        margin-top: 4px;
    }

    .admin-loader-ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 2px solid transparent;
        border-top-color: #22d3ee;
        border-right-color: rgba(147, 197, 253, 0.72);
        animation: admin-loader-spin 1.3s linear infinite;
    }

    .admin-loader-ring--delayed {
        inset: 14px;
        border-top-color: #818cf8;
        border-right-color: rgba(196, 181, 253, 0.7);
        animation: admin-loader-spin-reverse 1.7s linear infinite;
    }

    .admin-loader-core-dot {
        position: absolute;
        width: 18px;
        height: 18px;
        border-radius: 999px;
        background: radial-gradient(circle at 28% 28%, #f8fafc, #22d3ee 58%, #0ea5e9 100%);
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        box-shadow: 0 0 20px rgba(34, 211, 238, 0.7);
        animation: admin-loader-core-pulse 1.6s ease-in-out infinite;
    }

    .admin-loader-title {
        margin: 2px 0 0;
        color: #f8fafc;
        font-family: 'Sora', sans-serif;
        font-size: 1.2rem;
        font-weight: 700;
        letter-spacing: 0.2px;
    }

    .admin-loader-subtitle {
        margin: 0;
        color: rgba(226, 232, 240, 0.86);
        font-size: 0.92rem;
        font-weight: 500;
        line-height: 1.5;
    }

    .admin-loader-ellipsis::after {
        content: '...';
        display: inline-block;
        width: 0;
        overflow: hidden;
        vertical-align: bottom;
        animation: admin-loader-ellipsis 1.3s steps(4, end) infinite;
    }

    @keyframes admin-loader-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes admin-loader-spin-reverse {
        to {
            transform: rotate(-360deg);
        }
    }

    @keyframes admin-loader-core-pulse {
        0%,
        100% {
            transform: translate(-50%, -50%) scale(0.92);
            opacity: 0.92;
        }

        50% {
            transform: translate(-50%, -50%) scale(1.1);
            opacity: 1;
        }
    }

    @keyframes admin-loader-logo-breathe {
        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-3px);
        }
    }

    @keyframes admin-loader-glow-float {
        0%,
        100% {
            transform: translateY(0) scale(1);
            opacity: 0.8;
        }

        50% {
            transform: translateY(-16px) scale(1.06);
            opacity: 1;
        }
    }

    @keyframes admin-loader-ellipsis {
        to {
            width: 1.35em;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .admin-loader-bg-glow,
        .admin-loader-logo-wrap,
        .admin-loader-ring,
        .admin-loader-ring--delayed,
        .admin-loader-core-dot,
        .admin-loader-ellipsis::after {
            animation: none !important;
        }
    }

    /* ══════════════════════════════
       PREMIUM ACTION BUTTONS
    ══════════════════════════════ */
    .btn-action-premium {
        width: 44px !important;
        height: 44px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 14px !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border: none !important;
        margin: 0 4px !important;
        padding: 0 !important;
    }

    .btn-action-premium i {
        font-size: 1.4rem !important; /* Larger icons as requested (Increased to 1.4rem) */
        transition: all 0.3s ease !important;
    }

    .btn-action-premium:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .btn-action-premium:hover i {
        transform: scale(1.1);
        color: #ffffff !important;
    }

    /* Color Variants with Soft Tint */
    .btn-premium-edit {
        background: rgba(16, 185, 129, 0.1) !important;
        color: #10b981 !important;
    }
    .btn-premium-edit:hover { background: #10b981 !important; }

    .btn-premium-view {
        background: rgba(59, 130, 246, 0.1) !important;
        color: #3b82f6 !important;
    }
    .btn-premium-view:hover { background: #3b82f6 !important; }

    .btn-premium-status {
        background: rgba(245, 158, 11, 0.1) !important;
        color: #f59e0b !important;
    }
    .btn-premium-status:hover { background: #f59e0b !important; }

    .btn-premium-delete {
        background: rgba(239, 68, 68, 0.1) !important;
        color: #ef4444 !important;
    }
    .btn-premium-delete:hover { background: #ef4444 !important; }

    /* Header Buttons (Add/Trashed) */
    .btn-header-premium {
        border-radius: 12px !important;
        padding: 8px 20px !important;
        font-weight: 600 !important;
        font-family: 'Sora', sans-serif !important;
        font-size: 13px !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease !important;
    }

    .btn-premium-add {
        background: #059669 !important;
        background-color: #059669 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 15px rgba(91, 94, 246, 0.3) !important;
        border: none !important;
        border-radius: 12px !important;
    }
    .btn-premium-add:hover,
    button.btn-premium-add:hover,
    a.btn-premium-add:hover {
        background: #047857 !important;
        background-color: #047857 !important;
        color: #ffffff !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(91, 94, 246, 0.4) !important;
    }

    /* Shared style for SS-Primary buttons */
    .btn-ss-primary {
        background: #059669 !important;
        color: #ffffff !important;
        border-radius: 12px !important;
        padding: 10px 24px !important;
        font-weight: 700 !important;
        border: none !important;
        transition: all 0.3s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .btn-ss-primary:hover {
        background: #047857 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(91, 94, 246, 0.3) !important;
    }

    .btn-premium-trashed {
        background: rgba(239, 68, 68, 0.05) !important;
        border: 2px solid #ef4444 !important;
        color: #ef4444 !important;
    }
    .btn-premium-trashed:hover {
        background: #ef4444 !important;
        color: #1f2937 !important;
        transform: translateY(-2px);
    }


    /* Fix Summernote & CKEditor Bullets */
    .note-editable ul, .ck-content ul {
        list-style: disc !important;
        padding-left: 2rem !important;
    }

    .note-editable ol, .ck-content ol {
        list-style: decimal !important;
        padding-left: 2rem !important;
    }

    .note-editable li, .ck-content li {
        display: list-item !important;
    }

    /* Invisible Text Fixes */
    .glass-card .form-control,
    .glass-card .select2-container--default .select2-selection--single,
    .glass-card .select2-container--default .select2-selection--multiple {
        background-color: rgba(15, 23, 42, 0.6) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
    }

    .glass-card .form-control:focus {
        background-color: rgba(15, 23, 42, 0.8) !important;
        color: #ffffff !important;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25) !important;
    }

    /* Force Summernote/CKEditor to have readable text */
    .note-editable, .ck-content {
        color: #334155 !important; /* Dark text for white editor background */
        background-color: #ffffff !important;
    }

    /* Footer Fix */
    .footer {
        width: 100% !important;
        margin-top: auto !important;
        z-index: 100 !important;
        background: #0f172a !important;
        border-top: 1px solid rgba(255,255,255,0.1) !important;
    }

    /* ══════════════════════════════
       PAGINATION STYLING FIX
    ══════════════════════════════ */
    .pagination {
        display: flex !important;
        flex-wrap: wrap !important;
        justify-content: center !important;
        gap: 8px !important;
        margin-top: 25px !important;
        margin-bottom: 25px !important;
    }

    .page-item {
        margin: 0 !important;
    }

    .page-link {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 42px !important;
        height: 42px !important;
        padding: 0 16px !important;
        border-radius: 12px !important;
        font-family: 'Sora', sans-serif !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        color: #334155 !important;
        background-color: #ffffff !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        text-decoration: none !important;
    }

    .page-link:hover, .page-link:focus {
        background-color: #f8fafc !important;
        color: #059669 !important;
        border-color: rgba(91, 94, 246, 0.4) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(91, 94, 246, 0.15) !important;
    }

    .page-item.active .page-link,
    .page-item.active span.page-link {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 6px 15px rgba(91, 94, 246, 0.35) !important;
    }

    .page-item.disabled .page-link,
    .page-item.disabled span.page-link {
        color: #94a3b8 !important;
        background-color: #f1f5f9 !important;
        border-color: transparent !important;
        pointer-events: none !important;
        box-shadow: none !important;
    }

    /* Fix SVG Next/Prev arrows inside pagination */
    .page-link svg, .pagination svg {
        width: 18px !important;
        height: 18px !important;
        fill: currentColor !important;
        display: inline-block !important;
    }

    /* Fix DataTables Pagination specifically */
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 25px !important;
        text-align: center !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 42px !important;
        height: 42px !important;
        padding: 0 16px !important;
        border-radius: 12px !important;
        font-family: 'Sora', sans-serif !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        color: #334155 !important;
        background-color: #ffffff !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        margin: 0 4px !important;
        box-sizing: border-box !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f8fafc !important;
        color: #059669 !important;
        border-color: rgba(91, 94, 246, 0.4) !important;
        box-shadow: 0 4px 12px rgba(91, 94, 246, 0.15) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 6px 15px rgba(91, 94, 246, 0.35) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5 !important;
        pointer-events: none !important;
        background: #f1f5f9 !important;
        border-color: transparent !important;
    }
</style>
