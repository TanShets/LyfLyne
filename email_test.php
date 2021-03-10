<?php require('security.php'); ?>
<?php
    require_once('PHPMailer/PHPMailerAutoload.php');
    /*
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = '465';
        $mail->isHTML();
        $mail->Username = decode_level_7('~Lma;SNW~HCLAbV0~_S-E@I>~nb<TOXM~NWLma;S~b<TOXMn~O)A<E:[~RGh\6NI?Y~=rE;y~@aU/GBK~Fg[5MHQ~A<E:[O)~cW1IDMB~fZ4LGPE?+`3)gGl~Q+C>G<]~]7OJSHi~QFg[5MH');
        $mail->Password = decode_level_7("^KX5-:Z3~A<E:[O)~l`:RMVK~eY3KFOD^]6N[80=~]7OJSHi~R,D?H=^?rE;yY~=?Kp/d7-k?5+iIn-b?5+iIn-b?n-b5+iI");
        $mail->SetFrom('no-reply@lyflyne.org');
        $mail->Subject = 'Yo';
        $mail->Body = 'Hello there';
        $mail->AddAddress('shettytanish02@gmail.com');

        $mail->Send();
    }
    */

    function sendOTP($otp, $email, $username){
        echo $otp."<br>".$email."<br>".$username;
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = '465';
        $mail->isHTML();
        $mail->Username = decode_level_7('~Lma;SNW~HCLAbV0~_S-E@I>~nb<TOXM~NWLma;S~b<TOXMn~O)A<E:[~RGh\6NI?Y~=rE;y~@aU/GBK~Fg[5MHQ~A<E:[O)~cW1IDMB~fZ4LGPE?+`3)gGl~Q+C>G<]~]7OJSHi~QFg[5MH');
        $mail->Password = decode_level_7("^KX5-:Z3~A<E:[O)~l`:RMVK~eY3KFOD^]6N[80=~]7OJSHi~R,D?H=^?rE;yY~=?Kp/d7-k?5+iIn-b?5+iIn-b?n-b5+iI");
        $mail->SetFrom('no-reply@lyflyne.org');
        $mail->Subject = 'Your One Time Password';
        $mail->Body = "Hello $username!!!! Your One Time Password for account creation is $otp.";
        $mail->AddAddress($email);

        $mail->Send();
    }
?>

<!--
<html>

<head>
    <title>Email test</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <form action="email_test.php" method="post" style="display: flex; align-items: center; justify-content: center;">
        <button type="submit" class="btn btn-primary btn-lg" style="padding: 0.5rem 2rem;">Test</button>
    </form>
</body>

</html>
-->
