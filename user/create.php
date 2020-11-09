<!DOCTYPE html>
<html>
<head>
	<title>Create an Account</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<?php
		if(!isset($_SESSION))
			session_start();

		if(isset($_SESSION['user'])){
			$_SESSION['message'] = "You must be logged out to create a new account!";
			header("Location: create-request.php");
				exit();
		}
		if(!isset($_SESSION['create_temp']))
			$_SESSION['create_temp'] = array();
		
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
				$_SESSION['create_temp']['name'] = $_POST['name'];
				$count++;
			}

			if(isset($_POST['b-group']) && $_POST['b-group'] != ""){
				$_SESSION['create_temp']['btype'] = $_POST['b-group'];
				$count++;
			}

			if(isset($_POST['mobile']) && $_POST['mobile'] != ""){
				$_SESSION['create_temp']['mobile'] = $_POST['mobile'];
				$count++;
			}

			if(isset($_POST['landline']) && $_POST['landline'] != ""){
				$_SESSION['create_temp']['landline'] = $_POST['landline'];
				$count++;
			}

			if(isset($_POST['state']) && $_POST['state'] != ""){
				$_SESSION['create_temp']['state'] = $_POST['state'];
				$_SESSION['create_temp']['district'] = "";
				$_SESSION['create_temp']['city'] = "";
				$count++;
			}

			if(isset($_POST['district']) && $_POST['district'] != ""){
				$_SESSION['create_temp']['district'] = $_POST['district'];
				$_SESSION['create_temp']['city'] = "";
				$count++;
			}

			if(isset($_POST['city']) && $_POST['city'] != ""){
				$_SESSION['create_temp']['city'] = $_POST['city'];
				$count++;
			}

			if(isset($_POST['email']) && $_POST['email'] != ""){
				$_SESSION['create_temp']['email'] = $_POST['email'];
				$count++;
			}

			if(isset($_POST['username']) && $_POST['username'] != ""){
				$_SESSION['create_temp']['username'] = $_POST['username'];
				$count++;
			}

			if(isset($_POST['password']) && $_POST['password'] != ""){
				$_SESSION['create_temp']['password'] = $_POST['password'];
				$count++;
			}

			if(isset($_POST['cpassword']) && $_POST['cpassword'] != ""){
				$_SESSION['create_temp']['cpassword'] = $_POST['cpassword'];
				$count++;
			}

			if(isset($_POST['bdonor']) && $_POST['bdonor'] != ""){
				$_SESSION['create_temp']['bdonor'] = $_POST['bdonor'];
				$count++;
			}

			if(isset($_POST['mdonor']) && $_POST['mdonor'] != ""){
				$_SESSION['create_temp']['mdonor'] = $_POST['mdonor'];
				$count++;
			}

			if(isset($_POST['odonor']) && $_POST['odonor'] != ""){
				$_SESSION['create_temp']['odonor'] = $_POST['odonor'];
				$count++;
			}

			unset($_SESSION['create_temp']['b-group']);

			if($count == 14 && $_SESSION['create_temp']['password'] == $_SESSION['create_temp']['cpassword']){
				unset($_SESSION['create_temp']['cpassword']);
				$cmd = "SELECT lid FROM location WHERE state = '".$_SESSION['create_temp']['state']."' AND district = '".$_SESSION['create_temp']['district']."' AND city = '".$_SESSION['create_temp']['city']."';";
				echo $cmd;
				if(isset($conn) && $conn){
					$out = mysqli_query($conn, $cmd);
					if($out)
					{
						$lid = mysqli_fetch_array($out)['lid'];
						$_SESSION['create_temp']['lid'] = $lid;	
						print_r($lid);
					}
				}
				unset($_SESSION['create_temp']['state']);
				unset($_SESSION['create_temp']['district']);
				unset($_SESSION['create_temp']['city']);
				$cmd = "INSERT INTO user(";
				foreach ($_SESSION['create_temp'] as $x => $y) {
					$cmd = $cmd.$x.", ";
				}
				$cmd = substr($cmd, 0, -2).") VALUES(";
				foreach ($_SESSION['create_temp'] as $x) {
					$cmd = $cmd."'".$x."', ";
				}
				$cmd = substr($cmd, 0, -2).");";
				//echo $cmd;
				if(isset($conn) && $conn){
					//echo "Hear";
					$out = mysqli_query($conn, $cmd);
					if($out)
					{
						$cmd = "SELECT uid FROM user WHERE ";
						foreach ($_SESSION['create_temp'] as $x => $y) {
							$cmd = $cmd.$x."= '$y' AND ";
						}
						$cmd = substr($cmd, 0, -5).";";
						$out = mysqli_query($conn, $cmd);
						if($out){
							$arr = mysqli_fetch_array($out);
							$uid = $arr['uid'];
							$questions = 0;
							$answers = 0;
							if($_SESSION['create_temp']['bdonor'] == 1){
								$questions++;
								$cmd = "INSERT INTO blood(uid, btype, isbank, quantity, lid) VALUES('$uid', ";
								$cmd = $cmd."'".$_SESSION['create_temp']['btype']."', 0, 0, ";
								$cmd = $cmd."'".$_SESSION['create_temp']['lid']."');";
								$out = mysqli_query($conn, $cmd);
								if($out)
									$answers++;
							}

							if($_SESSION['create_temp']['mdonor'] == 1){
								$questions++;
								$cmd = "INSERT INTO marrow(uid, btype, isbank, quantity, lid) VALUES('$uid', ";
								$cmd = $cmd."'".$_SESSION['create_temp']['btype']."', 0, 0, ";
								$cmd = $cmd."'".$_SESSION['create_temp']['lid']."');";
								$out = mysqli_query($conn, $cmd);
								if($out)
									$answers++;
							}

							if($questions == $answers)
								$_SESSION['message'] = "Successful creation of account";
							else
								$_SESSION['message'] = "There was some at creation time!";
							
							header("Location: ../login.php");
							exit();
						}
						else{
							echo "<script>";
							echo "alert(\"Creation of account failed!\");";
							echo "</script>";
						}
					}
				}
				else{
					echo "<script>";
					echo "alert(\"Creation of account failed!\");";
					echo "</script>";
				}
			}
		}
	?>
