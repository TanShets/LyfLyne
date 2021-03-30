<?php
    $tab_names = null;
    $tab_links = null;
    $username = null;
    if(isset($_SESSION['user'])){
        $tabs = array(
            "Create Request" => "create-request.php", 
            "View Requests" => "view-request.php"
        );

        $username = $_SESSION['user']['username'];
        // $tab_names = array("Create Request", "View Requests");
        // $tab_links = array("create-request.php", "view-request.php");
    }
    elseif(isset($_SESSION['hospital_user'])){
        $tabs = array(
            "Create Requests" => "hospital-request.php", 
            "View Requests" => "hospital-view-request.php"
        );
        $username = $_SESSION['hospital_user']['username'];
        // $tab_names = array("Create Requests", "View Requests");
        // $tab_links = array("hospital-create.php", "hospital-view-request.php");
    }
    elseif(isset($_SESSION['emp-user'])){
        $tabs = array(
            "View Requests" => "emp-requests.php", 
            "Update Bank" => "update-bank.php", 
            "Confirm Admin Request" => "emp-confirm_request.php", 
            "Confirm Dead Donor Entry" => "emp-confirm_donor.php"
        );
        $username = $_SESSION['emp-user']['username'];
        // $tab_names = array("View Requests", "Update Bank", "Confirm Admin Request", "Confirm Dead Donor Entry");
        // $tab_links = array("emp-requests.php", "update-bank.php", "emp-confirm_request.php", "emp-confirm_donor.php");
    }
    elseif(isset($_SESSION['admin'])){
        $tabs = array(
            "Home" => "admin-home.php", 
            "View Employees" => "admin-view-employee.php", 
            "View Users" => "admin-view-user.php", 
            "View Requests" => "admin-view-request.php", 
            "Register New Employee" => "emp-create.php", 
            "Register Location" => "location-entry.php"
        );
        include_once("security.php");
        $username = decode($_SESSION['admin']['userid']);
        // $tab_names = array(
        //     "Home", "View Employees", "View Users", 
        //     "View Requests", "Register New Employee", "Register Location"
        // );

        // $tab_links = array(
        //     "admin-home.php", "admin-view-employee.php", "admin-view-user.php",
        //     "admin-view-request.php", "emp-create.php", "location-entry.php"
        // );
    }
    else{
        $tabs = array(
            "Login" => "#", "Employee Login" => "#", "Admin Login" => "#"
        );
        // $tab_names = array("Login", "Employee Login", "Admin Login");
        // $tab_links = array("#", "#", "#");
    }
?>

<div class="container-fluid">
    <nav class="navbar navbar-expand-lg" style="background-color: #BF170A;">
        <a class="navbar-brand" 
        <?php 
            echo "href=\"".$tabs[array_key_first($tabs)]."\" ";
        ?>
        style="color: rgb(240, 185, 4); font-family: Impact;"><big>LyfLyne</big></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <?php
                    if(isset($tabs)){
                        foreach ($tabs as $x => $y) {
                            echo "<li class=\"nav-item\">";
                                echo "<a class=\"nav-link\" href=\"$y\">$x</a>";
                            echo "</li>";   
                        }
                    }
                ?>
                <!-- <li class="nav-item">
                    <a class="nav-link" href="HomePage.html">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Songs
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="content\Bollywood.html">Bollywood </a>
                        <a class="dropdown-item" href="content\English.html">English </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="content\Anime.html">Anime Openings </a>
                    </div>
                </li>
                <li class="nav-item">
                <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Download your Song</a>
                </li> -->
            </ul>
            <!-- <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form> -->
            <?php
                if(isset($username) && $username != null){
                    echo "<div class=\"form-inline my-2 my-lg-0\">";
                        //echo "<a class = \"nav-link\" href = \"../logout.php\">Logout</a>";
                        echo "<div class = \"nav-item dropdown\">";
                            echo "<a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"navbarDropdown\" ";
                            echo "role=\"button\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
                                echo "$username";
                            echo "</a>";
                            echo "<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"navbarDropdown\">";
                                echo "<div class=\"dropdown-item\">$username</div>";
                                echo "<div class=\"dropdown-divider\"></div>";
                                echo "<a class=\"dropdown-item\" href=\"../logout.php\">Logout</a>";
                            echo "</div>";
                        echo "</div>";
                        // <!-- <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                        // <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button> -->
                    echo "</div>";
                }
            ?>
        </div>
    </nav>
</div>