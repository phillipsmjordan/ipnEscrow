<?php
include("config.php");

mysql_connect($dbhost, $dbuser, $dbpass);
mysql_select_db($dbname);

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

/* Parse the incoming data */
foreach($_POST as $key=>$value){
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

/* Create postback for validation
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

/* Local variables */
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];
$user_id = $item_number;

if($receiver_email != $master_paypal_email){
	exit();
}

if(!$sandbox){
	$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
	if(!$fp){
		file_put_contents("errors.log", "Failed to open socket to paypal!", FILE_APPEND);
		exit();
	}
	fputs ($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if(strcmp($res, "VERIFIED") == 0){
			// check the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your Primary PayPal email
			// check that payment_amount/payment_currency are correct
			// process payment
		}elseif(strcmp($res, "INVALID") == 0){
			// log for manual investigation
		}
	}
	fclose ($fp);
}else{
	$query = "INSERT INTO transactions (pp_trx_id, user_id, account_id, amount) VALUES ('$txn_id', '$user_id', '$user_id', '$payment_amount');";
	mysql_query($query);

	$query = "SELECT balance FROM users WHERE id='$user_id';";
	$result = mysql_query($query);
	list($balance) = mysql_fetch_row($result);

	$balance += $payment_amount;
	$query = "UPDATE users SET balance='$balance' WHERE id='$user_id';";
	mysql_query($query);
}

?>
