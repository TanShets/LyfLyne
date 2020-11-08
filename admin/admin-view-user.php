<!DOCTYPE html>
<html>
<head>
	<title>Create Individual Request</title>
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
        
        $cmd = "SELECT * FROM user;";
        $out = mysqli_query($conn, $cmd);
        
        $out = mysqli_query($conn, $cmd);

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
    ?>
</head>
<body>
	<form action = "admin-home.php" method = "post">
        <button type = "submit" value = "submit">Go to View Users</button>
    </form>
    <form action = "admin-home.php" method = "post">
        <button type = "submit" value = "submit">Go to View Employees</button>
    </form>
    <form action = "emp-create.php" method = "post">
        <button type = "submit" value = "submit">Add Employee</button>
    </form>
    <form action = "admin-home.php" method = "post">
        <button type = "submit" value = "submit">View all Requests</button>
    </form>
    <form action = "location-entry.php" method = "post">
        <button type = "submit" value = "submit">Enter New Location</button>
    </form>
    <form action = "../logout.php" method = "post">
        <button type = "submit" value = "submit">Logout</button>
    </form>
</body>
</html>