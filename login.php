<?php
$message = "";

if(isset($_POST['login']) && intval($_POST['login']) == 1){
	$query = "SELECT id, password FROM users WHERE LOWER(username)='".strtolower($_POST['username'])."' AND active='1';";
	$result = mysql_query($query) or die("ERROR: ".mysql_error());
	list($id, $password) = mysql_fetch_row($result);
	if($id > 0 && $password == md5($_POST['password'])){
		$_SESSION['user'] = base64_encode($id);

		?>
		<head><meta http-equiv="refresh" content="2; index.php"></head>
		<center><b>Logged in!</b></center>
		<?php
	}else{
		$message = "Username/Password Invalid!";
	}
}

if(strlen($message) > 0 || !isset($_POST['login']) || intval($_POST['login']) == 0){
?>
<center><h2>Login</h2><br/>
<?php if(strlen($message) > 0){ echo "<font color='#DD0000'>$message</font>"; } ?>
<form action='index.php' method='post'>
<input type='hidden' name='login' value='1'>
<table border='0'>
	<tr><td><b>Username:</b></td><td><input type='text' name='username'></td></tr>
	<tr><td><b>Password:</b></td><td><input type='password' name='password'></td></tr>
	<tr><td colspan='2'><center><input type='submit' name='submit' value='Login'></td></tr>
	<tr><td colspan='2'><center><a href='index.php?cmd=register'>Register Account</a></center></td></tr>
</table>
</form>
</center>
<?php
}
?>
