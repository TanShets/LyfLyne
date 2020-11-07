<!DOCTYPE html>
<html>
<head>
	<title>Login to LyfLyne</title>
	<?php
		if(!isset($_SESSION))
			session_start();
		
		if(isset($_SESSION['message'])){
			echo "<script>";
			echo "alert(\"".$_SESSION['message']."\");";
			echo "</script>";
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
	<form action = "emp-login.php" method = "post">
		<h1>Employee Login</h1>
		<table>
			<tr>
				<td>Username</td>
				<td><input type = "text" name = "name" placeholder="Enter your username"></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type = "password" name = "password" placeholder="Enter password"></td>
			</tr>
		</table>
		<button type = "submit">Submit</button>
	</form><br>
	<form action = "emp-create.php">
		<button type = "submit">Create Employee Account</button>
	</form>
</body>
</html>