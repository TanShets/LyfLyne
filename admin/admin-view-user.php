<!DOCTYPE html>
<html>
<head>
	<title>View Users</title>
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
            $_SESSION['message'] = "You must login before you access the admin view user page!";
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
		if(!isset($_SESSION['view-user']))
		{
			$_SESSION['view-user'] = array();
        }
        
        if($conn == NULL || !$conn){
			die("Failed: ".mysqli_connect_error());
        }

		if($_SERVER['REQUEST_METHOD'] == "POST"){
			if(isset($conn) && $conn){
				//print_r($_POST);
				if(isset($_POST['alter'])){
					switch($_POST['alter']){
						case 0:{
							remove_from_donor_database($conn, $_POST['uid']);
							$cmd = "DELETE FROM user WHERE uid = '".$_POST['uid']."';";
							$out = mysqli_query($conn, $cmd);
							if($out){
								echo "<script>";
								echo "alert(\"User id: ".$_POST['uid']." successfully deleted!\");";
								echo "</script>";
							}
							break;
						}
						
						case 1:{
							$cmd = "SELECT * FROM user WHERE uid = '".$_POST['uid']."';";
							//echo $cmd;
							$out = mysqli_query($conn, $cmd);
							if($out){
								$temp_arr = mysqli_fetch_array($out);
								//print_r($temp_arr);
								if($temp_arr['odonor'] == 1){
									$cmd = "SELECT tablename FROM control WHERE tablename != 'blood' AND ";
									$cmd = $cmd."tablename != 'marrow';";
									//echo $cmd;
									//echo "<br><br>";
									$out = mysqli_query($conn, $cmd);
									if($out){
										$temp_arr2 = mysqli_fetch_all($out);
										for($i = 0; $i < count($temp_arr2); $i++){
											$id_name = substr($temp_arr2[$i][0], 0, 2)."id";
											$cmd = "SELECT $id_name FROM ";
											$cmd = $cmd.$temp_arr2[$i][0]." WHERE lid = '".$temp_arr['lid']."' AND ";
											$cmd = $cmd."btype = '".$temp_arr['btype']."';";
											//echo $cmd;
											//echo "<br>";
											$out = mysqli_query($conn, $cmd);
											if($out){
												$temp_arr3 = mysqli_fetch_array($out);
												//echo "Till here";
												if(is_array($temp_arr3)){
													$temp_id = $temp_arr3[$id_name];
													//		$cmd = "UPDATE ".$temp_arr2[$i][0]." SET quantity = quantity + 1 WHERE ";
													//		$cmd = $cmd.$id_name." = '$temp_id';";
													//echo $cmd;
													$cmd = "INSERT INTO dead_donor_queue(dtid, dtype, lid, btype) VALUES('$temp_id', ";
												}
												else{
													//		$cmd = "INSERT INTO ".$temp_arr2[$i][0]."(btype, quantity, lid) VALUES(";
													//		$cmd = $cmd."'".$temp_arr['btype']."', 1, ";
													//		$cmd = $cmd."'".$temp_arr['lid']."');";
													//echo $cmd;
													$cmd = "INSERT INTO dead_donor_queue(dtype, lid, btype) VALUES(";
												}

												$cmd = $cmd."'".$temp_arr2[$i][0]."', '".$temp_arr['lid']."', '".$temp_arr['btype']."');";

												$out = mysqli_query($conn, $cmd);
												if($out){
													$cmd = "DELETE FROM user WHERE uid = '".$_POST['uid']."';";
													$out = mysqli_query($conn, $cmd);
													if($out){
														echo "<script>";
														echo "alert(\"User id: ".$_POST['uid']." successfully deleted\");";
														echo "</script>";
													}
													else{
														echo "<script>";
														echo "alert(\"Deletion error\");";
														echo "</script>";
													}
												}
												else{
													echo "<script>";
													echo "alert(\"Failure in changes\");";
													echo "</script>";
												}
											}
										}
									}
								}
							}
							break;
						}
					}
				}
			}
		}

		$cmd = "SELECT uid, username, name, mobile, landline, lid, email FROM user;";
		$out = mysqli_query($conn, $cmd);
		$heads = null;
		$arr = null;
		if($out){
			$heads = mysqli_fetch_array($out);
			if(is_array($heads)){
				$out = mysqli_query($conn, $cmd);
				if($out){
					$arr = mysqli_fetch_all($out);
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
			echo "<td></td><td></td>";
			echo "</tr>";
			//print_r($names);
			$i = null;
			foreach($arr as $x){
				$i = 0;
				$inputs = Array();
				echo "<tr>";
				//echo "<form action = \"emp-requests.php\" method = \"post\">";
				$temp = "<form action = \"admin-view-user.php\" method = \"post\">";
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
				$buttons = Array("Delete", "Deceased");
				//$temp = "<input type = \"hidden\" name = \"type\" value = \"".$_POST['type']."\">";
				array_push($inputs, $temp);
				for($k = 0; $k < count($buttons); $k++){
					if($k != 2 || ($k == 2 && $x[3] == "blood")){
						for($i = 0; $i < count($inputs); $i++){
							echo $inputs[$i];
						}
						echo "<input type = \"hidden\" name = \"alter\" value = \"".$k."\">";
						echo "<td><button class = \"btn btn-danger\" type=\"submit\">".$buttons[$k]."</button></td>";
						echo "</form>";
					}
				}
				echo "</tr>";
			}
			echo "</table>";
		}

		function remove_from_donor_database($conn, $uid){
			$cmd1 = "DELETE FROM blood WHERE uid = '$uid'";
			$cmd2 = "DELETE FROM marrow WHERE uid = '$uid'";

			$out1 = mysqli_query($conn, $cmd1);
			$out2 = mysqli_query($conn, $cmd2);
		}
    ?>
</head>
<body>
	<?php include_once('../navbar.php'); ?><br>
	<div class = "container-fluid">
	<h3>Current Users</h3>
	<?php
		if($heads != null && $arr != null)
			display($heads, $arr);
	?>
	</div>
</body>
</html>