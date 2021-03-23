<!DOCTYPE html>
<html>
<head>
	<title>Handle Requests</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="../style/admin/admin-view-request.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
	<?php
		if(!isset($_SESSION))
			session_start();

		if(isset($_SESSION['message'])){
			echo "<script>";
			echo "alert(\"".$_SESSION['message']."\");";
			echo "</script>";
			unset($_SESSION['message']);
		}

		if(!isset($_SESSION['admin'])){
			$_SESSION['message'] = "You must login to view requests!";
			header("Location: admin-login.php");
				exit();
		}
		$mainServe = "localhost";
		$mainuser = "root";
		$mainpass = "";
		$dbname = "lyflyne";
		$conn = mysqli_connect($mainServe, $mainuser, $mainpass, $dbname);
		$arr = null;
		$heads = null;
		if(!isset($_SESSION['admin-request']))
		{
			$_SESSION['admin-request'] = array();
		}
		//$_SESSION['admin-request']['isloc'] = "no";
		//$_SESSION['admin-request']['state'] = "a";
		//$_SESSION['admin-request']['district'] = "a";
		if($conn == NULL || !$conn){
			die("Failed: ".mysqli_connect_error());
		}

		if($_SERVER['REQUEST_METHOD'] == "POST"){
			if(isset($conn) && $conn)
			{
				if(isset($_POST['alter'])){
					//print_r($_POST);
					switch($_POST['alter']){
						case 0:{
							$temp_rid = $_POST['rid'];
							$out = null;
							$dtype = $_POST['dtype'];
							if($_SESSION['admin-request']['type'] == "hospital" || $_SESSION['admin-request']['type'] == "hospital-priority only"){
								$lid = $_POST['lid'];
								$quantity = $_POST['quantity'];
								//echo $_POST['btype'];
								$btype = $_POST['btype'];
								$outcome = enqueue_request($conn, $temp_rid, "hospital_request");
								if($outcome){
									$cmd = "DELETE FROM hospital_request WHERE rid = '$temp_rid';";
									$out = mysqli_query($conn, $cmd);
								}
							}
							else{
								$uid = $_POST['uid'];
								$cmd = "SELECT* FROM user WHERE uid = '$uid'";
								$out = mysqli_query($conn, $cmd);
								if($out){
									//echo "Step one<br>";
									$arr = mysqli_fetch_array($out);
									$out = null;
									if(is_array($arr)){
										//echo "Step two<br>";
										$lid = $arr['lid'];
										$btype = $arr['btype'];
										if($_POST['dtype'] == "blood"){
											if($_POST['priority'] == 1)
												$quantity = 1000;
											else
												$quantity = 500;
										}
										else
											$quantity = 1;
										$outcome = enqueue_request($conn, $temp_rid, "request");
										if($outcome){
											//echo "step 5<br>";
											$cmd = "DELETE FROM request WHERE rid = '$temp_rid';";
											$out = mysqli_query($conn, $cmd);
										}
										else{
											$message = "Insufficient supply in the bank. Please transfer request!";
										}
									}
								}
							}

							//$cmd = "DELETE FROM request WHERE rid = '$temp_rid';";
							//$out = mysqli_query($conn, $cmd);
							//$out = null;
							if($out){
								//echo "SUCCESS";
								echo "<script>";
								echo "alert(\"Successful Acceptance of Request id ".$_POST['rid']."\");";
								echo "</script>";
							}
							else{
								echo "<script>";
								if(isset($message))
									echo "alert(\"".$message."\");";
								else
									echo "alert(\"Failed to accept Request of Request id ".$_POST['rid'].". Try again!!!!!!\");";
								echo "</script>";
							}
							break;
						}

						case 1:{
							//$cmd = "SELECT state FROM location WHERE lid = '$lid'";
							$temp_rid = $_POST['rid'];
							$out = null;
							$dtype = $_POST['dtype'];
							if($_SESSION['admin-request']['type'] == "hospital" || $_SESSION['admin-request']['type'] == "hospital-priority only"){
                                //echo "Here hosp<br>";
                                $lid = $_POST['lid'];
								$quantity = $_POST['quantity'];
								//echo $_POST['btype'];
								$btype = $_POST['btype'];
								$lids = getLocations($conn, $lid, true);
								$new_lid = null;
								if(is_array($lids)){
                                    //echo "Here lids<br>";
									$cmd = "SELECT lid FROM ".$_POST['dtype']." WHERE ";
									if($_POST['dtype'] == "blood" || $_POST['dtype'] == "marrow"){
										$cmd = $cmd."isbank = 1 AND ";
									}
									$cmd = $cmd."btype = '$btype' AND quantity >= '$quantity' AND ";
									$cmd = $cmd."lid IN (";
									for($i = 0; $i < count($lids) - 1; $i++){
										$cmd = $cmd."'".$lids[$i][0]."', ";
									}
									$cmd = $cmd."'".$lids[count($lids) - 1]."');";
									//echo $cmd;
									$out = mysqli_query($conn, $cmd);
									if($out){
                                        //echo "Here out<br>";
                                        $arr = mysqli_fetch_array($out);
										if(is_array($arr)){
											$new_lid = $arr['lid'];
										}
									}
								}

								if($new_lid){
                                    //echo "new one<br>";
									$cmd = "UPDATE hospital_request SET lookin = '$new_lid' WHERE rid = '$rid';";
									$out = mysqli_query($conn, $cmd);
								}
								else{
									$out = null;
								}
							}
							else{
								$uid = $_POST['uid'];
								$cmd = "SELECT* FROM user WHERE uid = '$uid'";
								$out = mysqli_query($conn, $cmd);
								if($out){
									//echo "Step one<br>";
									$arr = mysqli_fetch_array($out);
									$out = null;
									if(is_array($arr)){
										//echo "Step two<br>";
										$lid = $arr['lid'];
										$btype = $arr['btype'];
										if($_POST['dtype'] == "blood"){
											if($_POST['priority'] == 1)
												$quantity = 1000;
											else
												$quantity = 300;
										}
										else
											$quantity = 1;
										$lids = getLocations($conn, $lid, true);
										$new_lid = null;
										if(is_array($lids)){
                                            //echo "Step three<br>";
											$cmd = "SELECT lid FROM ".$_POST['dtype']." WHERE ";
											if($_POST['dtype'] == "blood" || $_POST['dtype'] == "marrow"){
												$cmd = $cmd."isbank = 1 AND ";
											}
											$cmd = $cmd."btype = '$btype' AND quantity >= '$quantity' AND ";
											$cmd = $cmd."lid IN (";
											for($i = 0; $i < count($lids) - 1; $i++){
												$cmd = $cmd."'".$lids[$i][0].", ";
											}
											$cmd = $cmd."'".$lids[count($lids) - 1]."');";
											//echo $cmd;
											$out = mysqli_query($conn, $cmd);
											if($out){
                                                //echo "Step 5<br>";
												$arr = mysqli_fetch_array($out);
												if(is_array($arr)){
													$new_lid = $arr['lid'];
												}
											}
										}

										if($new_lid){
                                            //echo "Finalized";
											$cmd = "UPDATE request SET lookin = '$new_lid' WHERE rid = '$rid';";
											$out = mysqli_query($conn, $cmd);
										}
										else{
											$out = null;
										}
									}
								}
							}

							//$cmd = "DELETE FROM request WHERE rid = '$temp_rid';";
							//$out = mysqli_query($conn, $cmd);
							//$out = null;
							if($out){
								//echo "SUCCESS";
								echo "<script>";
								echo "alert(\"Successful Transfer of Request id ".$_POST['rid']."\");";
								echo "</script>";
							}
							else{
								echo "<script>";
								if(isset($message))
									echo "alert(\"".$message."\");";
								else
									echo "alert(\"Failed to accept Request of Request id ".$_POST['rid'].". Try again!!!!!!\");";
								echo "</script>";
							}
							break;
						}

						case 2:{
							$_SESSION['moved_request'] = array();
							$pass = null;
							if($_SESSION['admin-request']['type'] == "hospital" || $_SESSION['admin-request']['type'] == "hospital-priority only"){
								$_SESSION['moved_request']['isHospital'] = true;
								$_SESSION['moved_request']['btype'] = $_POST['btype'];
								$pass = 1;
							}
							elseif(isset($_SESSION['admin-request']['type']) && $_SESSION['admin-request']['type'] != ""){
								$_SESSION['moved_request']['isHospital'] = false;
								$temp_uid = $_POST['uid'];
								$cmd = "SELECT btype FROM user WHERE uid = '$uid';";
								$out = mysqli_query($conn, $cmd);
								if($out){
									$arr = mysqli_fetch_array($out);
									if(is_array($arr)){
										$pass = 1;
										$_SESSION['moved_request']['btype'] = $arr['btype'];
									}
								}
							}

							$_SESSION['moved_request']['rid'] = $_POST['rid'];
							$_SESSION['moved_request']['dtype'] = $_POST['dtype'];

							if($pass){
								$_SESSION['message'] = "Find the donor here!";
								header("Location: ../search.php");
									exit();
							}
							else{
								unset($_SESSION['moved_request']);
							}
							break;
						}
					}
				}

				if(isset($_POST['type'])){
					//if($_POST['type'] != "")
					$_SESSION['admin-request']['type'] = $_POST['type'];
					switch($_POST['type']){
						case "priority":{
							$cmd = "SELECT * FROM request ORDER BY priority;";
							//print_r($cmd);
							$out = mysqli_query($conn, $cmd);
							$heads = null;
							$arr = null;
							if($out){
								$heads = mysqli_fetch_array($out);
								$out = mysqli_query($conn, $cmd);
								if($out)
									$arr = mysqli_fetch_all($out);
							}
							//$heads = mysqli_fetch_array($out);
							//print_r($heads);
							//$out = mysqli_query($conn, $cmd);
							//$arr = mysqli_fetch_all($out);
							//print_r($arr);
							//display($arr);
							break;
						}
						case "time_d":{
							$cmd = "SELECT * FROM request ORDER BY request_time;";
							//$cmd = "SELECT * FROM request ORDER BY request_time;";
							$out = mysqli_query($conn, $cmd);
							$heads = null;
							$arr = null;
							if($out){
								$heads = mysqli_fetch_array($out);
								$out = mysqli_query($conn, $cmd);
								if($out)
									$arr = mysqli_fetch_all($out);
							}
							//print_r($arr);
							//display($arr);
							break;
						}
						case "time_n":{
							$cmd = "SELECT * FROM request ORDER BY request_time DESC;";
							//$cmd = "SELECT * FROM request ORDER BY request_time DESC;";
							$out = mysqli_query($conn, $cmd);
							$heads = null;
							$arr = null;
							if($out){
								$heads = mysqli_fetch_array($out);
								$out = mysqli_query($conn, $cmd);
								if($out)
									$arr = mysqli_fetch_all($out);
							}
							//print_r($arr);
							//display($arr);
							break;
						}
						case "hospital":{
							$cmd = "SELECT * FROM hospital_request ORDER BY hid, priority;";
							//$cmd = "SELECT * FROM hospital_request ORDER BY hid, priority;";
							$out = mysqli_query($conn, $cmd);
							$heads = null;
							$arr = null;
							if($out){
								$heads = mysqli_fetch_array($out);
								$out = mysqli_query($conn, $cmd);
								if($out)
									$arr = mysqli_fetch_all($out);
							}
							break;
						}
						case "hospital-priority only":{
							$cmd = "SELECT * FROM hospital_request ORDER BY priority;";
							//$cmd = "SELECT * FROM hospital_request ORDER BY priority;";
							$out = mysqli_query($conn, $cmd);
							$heads = null;
							$arr = null;
							if($out){
								$heads = mysqli_fetch_array($out);
								$out = mysqli_query($conn, $cmd);
								if($out)
									$arr = mysqli_fetch_all($out);
							}
							break;
						}
						default:{
							$arr = null;
						}
					}
				}
			}
		}

		function display($heads, $arr){
			//print_r($heads);
			echo "<table class = \"table table-striped\">";
			echo "<tr>";
			$names = Array();
			foreach ($heads as $x => $y) {
				if(!is_numeric($x)){
					echo "<th>".$x."</th>";
					array_push($names, $x);
				}
			}
			echo "<th></th><th></th><th></th>";
			echo "</tr>";
			//print_r($names);
			$i = null;
			foreach($arr as $x){
				$i = 0;
				$inputs = Array();
				echo "<tr>";
				//echo "<form action = \"admin-view-request.php\" method = \"post\">";
				$temp = "<form action = \"admin-view-request.php\" method = \"post\">";
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
				$buttons = Array("Accept", "Reject and Transfer", "Find donor");
				$temp = "<input type = \"hidden\" name = \"type\" value = \"".$_POST['type']."\">";
				array_push($inputs, $temp);
				for($k = 0; $k < count($buttons); $k++){
					if($k != 2 || ($k == 2 && $x[3] == "blood") || ($k == 2 && $x[3] == "marrow")){
						for($i = 0; $i < count($inputs); $i++){
							echo $inputs[$i];
						}
						$classname = "";
						$isDisabled = "";
						switch($buttons[$k]){
							case $buttons[0]:{
								$classname = "btn btn-success";
								break;
							}
							case $buttons[1]:{
								$classname = "btn btn-danger";
								$isDisabled = " disabled";
								break;
							}
							case $buttons[2]:{
								$classname = "btn btn-primary";
								break;
							}
						}
						echo "<input type = \"hidden\" name = \"alter\" value = \"".$k."\">";
						echo "<td><button class = \"$classname\" type=\"submit\"".$isDisabled.">".$buttons[$k]."</button></td>";
						echo "</form>";
					}
					else{
						echo "<td></td>";
					}
				}
				echo "</tr>";
			}
			echo "</table>";
		}

		function getSupply($conn, $dtype, $btype, $lid){
			if($conn && $dtype && $btype && $lid){
				$cmd = "SELECT* FROM ".$dtype." WHERE ";
				if($dtype == "blood" || $dtype == "marrow")
					$cmd = $cmd."isbank = 1 AND ";
				$cmd = $cmd."btype = '$btype' AND lid = '$lid';";
				echo $cmd;
				$out = mysqli_query($conn, $cmd);
				if($out)
				{
					$arr = mysqli_fetch_array($out);
					$out = null;
					if(is_array($arr))
						return $arr;
					else
						return null;
				}
				else
					return null;
			}
			else
				return null;
		}

		function updateSupply($conn, $dtype, $btype, $lid, $quantity){
			if($conn && $dtype && $btype && $lid && is_numeric($quantity)){
				/*
				$cmd = "UPDATE ".$dtype." SET quantity = quantity + ".$quantity." WHERE ";
				if($dtype == "blood" || $dtype == "marrow")
					$cmd = $cmd."isbank = 1 AND ";
				$cmd = $cmd."btype = '$btype' AND lid = '$lid';";
				*/
				$id_name = substr($dtype, 0, 2)."id";
				$cmd = "SELECT $id_name FROM $dtype WHERE btype = '$btype' AND lid = '$lid'";
				if($dtype == "blood" || $dtype == "marrow")
					$cmd = $cmd." isbank = 1;";
				else
					$cmd = $cmd.";";
				$out = mysqli_query($conn, $cmd);
				if($out)
				{
					$out = null;
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getLocations($conn, $lid, $isState){
			if($conn && $lid){
				if($isState){
					$cmd = "SELECT state FROM location WHERE lid = '$lid';";
					$out = mysqli_query($conn, $cmd);
					if($out){
						$arr = mysqli_fetch_array($out);
						$state = $arr['state'];
						$cmd = "SELECT lid FROM location state = '$state' AND lid != '$lid';";
						$out = mysqli_query($conn, $cmd);
						if($out){
							$arr = mysqli_fetch_all($out);
							if(is_array($arr)){
								$lids = array();
								for($i = 0; $i < count($arr); $i++){
									array_push($lids, $arr[$i][0]);
								}
								if(count($lids) > 0)
									return $lids;
							}
						}
					}
				}
				else{
					$cmd = "SELECT lid FROM location WHERE lid != '$lid';";
					$out = mysqli_query($conn, $cmd);
					if($out){
						$arr = mysqli_fetch_all($out);
						if(is_array($arr)){
							$lids = array();
							for($i = 0; $i < count($arr); $i++){
								array_push($lids, $arr[$i][0]);
							}
							if(count($lids) > 0)
								return $lids;
						}
					}
				}
			}
			return null;
		}

		function enqueue_request($conn, $rid, $tablename){
			$cmd = "SELECT * FROM $tablename WHERE rid = '$rid';";
			$out = mysqli_query($conn, $cmd);

			if($out){
				$arr = mysqli_fetch_array($out);
				if(is_array($arr) && count($arr) > 0){
					$lid = $arr['lid'];
					$priority = $arr['priority'];
					$dtype = $arr['dtype'];
					$request_time = $arr['request_time'];
					$lookin = $arr['lookin'];
					$aid = $_SESSION['admin']['aid'];
					$final_cmd = "";

					switch($tablename){
						case "request":{
							$uid = $arr['uid'];
							$btype = "";

							$cmd = "SELECT btype FROM user WHERE uid = '$uid';";
							$out = mysqli_query($conn, $cmd);

							if($out){
								$arr = mysqli_fetch_array($out);
								if(is_array($arr) && count($arr) > 0){
									$btype = $arr['btype'];
								}
								else
									return;
							}
							else
								return;

							if($dtype == 'blood')
								$quantity = 500;
							else
								$quantity = 1;
							
							$final_cmd = "INSERT INTO admin_request_queue VALUES('$rid', '$aid', '$lid', '$priority', '$dtype', '$uid', '$request_time', '$lookin', ";
							break;
						}

						case "hospital_request":{
							$hid = $arr['hid'];
							$name = $arr['name'];
							$btype = $arr['btype'];
							$quantity = $arr['quantity'];

							$final_cmd = "INSERT INTO admin_hospital_request_queue VALUES('$rid', '$aid', '$hid', '$name', '$dtype', '$btype', '$quantity', '$lid', '$priority', '$request_time', '$lookin', ";
							break;
						}

						default:
							return;
					}

					$id_name = substr($dtype, 0, 2)."id";

					$cmd = "SELECT $id_name FROM $dtype WHERE (lid = '$lid' OR lid = '$lookin') AND btype = '$btype' AND quantity >= '$quantity';";
					$out = mysqli_query($conn, $cmd);
					//print_r($cmd);
					if($out){
						$arr = mysqli_fetch_array($out);
						//echo "<br>Here<br>";
						if(is_array($arr) && count($arr) > 0){
							$dtid = $arr[$id_name];

							$final_cmd .= "'$dtid');";
							//print_r($final_cmd);
							$out = mysqli_query($conn, $final_cmd);
							if($out){
								return true;
							}
							else{
								//print_r(mysqli_error($conn));
								return null;
							}
						}
						else
							return null;
					}
					else
						return null;
				}
				else
					return null;
			}
			else
				return null;
		}
	?>
	<?php include_once('../navbar.php'); ?>
	<div class = "container-fluid">
		<div class = "container" id = "view-request-form">
			<h2>View Requests</h2>
			<form action = "admin-view-request.php" method = "post">
				<table><tr><td>
				Request order by</td><td> 
				<select class = "form-control" name = "type" onchange='this.form.submit()'
				style = "width: 100%;">
					<option value = ""
					<?php
						if(!isset($_SESSION['admin-request']['type']) || $_SESSION['admin-request']['type'] == "")
							echo " selected";
					?>
					>Select an option</option>
					<option value = "priority"
					<?php
						if(isset($_SESSION['admin-request']['type']) && $_SESSION['admin-request']['type'] == "priority")
							echo " selected";
					?>
					>Priority</option>
					<option value = "time_d"
					<?php
						if(isset($_SESSION['admin-request']['type']) && $_SESSION['admin-request']['type'] == "time_d")
							echo " selected";
					?>
					>Oldest to newest</option>
					<option value = "time_n"
					<?php
						if(isset($_SESSION['admin-request']['type']) && $_SESSION['admin-request']['type'] == "time_n")
							echo " selected";
					?>
					>Newest to Oldest</option>
					<option value = "hospital"
					<?php
						if(isset($_SESSION['admin-request']['type']) && $_SESSION['admin-request']['type'] == "hospital")
							echo " selected";
					?>
					>Hospital</option>
					<option value = "hospital-priority only"
					<?php
						if(isset($_SESSION['admin-request']['type']) && $_SESSION['admin-request']['type'] == "hospital-priority only")
							echo " selected";
					?>
					>Hospital - Priority only</option>
				</select>
				</td></tr></table>
			</form>
		</div>
		<?php
			if(isset($arr) && $arr != null)
				display($heads, $arr);
		?>
	</div>
</body>
</html>