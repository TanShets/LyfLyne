<!DOCTYPE html>
<html>
<head>
	<title>Create Hospital Request</title>
	<?php
		if(!isset($_SESSION))
			session_start();

		if(!isset($_SESSION['hospital_user'])){
			$_SESSION['message'] = "You must login to create a hospital request!";
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
		if(!isset($_SESSION['h_request']))
		{
			$_SESSION['h_request'] = array();
		}
		//print_r($_SESSION['h_request']);
		//print_r($_POST);
		if(!isset($_SESSION['request_count']))
			$_SESSION['request_count'] = 1;

		//$_SESSION['h_request']['isloc'] = "no";
		//$_SESSION['h_request']['state'] = "a";
		//$_SESSION['h_request']['district'] = "a";
		if($conn == NULL || !$conn){
			die("Failed: ".mysqli_connect_error());
		}

		if($_SERVER['REQUEST_METHOD'] == "POST"){
			if(isset($_POST['addMore']) && $_POST['addMore'] == "add"){
				$_SESSION['request_count']++;
			}
			elseif(isset($_POST['addMore']) && $_POST['addMore'] == "sub"){
				if($_SESSION['request_count'] > 1)
					$_SESSION['request_count']--;
			}
			$count = 0;
			for($j = 1; $j <= $_SESSION['request_count']; $j++){
				//$count = 0;
				if(isset($_POST['type'.$j])){
					$_SESSION['h_request']['type'.$j] = $_POST['type'.$j];
					if($_SESSION['h_request']['type'.$j] != "")
						$count++;
				}

				if(isset($_POST['quantity'.$j])){
					$_SESSION['h_request']['quantity'.$j] = $_POST['quantity'.$j];
					if($_SESSION['h_request']['quantity'.$j] != "")
						$count++;
				}

				if(isset($_POST['name'.$j])){
					$_SESSION['h_request']['name'.$j] = $_POST['name'.$j];
					if($_SESSION['h_request']['name'.$j] != "")
						$count++;
				}

				if(isset($_POST['btype'.$j])){
					$_SESSION['h_request']['btype'.$j] = $_POST['btype'.$j];
					if($_SESSION['h_request']['btype'.$j] != "")
						$count++;
				}

				if(isset($_POST['priority'.$j]) && $_POST['priority'.$j] != ""){
					$_SESSION['h_request']['priority'.$j] = $_POST['priority'.$j];
					if($_SESSION['h_request']['priority'.$j] != "")
						$count++;
				}
			}
			if($count == 5 * $_SESSION['request_count']){
				$success = 0;
				for($j = 1; $j <= $_SESSION['request_count']; $j++){
					if(isset($conn) && $conn){
						$lid = null;
						if(isset($_SESSION['hospital_user']))
						{
							$hid = $_SESSION['hospital_user']['hid'];
							$cmd = "SELECT lid FROM hospital_user WHERE hid = '$hid';";
							$out = mysqli_query($conn, $cmd);
							$lid = mysqli_fetch_array($out)['lid'];
						}

						if($lid != null)
						{
							if(isset($_SESSION['hospital_user']))
							{
								$hid = $_SESSION['hospital_user']['hid'];
								$priority = $_SESSION['h_request']['priority'.$j];
								$dtype = $_SESSION['h_request']['type'.$j];
								$btype = $_SESSION['h_request']['btype'.$j];
								$quantity = $_SESSION['h_request']['quantity'.$j];
								$name = $_SESSION['h_request']['name'.$j];
								//$cmd = "INSERT INTO hospital_request(lid, priority, dtype, btype, quantity, hid, name, request_time) VALUES('$lid', '$priority', '$dtype', '$btype', '$quantity', '$hid', '$name', NOW());";
								$cmd = "INSERT INTO hospital_request(hid, name, dtype, btype, quantity, lid, priority, request_time, lookin) VALUES('$hid', '$name', '$dtype', '$btype', '$quantity', '$lid', '$priority', NOW(), '$lid');";
								//print_r($cmd);
								$out = mysqli_query($conn, $cmd);
								//$out = null;
								if($out)
								{
									$success++;
								}
								else
								{
									//print_r($cmd);
									echo "QUERY FAILED";
									echo mysqli_error($conn);
								}
							}
							else
								echo "Can't find hospital account";
						}
						else
							echo "Location failure";
					}
					else
						echo "Connection failure";
				}

				if($success == $_SESSION['request_count']){
					$_SESSION['message'] = "Successfully created request/s";
					unset($_SESSION['h_request']);
					$_SESSION['request_count'] = 1;
					header("Location: hospital-view-request.php");
						exit();
				}
			}
		}
		//print_r($_SESSION);
	?>
</head>
<body>
	<form action = "hospital-request.php" method = "post">
		<?php
			for($j = 1; $j <= $_SESSION['request_count']; $j++){
				echo "<table>";
					echo "<tr>";
						echo "<td>Donation type</td>";
						echo "<td>";
							echo "<select name = \"type".$j."\" onchange = 'this.form.submit()'>";
								
								echo "<option value = \"\"";
								if(!isset($_SESSION['h_request']['type'.$j]) || $_SESSION['h_request']['type'.$j] == "")
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
										if(isset($_SESSION['h_request']['type'.$j]) && $_SESSION['h_request']['type'.$j] == $temp)
											echo " selected ";
										echo ">$temp</option>";
									}
								}
							echo "</select>";
							echo "<noscript><input type = \"submit\" value = \"submit\"></noscript>";
						echo "</td>";
					echo "</tr>";
					if(isset($_SESSION['h_request']['type'.$j]) && $_SESSION['h_request']['type'.$j] == "blood"){
						echo "<tr>";
							echo "<td>Quantity</td>";
							echo "<td>";
								echo "<input type = \"number\" name = \"quantity".$j."\" placeholder = \"in ml\"";
								if(isset($_SESSION['h_request']['quantity'.$j])){
									echo " value = \"".$_SESSION['h_request']['quantity'.$j]."\"";
								}
								echo ">";
							echo "</td>";
						echo "</tr>";
					}
					else{
						echo "<tr>";
						echo "<input type = \"hidden\" name = \"quantity".$j."\" value = \"1\"";
						echo "</tr>";
					}
					echo "<tr>";
						echo "<td>Patient Name</td>";
						echo "<td><input type = \"text\" name = \"name".$j."\"";
						if(isset($_SESSION['h_request']['name'.$j]) && $_SESSION['h_request']['name'.$j] != ""){
							echo " value = \"".$_SESSION['h_request']['name'.$j]."\"";
						}
						echo "></td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>Blood Group</td>";
						echo "<td>";
							echo "<select name = \"btype".$j."\">";
								echo "<option value = \"\"";
								if(!isset($_SESSION['h_request']['btype'.$j]) || $_SESSION['h_request']['btype'.$j] == "")
									echo " selected";
								echo ">Select an option</option>";
								$btypes = Array('A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-', 'Rh null');
								for($i = 0; $i < count($btypes); $i++)
								{
									$tempx = $btypes[$i];
									echo "<option value = \"$tempx\"";
									if(isset($_SESSION['h_request']['btype'.$j]) && $_SESSION['h_request']['btype'.$j] == $tempx)
										echo " selected";
									echo ">".$tempx."</option>";
								}
							echo "</select>";
						echo "</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>Priority</td>";
						echo "<td>";
							echo "<select name = \"priority".$j."\">";
								echo "<option value = \"\"";
								if(!isset($_SESSION['h_request']['priority'.$j]) || $_SESSION['h_request']['priority'.$j] == "")
									echo " selected ";
							echo ">Select any one</option>";
							for($i = 1; $i <= 10; $i++){
								echo "<option value = \"".$i."\"";
								if(isset($_SESSION['h_request']['priority'.$j]) && $_SESSION['h_request']['priority'.$j] == $i)
									echo " selected ";
								echo ">".$i."</option>";
							}
							echo "</select>";
						echo "</td>";
					echo "</tr>";
				echo "</table><br><br>";
			}
		?>
		<button type = "submit" value = "submit">Submit</button>
	</form><br>
	<table><tr><td>
	<form action = "hospital-request.php" method = "post">
		<input type = "hidden" name = "addMore" value = "add">
		<button type = "submit" value = "submit">Add</button>
	</form></td><td>
	<form action = "hospital-request.php" method = "post">
		<input type = "hidden" name = "addMore" value = "sub">
		<button type = "submit" value = "submit">Remove</button>
	</form></td>
	</tr>
	</table>
</body>
</html>