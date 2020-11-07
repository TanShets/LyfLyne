<!DOCTYPE html>
<html>
<?php
	session_start();
	if(isset($_SESSION['user'])){unset($_SESSION['user']);}
	if(isset($_SESSION['request'])){unset($_SESSION['request']);}
	if(isset($_SESSION['search'])){unset($_SESSION['search']);}
	session_destroy();
?>
<head>
	<title>Logged Out</title>
	<link rel = "stylesheet" type = "text/css" href = "interfacey.css">
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