<!DOCTYPE html>
<html>
<head>
	<title>Enter new location</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<?php
		if(!isset($_SESSION))
			session_start();

		if(!isset($_SESSION['admin'])){
			$_SESSION['message'] = "You must be logged in as admin to add a new location!";
			header("Location: admin-login.php");
				exit();
		}
		$mainServe = "localhost";
		$mainuser = "root";
		$mainpass = "";
		$dbname = "lyflyne";
		$conn = mysqli_connect($mainServe, $mainuser, $mainpass, $dbname);

		if($conn == NULL || !$conn){
			die("Failed: ".mysqli_connect_error());
		}

		if($_SERVER['REQUEST_METHOD'] == "POST"){
			if(isset($_POST['state']) && isset($_POST['district']) && isset($_POST['city']))
			{
				$temp1 = $_POST['state'];
				$temp2 = $_POST['district'];
				$temp3 = $_POST['city'];
				$cmd = "SELECT * FROM location WHERE state = '$temp1' AND district = '$temp2'AND city = '$temp3';";
				$out = mysqli_query($conn, $cmd);
				$arr = mysqli_fetch_all($out);
				if(!is_array($arr) || count($arr) == 0)
				{
					$cmd = "INSERT INTO location(state, district, city) VALUES('$temp1', '$temp2', '$temp3');";
					$out = mysqli_query($conn, $cmd);
				}
			}
		}
	?>
</head>
<body>
	<form action = "location-entry.php" method = "post">
		<table>
			<tr>
				<td>State</td>
				<td><input type = "text" name = "state"></td>
			</tr>
			<tr>
				<td>District</td>
				<td><input type="text" name="district"></td>
			</tr>
			<tr>
				<td>City</td>
				<td><input type="text" name="city"></td>
			</tr>
		</table>
		<button type = "submit" value = "submit">Submit</button>
	</form>
</body>
</html>