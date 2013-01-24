<?php
session_start();

include("config.php");

/* Create DB Connection */
$db = mysql_connect($dbhost, $dbuser, $dbpass);
if(!$db){
	die("Error connecting to database!<br/>".mysql_error());
}
if(!mysql_select_db($dbname)){
	die("Error selecting database!<br/>".mysql_error());
}

?>
<html>
<head>
<title>Paypal Gateway</title>
</head>
<body>
<?php

/* Check for a command and store it */
$cmd = "";
if(isset($_GET['cmd'])){
	$cmd = trim($_GET['cmd']);
}

/* Check for a user session and assign ID */
if(isset($_SESSION['user']) && strlen($_SESSION['user']) > 0){
	$user = intval(base64_decode($_SESSION['user']));
}else{
	$user = 0;
}

/* Check account status and load the common variables */
$query = "SELECT username, active, balance, admin FROM users WHERE id='$user';";
$result = mysql_query($query);
list($username, $active, $balance, $admin) = mysql_fetch_row($result);
if(intval($active) == 0 || strlen($username) == 0){
	$_SESSION['user'] = "";
	$user = 0;
}

/* Include the respective file for the operation */
if($user > 0 && $cmd == ""){
	include("dashboard.php");
}elseif($user > 0 && $cmd == "logout"){
	include("logout.php");
}elseif($user > 0 && $cmd == "sendfunds"){
	include("send_funds.php");
}elseif($user == 0 && $cmd == "register"){
	include("register.php");
}else{
	include("login.php");
}

?>
</body>
</html>
