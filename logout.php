<!DOCTYPE html>
<html>
<?php
	session_start();
	if(isset($_SESSION['user'])){unset($_SESSION['user']);}
	if(isset($_SESSION['emp-user'])){unset($_SESSION['emp-user']);}
	if(isset($_SESSION['request'])){unset($_SESSION['request']);}
	if(isset($_SESSION['search'])){unset($_SESSION['search']);}
	session_destroy();
?>
<head>
	<title>Logged Out</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
	<div class = "logout-1">
		<div class = "details">Logged out Successfully!</div>
	</div>
	<form action = "login.php" id = "pos">
		<button class= "submit" name = "login" value = "login">Back To Login Page</button>
	</form>
</body>
</html>