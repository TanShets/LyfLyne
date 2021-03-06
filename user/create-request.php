<!DOCTYPE html>
<html>
<head>
	<title>Create Individual Request</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<?php
		if(!isset($_SESSION))
			session_start();

		if(!isset($_SESSION['user'])){
			$_SESSION['message'] = "You must be logged in to create a request!";
			header("Location: ../login.php");
				exit();
		}

		if(isset($_SESSION['message'])){
			echo "<script>";
			echo "alert(\"".$_SESSION['message']."\");";
			echo "</script>";
			unset($_SESSION['message']);
		}
		$mainServe = "localhost";
		$mainuser = "root";
		$mainpass = "";
		$dbname = "lyflyne";
		$conn = mysqli_connect($mainServe, $mainuser, $mainpass, $dbname);
		if(!isset($_SESSION['request']))
		{
			$_SESSION['request'] = array();
		}
		//$_SESSION['request']['isloc'] = "no";
		//$_SESSION['request']['state'] = "a";
		//$_SESSION['request']['district'] = "a";
		if($conn == NULL || !$conn){
			die("Failed: ".mysqli_connect_error());
		}

		if($_SERVER['REQUEST_METHOD'] == "POST"){
			$count = 0;
			if(isset($_POST['type'])){
				$_SESSION['request']['type'] = $_POST['type'];
				if($_SESSION['request']['type'] != "")
					$count++;
			}

			if(isset($_POST['isloc']) && $_POST['isloc'] != ""){
				$_SESSION['request']['isloc'] = $_POST['isloc'];
				if($_SESSION['request']['isloc'] != ""){
					$count++;
					if($_SESSION['request']['isloc'] == "yes"){
						unset($_POST['state']);
						unset($_POST['district']);
						unset($_POST['city']);
					}
				}
			}

			if(isset($_POST['state']) && $_POST['state'] != ""){
				$_SESSION['request']['state'] = $_POST['state'];
				if($_SESSION['request']['state'] != "")
					$count++;
			}

			if(isset($_POST['district']) && $_POST['district'] != ""){
				$_SESSION['request']['district'] = $_POST['district'];
				if($_SESSION['request']['district'] != "")
					$count++;
			}

			if(isset($_POST['city']) && $_POST['city'] != ""){
				$_SESSION['request']['city'] = $_POST['city'];
				if($_SESSION['request']['city'] != "")
					$count++;
			}

			if(isset($_POST['area']) && $_POST['area'] != ""){
				$_SESSION['request']['area'] = $_POST['area'];
				if($_SESSION['request']['area'] != "")
					$count++;
			}

			if(isset($_POST['priority']) && $_POST['priority'] != ""){
				$_SESSION['request']['priority'] = $_POST['priority'];
				if($_SESSION['request']['priority'] != "")
					$count++;
			}

			//echo "Look";
			if(isset($conn) && $conn){
				$lid = null;
				if($count == 7)
				{
					$temp1 = $_SESSION['request']['state'];
					$temp2 = $_SESSION['request']['district'];
					$temp3 = $_SESSION['request']['city'];
					$temp4 = $_SESSION['request']['area'];
					$cmd = "SELECT lid FROM location WHERE state = '$temp1' AND district = '$temp2' AND city = '$temp3' AND area = '$temp4';";
					$out = mysqli_query($conn, $cmd);
					$lid = mysqli_fetch_array($out)['lid'];

					$cmd2 = "SELECT lid FROM area_location WHERE clid = '$lid';";
					$out2 = mysqli_query($conn, $cmd2);
					$lid2 = mysqli_fetch_array($out2)['lid'];
				}
				elseif($count == 3 && isset($_SESSION['request']['isloc']) && $_SESSION['request']['isloc'] == "yes")
				{
					if(isset($_SESSION['user']))
					{
						$uid = $_SESSION['user']['uid'];
						$cmd = "SELECT lid FROM user WHERE uid = '$uid'";
						$out = mysqli_query($conn, $cmd);
						$lid = mysqli_fetch_array($out)['lid'];
					}
				}
				//echo "Here";
				if($lid != null)
				{
					if(isset($_SESSION['user']))
					{
						$uid = $_SESSION['user']['uid'];
						$priority = $_SESSION['request']['priority'];
						$dtype = $_SESSION['request']['type'];
						$cmd = "INSERT INTO request(lid, priority, dtype, uid, request_time, lookin) VALUES('$lid', '$priority', '$dtype', '$uid', NOW(), '$lid');";
						$out = mysqli_query($conn, $cmd);
						if($out){
							header("Location: view-request.php");
								exit();
						}
					}
				}
			}
		}
		//print_r($_SESSION);
	?>
