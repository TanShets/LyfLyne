<!DOCTYPE html>
<html>
<head>
	<title>Search for Donor</title>
	<?php
		if(!isset($_SESSION))
			session_start();

		$mainServe = "localhost";
		$mainuser = "root";
		$mainpass = "";
		$dbname = "lyflyne";
		$conn = mysqli_connect($mainServe, $mainuser, $mainpass, $dbname);
		if(!isset($_SESSION['search']))
		{
			$_SESSION['search'] = array();
		}

		if($conn == NULL || !$conn){
			die("Failed: ".mysqli_connect_error());
		}
		$count = 0;
		if($_SERVER['REQUEST_METHOD'] == "POST"){
			if(isset($_POST['type']))
			{
				$_SESSION['search']['type'] = $_POST['type'];
				if($_SESSION['search']['type'] != "")
					$count++;
			}

			if(isset($_POST['b-group']))
			{
				$_SESSION['search']['b-group'] = $_POST['b-group'];
				if($_SESSION['search']['b-group'] != "")
					$count++;
			}

			if(isset($_POST['state']))
			{
				$_SESSION['search']['state'] = $_POST['state'];
				if($_SESSION['search']['state'] != "")
					$count++;
			}

			if(isset($_POST['state']) && isset($_POST['district']))
			{
				$_SESSION['search']['district'] = $_POST['district'];
				if($_SESSION['search']['district'] != "")
					$count++;
			}
			else
				$_SESSION['search']['district'] = "";

			if(isset($_POST['state']) && isset($_POST['district']) && isset($_POST['city']))
			{
				$_SESSION['search']['city'] = $_POST['city'];
				if($_SESSION['search']['city'] == "")
					$count++;
			}
			else
				$_SESSION['search']['city'] = "";

			if($count == 5)
				show();
		}

		function show()
		{
			if(isset($conn) && $conn)
			{
				$cmd = "";
			}
		}
	?>
</head>
<body>
	<h1>Search Donor</h1>
	<form action = "search.php" method = "post">
		<table>
			<tr>
				<td>Donor type</td>
				<td>
					<select name = "type" onchange = 'this.form.submit()'>
						<?php
							echo "<option value = \"\"";
							if(!isset($_SESSION['search']['type']) || $_SESSION['search']['type'] == "")
								echo " selected";
							echo ">Select a type</option>";
							if(isset($conn) && $conn)
							{
								$cmd = "SELECT tablename FROM control;";
								$out = mysqli_query($conn, $cmd);
								$arr = mysqli_fetch_all($out);
								for($i = 0; $i < count($arr); $i++)
								{
									$temp = $arr[$i][0];
									echo "<option value = \"$temp\"";
									if(isset($_SESSION['search']['type']) && $_SESSION['search']['type'] == $temp)
										echo " selected";
									echo ">".$temp."</option>";
								}
							}
						?>
						<!--<option value = "Blood">Blood</option>
						<option value = "Marrow">Bone Marrow</option>-->
					</select>
					<noscript><input type="submit" value="Submit"></noscript>
				</td>
			</tr>
			<tr>
				<td>Blood Group</td>
				<td>
					<select name = "b-group">
						<option value = "">Select an option</option>
						<option value = "A+">A+</option>
						<option value = "B+">B+</option>
						<option value = "AB+">AB+</option>
						<option value = "O+">O+</option>
						<option value = "A-">A-</option>
						<option value = "B-">B-</option>
						<option value = "AB-">AB-</option>
						<option value = "O-">O-</option>
						<option value = "null">Rh null</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>State</td>
				<td>
					<?php
						echo "<select name = \"state\" onchange = \"this.form.submit()\">";
						echo "<option value = \"\"";
						if(!isset($_SESSION['search']['state']) || $_SESSION['search']['state'] == "")
							echo " selected ";
						echo ">Select an option</option>";
						if(isset($conn) && $conn)
						{
							$cmd = "SELECT state FROM location GROUP BY(state);";
							$out = mysqli_query($conn, $cmd);
							$arr = mysqli_fetch_all($out);
							for($i = 0; $i < count($arr); $i++)
							{
								$temp = $arr[$i][0];
								echo "<option value = \"$temp\"";
								if(isset($_SESSION['search']['state']) && $_SESSION['search']['state'] == $temp)
									echo " selected ";
								echo ">".$temp."</option>";
							}
						}
						echo "</select>";
					?>
					<noscript><input type="submit" value="Submit"></noscript>
				</td>
			</tr>
			<tr>
				<td>District</td>
				<td>
					<select name = "district" onchange = 'this.form.submit()'
					<?php
						if(!isset($_SESSION['search']['state']) || $_SESSION['search']['state'] == "")
							echo " disabled";
					?>
					>
					<?php
						if(isset($_SESSION['search']['state']) && $_SESSION['search']['state'] != "")
						{
							echo "<option value = \"\"";
							if(!isset($_SESSION['search']['district']) || $_SESSION['search']['district'] == "")
								echo " selected";
							echo ">Select an option</option>";
							$temp = $_SESSION['search']['state'];
							$cmd = "SELECT district from location WHERE state = '$temp' GROUP BY(district);";
							$out = mysqli_query($conn, $cmd);
							$arr = mysqli_fetch_all($out);
							for($i = 0; $i < count($arr); $i++)
							{
								$temp = $arr[$i][0];
								echo "<option value = \"$temp\"";
								if(isset($_SESSION['search']['district']) && $_SESSION['search']['district'] == $temp)
									echo " selected";
								echo ">".$temp."</option>";
							}
						}
						else
							echo "<option value = \"\">Select an option</option>";
					?>
					</select>
					<noscript><input type="submit" value="Submit"></noscript>
				</td>
			</tr>
			<tr>
				<td>City/Town</td>
				<td>
					<select name = "city"
					<?php
						if(!isset($_SESSION['search']['state']) || $_SESSION['search']['state'] == "" || !isset($_SESSION['search']['district']) || $_SESSION['search']['district'] == "")
							echo " disabled";
					?>
					>
					<?php
						if(isset($_SESSION['search']['state']) && $_SESSION['search']['state'] != "" && isset($_SESSION['search']['district']) && $_SESSION['search']['district'] != "")
						{
							echo "<option value = \"\"";
							if(!isset($_SESSION['search']['city']) || $_SESSION['search']['city'])
								echo " selected";
							echo ">Select an option</option>";
							if(isset($conn) && $conn)
							{
								$temp = $_SESSION['search']['state'];
								$temp1 = $_SESSION['search']['district'];
								$cmd = "SELECT city FROM location WHERE state = $temp AND district = $temp1;";
								$out = mysqli_query($conn, $cmd);
								$arr = mysqli_fetch_all($out);
								for($i = 0; $i < count($arr); $i++)
								{
									$temp = $arr[$i][0];
									echo "<option value = \"$temp\"";
									if(!isset($_SESSION['search']['city']) || $_SESSION['search']['city'])
										echo " selected";
									echo ">".$temp."</option>";
								}
							}
						}
						else
							echo "<option value = \"\">Select an option</option>";
					?>
					</select>
				</td>
			</tr>
		</table>
		<button type = "submit" value = "Submit">Search</button>
	</form>
</body>
</html>