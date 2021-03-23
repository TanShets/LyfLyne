<?php
    include_once("../security.php");
    if(!isset($_SESSION))
        session_start();
    redirect("../../");
?>