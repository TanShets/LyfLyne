<!DOCTYPE html>
<html>

<head>
    <title>Login to LyfLyne</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        body {
            color: white;
        }
    </style>
    <?php
		if(!isset($_SESSION))
			session_start();
		
		if(isset($_SESSION['message'])){
			echo "<script>";
			echo "alert(\"".$_SESSION['message']."\");";
			echo "</script>";
			unset($_SESSION['message']);
		}

		if(isset($_SESSION['user'])){
			$_SESSION['message'] = "You must log out from your account before trying to login to another account.";
			header("Location: user/create-request.php");
				exit();
		}

		if(isset($_SESSION['hospital_user'])){
			$_SESSION['message'] = "You must log out from your account before trying to login to another account.";
			header("Location: hospital/hospital-request.php");
				exit();
		}

		if($_SERVER['REQUEST_METHOD'] == "POST"){
			$mainServe = "localhost";
			$mainuser = "root";
			$mainpass = "";
			$dbname = "lyflyne";
			$user1 = $_POST['name'];
			$pass1 = $_POST['password'];
			$hasStarted = mysqli_connect($mainServe, $mainuser, $mainpass, $dbname);

			if(!$hasStarted){
				die("Failed: ".mysqli_connect_error());
			}
			if(!isset($_POST['isHospital'])){
				$cmd = "SELECT * FROM user WHERE username = '$user1' AND password = '$pass1'";
			}
			else
				$cmd = "SELECT * FROM hospital_user WHERE username = '$user1' AND password = '$pass1'";

			$outcome = mysqli_query($hasStarted, $cmd);

			$vals = mysqli_fetch_array($outcome);
			if(is_array($vals) && isset($_POST['name']) && isset($_POST['password'])){
				//$_SESSION['username'] = $vals['username'];
				//$_SESSION['password'] = $vals['password'];
				if(!isset($_POST['isHospital'])){
					$_SESSION['user'] = $vals;
					header("Location: user/create-request.php");
						exit();
				}
				else{
					$_SESSION['hospital_user'] = $vals;
					header("Location: hospital/hospital-request.php");
						exit();
				}
			}
			else{
				if(isset($_POST['name']) && isset($_POST['password'])){
					$error = "Incorrect Username or Password.";
				}
				elseif(isset($_POST['name'])){
					$error = "Enter the password first";
				}
				elseif (isset($_POST['password'])) {
					$error = "Enter the username";
				}
			}
		}
	?>
</head>

<body style="background-image: url(./bg.jpg); background-repeat: no-repeat; background-size: cover;" class="blur">
    <div class="container-fluid" style="margin-left: 35%; margin-top: 12%; height: 200%;">
        <div style="width:29.8%; height: 200%; border: grey 2px solid; padding: 10px;">
            <center>
                <h2>Login to your account</h2>
                <form action="login.php" method="post">
                    <table>
                        <tr>
                            <td>Username</td>
                            <td><input class="form-control" type="text" name="name" placeholder="Enter your username">
                            </td>
                        </tr>
                        <tr>
                            <td>Password</td>
                            <td><input class="form-control" type="password" name="password"
                                    placeholder="Enter password"></td>
                        </tr>
                    </table>
                    <div style="padding-bottom: 10px;"></div>
                    <label for="isHospital">Hospital Account</label>
                    <input id="isHospital" class="form-check-label" type="checkbox" name="isHospital" value="1" />
                    <center style="padding-top: 10px;">
                        <button type="submit" class="btn btn-primary" style="width: 80%;">Login</button>
                    </center>
                </form><br>
                <table>
                    <tr>
                        <td>
                            <form action="user/create.php">
                                <button type="submit" class="btn btn-success">Create User Account</button>
                            </form>
                        </td>
                        <td>
                            <form action="hospital/hospital-create.php">
                                <button type="submit" class="btn btn-success">Create Hospital Account</button>
                            </form>
                        </td>
                    </tr>
                </table>
            </center>
        </div>
    </div>
</body>

</html>
