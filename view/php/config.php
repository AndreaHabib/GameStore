<?php
//if session is not set, set session
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
DEFINE('SERVER', 'csidatabase'); //server name
DEFINE('USER', 'habib'); //user
DEFINE('PASS', 'andrea3383'); //pass
DEFINE('DATABASE', 'habib_'); //database name

//check if database doesn't return any errors, catches any exception
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
try{
    $conn = new mysqli(SERVER, USER, PASS, DATABASE);
    $conn->set_charset("utf8mb4");
} catch(Execption $e){
    error_log($e->getMessage());
    exit("Error connecting to database.");
}

?>