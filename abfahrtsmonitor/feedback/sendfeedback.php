<?php

$header = 'From: noreply@swwv-bus.projekt.jade-hs.de' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
mail("johannes.rudolph@student.jade-hs.de","Feedback-Test","Test",$header);
/**
 * Created by JetBrains PhpStorm.
 * User: johannes
 * Date: 24.04.13
 * Time: 17:17
 * To change this template use File | Settings | File Templates.
 */

//require 'class.phpmailer.php';

/*$mail = new PHPMailer;

$mail->IsSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp1.jade-hs.de';  // Specify main and backup server
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'jo2450';                            // SMTP username
$mail->Password = 'pqmcm8';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
$mail->Port=587;

$mail->From = 'johannes.rudolph@student.jade-hs.de';
$mail->FromName = 'Johannes Rudolph';
$mail->AddAddress('johannes.rudolph@student.jade-hs.de', 'Johannes Rudolph');  // Add a recipient
//$mail->AddAddress('ellen@example.com');               // Name is optional
//$mail->AddReplyTo('info@example.com', 'Information');
//$mail->AddCC('cc@example.com');
//$mail->AddBCC('bcc@example.com');

$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
//$mail->AddAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->AddAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
//$mail->IsHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Feedback - Abfahrtsmonitor';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->Send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
    exit;
}*/

echo 'Message has been sent';