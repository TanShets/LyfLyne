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

        if(!isset($_SESSION['emp-confirm_donor']))
		{
			$_SESSION['emp-confirm_donor'] = array();
		}
		//$_SESSION['request']['isloc'] = "no";
		//$_SESSION['request']['state'] = "a";
		//$_SESSION['request']['district'] = "a";
		if($conn == NULL || !$conn){
			die("Failed: ".mysqli_connect_error());
		}

		if($_SERVER['REQUEST_METHOD'] == "POST"){
            $ddid = $_POST['ddid'];
            confirm_request($conn, $ddid);
        }

        function generate_table($conn){
            $cmd = "SELECT * FROM dead_donor_queue;";
            if($conn){
                $out = mysqli_query($conn, $cmd);

                if($out){
                    $arr_temp = mysqli_fetch_array($out);
                    if(is_array($arr_temp)){
                        echo "<div class = \"container-fluid\">";
                        echo "<table class = \"table table-striped\">";
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
                                    echo "<form action = \"emp-confirm_donor.php\" method = \"post\">";
                                    echo "<input type = \"hidden\" name = \"".$arr_heads[1]."\" 
                                    value = \"".$arr[$i][0]."\"/>";
                                    echo "<button type = \"submit\" class = \"btn btn-success\">Confirm</button>";
                                    echo "</form>";
                                    echo "</td>";

                                    echo "</tr>";
                                }
                            }
                        }
                        echo "</table>";
                        echo "</div>";
                    }
                    else{
                        echo "<center><h2>No Dead Donor Requests require confirmation at present</h2></center>";
                    }
                }
                else{
                    echo "<center><h1>No Dead Donor Requests require confirmation at present</h1></center>";
                }
            }
            else{
                echo "<h2>Connection Error</h2>: Please try refreshing the page and try again!";
            }
        }

        function confirm_request($conn, $ddid){
            $cmd = "SELECT * FROM dead_donor_queue WHERE ddid = '$ddid';";
            $out = mysqli_query($conn, $cmd);
            if($out){
                $arr = mysqli_fetch_array($out);
                if(is_array($arr)){
                    $dtype = $arr['dtype'];
                    $dtid = $arr['dtid'];
                    $lid = $arr['lid'];
                    $btype = $arr['btype'];

                    $id_name = substr($dtype, 0, 2)."id";
                    
                    if($dtid != -1){
                        $cmd = "UPDATE $dtype SET quantity = quantity + 1 WHERE $id_name = '$dtid';";
                    }
                    else{
                        $cmd = "INSERT INTO $dtype(btype, quantity, lid) VALUES('$btype', 1, '$lid');";
                    }

                    $out = mysqli_query($conn, $cmd);
                    if($out){
                        $cmd = "DELETE FROM dead_donor_queue WHERE ddid = '$ddid';";
                        $out = mysqli_query($conn, $cmd);
                        if($out){
                            $cmd = "INSERT INTO dead_donor_log VALUES('$ddid', '$dtype', '$dtid', '$lid', '$btype', NOW());";
                            $out = mysqli_query($conn, $cmd);
                            if($out){
                                $_SESSION['message'] = "Confirmed";
                            }
                            else
                                $_SESSION['message'] = "Confirmed but with log error";
                        }
                        else
                            $_SESSION['message'] = "Removal from queue error";
                    }
                }
            }
        }
    ?>
    <?php include_once('../navbar.php'); ?><br>

    <?php generate_table($conn); ?>
</body>
</html>