</head>
<body>
	<h1>Create Account</h1>
	<form action = "create.php" method = "post">
		<table>
			<tr>
				<td>Name</td>
				<td><input type = "text" name = "name"
				<?php 
					if(isset($_SESSION['create_temp']['name']) && $_SESSION['create_temp']['name'] != "")
						echo " value = \"".$_SESSION['create_temp']['name']."\"";
				?>></td>
			</tr>
			<tr>
				<td>Blood Group</td>
				<td>
					<select name = "b-group">
					<?php
						echo "<option value = \"\"";
						if(!isset($_SESSION['create_temp']['btype']) || $_SESSION['create_temp']['btype'] == "")
							echo " selected";
						echo ">Select an option</option>";
						$btypes = Array('A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-', 'Rh null');
						for($i = 0; $i < count($btypes); $i++)
						{
							$tempx = $btypes[$i];
							echo "<option value = \"$tempx\"";
							if(isset($_SESSION['create_temp']['btype']) && $_SESSION['create_temp']['btype'] == $tempx)
								echo " selected";
							echo ">".$tempx."</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Mobile No.</td>
				<td><input type = "number" name = "mobile"
				<?php
					if(isset($_SESSION['create_temp']['mobile']) && $_SESSION['create_temp']['mobile'] != "")
						echo " value = \"".$_SESSION['create_temp']['mobile']."\"";
				?>
				></td>
			</tr>
			<tr>
				<td>Landline No.</td>
				<td><input type = "number" name = "landline"
				<?php
					if(isset($_SESSION['create_temp']['landline']) && $_SESSION['create_temp']['landline'] != "")
						echo " value = \"".$_SESSION['create_temp']['landline']."\"";
				?>
				></td>
			</tr>
			<tr>
				<td>State</td>
				<td>
					<?php
						echo "<select name = \"state\" onchange = \"this.form.submit()\">";
						echo "<option value = \"\"";
						if(!isset($_SESSION['create_temp']['state']) || $_SESSION['create_temp']['state'] == "")
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
								if(isset($_SESSION['create_temp']['state']) && $_SESSION['create_temp']['state'] == $temp)
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
					<select name = "district" onchange = 'this.form.submit()'
					<?php
						if(!isset($_SESSION['create_temp']['state']) || $_SESSION['create_temp']['state'] == "")
							echo " disabled";
					?>
					>
					<?php
						if(isset($_SESSION['create_temp']['state']) && $_SESSION['create_temp']['state'] != "")
						{
							echo "<option value = \"\"";
							if(!isset($_SESSION['create_temp']['district']) || $_SESSION['create_temp']['district'] == "")
								echo " selected";
							echo ">Select an option</option>";
							$temp = $_SESSION['create_temp']['state'];
							$cmd = "SELECT district from location WHERE state = '$temp' GROUP BY(district);";
							$out = mysqli_query($conn, $cmd);
							$arr = mysqli_fetch_all($out);
							for($i = 0; $i < count($arr); $i++)
							{
								$temp = $arr[$i][0];
								echo "<option value = \"$temp\"";
								if(isset($_SESSION['create_temp']['district']) && $_SESSION['create_temp']['district'] == $temp)
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
					<select name = "city"
					<?php
						if(!isset($_SESSION['create_temp']['state']) || $_SESSION['create_temp']['state'] == "" || !isset($_SESSION['create_temp']['district']) || $_SESSION['create_temp']['district'] == "")
							echo " disabled";
					?>
					>
					<?php
						if(isset($_SESSION['create_temp']['state']) && $_SESSION['create_temp']['state'] != "" && isset($_SESSION['create_temp']['district']) && $_SESSION['create_temp']['district'] != "")
						{
							echo "<option value = \"\"";
							if(!isset($_SESSION['create_temp']['city']) || $_SESSION['create_temp']['city'] == "")
								echo " selected";
							echo ">Select an option</option>";
							if(isset($conn) && $conn)
							{
								$temp = $_SESSION['create_temp']['state'];
								$temp1 = $_SESSION['create_temp']['district'];
								$cmd = "SELECT city FROM location WHERE state = '$temp' AND district = '$temp1';";
								$out = mysqli_query($conn, $cmd);
								$arr = mysqli_fetch_all($out);
								for($i = 0; $i < count($arr); $i++)
								{
									$temp = $arr[$i][0];
									echo "<option value = \"$temp\"";
									if(!isset($_SESSION['create_temp']['city']) || $_SESSION['create_temp']['city'] == $temp)
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
				<td><input type = "email" name = "email"
				<?php
					if(isset($_SESSION['create_temp']['email']) && $_SESSION['create_temp']['email'] != "")
						echo " value = \"".$_SESSION['create_temp']['email']."\"";
				?>
				></td>
			</tr>
			<tr>
				<td>Username</td>
				<td><input type = "text" name = "username"
				<?php
					if(isset($_SESSION['create_temp']['username']) && $_SESSION['create_temp']['username'] != "")
						echo " value = \"".$_SESSION['create_temp']['username']."\"";
				?>
				></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type = "password" name = "password"></td>
			</tr>
			<tr>
				<td>Confirm Password</td>
				<td><input type = "password" name = "cpassword"></td>
			</tr>
			<tr>
				<td>Are you available as a blood donor?</td>
				<td>
					<select name = "bdonor">
						<option value = ""
						<?php 
							if(!isset($_SESSION['create_temp']['bdonor']) || $_SESSION['create_temp']['bdonor'] == ""){
								echo " selected";
							}
						?>
						>Select an option</option>
						<option value = "1"
						<?php 
							if(isset($_SESSION['create_temp']['bdonor']) && $_SESSION['create_temp']['bdonor'] == "1"){
								echo " selected";
							}
						?>
						>Yes</option>
						<option value = "0"
						<?php 
							if(isset($_SESSION['create_temp']['bdonor']) && $_SESSION['create_temp']['bdonor'] == "0"){
								echo " selected";
							}
						?>
						>No</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Do you consent to potentially donating your marrow?</td>
				<td>
					<select name = "mdonor">
						<option value = ""
						<?php 
							if(!isset($_SESSION['create_temp']['mdonor']) || $_SESSION['create_temp']['mdonor'] == ""){
								echo " selected";
							}
						?>
						>Select an option</option>
						<option value = "1"
						<?php 
							if(isset($_SESSION['create_temp']['mdonor']) && $_SESSION['create_temp']['mdonor'] == "1"){
								echo " selected";
							}
						?>
						>Yes</option>
						<option value = "0"
						<?php 
							if(isset($_SESSION['create_temp']['mdonor']) && $_SESSION['create_temp']['mdonor'] == "0"){
								echo " selected";
							}
						?>
						>No</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Upon your death, do you consent to donate your other organs?</td>
				<td>
					<select name = "odonor">
						<option value = ""
						<?php 
							if(!isset($_SESSION['create_temp']['odonor']) || $_SESSION['create_temp']['odonor'] == ""){
								echo " selected";
							}
						?>
						>Select an option</option>
						<option value = "1"
						<?php 
							if(isset($_SESSION['create_temp']['odonor']) && $_SESSION['create_temp']['odonor'] == "1"){
								echo " selected";
							}
						?>
						>Yes</option>
						<option value = "0"
						<?php 
							if(isset($_SESSION['create_temp']['odonor']) && $_SESSION['create_temp']['odonor'] == "0"){
								echo " selected";
							}
						?>
						>No</option>
					</select>
				</td>
			</tr>
		</table>
		<button type = "submit">Submit</button>
	</form>
</body>
</html>