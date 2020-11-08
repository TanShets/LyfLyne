<!DOCTYPE html>
<html>
<head>
	<title>Create Individual Request</title>
    <?php
        if(!isset($_SESSION))
            session_start();
        
        if(!isset($_SESSION['admin'])){
            $_SESSION['message'] = "You must login before you access the admin home page!";
			header("Location: admin-login.php");
				exit();
        }
    ?>
</head>
<body>
	<form action = "admin-view-user.php" method = "post">
        <button type = "submit" value = "submit">Go to View Users</button>
    </form>
    <form action = "admin-home.php" method = "post">
        <button type = "submit" value = "submit">Go to View Employees</button>
    </form>
    <form action = "emp-create.php" method = "post">
        <button type = "submit" value = "submit">Add Employee</button>
    </form>
    <form action = "admin-view-request.php" method = "post">
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