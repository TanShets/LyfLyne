<!DOCTYPE html>
<html>
<head>
	<title>Search for Donor</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<style type = "text/css">
		.pop-window{
			position: fixed;
			left: 42%;
			top: 42%;
			border: solid 1px black;
			background-color: white;
			padding: 20px;
		}
	</style>
	<?php
		if(!isset($_SESSION))
			session_start();

		$mainServe = "localhost";
		$mainuser = "root";
		$mainpass = "";
		$dbname = "lyflyne";
		$conn = mysqli_connect($mainServe, $mainuser, $mainpass, $dbname);
		if(!isset($_SESSION['search']))
		{
			$_SESSION['search'] = array();
		}

		if(isset($_SESSION['message'])){
			echo "<script>";
			echo "alert(\"".$_SESSION['message']."\");";
			echo "</script>";
			unset($_SESSION['message']);
		}

		if($conn == NULL || !$conn){
			die("Failed: ".mysqli_connect_error());
		}

		if(isset($_SESSION['moved_request'])){
			print_r($_SESSION['moved_request']);
		}

		$count = 0;
		if($_SERVER['REQUEST_METHOD'] == "POST"){
			if(isset($_POST['rid'])){
				if($_POST['rid'] == 0){
					echo "<script>";
					echo "alert(\"Proceed to find another donor then\");";
					echo "</script>";
					unset($_SESSION['search']);
					$_SESSION['search'] = array();
				}
				else{
					$rid = $_POST['rid'];
					$cmd = "DELETE FROM ";
					if($_POST['isHospital']){
						$cmd = $cmd."hospital_request ";
					}
					else
						$cmd = $cmd."request ";
					$cmd = $cmd."WHERE rid = '$rid';";
					$out = mysqli_query($conn, $cmd);
					if($out){
						$_SESSION['message'] = "Request no. $rid completed successfully!!!!";
						unset($_SESSION['final_solve']);
						unset($_SESSION['search']);
						header("Location: employee/emp-requests.php");
						exit();
					}
					else{
						echo "<script>";
						echo "alert(\"Failed. TRY AGAIN!!!\");";
						echo "</script>";
					}
				}
			}

			if(isset($_POST['alter']) && $_POST['alter'] == 0){
				$cmd = "SELECT mobile, landline FROM user WHERE uid = '".$_POST['uid']."';";
				$out = mysqli_query($conn, $cmd);
				if($out){
					$arr = mysqli_fetch_array($out);
					if(is_array($arr)){
						echo "<script>";
						echo "alert(\"Mobile: ".$arr['mobile'].", Landline: ".$arr['landline']."\");";
						echo "</script>";

						$_SESSION['final_solve'] = 1;
					}
				}
			}

			if(isset($_POST['type']))
			{
				$_SESSION['search']['type'] = $_POST['type'];
				if($_SESSION['search']['type'] != "")
					$count++;
			}

			if(isset($_POST['b-group']))
			{
				$_SESSION['search']['btype'] = $_POST['b-group'];
				if($_SESSION['search']['btype'] != "")
					$count++;
			}

			if(isset($_POST['state']))
			{
				$_SESSION['search']['state'] = $_POST['state'];
				if($_SESSION['search']['state'] != "")
					$count++;
			}

			if(isset($_POST['state']) && isset($_POST['district']))
			{
				$_SESSION['search']['district'] = $_POST['district'];
				if($_SESSION['search']['district'] != "")
					$count++;
			}
			else
				$_SESSION['search']['district'] = "";

			if(isset($_POST['state']) && isset($_POST['district']) && isset($_POST['city']))
			{
				$_SESSION['search']['city'] = $_POST['city'];
				if($_SESSION['search']['city'] != "")
					$count++;
			}
			else
				$_SESSION['search']['city'] = "";
			//echo $count;
			if($count == 5)
			{
				$cmd = "SELECT lid FROM location WHERE state = '".$_SESSION['search']['state']."' AND ";
				$cmd = $cmd."district = '".$_SESSION['search']['district']."' AND city = '".$_SESSION['search']['city']."';";
				$out = mysqli_query($conn, $cmd);
				if($out){
					$arr = mysqli_fetch_array($out);
					if(is_array($arr)){
						//echo "Till here";
						$lid = $arr['lid'];
						//show($conn, $_SESSION['search']['type'], $_SESSION['search']['b-group'], $lid);
					}
				}
			}
		}

		function show($conn, $dtype, $btype, $lid)
		{
			if(isset($conn) && $conn && $dtype && $btype && $lid)
			{
				$cmd = "SELECT* FROM ".$dtype." WHERE isbank = 0 AND btype = '$btype' AND lid = '$lid';";
				$out = mysqli_query($conn, $cmd);
				if($out){
					$heads = mysqli_fetch_array($out);
					if(is_array($heads)){
						$out = mysqli_query($conn, $cmd);
						if($out){
							$arr = mysqli_fetch_all($out);
							if(is_array($arr)){
								echo "<div class = \"container\">";
								echo "<table class = \"table table-striped\">";
								echo "<tr>";
								$names = Array();
								foreach ($heads as $x => $y) {
									if(!is_numeric($x)){
										echo "<th scope = \"col\">".$x."</th>";
										array_push($names, $x);
									}
								}
								echo "<th scope = \"col\"></th>";
								echo "</tr>";
								//print_r($names);
								$i = null;
								foreach($arr as $x){
									$i = 0;
									$inputs = Array();
									echo "<tr>";
									//echo "<form action = \"emp-requests.php\" method = \"post\">";
									$temp = "<form action = \"search.php\" method = \"post\">";
									array_push($inputs, $temp);
									foreach ($x as $y) {
										echo "<td>".$y."</td>";
										$temp = "<input type = \"hidden\" name = \"".$names[$i]."\" value = \"".$y."\">";
										array_push($inputs, $temp);
										//echo "<input type = \"hidden\" name = \"";
										//echo $names[$i]."\" value = \"".$y."\">";
										$i++;
									}
									//print_r($inputs);
									$buttons = Array("Contact");
									$temp = "<input type = \"hidden\" name = \"type\" value = \"".$_POST['type']."\">";
									array_push($inputs, $temp);
									for($k = 0; $k < count($buttons); $k++){
										if($k != 2 || ($k == 2 && $x[3] == "blood")){
											for($i = 0; $i < count($inputs); $i++){
												echo $inputs[$i];
											}
											echo "<input type = \"hidden\" name = \"alter\" value = \"".$k."\">";
											echo "<td><button type=\"submit\" class = \"btn btn-primary\">".$buttons[$k]."</button></td>";
											echo "</form>";
										}
									}
									echo "</tr>";
								}
								echo "</table>";
								echo "</div>";
							}
						}
					}
				}
			}
		}
	?>
