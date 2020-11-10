<!DOCTYPE html>
<html>
<head>
	<title>Login to LyfLyne</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<?php
		if(!isset($_SESSION))
			session_start();
		
		if(isset($_SESSION['message'])){
			echo "<script>";
			echo "alert(\"".$_SESSION['message']."\");";
			echo "</script>";
			unset($_SESSION['message']);
		}

		if($_SERVER['REQUEST_METHOD'] == "POST"){
			$mainServe = "localhost";
			$mainuser = "root";
			$mainpass = "";
			$dbname = "lyflyne";
			$user1 = $_POST['name'];
			$pass1 = $_POST['password'];
			$hasStarted = mysqli_connect($mainServe, $mainuser, $mainpass, $dbname);

			if(!$hasStarted){
				die("Failed: ".mysqli_connect_error());
			}
			$cmd = "SELECT * FROM emp_user WHERE username = '$user1' AND password = '$pass1'";
			$outcome = mysqli_query($hasStarted, $cmd);
			$vals = mysqli_fetch_array($outcome);
			if(is_array($vals) && isset($_POST['name']) && isset($_POST['password'])){
				//$_SESSION['username'] = $vals['username'];
				//$_SESSION['password'] = $vals['password'];
				$_SESSION['emp-user'] = $vals;
				header("Location: emp-requests.php");
					exit();
			}
			else{
				if(isset($_POST['name']) && isset($_POST['password'])){
					$error = "Incorrect Username or Password.";
				}
				elseif(isset($_POST['name'])){
					$error = "Enter the password first";
				}
				elseif (isset($_POST['password'])) {
					$error = "Enter the username";
				}
			}
		}
	?>
</head>
<body>
	<div style = "margin-left: 35%; margin-top: 18%; width: 30%; border: 1px solid grey; padding: 20px;">
		<form action = "emp-login.php" method = "post">
			<h1>Employee Login</h1>
			<table>
				<tr>
					<td>Username</td>
					<td><input class = "form-control" type = "text" name = "name" placeholder="Enter your username"></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input class = "form-control" type = "password" name = "password" placeholder="Enter password"></td>
				</tr>
			</table><br>
			<button class = "btn btn-primary" type = "submit" style = "margin-left: 60%; width: 34%;">Login</button>
		</form>
	</div>
</body>
</html>