<?php
    require_once('PHPMailer/PHPMailerAutoload.php');
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = '465';
        $mail->isHTML();
        $mail->Username = 
        $mail->Password = 
        $mail->SetFrom('no-reply@lyflyne.org');
        $mail->Subject = 'Yo';
        $mail->Body = 'Hello there';
        $mail->AddAddress('shettytanish02@gmail.com');

        $mail->Send();
    }
?>

<html>
    <head>
        <title>Email test</title>
    </head>
    <body>
        <form action = "email_test.php" method = "post">
            <button type = "submit">Test</button>
        </form>
    </body>
</html>
