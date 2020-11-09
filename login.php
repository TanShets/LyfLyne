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

		if(isset($_SESSION['user'])){
			$_SESSION['message'] = "You must log out from your account before trying to login to another account.";
			header("Location: user/create-request.php");
				exit();
		}

		if(isset($_SESSION['hospital_user'])){
			$_SESSION['message'] = "You must log out from your account before trying to login to another account.";
			header("Location: user/hospital-request.php");
				exit();
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
			if(!isset($_POST['isHospital'])){
				$cmd = "SELECT * FROM user WHERE username = '$user1' AND password = '$pass1'";
			}
			else
				$cmd = "SELECT * FROM hospital_user WHERE username = '$user1' AND password = '$pass1'";

			$outcome = mysqli_query($hasStarted, $cmd);

			$vals = mysqli_fetch_array($outcome);
			if(is_array($vals) && isset($_POST['name']) && isset($_POST['password'])){
				//$_SESSION['username'] = $vals['username'];
				//$_SESSION['password'] = $vals['password'];
				if(!isset($_POST['isHospital'])){
					$_SESSION['user'] = $vals;
					header("Location: user/create-request.php");
						exit();
				}
				else{
					$_SESSION['hospital_user'] = $vals;
					header("Location: hospital/hospital-request.php");
						exit();
				}
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
	<form action = "login.php" method = "post">
		<table>
			<tr>
				<td>Username</td>
				<td><input type = "text" name = "name" placeholder="Enter your username"></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type = "password" name = "password" placeholder="Enter password"></td>
			</tr>
			<tr>
				<td>Hospital Account</td>
				<td><input type = "checkbox" name = "isHospital" value = "1"/></td>
			</tr>
		</table>
		<button type = "submit">Login</button>
	</form><br>
	<table>
		<tr><td>
			<form action = "user/create.php">
				<button type = "submit">Create User Account</button>
			</form></td>	
			<td>
			<form action = "hospital/hospital-create.php">
				<button type = "submit">Create Hospital Account</button>
			</form></td>
		</tr>
	</table>
</body>
</html>