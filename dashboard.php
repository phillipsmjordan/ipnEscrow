<?php

$paypal_url = "";
if($sandbox){
	$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
}else{
	$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
}

$script_url = "";
if($_SERVER["HTTPS"] == "on"){
        $script_url .= "https://";
}else{
      	$script_url .= "http://";
}
$script_url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

?>
<center><h2>Dashboard</h2>
<p>Welcome <?php echo $username; ?>, <a href='index.php?cmd=logout'>Logout</a></p>
<b>Balance:</b> $<?php echo number_format($balance, 2); ?><br/>
<hr>
<b>Send Funds to User</b><br/>
<form action="index.php?cmd=sendfunds" method="post">
<b>Username:</b> <select name='pay_to'><?php
$query = "SELECT username, email_address FROM users ORDER BY username ASC;";
$results = mysql_query($query);
while(list($tempname, $tempaddress) = mysql_fetch_row($results)){
	echo "<option value='$tempaddress'>$tempname</option>";
}
?></select><br/>
<b>Amount:</b> $<input type="text" name="amount" size="5" value=""><br/>
<input type="submit" value="Send Funds">
</form>
<hr>
<b>Send Funds via PayPal</b><br/>
<form action="index.php?cmd=sendfunds" method="post">
<b>E-Mail Address:</b> <input type="text" name="pay_to" value=""><br/>
<b>Amount:</b> $<input type="text" name="amount" size="5" value=""><br/>
<input type="submit" value="Send Funds">
</form>
<hr>
<b>Add Funds</b><br/>
<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="business" value="<?php echo $master_paypal_email; ?>">
<input type="hidden" name="notify_url" value="<?php echo $ipn_url; ?>">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="item_name" value="Deposit User ID <?php echo $user; ?>">
<input type="hidden" name="item_number" value="<?php echo $user; ?>">
<b>Amount:</b> $<input type="text" name="amount" value="" size="5">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="return" value="<?php echo $script_url; ?>">
<input type="hidden" name="cancel_return" value="<?php echo $script_url; ?>">
<br/><input type="submit" value="Add Funds">
</form>
<hr>
<table border=1 width="50%">
<tr><td colspan=2><center>Last 5 Transactions</center></td></tr>
<?php
$query = "SELECT trx_timestamp, amount FROM transactions WHERE account_id='$user' ORDER BY trx_timestamp DESC LIMIT 5;";
$result = mysql_query($query);
while(list($timestamp, $amount) = mysql_fetch_row($result)){
	$modifier = "";
	if(floatval($amount) < 0){
		$modifier = "-";
		$amount *= -1;
	}
	echo "<tr><td>$timestamp</td><td>".$modifier."$".number_format($amount, 2)."</td></tr>\n";
}
?>
</table>
</center>
