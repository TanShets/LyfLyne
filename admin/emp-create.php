<!DOCTYPE html>
<html>
<head>
	<title>Create Employee Account</title>
	<?php
		if(!isset($_SESSION))
			session_start();

		if(isset($_SESSION['emp-user'])){
			$_SESSION['message'] = "You must be logged out to create a new account";
			header("Location: ../employee/emp-requests.php");
				exit();
		}

		if(!isset($_SESSION['admin'])){
			$_SESSION['message'] = "You must be logged in as admin to create a new employee";
			header("Location: admin-login.php");
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
				$_SESSION['emp_create_temp']['name'] = $_POST['name'];
				$count++;
			}

			if(isset($_POST['mobile']) && $_POST['mobile'] != ""){
				$_SESSION['emp_create_temp']['mobile'] = $_POST['mobile'];
				$count++;
			}

			if(isset($_POST['landline']) && $_POST['landline'] != ""){
				$_SESSION['emp_create_temp']['landline'] = $_POST['landline'];
				$count++;
			}

			if(isset($_POST['state']) && $_POST['state'] != ""){
				$_SESSION['emp_create_temp']['state'] = $_POST['state'];
				$_SESSION['emp_create_temp']['district'] = "";
				$_SESSION['emp_create_temp']['city'] = "";
				$count++;
			}

			if(isset($_POST['district']) && $_POST['district'] != ""){
				$_SESSION['emp_create_temp']['district'] = $_POST['district'];
				$_SESSION['emp_create_temp']['city'] = "";
				$count++;
			}

			if(isset($_POST['city']) && $_POST['city'] != ""){
				$_SESSION['emp_create_temp']['city'] = $_POST['city'];
				$count++;
			}

			if(isset($_POST['email']) && $_POST['email'] != ""){
				$_SESSION['emp_create_temp']['email'] = $_POST['email'];
				$count++;
			}

			if(isset($_POST['username']) && $_POST['username'] != ""){
				$_SESSION['emp_create_temp']['username'] = $_POST['username'];
				$count++;
			}

			if(isset($_POST['password']) && $_POST['password'] != ""){
				$_SESSION['emp_create_temp']['password'] = $_POST['password'];
				$count++;
			}

			if(isset($_POST['cpassword']) && $_POST['cpassword'] != ""){
				$_SESSION['emp_create_temp']['cpassword'] = $_POST['cpassword'];
				$count++;
			}

			if(isset($_POST['ecode']) && $_POST['ecode'] != ""){
				$_SESSION['emp_create_temp']['ecode'] = $_POST['ecode'];
				$count++;
			}

			if($count == 11 && $_SESSION['emp_create_temp']['password'] == $_SESSION['emp_create_temp']['cpassword']){
				unset($_SESSION['emp_create_temp']['cpassword']);
				$cmd = "SELECT lid FROM location WHERE state = '".$_SESSION['emp_create_temp']['state']."' AND district = '".$_SESSION['emp_create_temp']['district']."' AND city = '".$_SESSION['emp_create_temp']['city']."';";
				echo $cmd;
				if(isset($conn) && $conn){
					$out = mysqli_query($conn, $cmd);
					if($out)
					{
						$lid = mysqli_fetch_array($out)['lid'];
						$_SESSION['emp_create_temp']['lid'] = $lid;	
						//print_r($lid);
						echo "<br>";
					}
				}
				unset($_SESSION['emp_create_temp']['state']);
				unset($_SESSION['emp_create_temp']['district']);
				unset($_SESSION['emp_create_temp']['city']);
				$cmd = "INSERT INTO emp_user(";
				foreach ($_SESSION['emp_create_temp'] as $x => $y) {
					$cmd = $cmd.$x.", ";
				}
				$cmd = substr($cmd, 0, -2).") VALUES(";
				foreach ($_SESSION['emp_create_temp'] as $x) {
					$cmd = $cmd."'".$x."', ";
				}
				$cmd = substr($cmd, 0, -2).");";
				echo $cmd;
				if(isset($conn) && $conn){
					echo "Hear";
					$timex = 0;
					if($out)
						unset($out);
					while((!isset($out) || !$out) && $timex < 100){
						$out = mysqli_query($conn, $cmd);
						$timex++;	
					}
					echo $timex;
					if($out)
					{
						echo "me";
						print_r($cmd);
						//echo "Success";
						header("Location: ../employee/emp-login.php");
							exit();
					}
					else{
						echo "Failure: In query";
					}
				}
				else
					echo "Failed";
			}
		}
	?>
