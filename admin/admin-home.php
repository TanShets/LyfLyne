<!DOCTYPE html>
<html>
<head>
	<title>Create Individual Request</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/admin/admin-home.css">
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

    <script>
        function goto(word){
            document.getElementById(word).submit();
        }
    </script>
</head>
<body>
    <div class = "logout-tab">
        <form action = "../logout.php" method = "post">
            <button class = "btn btn-primary" type = "submit" value = "submit">Logout</button>
        </form>
    </div>
    <section class="containerx">
        <form action = "admin-view-user.php" method = "post" id = "user">
            <div class="tab1" id = "view-user" onclick = "goto('user')">Go to View Users</div>
        </form>
        <form action = "admin-view-employee.php" method = "post" id = "v_employee">
            <div class="tab2" id = "view-employee" onclick = "goto('v_employee')">Go to View Employees</div>
        </form>
    </section>
    <section class="containerx">
        <form action = "emp-create.php" method = "post" id = "c_employee">
            <div class="tab1" id = "emp-create" onclick = "goto('c_employee')">Add Employee</div>
        </form>
        <form action = "admin-view-request.php" method = "post" id = "request">
            <div class="tab2" id = "view-request" onclick = "goto('request')">View All Requests</div>
        </form>
    </section>
    <section class="containerx">
        <form action = "location-entry.php" method = "post" id = "location">
            <div id = "location-entry" onclick = "goto('location')">Enter New Location</div>
        </form>
    </section>
</body>
</html>