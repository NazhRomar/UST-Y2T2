<?php
// database connection
$nraservername = "localhost"; // offline default server name
$nrausername = "root"; // username from MySQL
$nrapassword = "";
$nradbase = "db_hotel";

$nraconn = new mysqli($nraservername, $nrausername, $nrapassword, $nradbase);
?>
