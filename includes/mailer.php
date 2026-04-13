<?php
// includes/mailer.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

/**
 * Sends login credentials to the school email.
 * 
 * @param string $toEmail   Recipient email address
 * @param string $toPhone   Recipient phone number (used as password)
 * @param string $schoolName Name of the school
 * @return array            Status and message
 */
function msd_send_credentials($toEmail, $toPhone, $schoolName) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Use this for local testing if needed
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com'; // Standard Hostinger SMTP host
        $mail->SMTPAuth   = true;
        $mail->Username   = 'test@techiesgateway.com'; 
        $mail->Password   = '2G@e31?rVACo'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Port 465 usually uses SMTPS
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('test@techiesgateway.com', 'MySchoolDesk Admin');
        $mail->addAddress($toEmail, $schoolName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to MySchoolDesk - Your Login Credentials';
        
        $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                <h2 style='color: #4318FF;'>Welcome, $schoolName!</h2>
                <p>We are excited to inform you that your school listing has been approved. You can now log in to your dashboard to manage your profile and enquiries.</p>
                <div style='background: #F4F7FE; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <p style='margin: 0;'><strong>Username (Email):</strong> $toEmail</p>
                    <p style='margin: 5px 0;'><strong>Password:</strong> $toPhone</p>
                </div>
                <p><strong>Login URL:</strong> <a href='https://myschooldesk.co.in/school_dashboard/login.php' style='color: #4318FF;'>School Login</a></p>
                <p style='color: #888; font-size: 12px;'>Note: For security reasons, please change your password after your first login.</p>
                <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='font-size: 10px; color: #AAA;'>This is an automated message. Please do not reply to this email.</p>
            </div>
        ";

        $mail->Body = $body;
        $mail->AltBody = "Welcome to MySchoolDesk, $schoolName!\n\nYour login credentials are:\nUsername: $toEmail\nPassword: $toPhone\n\nLogin here: https://myschooldesk.co.in/school_dashboard/login.php";

        $mail->send();
        return ['success' => true, 'message' => 'Credentials sent successfully!'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
    }
}
