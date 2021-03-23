<?php require('../security.php'); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Admin Login</title>
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

        if(isset($_SESSION['admin'])){
            $_SESSION['message'] = "You're already logged in!";
			header("Location: admin-home.php");
				exit();
        }
        elseif(isset($_SESSION['user'])){
			$_SESSION['message'] = "You must log out from your user account before trying to login to another account.";
			header("Location: ../user/create-request.php");
				exit();
		}
        elseif(isset($_SESSION['hospital_user'])){
			$_SESSION['message'] = "You must log out from your hospital account before trying to login to another account.";
			header("Location: ../hospital/hospital-request.php");
				exit();
		}
		elseif(isset($_SESSION['emp-user'])){
			$_SESSION['message'] = "You must logout of your employee account before trying to login again.";
			head("Location: ../employee/emp-requests.php");
			exit();
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
                header("Location: admin-home.php");
                    exit();
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

        
    ?>
</head>
<body>
	<div class = "container" style = "margin-left: 37%; margin-top: 15%; border: grey 1px solid; width: 25%; padding: 20px;">
        <form action = "admin-login.php" method = "post">
            <h1>Login as Admin</h1>
            <table>
                <tr>
                    <td>Admin ID</td>
                    <td><input class = "form-control" type = "text" name = "userid" placeholder="Enter your admin id"></td>
                </tr>
            </table><br>
            <center><button type = "submit" class = "btn btn-success" style = "width: 80%;">Login</button></center>
        </form>
    </div>
</body>
</html>