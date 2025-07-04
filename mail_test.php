
<?php

error_reporting(-1);
ini_set('display_errors', 'On');
// set_error_handler("var_dump");
    $to      = 'ck@jspinfotech.com';
    $subject = 'the subject';
    $message = 'hello';
    $headers = 'From: webmaster@example.com'       . "\r\n" .
                 'Reply-To: webmaster@example.com' . "\r\n" .
                 'X-Mailer: PHP/' . phpversion();

    mail($to, $subject, $message, $headers);
    
?>