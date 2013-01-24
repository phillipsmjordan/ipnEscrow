<?php
$message = "";

if(isset($_POST['register']) && intval($_POST['register']) == 1){
	if(preg_match("/^[[:alnum:]-_.]*$/", $_POST['username']) == 1 && preg_match("/^[[:alnum:]-_.]*\@[[:alnum:]-_]*.[A-Za-z]{2,4}$/", $_POST['email_address']) == 1 && $_POST['password1'] == $_POST['password2'] && strlen($_POST['password1']) > 0){
		$query = "INSERT INTO users (username, password, email_address) VALUES ('".$_POST['username']."', '".md5($_POST['password1'])."', '".$_POST['email_address']."');";
		mysql_query($query) or die("Error adding user!<br/>".mysql_error());

		$query = "SELECT id FROM users WHERE username='".$_POST['username']."';";
		$result = mysql_query($query);
		list($id) = mysql_fetch_row();
		
		$_SESSION['user'] = base64_encode($id);

		?>
		<head><meta http-equiv="refresh" content="2; index.php"></head>
		<center><b>Logged in!</b></center>
		<?php
	}else{
		if(preg_match("/^[[:alnum:]-_.]*$/", $_POST['username']) == 0){
			$message = "Usernames may only contain letters, number, -, _, and .";
		}elseif(preg_match("/^[[:alnum:]-_.]*.[A-Za-z]{2,4}$/", $_POST['email_address']) == 0){
			$message = "E-Mail address is not a proper address!";
		}elseif($_POST['password1'] != $_POST['password2']){
			$message = "Passwords did not match!";
		}elseif(strlen($_POST['password1']) == 0){
			$message = "Password cannot be blank!";
		}
	}
}else{
?>
<center><h2>Register</h2>
<form action='index.php?cmd=register' method='post'>
<input type='hidden' name='register' value='1'>
<?php if(strlen($message) > 0){ echo "<font color='#DD0000'>$message</font>"; } ?>
<table border='0'>
<tr><td><b>Username:</b></td><td><input type='text' name='username'></td></tr>
<tr><td><b>Password:</b></td><td><input type='password' name='password1'></td></tr>
<tr><td><b>Confirm Password:</b></td><td><input type='password' name='password2'></td></tr>
<tr><td><b>PayPal E-Mail Address:</b></td><td><input type='text' name='email_address'></td></tr>
<tr><td colspan='2'><center><input type='submit' value='Register'></center></td></tr>
</table>
<?php
}
?>
