<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Email NeoManga</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="margin: 0; padding: 0; background-color: #111827; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 20px 0;">
                <!--[if (gte mso 9)|(IE)]>
                <table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
                <tr>
                <td align="center" valign="top" width="600">
                <![endif]-->
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="
                    max-width: 600px;
                    border-collapse: collapse; 
                    background-color: #0c0a1d; 
                    border: 1px solid #00ffff;
                    border-radius: 12px;
                    box-shadow: 0 0 25px rgba(0, 255, 255, 0.5);
                ">
                    <!-- Header/Title -->
                    <tr>
                        <td align="center" style="padding: 30px 20px 20px 20px;">
                            <h1 style="
                                margin: 0; 
                                color: #ffffff; 
                                font-size: 28px; 
                                font-weight: bold;
                                text-shadow: 0 0 5px #fff, 0 0 10px #fff, 0 0 20px #00ffff, 0 0 35px #00ffff;
                            ">
                                VERIFIKASI EMAIL
                            </h1>
                            <div style="width: 80px; height: 2px; background-color: #00ffff; margin-top: 10px; border-radius: 2px;"></div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 20px 30px; color: #e5e7eb; font-size: 16px; line-height: 1.6;">
                            <p style="margin: 0 0 15px 0;">Halo!</p>
                            <p style="margin: 0 0 25px 0;">
                                Terima kasih telah mendaftar di <strong style="color: #22d3ee; text-shadow: 0 0 8px rgba(34, 211, 238, 0.7);">NeoManga</strong>. Untuk menyelesaikan pendaftaran, silakan gunakan kode di bawah ini:
                            </p>
                        </td>
                    </tr>
                    
                    <!-- OTP Code Section -->
                    <tr>
                        <td align="center" style="padding: 0 30px 25px 30px;">
                            <div style="
                                background-color: #111827; 
                                padding: 20px; 
                                border-radius: 8px; 
                                border: 1px solid #22d3ee;
                                text-align: center;
                            ">
                                <p style="
                                    margin: 0; 
                                    font-size: 36px; 
                                    font-weight: bold; 
                                    letter-spacing: 10px;
                                    color: #ffffff;
                                    text-shadow: 0 0 8px #00ffff, 0 0 15px #00ffff;
                                ">
                                    <!-- Variabel $otp dari controller Anda akan muncul di sini -->
                                    {{ $otp }}
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Expiry and Warning -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px; color: #9ca3af; font-size: 14px; text-align: center;">
                            <p style="margin: 0;">Kode ini akan kadaluarsa dalam 5 menit.</p>
                            <p style="margin-top: 20px; border-top: 1px solid #374151; padding-top: 20px;">
                                Jika Anda tidak merasa melakukan pendaftaran, mohon abaikan email ini.
                            </p>
                        </td>
                    </tr>

                    <!-- Signature -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px; color: #e5e7eb; font-size: 16px;">
                            <p style="margin: 0;">Salam,<br/>Tim NeoManga</p>
                        </td>
                    </tr>
                </table>
                <!--[if (gte mso 9)|(IE)]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
    </table>
</body>
</html>