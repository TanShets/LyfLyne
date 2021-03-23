<!DOCTYPE html>
<html>
<head>
	<title>View Requests</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php
        if(!isset($_SESSION))
            session_start();
        
        if(isset($_SESSION['message'])){
            echo "<script>";
            echo "alert(\"".$_SESSION['message']."\");";
            echo "</script>";
            unset($_SESSION['message']);
        }

        if(!isset($_SESSION['hospital_user'])){
            $_SESSION['message'] = "You must login before you access the view request page!";
			header("Location: ../login.php");
				exit();
        }

        $mainServe = "localhost";
		$mainuser = "root";
		$mainpass = "";
		$dbname = "lyflyne";
		$conn = mysqli_connect($mainServe, $mainuser, $mainpass, $dbname);
		$arr = null;
		$heads = null;
		if(!isset($_SESSION['view-hospital-request']))
		{
			$_SESSION['view-hospital-request'] = array();
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
							$cmd = "DELETE FROM hospital_request WHERE rid = '".$_POST['rid']."';";
							$out = mysqli_query($conn, $cmd);
							if($out){
								echo "<script>";
								echo "alert(\"Request id: ".$_POST['rid']." successfully deleted!\");";
								echo "</script>";
							}
							break;
						}
					}
				}
			}
		}

		//$cmd = "SELECT uid, username, name, mobile, landline, lid, email FROM user;";
        $cmd = "SELECT rid, priority, name, dtype, btype, quantity, request_time FROM hospital_request WHERE hid = '".$_SESSION['hospital_user']['hid']."';";
        //echo $cmd;
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
			echo "<th></th>";
			echo "</tr>";
			//print_r($names);
			$i = null;
			foreach($arr as $x){
				$i = 0;
				$inputs = Array();
				echo "<tr>";
				//echo "<form action = \"emp-requests.php\" method = \"post\">";
				$temp = "<form action = \"hospital-view-request.php\" method = \"post\">";
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
				$buttons = Array("Cancel");
				//$temp = "<input type = \"hidden\" name = \"type\" value = \"".$_POST['type']."\">";
				array_push($inputs, $temp);
				for($k = 0; $k < count($buttons); $k++){
					if($k != 2 || ($k == 2 && $x[3] == "blood")){
						for($i = 0; $i < count($inputs); $i++){
							echo $inputs[$i];
						}
						echo "<input type = \"hidden\" name = \"alter\" value = \"".$k."\">";
						echo "<td><button type=\"submit\" class = \"btn btn-danger\">".$buttons[$k]."</button></td>";
						echo "</form>";
					}
				}
				echo "</tr>";
			}
			echo "</table>";
		}
    ?>
</head>
<body>
	<?php include_once('../navbar.php'); ?>
	<div class = "container-fluid">
	<?php
		if($heads != null && $arr != null)
			display($heads, $arr);
	?>
	</div>
</body>
</html>