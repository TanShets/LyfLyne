<!DOCTYPE html>
<head>
	<title>Handle Requests</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

        if(!isset($_SESSION['emp-confirm_request']))
		{
			$_SESSION['emp-confirm_request'] = array();
		}

		if(isset($_POST['choice']) && $_POST['choice'] != ""){
			$_SESSION['emp-confirm_request']['choice'] = $_POST['choice'];
		}
		//$_SESSION['request']['isloc'] = "no";
		//$_SESSION['request']['state'] = "a";
		//$_SESSION['request']['district'] = "a";
		if($conn == NULL || !$conn){
			die("Failed: ".mysqli_connect_error());
		}

		if($_SERVER['REQUEST_METHOD'] == "POST"){
            //$ddid = $_POST['ddid'];
			//confirm_request($conn, $ddid);
			if(isset($_POST['rid']) && isset($_POST['form_type'])){
				$rid = $_POST['rid'];

				switch($_SESSION['emp-confirm_request']){
					case 'request':{
						if($_POST['form_type'] == 'Confirm'){
							confirm_request($conn, $rid, "admin_request_queue");
						}
						else{
							reject_request($conn, $rid, "admin_request_queue");
						}
						break;
					}
					case 'hospital_request':{
						if($_POST['form_type'] == 'Confirm'){
							confirm_request($conn, $rid, "admin_hospital_request_queue");
						}
						else{
							reject_request($conn, $rid, "admin_hospital_request_queue");
						}
						break;
					}
				}
			}
		}
		
		function generate_table($conn, $tablename){
            echo "<table>";

			$cmd = "SELECT * FROM $tablename;";
			//echo $cmd;
            $out = mysqli_query($conn, $cmd);

            if($out){
				//echo "Monke";
				$arr_temp = mysqli_fetch_array($out);
				//print_r($arr_temp);
                if(is_array($arr_temp)){
                    $arr_heads = array_keys($arr_temp);
                    //print_r($arr_heads);
                    echo "<tr>";
                    foreach($arr_heads as $i){
                        if(!is_numeric($i)){
                            echo "<th>$i</th>";
                        }
                    }
                    echo "<th>Confirm</th>";
                    echo "</tr>";

                    $out = mysqli_query($conn, $cmd);
                    if($out){
                        $arr = mysqli_fetch_all($out);

                        if(is_array($arr) && count($arr) > 0){
                            $i = 0;
                            $j = 0;
                            for($i = 0; $i < count($arr); $i++){
                                echo "<tr>";
                                
                                for($j = 0; $j < count($arr[$i]); $j++){
                                    echo "<td>";
                                    echo $arr[$i][$j];
                                    echo "</td>";
                                }

                                echo "<td>";
                                echo "<form action = \"emp-confirm_request.php\" method = \"post\">";
                                echo "<input type = \"hidden\" name = \"".$arr_heads[1]."\" 
                                value = \"".$arr[$i][0]."\"/>";
								echo "<input type = \"submit\" name = \"form_type\" class = \"btn btn-success\" value = \"Confirm\" />";
								echo "</td>";

								echo "<td>";
								echo "<form action = \"emp-confirm_request.php\" method = \"post\">";
								echo "<input type = \"hidden\" name = \"".$arr_heads[1]."\" value = \"".$arr[$i][1]."\"/>";
								echo "<input type = \"submit\" name = \"form_type\" class = \"btn btn-danger\" value = \"Reject\" />";
                                echo "</form>";
                                echo "</td>";

                                echo "</tr>";
                            }
                        }
                    }
                }
			}

            echo "</table>";
		}
		
		function confirm_request($conn, $rid, $tablename){
            $cmd = "SELECT * FROM $tablename WHERE rid = '$rid';";
            $out = mysqli_query($conn, $cmd);
            if($out){
                $arr = mysqli_fetch_array($out);
                if(is_array($arr)){
					$aid = $arr['aid'];
					$lid = $arr['lid'];
					$priority = $arr['priority'];
					$request_time = $arr['request_time'];
					$lookin = $arr['lookin'];
					$dtid = $arr['dtid'];

					switch($tablename){
						case "admin_request_queue":{
							$uid = $arr['uid'];
							$quantity = 1;
							$cmd = "SELECT btype FROM user WHERE uid = '$uid';";
							$out = mysqli_query($conn, $cmd);
							$arr_temp = mysqli_fetch_array($out);
							$btype = $arr_temp['btype'];
							break;
						}

						case "admin_hospital_request_queue":{
							$name = $arr['name'];
							$btype = $arr['btype'];
							$quantity = $arr['quantity'];
							$hid = $arr['hid'];
							break;
						}

						default:
							return;
					}

                    $id_name = substr($dtype, 0, 2)."id";
                    
                    if($dtid != -1){
                        $cmd = "UPDATE $dtype SET quantity = quantity - $quantity WHERE $id_name = '$dtid';";
                    }
                    else{
                        $cmd = "INSERT INTO $dtype(btype, quantity, lid) VALUES('$btype', $quantity, '$lid');";
                    }

                    $out = mysqli_query($conn, $cmd);
                    if($out){
                        $cmd = "DELETE FROM $tablename WHERE rid = '$rid';";
                        $out = mysqli_query($conn, $cmd);
                        if($out){
                            $_SESSION['message'] = "Confirmed";
                        }
                        else
                            $_SESSION['message'] = "Removal from queue error";
                    }
                }
            }
		}
		
		function reject_request($conn, $rid, $tablename){
			$req_table = array(
				'admin_request_queue' => 'request',
				'admin_hospital_request_queue' => 'hospital_request'
			);

			$cmd = "SELECT * FROM $tablename WHERE rid = '$rid';";
			if($conn){
				$out = mysqli_query($conn, $cmd);
				if($out){
					$arr = mysqli_fetch_array($out);
					if(is_array($arr) && count($arr) > 0){
						switch($tablename){
							case 'admin_request_queue':{
								$table_name = 'request';
								$lid = $arr['lid'];
								$priority = $arr['priority'];
								$dtype = $arr['dtype'];
								$uid = $arr['uid'];
								$request_time = $arr['request_time'];
								$lookin = $arr['lookin'];

								$cmd = "INSERT INTO request VALUES('$rid', '$lid', '$priority', '$dtype',
								'$uid', '$request_time', '$lookin');";
								break;
							}

							case 'admin_hospital_request_queue':{
								$table_name = 'hospital_request';
								$hid = $arr['hid'];
								$name = $arr['name'];
								$dtype = $arr['dtype'];
								$btype = $arr['btype'];
								$quantity = $arr['quantity'];
								$lid = $arr['lid'];
								$priority = $arr['priority'];
								$request_time = $arr['request_time'];
								$lookin = $arr['lookin'];

								$cmd = "INSERT INTO hospital_request VALUES('$rid', '$hid', '$name', 
								'$dtype', '$btype', '$quantity', '$lid', '$priority', '$request_time',
								'$lookin');";
								break;
							}

							default:
								return;
						}

						$out = mysqli_query($conn, $cmd);
						if($out){
							$_SESSION['message'] = "Successful updation";
						}
						else{
							$_SESSION['message'] = "Deletion error";
						}
					}
				}
			}
		}
    ?>
	<form action = "emp-confirm_request.php" method = "post">
		<select class = "form-control" name = "choice" onchange = 'this.form.submit()'>
			<option value = ""
			<?php
				if(!isset($_SESSION['emp-confirm_request']) || !isset($_SESSION['emp-confirm_request']['choice']) || $_SESSION['emp-confirm_request']['choice'] == ""){
					echo " selected";
				}
			?>>Select an option</option>
			<option value = "request"
			<?php
				if(isset($_SESSION['emp-confirm_request']) && isset($_SESSION['emp-confirm_request']['choice']) && $_SESSION['emp-confirm_request']['choice'] == "request"){
					echo " selected";
				}
			?>>Individual Requests</option>

			<option value = "hospital_request"
			<?php
				if(isset($_SESSION['emp-confirm_request']) && isset($_SESSION['emp-confirm_request']['choice']) && $_SESSION['emp-confirm_request']['choice'] == "hospital_request"){
					echo " selected";
				}
			?>>Hospital Requests</option>
		</select>
	</form>
	<?php
		if(isset($_SESSION['emp-confirm_request']['choice']) && $_SESSION['emp-confirm_request']['choice'] != ""){
			$table_namex = array(
				"request" => 'admin_request_queue',
				"hospital_request" => 'admin_hospital_request_queue'
			);
			generate_table($conn, $table_namex[$_SESSION['emp-confirm_request']['choice']]);
		}
	?>

</body>
</html>