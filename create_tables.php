<?php
    $mainServe = "localhost";
    $mainuser = "root";
    $mainpass = "";
    $dbname = "lyflyne";
    $conn = mysqli_connect($mainServe, $mainuser, $mainpass, $dbname);

    if($conn == NULL){
        echo "Failed";
    }
    else{
        $cmds = array(
            "CREATE TABLE IF NOT EXISTS admin(aid INT, userid VARCHAR(60));",
            "CREATE TABLE IF NOT EXISTS area_location(lid INT PRIMARY KEY, clid INT, area VARCHAR(60));",
            "CREATE TABLE IF NOT EXISTS blood(blid INT PRIMARY KEY, uid INT, btype VARCHAR(10), isbank INT, quantity INT, lid INT);",
            "CREATE TABLE IF NOT EXISTS control(id INT PRIMARY KEY, tablename VARCHAR(40));",
            
            "CREATE TABLE IF NOT EXISTS emp_user(eid INT PRIMARY KEY, name VARCHAR(60), username VARCHAR(40), password VARCHAR(40)
            email VARCHAR(60), lid INT, ecode INT UNIQUE, mobile DECIMAL(10), landline DECIMAL(8));",
            
            "CREATE TABLE IF NOT EXISTS heart(heid INT PRIMARY KEY, uid INT, btype VARCHAR(10), quantity INT, lid INT);",
            
            "CREATE TABLE IF NOT EXISTS hospital_request(rid INT PRIMARY KEY, hid INT, name VARCHAR(60), dtype VARCHAR(30),
            btype VARCHAR(10), quantity INT, lid INT, priority INT, request_time DATETIME, lookin INT);",
            
            "CREATE TABLE IF NOT EXISTS hospital_user(hid INT PRIMARY KEY, username VARCHAR(40), password VARCHAR(30), 
            name VARCHAR(120), lid INT, hcode INT UNIQUE, mobile DECIMAL(10), landline DECIMAL(8), 
            email VARCHAR(60));",
            
            "CREATE TABLE IF NOT EXISTS location(lid INT PRIMARY KEY, state VARCHAR(100), district VARCHAR(60), city VARCHAR(60),
            area VARCHAR(60));",
            
            "CREATE TABLE IF NOT EXISTS marrow(maid INT PRIMARY KEY, uid INT, btype VARCHAR(10), isbank INT, quantity INT, lid INT);",
            
            "CREATE TABLE IF NOT EXISTS request(rid INT PRIMARY KEY, lid INT, priority INT, dtype VARCHAR(30), uid INT, 
            request_time DATETIME, lookin INT);",
            
            "CREATE TABLE retina(reid INT PRIMARY KEY, uid INT, btype VARCHAR(10), quantity INT, lid INT);",
            
            "CREATE TABLE IF NOT EXISTS user(uid INT, username VARCHAR(40), password VARCHAR(30), name VARCHAR(40),
            mobile DECIMAL(10), landline DECIMAL(8), lid INT, email VARCHAR(60), 
            bdonor INT, mdonor INT, odonor INT, btype VARCHAR(10));",

            "CREATE TABLE IF NOT EXISTS admin_hospital_request_queue(rid INT PRIMARY KEY, aid INT, hid INT, name VARCHAR(60), 
            dtype VARCHAR(30), btype VARCHAR(10), quantity INT, lid INT, priority INT, request_time DATETIME, lookin INT, dtid INT);",

            "CREATE TABLE IF NOT EXISTS admin_request_queue(rid INT PRIMARY KEY, aid INT, lid INT, priority INT, dtype VARCHAR(30), 
            uid INT, request_time DATETIME, lookin INT, dtid INT);",

            "CREATE TABLE IF NOT EXISTS dead_donor_log(ddid INT PRIMARY KEY, dtype VARCHAR(40), dtid INT, lid INT, 
            btype VARCHAR(10), finish_time DATETIME);",

            "CREATE TABLE IF NOT EXISTS dead_donor_queue(ddid INT PRIMARY KEY, dtype VARCHAR(40), dtid INT, lid INT, 
            btype VARCHAR(10));",

            "CREATE TABLE IF NOT EXISTS hospital_request_log(rid INT PRIMARY KEY, hid INT, name VARCHAR(60), dtype VARCHAR(30),
            btype VARCHAR(10), quantity INT, lid INT, priority INT, request_time DATETIME, lookin INT, finish_time DATETIME);",

            "CREATE TABLE IF NOT EXISTS request_log(rid INT PRIMARY KEY, lid INT, priority INT, dtype VARCHAR(30), uid INT, 
            request_time DATETIME, lookin INT, finish_time DATETIME);",

            "INSERT INTO admin VALUES(1, ';W6HQ:UJ?:X6OM=LO<[GAZH@');",
            "INSERT INTO control VALUES(1, 'blood');",
            "INSERT INTO control VALUES(2, 'marrow');",
            "INSERT INTO control VALUES(3, 'retina');",
            "INSERT INTO control VALUES(4, 'heart');"
        );
        
        $i = 0;
        for($i = 0; $i < count($cmds); $i++){
            $out = mysqli_query($conn, $cmds[$i]);
            if(!$out)
                echo "Could not implement ".$cmds[$i]."<br>";
        }
    }
?>