</head>
<body>
	<h1>Create Employee Account</h1>
	<form action = "emp-create.php" method = "post">
		<table>
			<tr>
				<td>Name</td>
				<td><input type = "text" name = "name"
				<?php 
					if(isset($_SESSION['emp_create_temp']['name']) && $_SESSION['emp_create_temp']['name'] != "")
						echo " value = \"".$_SESSION['emp_create_temp']['name']."\"";
				?>></td>
			</tr>
			<tr>
				<td>Contact No.</td>
				<td><input type = "number" name = "mobile"
				<?php
					if(isset($_SESSION['emp_create_temp']['mobile']) && $_SESSION['emp_create_temp']['mobile'] != "")
						echo " value = \"".$_SESSION['emp_create_temp']['mobile']."\"";
				?>></td>
			</tr>
			<tr>
				<td>Landline No.</td>
				<td><input type = "number" name = "landline"
				<?php
					if(isset($_SESSION['emp_create_temp']['landline']) && $_SESSION['emp_create_temp']['landline'] != "")
						echo " value = \"".$_SESSION['emp_create_temp']['landline']."\"";
				?>
				></td>
			</tr>
			<tr>
				<td>State</td>
				<td>
					<?php
						echo "<select name = \"state\" onchange = \"this.form.submit()\">";
						echo "<option value = \"\"";
						if(!isset($_SESSION['emp_create_temp']['state']) || $_SESSION['emp_create_temp']['state'] == "")
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
								if(isset($_SESSION['emp_create_temp']['state']) && $_SESSION['emp_create_temp']['state'] == $temp)
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
						if(!isset($_SESSION['emp_create_temp']['state']) || $_SESSION['emp_create_temp']['state'] == "")
							echo " disabled";
					?>
					>
					<?php
						if(isset($_SESSION['emp_create_temp']['state']) && $_SESSION['emp_create_temp']['state'] != "")
						{
							echo "<option value = \"\"";
							if(!isset($_SESSION['emp_create_temp']['district']) || $_SESSION['emp_create_temp']['district'] == "")
								echo " selected";
							echo ">Select an option</option>";
							$temp = $_SESSION['emp_create_temp']['state'];
							$cmd = "SELECT district from location WHERE state = '$temp' GROUP BY(district);";
							$out = mysqli_query($conn, $cmd);
							$arr = mysqli_fetch_all($out);
							for($i = 0; $i < count($arr); $i++)
							{
								$temp = $arr[$i][0];
								echo "<option value = \"$temp\"";
								if(isset($_SESSION['emp_create_temp']['district']) && $_SESSION['emp_create_temp']['district'] == $temp)
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
						if(!isset($_SESSION['emp_create_temp']['state']) || $_SESSION['emp_create_temp']['state'] == "" || !isset($_SESSION['emp_create_temp']['district']) || $_SESSION['emp_create_temp']['district'] == "")
							echo " disabled";
					?>
					>
					<?php
						if(isset($_SESSION['emp_create_temp']['state']) && $_SESSION['emp_create_temp']['state'] != "" && isset($_SESSION['emp_create_temp']['district']) && $_SESSION['emp_create_temp']['district'] != "")
						{
							echo "<option value = \"\"";
							if(!isset($_SESSION['emp_create_temp']['city']) || $_SESSION['emp_create_temp']['city'] == "")
								echo " selected";
							echo ">Select an option</option>";
							if(isset($conn) && $conn)
							{
								$temp = $_SESSION['emp_create_temp']['state'];
								$temp1 = $_SESSION['emp_create_temp']['district'];
								$cmd = "SELECT city FROM location WHERE state = '$temp' AND district = '$temp1';";
								$out = mysqli_query($conn, $cmd);
								$arr = mysqli_fetch_all($out);
								for($i = 0; $i < count($arr); $i++)
								{
									$temp = $arr[$i][0];
									echo "<option value = \"$temp\"";
									if(!isset($_SESSION['emp_create_temp']['city']) || $_SESSION['emp_create_temp']['city'] == $temp)
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
					if(isset($_SESSION['emp_create_temp']['email']) && $_SESSION['emp_create_temp']['email'] != "")
						echo " value = \"".$_SESSION['emp_create_temp']['email']."\"";
				?>
				></td>
			</tr>
			<tr>
				<td>Username</td>
				<td><input type = "text" name = "username"
				<?php
					if(isset($_SESSION['emp_create_temp']['username']) && $_SESSION['emp_create_temp']['username'] != "")
						echo " value = \"".$_SESSION['emp_create_temp']['username']."\"";
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
				<td>Employee Code</td>
				<td><input type = "number" name = "ecode"></td>
			</tr>
		</table>
		<button type = "submit">Submit</button>
	</form>
</body>
</html>