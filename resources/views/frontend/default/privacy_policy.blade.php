<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - {{ config('app.name', 'App') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Phosphor Icons for modern sleek icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        :root {
            --brand-dark: #1e1b4b; /* Deep Indigo / Dark Blue */
            --brand-accent: #f59e0b; /* Orange */
            --brand-accent-hover: #d97706;
            --bg-light: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #334155;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-main);
            line-height: 1.6;
        }

        /* Hero Banner */
        .hero-banner {
            background-color: var(--brand-dark);
            color: #ffffff;
            padding: 80px 20px 100px;
            text-align: center;
        }

        .hero-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }

        .hero-icon i {
            font-size: 32px;
            color: #ffffff;
        }

        .hero-banner h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .hero-banner p.subtitle {
            font-size: 18px;
            color: #cbd5e1;
            max-width: 700px;
            margin: 0 auto 20px;
            line-height: 1.5;
        }

        .hero-banner p.last-updated {
            font-size: 14px;
            color: #94a3b8;
        }

        /* Layout Container */
        .layout-container {
            max-width: 1200px;
            margin: 40px auto 60px;
            padding: 0 20px;
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: 280px;
            background: var(--card-bg);
            border-radius: 12px;
            padding: 24px 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            flex-shrink: 0;
            position: sticky;
            top: 24px;
        }

        .sidebar h3 {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 16px;
            padding-left: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nav-list {
            list-style: none;
        }

        .nav-list li {
            margin-bottom: 4px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .nav-link i {
            font-size: 18px;
        }

        .nav-link:hover {
            background-color: #f1f5f9;
            color: var(--text-main);
        }

        .nav-link.active {
            background-color: var(--brand-accent);
            color: #ffffff;
        }

        .nav-link.active i {
            color: #ffffff;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            background: var(--card-bg);
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            min-width: 0;
        }

        /* Dynamic Content Styling */
        .content-body h1, 
        .content-body h2, 
        .content-body h3, 
        .content-body h4 {
            color: var(--brand-dark);
            font-weight: 700;
            margin-top: 24px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .content-body h3 { font-size: 24px; }
        .content-body h4 {
            font-size: 18px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border-color);
        }

        .section-header-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 24px;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border-color);
        }

        .section-header-wrap i {
            color: var(--brand-accent);
            font-size: 20px;
        }

        .section-header-wrap h1,
        .section-header-wrap h2,
        .section-header-wrap h3,
        .section-header-wrap h4 {
            margin: 0;
            padding: 0;
            border: none;
            color: var(--brand-dark);
        }

        .content-body h1:first-child, 
        .content-body h2:first-child,
        .content-body h3:first-child,
        .section-header-wrap:first-child {
            margin-top: 0;
        }

        .content-body p {
            margin-bottom: 8px;
            font-size: 15px;
            color: var(--text-main);
        }

        /* Aggressively hide empty paragraphs generated by Summernote */
        .content-body p:empty,
        .content-body p:has(br:only-child) {
            display: none !important;
        }

        .content-body ul, .content-body ol {
            margin-bottom: 16px;
            padding-left: 20px;
        }

        .content-body li {
            margin-bottom: 4px;
            margin-top: 0;
            padding-bottom: 0;
            font-size: 15px;
        }

        /* If summernote wraps list text in p tags */
        .content-body li > p {
            margin: 0 !important;
            padding: 0 !important;
            display: inline;
        }

        /* Remove any extra breaks inside lists */
        .content-body li > br {
            display: none;
        }

        .content-body a {
            color: var(--brand-accent);
            text-decoration: none;
            font-weight: 500;
        }

        .content-body a:hover {
            text-decoration: underline;
        }

        /* Top Nav */
        .top-nav {
            position: absolute;
            top: 20px;
            left: 0;
            width: 100%;
            padding: 0 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            font-size: 18px;
        }
        
        .logo i {
            background: rgba(255,255,255,0.2);
            padding: 6px;
            border-radius: 8px;
            font-size: 20px;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Footer */
        .site-footer {
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            padding: 60px 20px 20px;
            margin-top: 80px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-brand h4 {
            color: var(--brand-dark);
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .footer-brand p {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 20px;
            max-width: 300px;
        }

        .social-links {
            display: flex;
            gap: 12px;
        }

        .social-links a {
            color: var(--text-muted);
            font-size: 20px;
            transition: color 0.2s;
        }

        .social-links a:hover {
            color: var(--brand-accent);
        }

        .footer-links h4 {
            color: var(--brand-dark);
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links ul li {
            margin-bottom: 12px;
        }

        .footer-links ul li a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .footer-links ul li a:hover {
            color: var(--brand-accent);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            font-size: 13px;
            color: var(--text-muted);
        }

        @media (max-width: 900px) {
            .layout-container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                position: static;
            }
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .footer-bottom {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <!-- Top Nav -->
    <div class="top-nav">
        <a href="/" class="logo">
            <i class="ph-fill ph-shield-check"></i>
            {{ config('app.name', 'Expense Management') }}
        </a>
        <a href="javascript:history.back()" class="back-btn">
            <i class="ph ph-arrow-left"></i> Go Back
        </a>
    </div>

    <!-- Hero Banner -->
    <div class="hero-banner">
        <div class="hero-icon">
            <i class="ph ph-shield-check"></i>
        </div>
        <h1>Privacy Policy</h1>
        <p class="subtitle">We're committed to protecting your privacy and ensuring transparency about how we collect, use, and safeguard your personal information.</p>
        @if(isset($privacy))
        <p class="last-updated">Last updated: {{ $privacy->updated_at ? $privacy->updated_at->format('F j, Y') : date('F j, Y') }}</p>
        @endif
    </div>

    <!-- Layout Container -->
    <div class="layout-container">
        
        <!-- Sidebar Navigation (Generated via JS) -->
        <aside class="sidebar">
            <h3>Quick Navigation</h3>
            <ul class="nav-list" id="dynamicNav">
                <!-- Links injected by JS -->
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-body" id="mainContent">
                @if(isset($privacy) && $privacy->content)
                    {!! html_entity_decode($privacy->content) !!}
                @else
                    <h4>Introduction</h4>
                    <p>No privacy policy has been set up yet. Please check back later.</p>
                @endif
            </div>
        </main>

    </div>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h4>{{ config('app.name', 'Expense Management') }}</h4>
                    <p>Empowering users worldwide with seamless financial management and innovative experiences.</p>
                    <div class="social-links">
                        <a href="#"><i class="ph ph-facebook-logo"></i></a>
                        <a href="#"><i class="ph ph-twitter-logo"></i></a>
                        <a href="#"><i class="ph ph-linkedin-logo"></i></a>
                        <a href="#"><i class="ph ph-instagram-logo"></i></a>
                    </div>
                </div>
                
                <div class="footer-links">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Terms of Use</a></li>
                        <li><a href="{{ route('privacy.policy') }}">Privacy Policy</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Community</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Expense Management') }}. All rights reserved.</p>
                <p>Made with <i class="ph-fill ph-heart" style="color: #ef4444;"></i> for our users</p>
            </div>
        </div>
    </footer>

    <!-- Script to Generate Sidebar -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const contentBody = document.getElementById('mainContent');
            const navList = document.getElementById('dynamicNav');
            
            // Find all h3 and h4 elements (matching your HTML structure)
            const headings = contentBody.querySelectorAll('h2, h3, h4');
            
            // Available icons to assign to sections
            const icons = ['ph-file-text', 'ph-database', 'ph-gear', 'ph-globe', 'ph-shield', 'ph-lock-key', 'ph-users', 'ph-cookie', 'ph-bell', 'ph-clock', 'ph-envelope'];
            
            if(headings.length === 0) {
                // If no headings, hide sidebar
                document.querySelector('.sidebar').style.display = 'none';
                return;
            }

            headings.forEach((heading, index) => {
                // Ignore the very first main heading if it's "Privacy Policy" since we already have a Hero banner
                if(index === 0 && heading.tagName.toLowerCase() === 'h3' && heading.innerText.toLowerCase().includes('privacy policy')) {
                    heading.style.display = 'none'; // Hide the duplicate title
                    return; // Skip adding to sidebar
                }

                // 1. Create a unique ID for the heading if it doesn't have one
                const id = 'section-' + index;
                heading.id = id;
                
                const title = heading.innerText.replace(/^\d+\.\s*/, '').trim(); // Remove leading numbers for cleaner sidebar
                const iconClass = icons[index % icons.length];

                // 2. Format the heading visually in the content (Wrap it to add icon like in screenshot)
                if(heading.tagName.toLowerCase() === 'h4' || heading.tagName.toLowerCase() === 'h3' || heading.tagName.toLowerCase() === 'h2') {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'section-header-wrap';
                    
                    const iconEl = document.createElement('i');
                    iconEl.className = 'ph ' + iconClass;
                    
                    const newHeading = document.createElement('h4');
                    newHeading.id = id;
                    newHeading.innerHTML = heading.innerHTML;
                    
                    wrapper.appendChild(iconEl);
                    wrapper.appendChild(newHeading);
                    
                    heading.parentNode.insertBefore(wrapper, heading);
                    heading.remove();
                }

                // 3. Add link to sidebar
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.href = '#' + id;
                // Add active class only to the first added item
                if(navList.children.length === 0) {
                    a.className = 'nav-link active';
                } else {
                    a.className = 'nav-link';
                }
                
                a.innerHTML = `<i class="ph ${iconClass}"></i> ${title}`;
                
                // Click handler
                a.addEventListener('click', function(e) {
                    // Update active state
                    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
                    this.classList.add('active');
                });

                li.appendChild(a);
                navList.appendChild(li);
            });

            // Scroll spy to update active link
            window.addEventListener('scroll', () => {
                let current = '';
                const targets = contentBody.querySelectorAll('.section-header-wrap h4');
                
                targets.forEach(heading => {
                    const headingTop = heading.getBoundingClientRect().top;
                    if (headingTop < 150) {
                        current = heading.id;
                    }
                });

                if (current) {
                    document.querySelectorAll('.nav-link').forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === '#' + current) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
