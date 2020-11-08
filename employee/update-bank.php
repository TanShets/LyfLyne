<!DOCTYPE html>
<html>
<head>
	<title>Update Bank</title>
	<?php
		if(!isset($_SESSION))
			session_start();

		if(!isset($_SESSION['emp-user'])){
			$_SESSION['message'] = "You need to login before you can update the organ/blood bank!";
			header("Location: emp-login.php");
				exit();
		}
		$mainServe = "localhost";
		$mainuser = "root";
		$mainpass = "";
		$dbname = "lyflyne";
		$conn = mysqli_connect($mainServe, $mainuser, $mainpass, $dbname);
		if(!isset($_SESSION['update-bank']))
		{
			$_SESSION['update-bank'] = array();
		}
		//$_SESSION['update-bank']['isloc'] = "no";
		//$_SESSION['update-bank']['state'] = "a";
		//$_SESSION['update-bank']['district'] = "a";
		if($conn == NULL || !$conn){
			die("Failed: ".mysqli_connect_error());
		}

		if($_SERVER['REQUEST_METHOD'] == "POST"){
			$count = 0;
			if(isset($_POST['type'])){
				$_SESSION['update-bank']['type'] = $_POST['type'];
				if($_SESSION['update-bank']['type'] != "")
					$count++;
			}

			if(isset($_POST['quantity'])){
				$_SESSION['update-bank']['quantity'] = $_POST['quantity'];
				if($_SESSION['update-bank']['quantity'] != "")
					$count++;
			}

			if(isset($_POST['b-group'])){
				$_SESSION['update-bank']['btype'] = $_POST['b-group'];
				if($_SESSION['update-bank']['btype'] != "")
					$count++;
			}

			if(isset($conn) && $conn){
				$lid = null;
				if($count == 3)
				{
					$lid = $_SESSION['emp-user']['lid'];
				}

				if($lid != null)
				{
					$cmd = "SELECT ".substr($_SESSION['update-bank']['type'], 0, 2)."id FROM ".$_SESSION['update-bank']['type']." WHERE ";
					if($_SESSION['update-bank']['type'] == "blood" || $_SESSION['update-bank']['type'] == "marrow")
						$cmd = $cmd."isbank = 1 AND ";
					$cmd = $cmd."lid = '$lid' AND btype = '".$_SESSION['update-bank']['btype']."';";
					//echo $cmd;
					$out = mysqli_query($conn, $cmd);
					if($out){
						//echo "Success";
						$arr = mysqli_fetch_array($out);
						if(is_array($arr) && isset($arr[substr($_SESSION['update-bank']['type'], 0, 2)."id"])){
							$temp_id = $arr[substr($_SESSION['update-bank']['type'], 0, 2)."id"];
							$cmd = "UPDATE ".$_SESSION['update-bank']['type']." SET quantity = quantity + ".$_SESSION['update-bank']['quantity']." WHERE ".substr($_SESSION['update-bank']['type'], 0, 2)."id = "."'$temp_id';";
							//echo $cmd;
							$out = mysqli_query($conn, $cmd);
							echo "<script>";
							echo "alert(\"";
							$out = 1;
							if($out){
								echo "Successfully registered an additional ";
								echo $_SESSION['update-bank']['quantity'];
								if($_SESSION['update-bank']['type'] == "blood")
									echo "ml ";
								else{
									echo " ".$_SESSION['update-bank']['type'];
									if($_SESSION['update-bank']['quantity'] > 1)
										echo "s ";
									else
										echo " ";
								}
								echo "of blood type ".$_SESSION['update-bank']['btype']." into the ".$_SESSION['update-bank']['type']." bank!";
								unset($_SESSION['update-bank']['type']);
								unset($_SESSION['update-bank']['quantity']);
								unset($_SESSION['update-bank']['btype']);
							}
							else{
								echo "Failed to update data. Try again";
							}
							echo "\");";
							echo "</script>";
						}
						else{
							$cmd = "INSERT INTO ".$_SESSION['update-bank']['type']."(btype, ";
							if($_SESSION['update-bank']['type'] == "blood" || $_SESSION['update-bank']['type'] == "marrow"){
								$cmd = $cmd."isbank, ";
							}
							$cmd = $cmd."quantity, lid) VALUES('".$_SESSION['update-bank']['btype']."', ";
							if($_SESSION['update-bank']['type'] == "blood" || $_SESSION['update-bank']['type'] == "marrow"){
								$cmd = $cmd."1, ";
							}
							$cmd = $cmd."'".$_SESSION['update-bank']['quantity']."', '$lid');";

							$out = mysqli_query($conn, $cmd);
							if($out){
								echo "<script>";
								echo "alert(\"Successfully registered ";
								echo $_SESSION['update-bank']['quantity'];
								if($_SESSION['update-bank']['type'] == "blood")
									echo "ml ";
								else{
									echo " ".$_SESSION['update-bank']['type'];
									if($_SESSION['update-bank']['quantity'] > 1)
										echo "s ";
									else
										echo " ";
								}
								echo "of blood type ".$_SESSION['update-bank']['btype']." into the ".$_SESSION['update-bank']['type']." bank!\");";
								echo "</script>";
								unset($_SESSION['update-bank']['type']);
								unset($_SESSION['update-bank']['quantity']);
								unset($_SESSION['update-bank']['btype']);
							}
							else
								echo "FAILURE";
							//echo $cmd;
						}
					}
					else
						echo "Failed";
				}
			}
		}
		//print_r($_SESSION);
	?>
</head>
<body>
	<form action = "update-bank.php" method = "post">
		<table>
			<tr>
				<td>Organ/Fluid concerned</td>
				<td>
					<select name = "type" onchange = 'this.form.submit()'>
						<?php
							echo "<option value = \"\"";
							if(!isset($_SESSION['update-bank']['type']) || $_SESSION['update-bank']['type'] == "")
								echo " selected ";
							echo ">Select an option</option>";
							if(isset($conn) && $conn)
							{
								$cmd = "SELECT tablename from control;";
								$out = mysqli_query($conn, $cmd);
								$arr = mysqli_fetch_all($out);
								for($i = 0; $i < count($arr); $i++)
								{
									$temp = $arr[$i][0];
									echo "<option value = \"$temp\"";
									if(isset($_SESSION['update-bank']['type']) && $_SESSION['update-bank']['type'] == $temp)
										echo " selected ";
									echo ">$temp</option>";
								}
								//print_r($arr);
							}
						?>
					</select>
					<noscript><input type = "submit" value = "submit"></noscript>
				</td>
			</tr>
			<tr>
				<td>Blood Group</td>
				<td>
					<select name = "b-group">
					<?php
						echo "<option value = \"\"";
						if(!isset($_SESSION['update-bank']['btype']) || $_SESSION['update-bank']['btype'] == "")
							echo " selected";
						echo ">Select an option</option>";
						$btypes = Array('A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-', 'Rh null');
						for($i = 0; $i < count($btypes); $i++)
						{
							$tempx = $btypes[$i];
							echo "<option value = \"$tempx\"";
							if(isset($_SESSION['update-bank']['btype']) && $_SESSION['update-bank']['btype'] == $tempx)
								echo " selected";
							echo ">".$tempx."</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Quantity</td>
				<td>
					<input type = "number" name = "quantity"
					placeholder="ml for blood numbers for others"
					style = "width: 115%;"
					<?php
						if(isset($_SESSION['update-bank']['quantity']))
							echo " value = \"".$_SESSION['update-bank']['quantity']."\"";
					?>
					>
				</td>
			</tr>
		</table>
		<button type = "submit" value = "submit">Submit</button>
	</form>
</body>
</html>