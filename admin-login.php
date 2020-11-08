<!DOCTYPE html>
<html>
<head>
	<title>Admin Login</title>
    <?php
        if(!isset($_SESSION))
            session_start();

        if(isset($_SESSION['message'])){
            echo "<script>";
            echo "alert(\"".$_SESSION['message']."\");";
            echo "</script>";
            unset($_SESSION['message']);
        }

        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $mainServe = "localhost";
            $mainuser = "root";
            $mainpass = "";
            $dbname = "lyflyne";
            $user1 = $_POST['userid'];
            $userid = encode($user1);
            //$pass1 = $_POST['password'];
            $hasStarted = mysqli_connect($mainServe, $mainuser, $mainpass, $dbname);

            if(!$hasStarted){
                die("Failed: ".mysqli_connect_error());
            }
            $cmd = "SELECT * FROM admin WHERE userid = '$userid';";
            $outcome = mysqli_query($hasStarted, $cmd);
            $vals = mysqli_fetch_array($outcome);
            if(is_array($vals) && isset($_POST['userid'])){
                //$_SESSION['username'] = $vals['username'];
                //$_SESSION['password'] = $vals['password'];
                $_SESSION['admin'] = $vals;
                echo "<script>";
                echo "alert(\"Success\");";
                echo "</script>";
                //header("Location: emp-requests.php");
                //    exit();
            }
            else{
                if(isset($_POST['userid'])){
                    $error = "Incorrect admin id!";
                }
                else{
                    $error = "Enter the admin id first!";
                }
            }
        }

        function encode($word){
            if(strlen($word) > 15)
                return null;
            $enc_word = "";
            for($i = 0; $i < strlen($word); $i++){
                if(is_numeric($word[$i])){
                $val = ord($word[$i]) - ord('0');
                $x = chr(ord('A') - $val);
                $y = chr(ord('a') + 2 * $val);
                $z = chr(40 + $val / 2);
                }
                elseif(ctype_alpha($word[$i])){
                    $val = ord($word[$i]) - ord('A');
                    $x = chr(40 + $val);
                    $y = chr(ord('a') - $val / 2);
                    $z = chr(ord('0') + $val / 3);
                }
                else{
                    return null;
                }

                $enc_word = $enc_word.$x.$y.$z;
            }
            return $enc_word;
        }

        //print_r(encode("TanShets"));
    ?>
</head>
<body>
	<form action = "admin-login.php" method = "post">
		<h1>Login as Admin</h1>
		<table>
			<tr>
				<td>Admin ID</td>
				<td><input type = "text" name = "userid" placeholder="Enter your admin id"></td>
			</tr>
		</table>
		<button type = "submit">Login</button>
	</form><br>
</body>
</html>