</head>
<body>
	<?php include_once('../navbar.php'); ?>
	<div style = "margin-left: 30%; margin-top: 10%; border: 1px solid grey; width: 40%; padding: 20px;">
		<h2>Create Request</h2>
		<form action = "create-request.php" method = "post">
			<table>
				<tr>
					<td>Donation type</td>
					<td>
						<select class = "form-control" name = "type" onchange = 'this.form.submit()'>
							<?php
								echo "<option value = \"\"";
								if(!isset($_SESSION['request']['type']) || $_SESSION['request']['type'] == "")
									echo " selected ";
								echo ">Select an option</option>";
								if(isset($conn) && $conn)
								{
									$cmd = "SELECT tablename from control;";
									$out = mysqli_query($conn, $cmd);
									$arr = mysqli_fetch_all($out);
									for($i = 0; $i < count($arr); $i++)
									{
										$temp = $arr[$i][0];
										echo "<option value = \"$temp\"";
										if(isset($_SESSION['request']['type']) && $_SESSION['request']['type'] == $temp)
											echo " selected ";
										echo ">$temp</option>";
									}
									//print_r($arr);
								}
							?>
						</select>
						<noscript><input type = "submit" value = "submit"></noscript>
					</td>
				</tr>
				<tr>
					<td>Is it the same location as original?</td>
					<td>
						<select class = "form-control" name = "isloc" onchange = 'this.form.submit()'>
							<option value = ""
							<?php 
								if(!isset($_SESSION['request']['isloc']) || $_SESSION['request']['isloc'] == "")
									echo " selected";
							?>
							>Select one</option>
							<option value = "yes"
							<?php 
								if(!isset($_SESSION['request']['isloc']) || $_SESSION['request']['isloc'] == "yes")
									echo " selected ";
							?>
							>Yes</option>
							<option value = "no"
							<?php 
								if(!isset($_SESSION['request']['isloc']) || $_SESSION['request']['isloc'] == "no")
									echo " selected ";
							?>
							>No</option>
						</select>
					</td>
				</tr>
				<?php
					if(!isset($_SESSION['request']['isloc']) || (isset($_SESSION['request']['isloc']) && $_SESSION['request']['isloc'] == "no"))
					{
						echo "<tr>";
						echo "<td>State</td><td>";
						echo "<select class = \"form-control\" name = \"state\" onchange = \"this.form.submit()\">";
						echo "<option value = \"\"";
						if(!isset($_SESSION['request']['state']) || $_SESSION['request']['state'] == "")
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
								if(isset($_SESSION['request']['state']) && $_SESSION['request']['state'] == $temp)
									echo " selected ";
								echo ">".$temp."</option>";
							}
						}
						echo "</select><noscript><input type=\"submit\" value=\"submit\"></noscript>";
						echo "</td>";
						echo "</tr>";

						if(isset($_SESSION['request']['state']) && $_SESSION['request']['state'] != "")
						{
							echo "<tr>";
							echo "<td>District</td><td>";
							echo "<select name = \"district\" class = \"form-control\" onchange = \"this.form.submit()\">";
							echo "<option value = \"\"";
							if(!isset($_SESSION['request']['district']) || $_SESSION['request']['district'] == "")
								echo " selected ";
							echo ">Select an option</option>";
							if(isset($conn) && $conn)
							{
								$temp = $_SESSION['request']['state'];
								$cmd = "SELECT district FROM location WHERE state = '$temp' GROUP BY(district);";
								$out = mysqli_query($conn, $cmd);
								$arr = mysqli_fetch_all($out);
								for($i = 0; $i < count($arr); $i++)
								{
									$temp = $arr[$i][0];
									echo "<option value = \"$temp\"";
									if(isset($_SESSION['request']['district']) && $_SESSION['request']['district'] == $temp)
										echo " selected ";
									echo ">".$temp."</option>";
								}
							}
							echo "</select><noscript><input type=\"submit\" value=\"submit\"></noscript>";
							echo "</td>";
							echo "</tr>";

							if(isset($_SESSION['request']['district']) && $_SESSION['request']['district'] != "")
							{
								echo "<tr>";
								echo "<td>City</td><td>";
								echo "<select name = \"city\" class = \"form-control\" onchange = \"this.form.submit()\">";
								echo "<option value = \"\"";
								if(!isset($_SESSION['request']['city']) || $_SESSION['request']['city'] == "")
									echo " selected ";
								echo ">Select an option</option>";
								if(isset($conn) && $conn)
								{
									$temp = $_SESSION['request']['district'];
									$temp1 = $_SESSION['request']['state'];
									$cmd = "SELECT city FROM location WHERE state = '$temp1' AND district = '$temp' GROUP BY(city);";
									
									$out = mysqli_query($conn, $cmd);
									$arr = mysqli_fetch_all($out);
									for($i = 0; $i < count($arr); $i++)
									{
										$temp = $arr[$i][0];
										echo "<option value = \"$temp\"";
										if(isset($_SESSION['request']['city']) && $_SESSION['request']['city'] == $temp)
											echo " selected ";
										echo ">".$temp."</option>";
									}
								}
								echo "</select>";
								echo "</td>";
								echo "</tr>";

								if(isset($_SESSION['request']['city']) && $_SESSION['request']['city'] != ""){
									echo "<tr>";
									echo "<td>Area</td><td>";
									echo "<select name = \"area\" class = \"form-control\">";
									echo "<option value = \"\"";
									if(!isset($_SESSION['request']['area']) || $_SESSION['request']['area'] == "")
										echo " selected ";
									echo ">Select an option</option>";
									if(isset($conn) && $conn)
									{
										$temp = $_SESSION['request']['district'];
										$temp1 = $_SESSION['request']['state'];
										$temp2 = $_SESSION['request']['city'];
										$cmd = "SELECT area FROM location WHERE state = '$temp1' AND district = '$temp' AND city = '$temp2';";
										
										$out = mysqli_query($conn, $cmd);
										$arr = mysqli_fetch_all($out);
										for($i = 0; $i < count($arr); $i++)
										{
											$temp = $arr[$i][0];
											echo "<option value = \"$temp\"";
											if(isset($_SESSION['request']['area']) && $_SESSION['request']['area'] == $temp)
												echo " selected ";
											echo ">".$temp."</option>";
										}
									}
									echo "</select>";
									echo "</td>";
									echo "</tr>";
								}
							}
						}
					}
				?>
				<tr>
					<td>Is the situation immediately life-threatening?</td>
					<td>
						<select name = "priority" class = "form-control">
							<option value = ""
							<?php
								if(!isset($_SESSION['request']['priority']) || $_SESSION['request']['priority'] == "")
									echo " selected ";
							?>
							>Select any one</option>
							<option value = "1"
							<?php
								if(isset($_SESSION['request']['priority']) && $_SESSION['request']['priority'] == "1")
									echo " selected ";
							?>
							>Yes</option>
							<option value = "2"
							<?php
								if(isset($_SESSION['request']['priority']) && $_SESSION['request']['priority'] == "0")
									echo " selected ";
							?>
							>No</option>
						</select>
					</td>
				</tr>
			</table><br>
			<button type = "submit" value = "submit" class = "btn btn-success" style = "width: 40%; margin-left: 55%;">
				Submit
			</button>
		</form>
	</div>
</body>
</html>