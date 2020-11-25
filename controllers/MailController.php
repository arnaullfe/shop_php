<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once ('../dependencies/PHPMailer/src/Exception.php');
require_once ('../dependencies/PHPMailer/src/PHPMailer.php');
require_once ('../dependencies/PHPMailer/src/SMTP.php');
require_once ('./MailContentController.php');
require_once ('../modals/User.php');
function sendMailRecoverPassword($user){
    $mail = new PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'app.eshop.online@gmail.com';                 // SMTP username
    $mail->Password = 'testroot123';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
    $mail->SMTPDebug = 0;
    $mail->From = 'hola.eshop.online@gmail.com';
    $mail->FromName = 'eshop';
    $mail->addAddress($user->getEmail(), $user->getName()." ".$user->getLastnames());     // Add a recipient
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = "Canviar contrasenya";
    $mail->Body    = recoverUserEmail($user->getId(),$user->getTokenPass());

    if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent';
    }
}

function sendMailActivatedUser($user){
    $mail = new PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'app.eshop.online@gmail.com';                 // SMTP username
    $mail->Password = 'testroot123';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
    $mail->SMTPDebug = 0;
    $mail->From = 'hola.eshop.online@gmail.com';
    $mail->FromName = 'eshop';
    $mail->addAddress($user->getEmail(), $user->getName()." ".$user->getLastnames());     // Add a recipient
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = "Activa el teu usuari";
    $mail->Body    = activateUserEmail($user->getId(),$user->getTokenPass());

    if(!$mail->send()) {
        //echo 'Message could not be sent.';
        //echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
      //  echo 'Message has been sent';
    }
}

function sendChangeEmailUser($user){
    $mail = new PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'app.eshop.online@gmail.com';                 // SMTP username
    $mail->Password = 'testroot123';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
    $mail->SMTPDebug = 0;
    $mail->From = 'hola.eshop.online@gmail.com';
    $mail->FromName = 'eshop';
    $mail->addAddress($user->getEmail(), $user->getName()." ".$user->getLastnames());     // Add a recipient
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = "Activa el teu usuari";
    $mail->Body    = changeUserEmail($user->getId(),$user->getTokenPass(),$user->getEmail());

    if(!$mail->send()) {
        //echo 'Message could not be sent.';
        //echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        //  echo 'Message has been sent';
    }
}