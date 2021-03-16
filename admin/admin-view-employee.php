<!DOCTYPE html>
<html>
	<head>
		<title>View Employees</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="../style/admin/admin-view-employee.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <!-- <style type = "text/css">
            .window-box{
                position: absolute;
                left: 43%;
                top: 40%;
                border: solid black 1px;
                padding: 20px;
            }
        </style> -->
        <?php
            session_start();
            
            if(isset($_SESSION['message'])){
                echo "<script>";
                echo "alert(\"".$_SESSION['message']."\");";
                echo "</script>";
                unset($_SESSION['message']);
            }

            if(!isset($_SESSION['admin'])){
                $_SESSION['message'] = "You must login to view requests!";
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
            if(!isset($_SESSION['admin-request']))
            {
                $_SESSION['admin-request'] = array();
            }
            //$_SESSION['admin-request']['isloc'] = "no";
            //$_SESSION['admin-request']['state'] = "a";
            //$_SESSION['admin-request']['district'] = "a";
            if($conn == NULL || !$conn){
                die("Failed: ".mysqli_connect_error());
            }
            
            $cmd = "SELECT eid, name, username, email, lid, ecode, mobile, landline FROM emp_user;";
            $out = mysqli_query($conn, $cmd);
            $heads = null;
            $arr = null;
            if($out){
                $heads = mysqli_fetch_array($out);
                if(is_array($heads)){
                    $out = mysqli_query($conn, $cmd);
                    if($out){
                        $arr = mysqli_fetch_all($out);
                    }
                }
            }

            if($_SERVER['REQUEST_METHOD'] == "POST"){
                if(isset($_POST['isCancelled']) && $_POST['isCancelled'] == "Close"){
                    $_SESSION['admin-request'] = array();
                }

                if(isset($conn) && $conn){
                    $count = 0;
                    if(isset($_POST['state'])){
                        $_SESSION['admin-request']['state'] = $_POST['state'];
                        if($_SESSION['admin-request']['state'] != "")
                            $count++;
                        else{
                            $_SESSION['admin-request']['district'] = "";
                            $_SESSION['admin-request']['city'] = "";
                        }
                    }

                    if(isset($_POST['district'])){
                        $_SESSION['admin-request']['district'] = $_POST['district'];
                        if($_SESSION['admin-request']['district'] != "")
                            $count++;
                        else
                            $_SESSION['admin-request']['city'] = "";
                    }

                    if(isset($_POST['city'])){
                        $_SESSION['admin-request']['city'] = $_POST['city'];
                        if($_SESSION['admin-request']['city'] != "")
                            $count++;
                    }

                    if(isset($_POST['area'])){
                        $_SESSION['admin-request']['area'] = $_POST['area'];
                        if($_SESSION['admin-request']['area'] != "")
                            $count++;
                    }

                    if(isset($_POST['used'])){
                        switch($_POST['used']){
                            case 0:{
                                $cmd = "UPDATE emp_user SET name = '".$_POST['name']."', username = ";
                                $cmd = $cmd."'".$_POST['username']."', email = '".$_POST['email']."', ";
                                $cmd = $cmd."ecode = '".$_POST['ecode']."', mobile = '".$_POST['mobile']."', ";
                                $cmd = $cmd."landline = '".$_POST['landline']."' WHERE eid = '".$_POST['eid']."';";
                                //echo $cmd;
                                $out = mysqli_query($conn, $cmd);
                                if($out){
                                    // $cmd = "SELECT * FROM emp_user WHERE name = '".$_POST['name']."' AND ";
                                    // $cmd = $cmd."username = '".$_POST['username']."' AND ";
                                    // $cmd = $cmd."email = '".$_POST['email']."' AND ecode = '".$_POST['ecode']."' AND ";
                                    // $cmd = $cmd."mobile = '".$_POST['mobile']."' AND landline = '".$_POST['landline']."' AND ";
                                    // $cmd = $cmd."eid = '".$_POST['eid']."';";
                                    $cmd = "SELECT eid, name, username, email, lid, ecode, mobile, landline FROM emp_user;";
                                    $out = mysqli_query($conn, $cmd);
                                    if($out){
                                        $new_arr = mysqli_fetch_all($out);
                                        if(is_array($new_arr) && count($new_arr) > 0){
                                            $arr = $new_arr;
                                        }
                                    }
                                    echo "<script>";
                                    echo "alert(\"Employee id: ".$_POST['eid']." successfully updated!\");";
                                    echo "</script>";
                                }
                                else{
                                    echo "<script>";
                                    echo "alert(\"Update Failure!\");";
                                    echo "</script>";
                                }
                                break;
                            }

                            case 1:{
                                $_SESSION['admin-request']['eid'] = $_POST['eid'];
                                break;
                            }

                            case 2:{
                                $cmd = "DELETE FROM emp_user WHERE eid = '".$_POST['eid']."';";
                                $out = mysqli_query($conn, $cmd);
                                if($out){
                                    echo "<script>";
                                    echo "alert(\"Employee id: ".$_POST['eid']." successfully deleted!\");";
                                    echo "</script>";
                                }
                                else{
                                    echo "<script>";
                                    echo "alert(\"Deletion Failure!\");";
                                    echo "</script>";
                                }
                                //echo $cmd;
                                break;
                            }

                            case 3:{
                                if($count == 4){
                                    $cmd = "SELECT lid FROM location WHERE state = '".$_SESSION['admin-request']['state']."'";
                                    $cmd = $cmd." AND district = '".$_SESSION['admin-request']['district']."'";
                                    $cmd = $cmd." AND city = '".$_SESSION['admin-request']['city']."'";
                                    $cmd = $cmd." AND area = '".$_SESSION['admin-request']['area']."';";
                                    $out = mysqli_query($conn, $cmd);
                                    if($out){
                                        $arr_temp = mysqli_fetch_array($out);
                                        $lid = $arr_temp['lid'];
                                        //echo $_SESSION['admin-request']['eid'];
                                        $cmd = "UPDATE emp_user SET lid = '$lid' WHERE eid = '".$_SESSION['admin-request']['eid']."';";
                                        $out = mysqli_query($conn, $cmd);
                                        if($out){
                                            unset($_SESSION['admin-request']['eid']);
                                            echo "<script>";
                                            echo "alert(\"Employee id: ".$_POST['eid']." location successfully updated!\");";
                                            echo "</script>";
                                        }
                                        else{
                                            echo "<script>";
                                            echo "alert(\"Update Failure!\");";
                                            echo "</script>";
                                        }
                                    }
                                }
                                //echo $cmd;
                                break;
                            }
                        }
                    }
                }
            }

            function display($heads, $arr){
                //print_r($heads);
                echo "<table class = \"table table-striped\">";
                echo "<tr>";
                $names = Array();
                foreach ($heads as $x => $y) {
                    if(!is_numeric($x)){
                        echo "<th>".$x."</th>";
                        array_push($names, $x);
                    }
                }
                echo "<td></td><td></td><td></td>";
                echo "</tr>";
                //print_r($names);
                $i = null;
                $keep = array("eid", "lid");
                $text_inputs = array("name", "username", "email");
                $num_inputs = array("ecode", "mobile", "landline");
                for($i = 0; $i < count($arr); $i++){
                    echo "<tr>";
                    echo "<form action = \"admin-view-employee.php\" method = \"post\">";
                    for($j = 0; $j < count($names); $j++){
                        echo "<td>";
                        if(in_array($names[$j], $keep)){
                            echo $arr[$i][$j];
                            echo "<input type = \"hidden\" name = \"".$names[$j]."\" value = \"".$arr[$i][$j]."\">";
                        }
                        elseif(in_array($names[$j], $text_inputs)){
                            echo "<input type = \"text\" name = \"".$names[$j]."\" value = \"".$arr[$i][$j]."\">";
                        }
                        elseif(in_array($names[$j], $num_inputs)){
                            echo "<input type = \"number\" name = \"".$names[$j]."\" value = \"".$arr[$i][$j]."\">";
                        }
                        echo "</td>";
                    }
                    echo "<input type = \"hidden\" name = \"used\" value = \"0\">";
                    echo "<td><button class = \"btn btn-success\" type = \"submit\">Update</button></td>";
                    echo "</form>";
                    echo "<td><form action = \"admin-view-employee.php\" method = \"post\">";
                    echo "<input type = \"hidden\" name = \"eid\" value = \"".$arr[$i][0]."\">";
                    echo "<input type = \"hidden\" name = \"used\" value = \"1\">";
                    echo "<button style = \"width: 120%;\" class = \"btn btn-primary\" type = \"submit\">Update Location</button>";
                    echo "</form></td>";
                    echo "<td><form action = \"admin-view-employee.php\" method = \"post\">";
                    echo "<input type = \"hidden\" name = \"eid\" value = \"".$arr[$i][0]."\">";
                    echo "<input type = \"hidden\" name = \"used\" value = \"2\">";
                    echo "<button class = \"btn btn-danger\" type = \"submit\">Delete</button>";
                    echo "</form></td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            
            function window_box($conn, $eid){
                echo "<div class = \"window-box\">";
                echo "<form action = \"admin-view-employee.php\" method = \"post\">";
                echo "<table>";
                echo "<tr>";
                    echo "<td>State:</td>";
                    echo "<td>";
                    //echo "Yes";
                        echo "<select class = \"form-control\" name = \"state\" onchange = \"this.form.submit()\">";
                        echo "<option value = \"\"";
                        if(!isset($_SESSION['admin-request']['state']) || $_SESSION['admin-request']['state'] == "")
                            echo " selected ";
                        echo ">Select an option</option>";
                        if(isset($conn) && $conn)
                        {
                            $cmd = "SELECT state FROM location GROUP BY(state);";
                            $out = mysqli_query($conn, $cmd);
                            if($out)
                                echo "Success";
                            $arr = mysqli_fetch_all($out);
                            //print_r($arr);
                            for($i = 0; $i < count($arr); $i++)
                            {
                                $temp = $arr[$i][0];
                                echo "<option value = \"$temp\"";
                                if(isset($_SESSION['admin-request']['state']) && $_SESSION['admin-request']['state'] == $temp)
                                    echo " selected ";
                                echo ">".$temp."</option>";
                            }
                        }
                        echo "</select>";
                        echo "<noscript><input type=\"submit\" value=\"Submit\"></noscript>";
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>District:</td>";
                    echo "<td>";
                        echo "<select class = \"form-control\" name = \"district\" onchange = 'this.form.submit()'";
                        if(!isset($_SESSION['admin-request']['state']) || $_SESSION['admin-request']['state'] == "")
                            echo " disabled";
                        echo ">";
                        if(isset($_SESSION['admin-request']['state']) && $_SESSION['admin-request']['state'] != ""){
                            echo "<option value = \"\"";
                            if(!isset($_SESSION['admin-request']['district']) || $_SESSION['admin-request']['district'] == "")
                                echo " selected";
                            echo ">Select an option</option>";
                            $temp = $_SESSION['admin-request']['state'];
                            $cmd = "SELECT district from location WHERE state = '$temp' GROUP BY(district);";
                            $out = mysqli_query($conn, $cmd);
                            $arr = mysqli_fetch_all($out);
                            for($i = 0; $i < count($arr); $i++){
                                $temp = $arr[$i][0];
                                echo "<option value = \"$temp\"";
                                if(isset($_SESSION['admin-request']['district']) && $_SESSION['admin-request']['district'] == $temp)
                                    echo " selected";
                                echo ">".$temp."</option>";
                            }
                        }
                        else
                            echo "<option value = \"\">Select an option</option>";
                        echo "</select>";
                        echo "<noscript><input type=\"submit\" value=\"Submit\"></noscript>";
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>City/Town:</td>";
                    echo "<td>";
                        echo "<select class = \"form-control\" name = \"city\" onchange = 'this.form.submit()'";
                        if(!isset($_SESSION['admin-request']['state']) || $_SESSION['admin-request']['state'] == "" || !isset($_SESSION['admin-request']['district']) || $_SESSION['admin-request']['district'] == "")
                            echo " disabled";
                        echo ">";
                        if(isset($_SESSION['admin-request']['state']) && $_SESSION['admin-request']['state'] != "" && isset($_SESSION['admin-request']['district']) && $_SESSION['admin-request']['district'] != ""){
                            echo "<option value = \"\"";
                            if(!isset($_SESSION['admin-request']['city']) || $_SESSION['admin-request']['city'] == "")
                                echo " selected";
                            echo ">Select an option</option>";
                            if(isset($conn) && $conn)
                            {
                                $temp = $_SESSION['admin-request']['state'];
                                $temp1 = $_SESSION['admin-request']['district'];
                                $cmd = "SELECT city FROM location WHERE state = '$temp' AND district = '$temp1' GROUP BY(city);";
                                $out = mysqli_query($conn, $cmd);
                                $arr = mysqli_fetch_all($out);
                                for($i = 0; $i < count($arr); $i++){
                                    $temp = $arr[$i][0];
                                    echo "<option value = \"$temp\"";
                                    if(isset($_SESSION['admin-request']['city']) && $_SESSION['admin-request']['city'] == $temp)
                                        echo " selected";
                                    echo ">".$temp."</option>";
                                }
                            }
                        }
                        else
                            echo "<option value = \"\">Select an option</option>";
                        echo "</select>";
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>Area:</td>";
                    echo "<td>";
                        echo "<select class = \"form-control\" name = \"area\"";
                        if(
                            !isset($_SESSION['admin-request']['state']) || 
                            $_SESSION['admin-request']['state'] == "" || 
                            !isset($_SESSION['admin-request']['district']) || 
                            $_SESSION['admin-request']['district'] == "" || 
                            !isset($_SESSION['admin-request']['city']) || 
                            $_SESSION['admin-request']['city'] == ""
                        )
                            echo " disabled";
                        echo ">";
                        if(
                            isset($_SESSION['admin-request']['state']) && $_SESSION['admin-request']['state'] != "" && 
                            isset($_SESSION['admin-request']['district']) && $_SESSION['admin-request']['district'] != "" && 
                            isset($_SESSION['admin-request']['city']) && $_SESSION['admin-request']['city'] != ""
                        ){
                            echo "<option value = \"\"";
                            if(!isset($_SESSION['admin-request']['area']) || $_SESSION['admin-request']['area'] == "")
                                echo " selected";
                            echo ">Select an option</option>";
                            if(isset($conn) && $conn)
                            {
                                $temp = $_SESSION['admin-request']['state'];
                                $temp1 = $_SESSION['admin-request']['district'];
                                $temp2 = $_SESSION['admin-request']['city'];
                                $cmd = "SELECT area FROM location WHERE state = '$temp' AND district = '$temp1' AND city = '$temp2';";
                                $out = mysqli_query($conn, $cmd);
                                $arr = mysqli_fetch_all($out);
                                for($i = 0; $i < count($arr); $i++){
                                    $temp = $arr[$i][0];
                                    echo "<option value = \"$temp\"";
                                    if(isset($_SESSION['admin-request']['area']) && $_SESSION['admin-request']['area'] == $temp)
                                        echo " selected";
                                    echo ">".$temp."</option>";
                                }
                            }
                        }
                        else
                            echo "<option value = \"\">Select an option</option>";
                        echo "</select>";
                    echo "</td>";
                echo "</tr>";
                echo "</table><br>";
                echo "<input type = \"hidden\" name = \"eid\" value = \"".$_SESSION['admin-request']['eid']."\">";
                echo "<input type = \"hidden\" name = \"used\" value = \"3\">";
                echo "<center><button type = \"submit\" class = \"btn btn-success\">Update Location</button></center>";
                echo "</form><br>";
                echo "<center>";
                echo "<form action = \"admin-view-employee.php\" method = \"post\">";
                    echo "<input type = \"submit\" class = \"btn btn-danger\" name = \"isCancelled\" value = \"Close\">";
                echo "</form>";
                echo "</center>";
                echo "</div>";
            }
        ?>
	</head>
	<body>
        <center><h2>Employee List</h2></center>
		<?php
            if($heads != null && $arr != null)
                display($heads, $arr);
            
            if(isset($_SESSION['admin-request']['eid'])){
                //echo $_SESSION['admin-request']['eid'];
                window_box($conn, $_SESSION['admin-request']['eid']);
            }
        ?>
	</body>
</html>