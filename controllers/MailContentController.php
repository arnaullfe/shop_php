<?php
require_once ('../modals/User.php');
ob_start();
function recoverUserEmail($id,$token_pass){
    $url_server = 'http://'.$_SERVER["SERVER_NAME"];
    $url_image = $url_server.'/pages/botiga_view/images/logo.png';
    $url_button = $url_server.'/pages/admin_view/recover_password.php?id='.$id.'&token_pass='.$token_pass;
    $html = "
    <!doctype html>
<html lang='en-US'>

<head>
    <meta content='text/html; charset=utf-8' http-equiv='Content-Type' />
    <title>Reset Password Email Template</title>
    <meta name='description' content='Reset Password Email Template.'>
    <style type='text/css'>
    a:hover {text-decoration: underline !important;}
    </style>
</head>

<body marginheight='0' topmargin='0' marginwidth='0' style='margin: 0px; background-color: #f2f3f8;' leftmargin='0'>
<table cellspacing='0' border='0' cellpadding='0' width='100%' bgcolor='#f2f3f8'
       style='@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;'>
<tr>
    <td>
        <table style='background-color: #f2f3f8; max-width:670px;  margin:0 auto;' width='100%' border='0'
               align='center' cellpadding='0' cellspacing='0'>
            <tr>
                <td style='height:80px;'>&nbsp;</td>
            </tr>
            <tr>
                <td style='text-align:center;'>
                    <a href=$url_server title='logo' target='_blank'>
                        <img width='100' src='https://i.ibb.co/dpWNpYh/logo.png' title='logo' alt='logo'>
                    </a>
                </td>
            </tr>
            <tr>
                <td style='height:20px;'>&nbsp;</td>
            </tr>
            <tr>
                <td>
                    <table width='95%' border='0' align='center' cellpadding='0' cellspacing='0'
                           style='max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);'>
                        <tr>
                            <td style='height:40px;'>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style='padding:0 35px;'>
                                <h1 style='color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;'>Has demanat canviar la contrasenya</h1>
                                <span
                                        style='display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;'></span>
                                <p style='color:#455056; font-size:15px;line-height:24px; margin:0;'>
                                No et podem enviar la teva contrasenya anterior per seguretat, però si que et podem ajudar a canviar-la.
                                Fés clic al botó per procedir a fer el canvi de la teva contrasenya.
                                </p>
                                <a href=$url_button
                                   style='background:#F6931D;text-decoration:none !important; font-weight:500; margin-top:35px; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;cursor: pointer'>
                                   Recuperar contrasenya</a>
                            </td>
                        </tr>
                        <tr>
                            <td style='height:40px;'>&nbsp;</td>
                        </tr>
                    </table>
                </td>
            <tr>
                <td style='height:20px;'>&nbsp;</td>
            </tr>
            <tr>
                <td style='text-align:center;'>
                    <p style='font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;'>&copy; <strong>Eshop</strong></p>
                </td>
            </tr>
            <tr>
                <td style='height:80px;'>&nbsp;</td>
            </tr>
        </table>
    </td>
</tr>
</table>
</body>

</html>";
    return $html;
}


 function activateUserEmail($id,$token_pass){
    $url_server = 'http://'.$_SERVER["SERVER_NAME"];
    $url_image = $url_server.'/pages/botiga_view/images/logo.png';
    $url_button = $url_server.'/pages/admin_view/user_activated.php?id='.$id.'&token_pass='.$token_pass;
    $html = " <!doctype html>
<html lang='en-US'>

<head>
    <meta content='text/html; charset=utf-8' http-equiv='Content-Type' />
    <title>Reset Password Email Template</title>
    <meta name='description' content='Reset Password Email Template.'>
    <style type='text/css'>
    a:hover {text-decoration: underline !important;}
    </style>
</head>

<body marginheight='0' topmargin='0' marginwidth='0' style='margin: 0px; background-color: #f2f3f8;' leftmargin='0'>
<table cellspacing='0' border='0' cellpadding='0' width='100%' bgcolor='#f2f3f8'
       style='@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;'>
<tr>
    <td>
        <table style='background-color: #f2f3f8; max-width:670px;  margin:0 auto;' width='100%' border='0'
               align='center' cellpadding='0' cellspacing='0'>
            <tr>
                <td style='height:80px;'>&nbsp;</td>
            </tr>
            <tr>
                <td style='text-align:center;'>
                    <a href=$url_server title='logo' target='_blank'>
                        <img width='100' src='https://i.ibb.co/dpWNpYh/logo.png'>
                    </a>
                </td>
            </tr>
            <tr>
                <td style='height:20px;'>&nbsp;</td>
            </tr>
            <tr>
                <td>
                    <table width='95%' border='0' align='center' cellpadding='0' cellspacing='0'
                           style='max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);'>
                        <tr>
                            <td style='height:40px;'>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style='padding:0 35px;'>
                                <h1 style='color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;'>Activació del teu usuari</h1>
                                <span
                                        style='display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;'></span>
                                <p style='color:#455056; font-size:15px;line-height:24px; margin:0;'>
                                Per poder activar el teu usuari i així procedir a realitzar totes les teves operacions
                                fés clic al botó.
                                </p>
                                <a href=$url_button
                                   style='background:#F6931D;text-decoration:none !important; font-weight:500; margin-top:35px; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;cursor: pointer'>
                                   Activar el meu usuari</a>
                            </td>
                        </tr>
                        <tr>
                            <td style='height:40px;'>&nbsp;</td>
                        </tr>
                    </table>
                </td>
            <tr>
                <td style='height:20px;'>&nbsp;</td>
            </tr>
            <tr>
                <td style='text-align:center;'>
                    <p style='font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;'>&copy; <strong>Eshop</strong></p>
                </td>
            </tr>
            <tr>
                <td style='height:80px;'>&nbsp;</td>
            </tr>
        </table>
    </td>
</tr>
</table>
</body>

</html>";

    return $html;
}

