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
	<style type = "text/css">
		.details{
			font-size: 20px;
			padding-bottom: 7px;
			margin-left: 6%;
		}
	</style>
</head>
<body>
	<div class = "container" style = "margin-left: 38%; margin-top: 18%;">
		<div class = "details">Logged out Successfully!</div>
		<form action = "login.php" id = "pos">
			<button class = "btn btn-primary" name = "login" value = "login" style = "width: 32%;">
				Back To Login Page
			</button>
		</form>
	</div>
</body>
</html>