<body style="margin:0; padding:0; font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif; background-color:#f8f9fa;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:40px 0;">
        <tr>
            <td align="center">
                <!-- Container -->
                <table width="600" cellpadding="0" cellspacing="0" border="0" class="container"
                    style="max-width:600px; width:100%; background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border:1px solid #eaeaea;">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding:32px 25px 24px; background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);">
                            <img src="{{ settings('favicon')}}"  alt="{{ config('app.name') }}"
                                style="display:block; max-width:120px; height:auto; margin:0 auto;">
                            <div style="height:4px; width:60px; background-color:#4a90e2; margin:20px auto 0;"></div>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td align="center" style="padding:40px 40px 32px;" class="mobile-padding">
                            <h1 style="margin:0 0 24px; color:#2c3e50; font-size:28px; font-weight:600; text-align:center; letter-spacing:-0.5px;">
                                Welcome to {{ config('app.name') }}
                            </h1>

                            <p style="margin:0 0 8px; color:#2c3e50; font-size:18px; font-weight:500; text-align:center;">
                                Hello {{ $user->username }},
                            </p>

                            <p style="margin:24px 0; color:#5a6c7d; font-size:16px; line-height:1.6; text-align:center; max-width:520px;">
                                Thank you for registering your {{ config('app.name') }}. To complete your registration and access all features, please verify your email address by clicking the button below.
                            </p>
                            <!-- Button -->
                            <table width="100%" cellspacing="0" cellpadding="0" style="margin:32px 0 40px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $verificationUrl }}"
                                            style="background-color:#4a90e2; color:#ffffff; padding:16px 36px; border-radius:6px; cursor:pointer; font-weight:600; font-size:16px; text-decoration:none; display:inline-block; transition:all 0.3s ease; box-shadow: 0 4px 6px rgba(74, 144, 226, 0.2);">
                                            Verify Email Address
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Info Box -->
                            <table width="100%" cellspacing="0" cellpadding="0" style="margin:32px 0; background-color:#f8f9fa; border-radius:6px; border-left:4px solid #4a90e2;">
                                <tr>
                                    <td style="padding:20px 24px;">
                                        <p style="margin:0; color:#5a6c7d; font-size:14px; line-height:1.5;">
                                            <strong>Important:</strong> This verification link will expire in <strong style="color:#2c3e50;">24 hours</strong> for security purposes. If you did not create this account, please disregard this email.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding:28px 40px; font-size:13px; color:#7b8a9b; background-color:#f8f9fa; border-top:1px solid #eaeaea;">
                            <p style="margin:0 0 12px;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                            <p style="margin:0 0 16px; font-size:12px; color:#95a5a6; max-width:480px; line-height:1.5;">
                                This is an automated message. Please do not reply to this email. If you have questions, contact our support team.
                            </p>
                            <p style="margin:0; font-size:12px; color:#95a5a6;">
                                {{ config('app.name') }} • Education Platform
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
