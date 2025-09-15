<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require_once __DIR__ . '/../../../vendor/autoload.php';

function send_verification($fullname, $email, $authCode){


    $mail = new PHPMailer(true);                               // Passing true enables exceptions
    try {

       
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'nazhromar02@gmail.com';                 // SMTP username
        $mail->Password = 'wiyb ohaw uwfn pgzf';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, ssl also accepted
        $mail->Port = 587;                                    // TCP port to connect to
    
        //Recipients
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->setFrom('nazhromar02@gmail.com','Romaré Suites Verification');
        $mail->addAddress($email);     // Add a recipient
        //Content
        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = "OTP Verification";
        $mail->Body = '
                        <div style="font-family: Arial, sans-serif; padding: 20px; color: #333;">
                            <h2 style="color: #004085;">Hello ' . $fullname . ',</h2>
                            <p>Thank you for registering with <strong>Romaré Suites</strong>.</p>
                            <p>Your one-time verification code is:</p>
                            <div style="font-size: 24px; font-weight: bold; margin: 20px 0; color: #155724;">
                                ' . $authCode . '
                            </div>
                            <p>Please use this code to verify your account. If you didn’t request this, you can ignore this email.</p>
                            <br>
                            <p>Best regards,</p>
                            <p><strong>Romaré Suites Management</strong></p>
                            <hr style="margin-top: 30px;">
                            <p style="font-size: 12px; color: #777;">This is an automated message. Please do not reply to this email.</p>
                        </div>
                    ';
        $mail->send();

        

    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
}
?>