function changeUserEmail($id,$token_pass,$email){
    $url_server = 'http://'.$_SERVER["SERVER_NAME"];
    $url_image = $url_server.'/pages/botiga_view/images/logo.png';
    $url_button = $url_server.'/pages/admin_view/change_email.php?id='.$id.'&token_pass='.$token_pass.'&email='.$email;
    $html = "
    <!doctype html>
<html lang='en-US'>

<head>
    <meta content='text/html; charset=utf-8' http-equiv='Content-Type' />
    <title>Reset Password Email Template</title>
    <meta name='description' content='Reset Password Email Template.'>
    <style type='text/css'>
    a:hover {text-decoration: underline !important;}
    </style>
</head>

<body marginheight='0' topmargin='0' marginwidth='0' style='margin: 0px; background-color: #f2f3f8;' leftmargin='0'>
<table cellspacing='0' border='0' cellpadding='0' width='100%' bgcolor='#f2f3f8'
       style='@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;'>
<tr>
    <td>
        <table style='background-color: #f2f3f8; max-width:670px;  margin:0 auto;' width='100%' border='0'
               align='center' cellpadding='0' cellspacing='0'>
            <tr>
                <td style='height:80px;'>&nbsp;</td>
            </tr>
            <tr>
                <td style='text-align:center;'>
                    <a href=$url_server title='logo' target='_blank'>
                        <img width='100' src='https://i.ibb.co/dpWNpYh/logo.png' title='logo' alt='logo'>
                    </a>
                </td>
            </tr>
            <tr>
                <td style='height:20px;'>&nbsp;</td>
            </tr>
            <tr>
                <td>
                    <table width='95%' border='0' align='center' cellpadding='0' cellspacing='0'
                           style='max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);'>
                        <tr>
                            <td style='height:40px;'>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style='padding:0 35px;'>
                                <h1 style='color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;'>Has demanat canviar l'email</h1>
                                <span
                                        style='display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;'></span>
                                <p style='color:#455056; font-size:15px;line-height:24px; margin:0;'>
                               Per acabar de canviar el email de l'usuari, fés clic en el butó per aplicar els canvis
                                </p>
                                <a href=$url_button
                                   style='background:#F6931D;text-decoration:none !important; font-weight:500; margin-top:35px; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;cursor: pointer'>
                                   Canviar email</a>
                            </td>
                        </tr>
                        <tr>
                            <td style='height:40px;'>&nbsp;</td>
                        </tr>
                    </table>
                </td>
            <tr>
                <td style='height:20px;'>&nbsp;</td>
            </tr>
            <tr>
                <td style='text-align:center;'>
                    <p style='font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;'>&copy; <strong>Eshop</strong></p>
                </td>
            </tr>
            <tr>
                <td style='height:80px;'>&nbsp;</td>
            </tr>
        </table>
    </td>
</tr>
</table>
</body>

</html>";
    return $html;
}