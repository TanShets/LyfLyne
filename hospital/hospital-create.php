<!DOCTYPE html>
<html>
<head>
	<title>Create Hospital Account</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<?php
		if(!isset($_SESSION))
			session_start();
		
		if(isset($_SESSION['hospital_user'])){
			$_SESSION['message'] = "You must be logged out to create a hospital account!";
			header("Location: hospital-request.php");
				exit();
		}
		
		if(!isset($_SESSION['h_create_temp']))
			$_SESSION['h_create_temp'] = array();
		
		$mainServe = "localhost";
		$mainuser = "root";
		$mainpass = "";
		$dbname = "lyflyne";
		$conn = mysqli_connect($mainServe, $mainuser, $mainpass, $dbname);

		if($conn == NULL || !$conn){
			die("Failed: ".mysqli_connect_error());
		}

		if($_SERVER['REQUEST_METHOD'] == "POST"){
			$count = 0;
			if(isset($_POST['name']) && $_POST['name'] != ""){
				$_SESSION['h_create_temp']['name'] = $_POST['name'];
				$count++;
			}

			if(isset($_POST['mobile']) && $_POST['mobile'] != ""){
				$_SESSION['h_create_temp']['mobile'] = $_POST['mobile'];
				$count++;
			}

			if(isset($_POST['landline']) && $_POST['landline'] != ""){
				$_SESSION['h_create_temp']['landline'] = $_POST['landline'];
				$count++;
			}

			if(isset($_POST['state']) && $_POST['state'] != ""){
				$_SESSION['h_create_temp']['state'] = $_POST['state'];
				$_SESSION['h_create_temp']['district'] = "";
				$_SESSION['h_create_temp']['city'] = "";
				$_SESSION['h_create_temp']['area'] = "";
				$count++;
			}

			if(isset($_POST['district']) && $_POST['district'] != ""){
				$_SESSION['h_create_temp']['district'] = $_POST['district'];
				$_SESSION['h_create_temp']['city'] = "";
				$_SESSION['h_create_temp']['area'] = "";
				$count++;
			}

			if(isset($_POST['city']) && $_POST['city'] != ""){
				$_SESSION['h_create_temp']['city'] = $_POST['city'];
				$_SESSION['h_create_temp']['area'] = "";
				$count++;
			}

			if(isset($_POST['area']) && $_POST['area'] != ""){
				$_SESSION['h_create_temp']['area'] = $_POST['area'];
				$count++;
			}

			if(isset($_POST['email']) && $_POST['email'] != ""){
				$_SESSION['h_create_temp']['email'] = $_POST['email'];
				$count++;
			}

			if(isset($_POST['username']) && $_POST['username'] != ""){
				$_SESSION['h_create_temp']['username'] = $_POST['username'];
				$count++;
			}

			if(isset($_POST['password']) && $_POST['password'] != ""){
				$_SESSION['h_create_temp']['password'] = $_POST['password'];
				$count++;
			}

			if(isset($_POST['cpassword']) && $_POST['cpassword'] != ""){
				$_SESSION['h_create_temp']['cpassword'] = $_POST['cpassword'];
				$count++;
			}

			if(isset($_POST['hcode']) && $_POST['hcode'] != ""){
				$_SESSION['h_create_temp']['hcode'] = $_POST['hcode'];
				$count++;
			}

			if($count == 12 && $_SESSION['h_create_temp']['password'] == $_SESSION['h_create_temp']['cpassword']){
				unset($_SESSION['h_create_temp']['cpassword']);
				$cmd = "SELECT lid FROM location WHERE state = '".$_SESSION['h_create_temp']['state']."' AND district = '".$_SESSION['h_create_temp']['district']."' AND city = '".$_SESSION['h_create_temp']['city']."' AND area = '".$_SESSION['h_create_temp']['area']."';";
				//echo $cmd;
				if(isset($conn) && $conn){
					$out = mysqli_query($conn, $cmd);
					if($out)
					{
						$lid = mysqli_fetch_array($out)['lid'];
						$_SESSION['h_create_temp']['lid'] = $lid;	
						//print_r($lid);
					}
				}
				unset($_SESSION['h_create_temp']['state']);
				unset($_SESSION['h_create_temp']['district']);
				unset($_SESSION['h_create_temp']['city']);
				unset($_SESSION['h_create_temp']['area']);
				$cmd = "INSERT INTO hospital_user(";
				foreach ($_SESSION['h_create_temp'] as $x => $y) {
					$cmd = $cmd.$x.", ";
				}
				$cmd = substr($cmd, 0, -2).") VALUES(";
				foreach ($_SESSION['h_create_temp'] as $x) {
					$cmd = $cmd."'".$x."', ";
				}
				$cmd = substr($cmd, 0, -2).");";
				//echo $cmd;
				if(isset($conn) && $conn){
					echo "Hear";
					//echo $cmd;
					$out = mysqli_query($conn, $cmd);
					if($out)
					{
						echo "me";
						header("Location: ../login.php");
							exit();
					}
				}
				else
					echo "Failed";
			}
		}

		function get_area($conn, $city){
			$cmd1 = "SELECT lid FROM location WHERE city = '$city';";
			$out1 = mysqli_query($conn, $cmd1);
			if($out1){
				$arr1 = mysqli_fetch_array($out);
				$cmd2 = "SELECT area FROM area_location WHERE clid = '".$arr1['lid']."';";

				$out2 = mysqli_query($conn, $cmd2);
				if($out2){
					$arr2 = mysqli_fetch_all($out2);
					return $arr2;
				}
			}

			return array();
		}
	?>
