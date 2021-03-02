<?php require('security.php'); ?>
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
        $mail->Username = "shetstan@gmail.com";
        $mail->Password = decode_level_7("^KX5-:Z3~A<E:[O)~l`:RMVK~eY3KFOD^]6N[80=~]7OJSHi~R,D?H=^?rE;yY~=?Kp/d7-k?5+iIn-b?5+iIn-b?n-b5+iI");
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
