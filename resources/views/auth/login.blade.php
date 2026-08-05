@extends('auth.app')

@section('content')
<!-- CONTAINER OPEN -->


@section('content')
<style>
    /* =========================
       CSS VARIABLES
    ========================= */
    :root {
        --primary-color: #6366f1;
        --primary-dark: #4f46e5;
        --primary-light: #818cf8;
        --secondary-color: #f59e0b;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --dark: #0f172a;
        --dark-light: #1e293b;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --text-light: #94a3b8;
        --border-color: #e2e8f0;
        --bg-light: #f8fafc;
        --white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* =========================
       GLOBAL STYLES
    ========================= */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: linear-gradient(135deg, #126cc5 0%, #a58312 50%, #dee2e6 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

    /* Animated Background */
    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(circle at 20% 50%, rgba(99, 102, 241, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(168, 85, 247, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 40% 20%, rgba(59, 130, 246, 0.3) 0%, transparent 50%);
        animation: backgroundShift 15s ease infinite;
        pointer-events: none;
    }

    @keyframes backgroundShift {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    /* Floating particles effect */
    .particles {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        pointer-events: none;
        z-index: 1;
    }

    .particle {
        position: absolute;
        background: rgba(255, 255, 255, 0.5);
        /* border-radius: 50%; */
        animation: float 15s infinite;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0) translateX(0) scale(1);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translateY(-100vh) translateX(100px) scale(0);
            opacity: 0;
        }
    }

    /* =========================
       MAIN CONTAINER
    ========================= */
    .login-wrapper {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        position: relative;
        z-index: 2;
    }

    .col-login {
        margin-bottom: 2rem;
        animation: fadeInDown 0.8s ease;
    }

    .header-brand-img {
        max-width: 180px;
        height: auto;
        filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        transition: transform 0.3s ease;
    }

    .header-brand-img:hover {
        transform: scale(1.05);
    }

    /* =========================
       LOGIN CARD
    ========================= */
    .container-login100 {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .wrap-login100 {
        background: var(--white);
        /* border-radius: 24px; */
        box-shadow:
            0 20px 25px -5px rgba(0, 0, 0, 0.1),
            0 10px 10px -5px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        width: 100%;
        max-width: 480px;
        position: relative;
        animation: fadeInUp 0.8s ease;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Decorative elements */
    .wrap-login100::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .card-body {
        padding: 3rem 2.5rem;
        position: relative;
        z-index: 1;
    }

    @media (max-width: 576px) {
        .card-body {
            padding: 2rem 1.5rem;
        }
    }

    /* =========================
       FORM STYLES
    ========================= */
    .login100-form {
        width: 100%;
    }

    .login100-form-title {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .login100-form-title h2 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .login100-form-title p {
        color: var(--text-secondary);
        font-size: 0.95rem;
        margin: 0;
    }

    /* =========================
       INPUT FIELDS
    ========================= */
    .wrap-input100 {
        position: relative;
        width: 100%;
        margin-bottom: 1.5rem;
    }

    .input100 {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 3.5rem;
        font-size: 1rem;
        color: var(--text-primary);
        line-height: 1.5;
        background: var(--bg-light);
        border: 2px solid transparent;
        /* border-radius: 12px; */
        outline: none;
        transition: var(--transition);
    }

    .input100::placeholder {
        color: var(--text-light);
        font-weight: 400;
    }

    .input100:focus {
        background: var(--white);
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    /* Icon styling */
    .symbol-input100 {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.25rem;
        color: var(--text-light);
        transition: var(--transition);
        z-index: 1;
    }

    .wrap-input100:focus-within .symbol-input100 {
        color: var(--primary-color);
        transform: translateY(-50%) scale(1.1);
    }

    /* Focus animation line */
    .focus-input100 {
        position: absolute;
        display: block;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        pointer-events: none;
        border-radius: 12px;
    }

    .focus-input100::before {
        content: "";
        display: block;
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
        transition: var(--transition);
    }

    .input100:focus ~ .focus-input100::before {
        width: 100%;
    }

    /* =========================
       ERROR MESSAGES
    ========================= */
    .alert {
        padding: 0.75rem 1rem;
        margin-top: 0.5rem;
        margin-bottom: 1rem;
        /* border-radius: 10px; */
        font-size: 0.875rem;
        border: none;
        animation: shake 0.4s ease;
    }

    .alert-danger {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
        color: #dc2626;
        border-left: 4px solid #ef4444;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }

    /* =========================
       LINKS
    ========================= */
    .text-end {
        text-align: right;
    }

    .text-end p {
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }

    .text-primary {
        color: var(--primary-color) !important;
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
        position: relative;
    }

    .text-primary::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--primary-color);
        transition: width 0.3s ease;
    }

    .text-primary:hover {
        color: var(--primary-dark) !important;
    }

    .text-primary:hover::after {
        width: 100%;
    }

    /* =========================
       RECAPTCHA
    ========================= */
    .bi-login-input-wrapper {
        margin: 1.5rem 0;
        display: flex;
        justify-content: center;
    }

    .bi-login-input-wrapper > div {
        display: inline-block;
        /* border-radius: 8px; */
        overflow: hidden;
        box-shadow: var(--shadow-md);
    }

    /* =========================
       SUBMIT BUTTON
    ========================= */
    .container-login100-form-btn {
        width: 100%;
        margin-top: 1.5rem;
    }

    .login100-form-btn {
        width: 100%;
        height: 52px;
        font-size: 1rem;
        font-weight: 600;
        color: var(--white);
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        border: none;
        /* border-radius: 12px; */
        outline: none;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.4);
        position: relative;
        overflow: hidden;
    }

    .login100-form-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }

    .login100-form-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px 0 rgba(99, 102, 241, 0.5);
    }

    .login100-form-btn:hover::before {
        left: 100%;
    }

    .login100-form-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 10px 0 rgba(99, 102, 241, 0.4);
    }

    /* Loading state */
    .login100-form-btn.loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .login100-form-btn.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin-top: -10px;
        margin-left: -10px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        /* border-radius: 50%; */
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* =========================
       ADDITIONAL LINKS
    ========================= */
    .text-center.mt-4 {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid var(--border-color);
    }

    .text-center.mt-4 p {
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .text-center.mt-4 a {
        color: var(--primary-color);
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition);
    }

    .text-center.mt-4 a:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    /* =========================
       ANIMATIONS
    ========================= */
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* =========================
       RESPONSIVE DESIGN
    ========================= */
    @media (max-width: 768px) {
        .login100-form-title h2 {
            font-size: 1.75rem;
        }

        .wrap-login100 {
            margin: 1rem;
            /* border-radius: 20px; */
        }

        .header-brand-img {
            max-width: 140px;
        }
    }

    @media (max-width: 480px) {
        .login100-form-title h2 {
            font-size: 1.5rem;
        }

        .input100 {
            padding: 0.75rem 1rem 0.75rem 3rem;
            font-size: 0.95rem;
        }

        .symbol-input100 {
            font-size: 1.1rem;
            left: 0.875rem;
        }

        .login100-form-btn {
            height: 48px;
            font-size: 0.95rem;
        }
    }

    /* =========================
       DARK MODE (OPTIONAL)
    ========================= */
    @media (prefers-color-scheme: dark) {
        :root {
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-light: #94a3b8;
            --border-color: #334155;
            --bg-light: #1e293b;
        }

        .wrap-login100 {
            background: rgba(15, 23, 42, 0.95);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .input100 {
            background: var(--dark-light);
            color: var(--white);
        }

        .input100:focus {
            background: var(--dark);
        }
    }

    /* =========================
       UTILITY CLASSES
    ========================= */
    .mb-0 { margin-bottom: 0 !important; }
    .mt-3 { margin-top: 1rem !important; }
    .mb-1 { margin-bottom: 0.25rem !important; }
    .pt-1 { padding-top: 0.25rem !important; }
</style>

<!-- Floating Particles Background -->
<div class="particles">
    <div class="particle" style="width: 4px; height: 4px; left: 10%; animation-delay: 0s;"></div>
    <div class="particle" style="width: 6px; height: 6px; left: 20%; animation-delay: 2s;"></div>
    <div class="particle" style="width: 3px; height: 3px; left: 30%; animation-delay: 4s;"></div>
    <div class="particle" style="width: 5px; height: 5px; left: 40%; animation-delay: 1s;"></div>
    <div class="particle" style="width: 4px; height: 4px; left: 50%; animation-delay: 3s;"></div>
    <div class="particle" style="width: 6px; height: 6px; left: 60%; animation-delay: 5s;"></div>
    <div class="particle" style="width: 3px; height: 3px; left: 70%; animation-delay: 2s;"></div>
    <div class="particle" style="width: 5px; height: 5px; left: 80%; animation-delay: 4s;"></div>
    <div class="particle" style="width: 4px; height: 4px; left: 90%; animation-delay: 1s;"></div>
</div>

<div class="login-wrapper">
    <!-- LOGO -->
    <div class="col col-login mx-auto text-center mr-5" >
        <a href="{{ url('/') }}" class="text-center">
            <img src="{{ asset($settings->logo ?? 'default/logo.svg') }}" class="header-brand-img" alt="Logo">
        </a>
    </div>

    <!-- LOGIN CONTAINER -->
    <div class="container-login100">
        <div class="wrap-login100 p-0">
            <div class="card-body">
                <form class="login100-form validate-form" method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <!-- Form Title -->
                    <div class="login100-form-title">
                        <h2>Welcome Back Cryptax </h2>
                        <p>Please sign in to continue</p>
                    </div>

                    <!-- Email Input -->
                    <div class="wrap-input100 validate-input" data-bs-validate="Valid email is required">
                        <input class="input100"
                               type="email"
                               name="email"
                               placeholder="Email Address"
                               value="{{ old('email') }}"
                               required
                               autocomplete="email"
                               autofocus>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="zmdi zmdi-email" aria-hidden="true"></i>
                        </span>
                    </div>
                    @error('email')
                    <div class="alert alert-danger">
                        <i class="zmdi zmdi-alert-circle"></i> {{ $message }}
                    </div>
                    @enderror

                    <!-- Password Input -->
                    <div class="wrap-input100 validate-input" data-bs-validate="Password is required">
                        <input class="input100"
                               type="password"
                               name="password"
                               placeholder="Password"
                               required
                               autocomplete="current-password">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="zmdi zmdi-lock" aria-hidden="true"></i>
                        </span>
                    </div>
                    @error('password')
                    <div class="alert alert-danger">
                        <i class="zmdi zmdi-alert-circle"></i> {{ $message }}
                    </div>
                    @enderror

                    <!-- Forgot Password Link -->


                    <!-- reCAPTCHA -->
                    @if(config('settings.recaptcha') === 'yes')
                    <div class="bi-login-input-wrapper save mt-3 mb-1">
                        {!! htmlFormSnippet() !!}
                        @if ($errors->has('g-recaptcha-response'))
                            <div>
                                <small class="text-danger">
                                    {{ $errors->first('g-recaptcha-response') }}
                                </small>
                            </div>
                        @endif
                    </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="container-login100-form-btn">
                        <button type="submit" class="login100-form-btn btn-primary">
                            Sign In
                        </button>
                    </div>

                    <!-- Register Link (Optional) -->
                    @if(Route::has('register'))

                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Form submission loading state
    document.getElementById('loginForm')?.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('.login100-form-btn');
        submitBtn.classList.add('loading');
        submitBtn.textContent = '';
    });

    // Input validation feedback
    document.querySelectorAll('.input100').forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.style.borderColor = '#ef4444';
            }
        });

        input.addEventListener('focus', function() {
            this.style.borderColor = '';
        });
    });

    // Auto-hide alerts after 5 seconds
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
</script>

<!-- CONTAINER CLOSED -->
@endsection