</head>
<body>
	<div style = "margin-left: 33%; margin-top: 2%; border: 1px solid grey; width: 35%; padding: 15px;">
		<center><h2>Create Hospital Account</h2></center><center>
		<form action = "hospital-create.php" method = "post">
			<table>
				<tr>
					<td>Name</td>
					<td><input type = "text" name = "name" class = "form-control"
					<?php 
						if(isset($_SESSION['h_create_temp']['name']) && $_SESSION['h_create_temp']['name'] != "")
							echo " value = \"".$_SESSION['h_create_temp']['name']."\"";
					?>
					></td>
				</tr>
				<tr>
					<td>Contact No.</td>
					<td><input type = "number" name = "mobile" class = "form-control"
					<?php
						if(isset($_SESSION['h_create_temp']['mobile']) && $_SESSION['h_create_temp']['mobile'] != "")
							echo " value = \"".$_SESSION['h_create_temp']['mobile']."\"";
					?>
					></td>
				</tr>
				<tr>
					<td>Landline No.</td>
					<td><input type = "number" name = "landline" class = "form-control"
					<?php
						if(isset($_SESSION['h_create_temp']['landline']) && $_SESSION['h_create_temp']['landline'] != "")
							echo " value = \"".$_SESSION['h_create_temp']['landline']."\"";
					?>
					></td>
				</tr>
				<tr>
					<td>State</td>
					<td>
						<?php
							echo "<select name = \"state\" class = \"form-control\" onchange = \"this.form.submit()\">";
							echo "<option value = \"\"";
							if(!isset($_SESSION['h_create_temp']['state']) || $_SESSION['h_create_temp']['state'] == "")
								echo " selected ";
							echo ">Select an option</option>";
							if(isset($conn) && $conn)
							{
								$cmd = "SELECT state FROM location GROUP BY(state);";
								$out = mysqli_query($conn, $cmd);
								$arr = mysqli_fetch_all($out);
								for($i = 0; $i < count($arr); $i++)
								{
									$temp = $arr[$i][0];
									echo "<option value = \"$temp\"";
									if(isset($_SESSION['h_create_temp']['state']) && $_SESSION['h_create_temp']['state'] == $temp)
										echo " selected ";
									echo ">".$temp."</option>";
								}
							}
							echo "</select>";
						?>
						<noscript><input type="submit" value="Submit"></noscript>
					</td>
				</tr>
				<tr>
					<td>District</td>
					<td>
						<select name = "district" class = "form-control" onchange = 'this.form.submit()'
						<?php
							if(!isset($_SESSION['h_create_temp']['state']) || $_SESSION['h_create_temp']['state'] == "")
								echo " disabled";
						?>
						>
						<?php
							if(isset($_SESSION['h_create_temp']['state']) && $_SESSION['h_create_temp']['state'] != "")
							{
								echo "<option value = \"\"";
								if(!isset($_SESSION['h_create_temp']['district']) || $_SESSION['h_create_temp']['district'] == "")
									echo " selected";
								echo ">Select an option</option>";
								$temp = $_SESSION['h_create_temp']['state'];
								$cmd = "SELECT district from location WHERE state = '$temp' GROUP BY(district);";
								$out = mysqli_query($conn, $cmd);
								$arr = mysqli_fetch_all($out);
								for($i = 0; $i < count($arr); $i++)
								{
									$temp = $arr[$i][0];
									echo "<option value = \"$temp\"";
									if(isset($_SESSION['h_create_temp']['district']) && $_SESSION['h_create_temp']['district'] == $temp)
										echo " selected";
									echo ">".$temp."</option>";
								}
							}
							else
								echo "<option value = \"\">Select an option</option>";
						?>
						</select>
						<noscript><input type="submit" value="Submit"></noscript>
					</td>
				</tr>
				<tr>
					<td>City/Town</td>
					<td>
						<select name = "city" class = "form-control" onchange = 'this.form.submit()'
						<?php
							if(!isset($_SESSION['h_create_temp']['state']) || $_SESSION['h_create_temp']['state'] == "" || !isset($_SESSION['h_create_temp']['district']) || $_SESSION['h_create_temp']['district'] == "")
								echo " disabled";
						?>
						>
						<?php
							if(isset($_SESSION['h_create_temp']['state']) && $_SESSION['h_create_temp']['state'] != "" && isset($_SESSION['h_create_temp']['district']) && $_SESSION['h_create_temp']['district'] != "")
							{
								echo "<option value = \"\"";
								if(!isset($_SESSION['h_create_temp']['city']) || $_SESSION['h_create_temp']['city'] == "")
									echo " selected";
								echo ">Select an option</option>";
								if(isset($conn) && $conn)
								{
									$temp = $_SESSION['h_create_temp']['state'];
									$temp1 = $_SESSION['h_create_temp']['district'];
									$cmd = "SELECT city FROM location WHERE state = '$temp' AND district = '$temp1' GROUP BY(city);";
									$out = mysqli_query($conn, $cmd);
									$arr = mysqli_fetch_all($out);
									for($i = 0; $i < count($arr); $i++)
									{
										$temp = $arr[$i][0];
										echo "<option value = \"$temp\"";
										if(!isset($_SESSION['h_create_temp']['city']) || $_SESSION['h_create_temp']['city'] == $temp)
											echo " selected";
										echo ">".$temp."</option>";
									}
								}
							}
							else
								echo "<option value = \"\">Select an option</option>";
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Area</td>
					<td>
						<select name = "area" class = "form-control"
						<?php
							if(
								!isset($_SESSION['h_create_temp']['state']) || $_SESSION['h_create_temp']['state'] == "" || 
								!isset($_SESSION['h_create_temp']['district']) || $_SESSION['h_create_temp']['district'] == "" || 
								!isset($_SESSION['h_create_temp']['city']) || $_SESSION['h_create_temp']['city'] == ""
							)
								echo " disabled";
						?>
						>
						<?php
							if(
								isset($_SESSION['h_create_temp']['state']) && $_SESSION['h_create_temp']['state'] != "" && 
								isset($_SESSION['h_create_temp']['district']) && $_SESSION['h_create_temp']['district'] != "" && 
								isset($_SESSION['h_create_temp']['city']) && $_SESSION['h_create_temp']['city'] != ""
							)
							{
								echo "<option value = \"\"";
								if(!isset($_SESSION['h_create_temp']['area']) || $_SESSION['h_create_temp']['area'] == "")
									echo " selected";
								echo ">Select an option</option>";
								if(isset($conn) && $conn)
								{
									$temp = $_SESSION['h_create_temp']['state'];
									$temp1 = $_SESSION['h_create_temp']['district'];
									$temp2 = $_SESSION['h_create_temp']['city'];
									$cmd = "SELECT area FROM location WHERE state = '$temp' AND district = '$temp1' AND city = '$temp2';";
									
									//$arr2 

									$out = mysqli_query($conn, $cmd);
									$arr = mysqli_fetch_all($out);
									$arr2 = get_area($conn, $temp2);
									
									for($i = 0; $i < count($arr); $i++)
									{
										$temp = $arr[$i][0];
										echo "<option value = \"$temp\"";
										if(!isset($_SESSION['h_create_temp']['area']) || $_SESSION['h_create_temp']['area'] == $temp)
											echo " selected";
										echo ">".$temp."</option>";
									}
								}
							}
							else
								echo "<option value = \"\">Select an option</option>";
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Email</td>
					<td><input type = "email" name = "email" class = "form-control"
					<?php
						if(isset($_SESSION['h_create_temp']['email']) && $_SESSION['h_create_temp']['email'] != "")
							echo " value = \"".$_SESSION['h_create_temp']['email']."\"";
					?>
					></td>
				</tr>
				<tr>
					<td>Username</td>
					<td><input type = "text" name = "username" class = "form-control"
					<?php
						if(isset($_SESSION['h_create_temp']['username']) && $_SESSION['h_create_temp']['username'] != "")
							echo " value = \"".$_SESSION['h_create_temp']['username']."\"";
					?>
					></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type = "password" name = "password" class = "form-control"></td>
				</tr>
				<tr>
					<td>Confirm Password</td>
					<td><input type = "password" name = "cpassword" class = "form-control"></td>
				</tr>
				<tr>
					<td>Hospital Code</td>
					<td><input type = "number" name = "hcode" class = "form-control"
					<?php
						if(isset($_SESSION['h_create_temp']['hcode']) && $_SESSION['h_create_temp']['hcode'] != "")
							echo " value = \"".$_SESSION['h_create_temp']['hcode']."\"";
					?>
					></td>
				</tr>
			</table><br>
			<button type = "submit" class = "btn btn-success" style = "width: 40%; margin-left: 33%;">
				Create
			</button>
		</form></center>
	</div>
</body>
</html>