</head>
<body>
	<div class = "container" style = "margin-left: 40%;">
		<h1>Search Donor</h1>
		<form action = "search.php" method = "post">
			<table>
				<tr>
					<td>Donor type</td>
					<td>
						<select class = "form-control" name = "type" onchange = 'this.form.submit()'>
							<?php
								echo "<option value = \"\"";
								if(!isset($_SESSION['search']['type']) || $_SESSION['search']['type'] == "")
									echo " selected";
								echo ">Select a type</option>";
								if(isset($conn) && $conn)
								{
									$arr = array('blood', 'marrow');
									for($i = 0; $i < count($arr); $i++)
									{
										$temp = $arr[$i];
										echo "<option value = \"$temp\"";
										if(isset($_SESSION['search']['type']) && $_SESSION['search']['type'] == $temp)
											echo " selected";
										echo ">".$temp."</option>";
									}
								}
							?>
							<!--<option value = "Blood">Blood</option>
							<option value = "Marrow">Bone Marrow</option>-->
						</select>
						<noscript><input type="submit" value="Submit"></noscript>
					</td>
				</tr>
				<tr>
					<td>Blood Group</td>
					<td>
						<select class = "form-control" name = "b-group">
						<?php
							echo "<option value = \"\"";
							if(!isset($_SESSION['search']['btype']) || $_SESSION['search']['btype'] == "")
								echo " selected";
							echo ">Select an option</option>";
							$btypes = Array('A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-', 'Rh null');
							for($i = 0; $i < count($btypes); $i++)
							{
								$tempx = $btypes[$i];
								echo "<option value = \"$tempx\"";
								if(isset($_SESSION['search']['btype']) && $_SESSION['search']['btype'] == $tempx)
									echo " selected";
								echo ">".$tempx."</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td>State</td>
					<td>
						<?php
							echo "<select class = \"form-control\" name = \"state\" onchange = \"this.form.submit()\">";
							echo "<option value = \"\"";
							if(!isset($_SESSION['search']['state']) || $_SESSION['search']['state'] == "")
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
									if(isset($_SESSION['search']['state']) && $_SESSION['search']['state'] == $temp)
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
						<select class = "form-control" name = "district" onchange = 'this.form.submit()'
						<?php
							if(!isset($_SESSION['search']['state']) || $_SESSION['search']['state'] == "")
								echo " disabled";
						?>
						>
						<?php
							if(isset($_SESSION['search']['state']) && $_SESSION['search']['state'] != "")
							{
								echo "<option value = \"\"";
								if(!isset($_SESSION['search']['district']) || $_SESSION['search']['district'] == "")
									echo " selected";
								echo ">Select an option</option>";
								$temp = $_SESSION['search']['state'];
								$cmd = "SELECT district from location WHERE state = '$temp' GROUP BY(district);";
								$out = mysqli_query($conn, $cmd);
								$arr = mysqli_fetch_all($out);
								for($i = 0; $i < count($arr); $i++)
								{
									$temp = $arr[$i][0];
									echo "<option value = \"$temp\"";
									if(isset($_SESSION['search']['district']) && $_SESSION['search']['district'] == $temp)
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
						<select class = "form-control" name = "city"
						<?php
							if(!isset($_SESSION['search']['state']) || $_SESSION['search']['state'] == "" || !isset($_SESSION['search']['district']) || $_SESSION['search']['district'] == "")
								echo " disabled";
						?>
						>
						<?php
							if(isset($_SESSION['search']['state']) && $_SESSION['search']['state'] != "" && isset($_SESSION['search']['district']) && $_SESSION['search']['district'] != "")
							{
								echo "<option value = \"\"";
								if(!isset($_SESSION['search']['city']) || $_SESSION['search']['city'] == "")
									echo " selected";
								echo ">Select an option</option>";
								if(isset($conn) && $conn)
								{
									$temp = $_SESSION['search']['state'];
									$temp1 = $_SESSION['search']['district'];
									$cmd = "SELECT city FROM location WHERE state = '$temp' AND district = '$temp1';";
									$out = mysqli_query($conn, $cmd);
									$arr = mysqli_fetch_all($out);
									for($i = 0; $i < count($arr); $i++)
									{
										$temp = $arr[$i][0];
										echo "<option value = \"$temp\"";
										if(!isset($_SESSION['search']['city']) || $_SESSION['search']['city'] == $temp)
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
			</table><br>
			<button type = "submit" class = "btn btn-primary" value = "Submit"
			style = "width: 23%;"
			>Search</button>
		</form><br><br>
	</div>
	<?php
		if(isset($conn) && isset($_SESSION['search']) && isset($_SESSION['search']['type']) && isset($_SESSION['search']['btype']) && isset($lid)){
			show($conn, $_SESSION['search']['type'], $_SESSION['search']['btype'], $lid);
		}
	?>
	<?php
		//print_r($_SESSION['moved_request']);
		//echo "<br><br>";
		//print_r($_SESSION['emp-user']);
		//echo "<br><br>";
		//print_r($_SESSION['search']);
		if(isset($_SESSION['search']['type']) && isset($_SESSION['search']['btype']) && isset($_SESSION['emp-user']) && isset($_SESSION['final_solve']) && isset($_SESSION['moved_request'])){
			//echo "Step1<br>";
			if($_SESSION['moved_request']['dtype'] == $_SESSION['search']['type'] && $_SESSION['moved_request']['btype'] == $_SESSION['search']['btype']){
				//echo "Step2<br>";
				if(isset($_SESSION['moved_request']['isHospital']) && $_SESSION['moved_request']['rid']){
					//echo "Step3<br>";
					echo "<div class = \"pop-window\">";
					echo "<table><tr>";
					echo "Has the request been finalized?</tr><tr><td>";
					echo "<form action = \"search.php\" method = \"post\">";
					echo "<input type = \"hidden\" name = \"rid\" value = \"".$_SESSION['moved_request']['rid']."\">";
					echo "<input type = \"hidden\" name = \"isHospital\" value = \"".$_SESSION['moved_request']['isHospital']."\">";
					echo "<button type = \"submit\" value = \"submit\" class = \"btn btn-success\">Yes</button>";
					echo "</form></td><td>";
					echo "<form action = \"search.php\" method = \"post\">";
					echo "<input type = \"hidden\" name = \"rid\" value = \"0\">";
					echo "<button type = \"submit\" value = \"submit\" class = \"btn btn-danger\">No</button>";
					echo "</form></td></tr></table>";
					echo "</div>";
				}
			}
		}
	?>
</body>
</html>