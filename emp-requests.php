<!DOCTYPE html>
<html>
<head>
	<title>Handle Requests</title>
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

		if(!isset($_SESSION['emp-user'])){
			$_SESSION['message'] = "You must login to view requests!";
			header("Location: emp-login.php");
				exit();
		}
		$mainServe = "localhost";
		$mainuser = "root";
		$mainpass = "";
		$dbname = "lyflyne";
		$conn = mysqli_connect($mainServe, $mainuser, $mainpass, $dbname);
		$arr = null;
		$heads = null;
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
			if(isset($conn) && $conn)
			{
				if(isset($_POST['alter'])){
					//print_r($_POST);
					switch($_POST['alter']){
						case 0:{
							$temp_rid = $_POST['rid'];
							$out = null;
							$dtype = $_POST['dtype'];
							if($_SESSION['request']['type'] == "hospital" || $_SESSION['request']['type'] == "hospital-priority only"){
								$lid = $_POST['lid'];
								$quantity = $_POST['quantity'];
								//echo $_POST['btype'];
								$btype = $_POST['btype'];
								$arr = getSupply($conn, $dtype, $btype, $lid);
								if(is_array($arr)){
									$max_quantity = $arr['quantity'];
									if($quantity <= $max_quantity){
										//$cmd = "UPDATE ".$dtype." SET quantity = quantity - ".$quantity." WHERE btype = '$btype' AND lid = '$lid';";
										$outcome = updateSupply($conn, $dtype, $btype, $lid, -$quantity);
										if($outcome){
											$cmd = "DELETE FROM hospital_request WHERE rid = '$temp_rid';";
											$out = mysqli_query($conn, $cmd);
										}
									}
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
										$arr = getSupply($conn, $dtype, $btype, $lid);
										//print_r($arr);
										if(is_array($arr)){
											//echo "step 3<br>";
											$max_quantity = $arr['quantity'];
											if($quantity <= $max_quantity){
												//echo "step 4<br>";
												//$cmd = "UPDATE ".$dtype." SET quantity = quantity - ".$quantity." WHERE btype = '$btype' AND lid = '$lid';";
												$outcome = updateSupply($conn, $dtype, $btype, $lid, -$quantity);
												if($outcome){
													//echo "step 5<br>";
													$cmd = "DELETE FROM request WHERE rid = '$temp_rid';";
													$out = mysqli_query($conn, $cmd);
												}
											}
											else{
												$message = "Insufficient supply in the bank. Please transfer request!";
											}
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
					}
				}

				if(isset($_POST['type'])){
					//if($_POST['type'] != "")
					$_SESSION['request']['type'] = $_POST['type'];
					switch($_POST['type']){
						case "priority":{
							$cmd = "SELECT * FROM request ORDER BY priority;";
							$out = mysqli_query($conn, $cmd);
							$heads = mysqli_fetch_array($out);
							//print_r($heads);
							$out = mysqli_query($conn, $cmd);
							$arr = mysqli_fetch_all($out);
							//print_r($arr);
							//display($arr);
							break;
						}
						case "time_d":{
							$cmd = "SELECT * FROM request ORDER BY request_time;";
							$out = mysqli_query($conn, $cmd);
							$heads = mysqli_fetch_array($out);
							$out = mysqli_query($conn, $cmd);
							$arr = mysqli_fetch_all($out);
							//print_r($arr);
							//display($arr);
							break;
						}
						case "time_n":{
							$cmd = "SELECT * FROM request ORDER BY request_time DESC;";
							$out = mysqli_query($conn, $cmd);
							$heads = mysqli_fetch_array($out);
							$out = mysqli_query($conn, $cmd);
							$arr = mysqli_fetch_all($out);
							//print_r($arr);
							//display($arr);
							break;
						}
						case "hospital":{
							$cmd = "SELECT * FROM hospital_request ORDER BY hid, priority;";
							$out = mysqli_query($conn, $cmd);
							$heads = mysqli_fetch_array($out);
							$out = mysqli_query($conn, $cmd);
							$arr = mysqli_fetch_all($out);
							break;
						}
						case "hospital-priority only":{
							$cmd = "SELECT * FROM hospital_request ORDER BY priority;";
							$out = mysqli_query($conn, $cmd);
							$heads = mysqli_fetch_array($out);
							$out = mysqli_query($conn, $cmd);
							$arr = mysqli_fetch_all($out);
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
			echo "<table>";
			echo "<tr>";
			$names = Array();
			foreach ($heads as $x => $y) {
				if(!is_numeric($x)){
					echo "<th>".$x."</th>";
					array_push($names, $x);
				}
			}
			echo "</tr>";
			//print_r($names);
			$i = null;
			foreach($arr as $x){
				$i = 0;
				$inputs = Array();
				echo "<tr>";
				//echo "<form action = \"emp-requests.php\" method = \"post\">";
				$temp = "<form action = \"emp-requests.php\" method = \"post\">";
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
					if($k != 2 || ($k == 2 && $x[3] == "blood")){
						for($i = 0; $i < count($inputs); $i++){
							echo $inputs[$i];
						}
						echo "<input type = \"hidden\" name = \"alter\" value = \"".$k."\">";
						echo "<td><button type=\"submit\">".$buttons[$k]."</button></td>";
						echo "</form>";
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
				$cmd = "UPDATE ".$dtype." SET quantity = quantity + ".$quantity." WHERE ";
				if($dtype == "blood" || $dtype == "marrow")
					$cmd = $cmd."isbank = 1 AND ";
				$cmd = $cmd."btype = '$btype' AND lid = '$lid';";
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
	?>
	<form action = "emp-requests.php" method = "post">
		Request order by 
		<select name = "type" onchange='this.form.submit()'>
			<option value = ""
			<?php
				if(!isset($_SESSION['request']['type']) || $_SESSION['request']['type'] == "")
					echo " selected";
			?>
			>Select an option</option>
			<option value = "priority"
			<?php
				if(isset($_SESSION['request']['type']) && $_SESSION['request']['type'] == "priority")
					echo " selected";
			?>
			>Priority</option>
			<option value = "time_d"
			<?php
				if(isset($_SESSION['request']['type']) && $_SESSION['request']['type'] == "time_d")
					echo " selected";
			?>
			>Oldest to newest</option>
			<option value = "time_n"
			<?php
				if(isset($_SESSION['request']['type']) && $_SESSION['request']['type'] == "time_n")
					echo " selected";
			?>
			>Newest to Oldest</option>
			<option value = "hospital"
			<?php
				if(isset($_SESSION['request']['type']) && $_SESSION['request']['type'] == "hospital")
					echo " selected";
			?>
			>Hospital</option>
			<option value = "hospital-priority only"
			<?php
				if(isset($_SESSION['request']['type']) && $_SESSION['request']['type'] == "hospital-priority only")
					echo " selected";
			?>
			>Hospital - Priority only</option>
		</select>
	</form>
	<?php
		if(isset($arr) && $arr != null)
			display($heads, $arr);
	?>
</body>
</html>