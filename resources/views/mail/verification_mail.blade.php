
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cryptax – Verify Your Email</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      background: #f0f4f8;
      font-family: 'Outfit', sans-serif;
      padding: 40px 16px;
      min-height: 100vh;
    }

    .wrapper {
      max-width: 560px;
      margin: 0 auto;
    }

    .card {
      background: #ffffff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    }

    /* Header stripe */
    .header-stripe {
      height: 5px;
      background: linear-gradient(90deg, #2563eb, #3b82f6, #60a5fa);
    }

    .body-content {
      padding: 44px 48px 36px;
      text-align: center;
    }

    /* Logo */
    .logo {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 32px;
    }

    .logo-icon {
      width: 32px;
      height: 32px;
      background: linear-gradient(135deg, #2563eb, #3b82f6);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .logo-icon svg { width: 18px; height: 18px; fill: #fff; }

    .logo-text {
      font-size: 20px;
      font-weight: 700;
      letter-spacing: 0.08em;
      color: #1e293b;
    }

    .logo-text span { color: #2563eb; }

    /* Divider */
    .logo-divider {
      width: 40px;
      height: 2px;
      background: linear-gradient(90deg, #2563eb, #60a5fa);
      border-radius: 2px;
      margin: 0 auto 28px;
    }

    .greeting {
      font-size: 22px;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 6px;
    }

    .subheading {
      font-size: 13px;
      font-weight: 500;
      color: #94a3b8;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      margin-bottom: 20px;
    }

    .message {
      font-size: 14.5px;
      color: #475569;
      line-height: 1.7;
      margin-bottom: 32px;
      max-width: 400px;
      margin-left: auto;
      margin-right: auto;
    }

    .btn-wrap {
      margin-bottom: 32px;
    }

    .btn {
      display: inline-block;
      background: linear-gradient(135deg, #2563eb, #3b82f6);
      color: #fff !important;
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
      letter-spacing: 0.04em;
      padding: 13px 36px;
      border-radius: 8px;
      box-shadow: 0 4px 14px rgba(37,99,235,0.35);
    }

    /* Warning box */
    .warning-box {
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      border-left: 3px solid #2563eb;
      border-radius: 8px;
      padding: 14px 18px;
      text-align: left;
      margin-bottom: 32px;
    }

    .warning-box p {
      font-size: 12.5px;
      color: #3b4f6b;
      line-height: 1.6;
    }

    .warning-box strong {
      color: #1e40af;
    }

    /* Fallback link */
    .fallback {
      font-size: 12px;
      color: #94a3b8;
      margin-bottom: 8px;
    }

    .fallback a {
      color: #2563eb;
      word-break: break-all;
      font-size: 11px;
    }

    /* Footer */
    .footer {
      border-top: 1px solid #f1f5f9;
      padding: 20px 48px;
      text-align: center;
    }

    .footer p {
      font-size: 11.5px;
      color: #94a3b8;
      line-height: 1.7;
    }

    .footer a {
      color: #64748b;
      text-decoration: none;
    }

    .footer a:hover { text-decoration: underline; }

    .footer-links {
      margin-top: 10px;
      display: flex;
      justify-content: center;
      gap: 16px;
    }

    .footer-links a {
      font-size: 11px;
      color: #94a3b8;
    }

    @media (max-width: 480px) {
      .body-content { padding: 32px 24px 28px; }
      .footer { padding: 18px 24px; }
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="card">
      <div class="header-stripe"></div>

      <div class="body-content">

        <!-- Logo -->
        <div class="logo">
          <div class="logo-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
          </div>
          <div class="logo-text">CRYPT<span>AX</span></div>
        </div>

        <div class="logo-divider"></div>

        <p class="subheading">Email Verification</p>
        <h1 class="greeting">Verify your email address</h1>

        <p class="message">
          Hello, <strong>{{ $user->name }}</strong>.<br><br>
          You recently requested to reset your password. Click the button below
          to verify your email and proceed. This link will expire in
          <strong>24&nbsp;hours</strong>.
        </p>

        <div class="btn-wrap">
          <a href="{{ $verificationUrl }}" class="btn">Verify Email Address →</a>
        </div>

        <div class="warning-box">
          <p>
            <strong>Important:</strong> This verification link will expire in
            <strong>24 hours</strong> for security purposes. If you did not
            request this, please disregard this email — no changes will be made
            to your account.
          </p>
        </div>

      </div>

      <div class="footer">
        <p>© 2024 Cryptax. All rights reserved.</p>
        <p>This is an automated message. Please do not reply to this email.</p>
        <div class="footer-links">
          <a href="#">Privacy Policy</a>
          <a href="#">Terms of Service</a>
          <a href="#">Support</a>
        </div>
        <p style="margin-top:10px; font-size:10.5px; color:#cbd5e1;">
          Cryptax — Education Platform
        </p>
      </div>
    </div>
  </div>
</body>
</html>

