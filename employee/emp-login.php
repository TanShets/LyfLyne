<!DOCTYPE html>
<html>
<head>
	<title>Login to LyfLyne</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="../style/employee/login.css">
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

		if(isset($_SESSION['emp-user'])){
            $_SESSION['message'] = "You're already logged in!";
			header("Location: emp-requests.php");
				exit();
        }
		elseif(isset($_SESSION['user'])){
			$_SESSION['message'] = "You must log out from your user account before trying to login to another account.";
			header("Location: ../user/create-request.php");
				exit();
		}
		elseif(isset($_SESSION['hospital_user'])){
			$_SESSION['message'] = "You must log out from your hospital account before trying to login to another account.";
			header("Location: ../hospital/hospital-request.php");
				exit();
		}
		elseif(isset($_SESSION['admin'])){
			$_SESSION['message'] = "You must logout of your admin account before trying to login again.";
			header("Location: ../admin/admin-home.php");
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
	<!-- <div style = "margin-left: 35%; margin-top: 15%; width: 30%; border: 1px solid grey; padding: 20px;"> -->
	<div class = "login-form">
		<form action = "emp-login.php" method = "post">
			<center><h1>Employee Login</h1></center><br>
			<table>
				<tr>
					<td>Username:</td>
					<td>
						<input class = "form-control" type = "text" name = "name" placeholder="Enter your username" id = "login-input1">
					</td>
				</tr>
				<tr>
					<td>Password:</td>
					<td>
						<input class = "form-control" type = "password" name = "password" placeholder="Enter password" id = "login-input2">
					</td>
				</tr>
			</table><br>
			<center>
			<button class = "btn btn-primary" type = "submit" id = "login-button">Login</button>
			</center>
		</form>
	</div>
</body>
</html>