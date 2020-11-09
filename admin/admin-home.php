<!DOCTYPE html>
<html>
<head>
	<title>Create Individual Request</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
    <form action = "admin-view-employee.php" method = "post">
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