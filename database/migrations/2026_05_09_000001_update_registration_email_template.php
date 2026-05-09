<?php

use App\Models\EmailTemplate;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $template = EmailTemplate::where('type', 'Registration')->first();
        if ($template) {
            $template->subject = 'Welcome to {site_title} - Registration Successful';
            $template->body = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Welcome to {site_title}</title>
</head>
<body style="margin:0; padding:0; background-color:#f5f5f5; font-family:\'Segoe UI\', \'Helvetica Neue\', Arial, sans-serif; -webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f5f5f5; min-height:100vh;">
        <tr>
            <td align="center" style="padding:20px 10px;">
                <table role="presentation" width="600" max-width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08); max-width:100%;">
                    <!-- Header Banner -->
                    <tr>
                        <td style="background:linear-gradient(135deg, #8B0000 0%, #C62828 50%, #8B0000 100%); padding:30px 30px 25px 30px; text-align:center;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="text-align:center;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 12px auto;">
                                            <tr>
                                                <td width="60" height="60" style="background-color:rgba(255,215,0,0.15); border-radius:50%; text-align:center; vertical-align:middle;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="60" height="60">
                                                        <tr>
                                                            <td align="center" valign="middle" style="font-size:30px; line-height:60px; color:#FFD700;">&#10004;</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        <h1 style="color:#FFD700; font-size:26px; font-weight:700; margin:0 0 4px 0; letter-spacing:0.5px;">Welcome Aboard!</h1>
                                        <p style="color:#ffffff; font-size:14px; margin:0; opacity:0.9;">Your account has been created successfully</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Content Area -->
                    <tr>
                        <td style="padding:30px 25px 20px 25px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <!-- Greeting -->
                                <tr>
                                    <td style="padding-bottom:18px;">
                                        <p style="font-size:15px; color:#333333; line-height:1.6; margin:0;">Hello <strong style="color:#8B0000;">{user_name}</strong>,</p>
                                        <p style="font-size:14px; color:#555555; line-height:1.6; margin:8px 0 0 0;">Thank you for registering with <strong style="color:#8B0000;">{site_title}</strong>. We are thrilled to have you on board!</p>
                                    </td>
                                </tr>
                                <!-- Info Cards -->
                                <tr>
                                    <td style="padding-bottom:15px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="width:50%; padding-right:8px; vertical-align:top;">
                                                    <table role="presentation" width="100%" cellpadding="15" cellspacing="0" border="0" style="background:linear-gradient(135deg, #FFF8E1 0%, #FFFDE7 100%); border:1px solid #FFD700; border-radius:8px;">
                                                        <tr>
                                                            <td style="text-align:center; font-size:13px; color:#555;">
                                                                <strong style="font-size:14px; color:#333; display:block; margin-bottom:4px;">Start Shopping</strong>
                                                                Browse our wide range of products and place your first order.
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td style="width:50%; padding-left:8px; vertical-align:top;">
                                                    <table role="presentation" width="100%" cellpadding="15" cellspacing="0" border="0" style="background:linear-gradient(135deg, #FFF8E1 0%, #FFFDE7 100%); border:1px solid #FFD700; border-radius:8px;">
                                                        <tr>
                                                            <td style="text-align:center; font-size:13px; color:#555;">
                                                                <strong style="font-size:14px; color:#333; display:block; margin-bottom:4px;">Track Orders</strong>
                                                                Stay updated with real-time order tracking and notifications.
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <!-- Support Info -->
                                <tr>
                                    <td style="padding:15px 0 5px 0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#FAFAFA; border-radius:8px;">
                                            <tr>
                                                <td style="padding:15px; text-align:center;">
                                                    <p style="font-size:13px; color:#555; line-height:1.6; margin:0;">Need assistance? We are here to help<br><span style="color:#8B0000; font-weight:600;">{site_title}</span></p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#1a1a2e; padding:20px 25px; text-align:center;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="text-align:center; color:#999; font-size:12px; line-height:1.6;">
                                        <p style="margin:0 0 4px 0; color:#FFD700; font-weight:600; font-size:13px;">{site_title}</p>
                                        <p style="margin:0 0 6px 0;">Thank you for joining us!</p>
                                        <p style="margin:0; font-size:10px; color:#666;">This is an automated email, please do not reply to this message.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table role="presentation" width="600" style="max-width:100%;">
                    <tr>
                        <td style="padding:12px 10px 5px; text-align:center; font-size:10px; color:#999;">&copy; 2025 {site_title}. All rights reserved.</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
            $template->save();
        }
    }

    public function down(): void
    {
        $template = EmailTemplate::where('type', 'Registration')->first();
        if ($template) {
            $template->subject = 'Welcome To Omnimart';
            $template->body = '<p>Hello ; {user_name},</p><p>You have successfully registered to {site_title}, We wish you will have a wonderful experience using our service.</p><p>Thank You .<br></p>';
            $template->save();
        }
    }
};
