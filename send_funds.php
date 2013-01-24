<?php
$script_url = "";
if($_SERVER["HTTPS"] == "on"){
        $script_url .= "https://";
}else{
      	$script_url .= "http://";
}
$script_url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

if(isset($_POST['pay_to']) && preg_match("/^[[:alnum:]-_]*\@[[:alnum:]-_]*.[[:alnum:]]{2,4}/", $_POST['pay_to']) == 1 && floatval($_POST['amount']) > 0 && $balance > 0 && floatval($_POST['amount']) <= $balance){

	if($sandbox){
		$api_url = "https://svcs.sandbox.paypal.com/AdaptivePayments/Pay";
	}else{
		$api_url = "";
	}

	$content = array("requestEnvelope.errorLanguage" => "en_US",
		"actionType" => "PAY",
		"currencyCode" => "USD",
		"receiverList.receiver(0).email" => $_POST['pay_to'],
		"receiverList.receiver(0).amount" => floatval($_POST['amount']),
		"senderEmail" => $master_paypal_email,
		"memo" => "Withdrawl User ID: $user",
		"cancelUrl" => $script_url,
		"returnUrl" => $script_url
	);

	$params = array("http" => array( 
		"method" => "POST",
		"content" => http_build_query($content, "", "&"),
		"header" => "X-PAYPAL-SECURITY-USERID: ".$api_username."\r\n" .
		"X-PAYPAL-SECURITY-SIGNATURE: ".$api_signature."\r\n" .
		"X-PAYPAL-SECURITY-PASSWORD: ".$api_password."\r\n" .
		"X-PAYPAL-APPLICATION-ID: APP-80W284485P519543T\r\n" .
		"X-PAYPAL-REQUEST-DATA-FORMAT: NV\r\n" .
		"X-PAYPAL-RESPONSE-DATA-FORMAT: NV\r\n" )
	);

	try{
		$ctx = stream_context_create($params);
		$fp = fopen($api_url, "r", false, $ctx);
		if(!$fp){
			throw new Exception("Error opening stream.");
		}
		$response = stream_get_contents($fp);
		if(!$response){
			throw new Exception("Error getting response from PayPal Server.");
		}

		fclose($fp);

		$response = explode("&", $response);

		$response_array = array();
		for($i = 0; $i < count($response); $i++){
			list($key, $value) = explode("=", $response[$i]);
			$response_array[$key] = $value;
		}

		if($response_array["responseEnvelope.ack"] == "Success"){
			$balance -= floatval($_POST['amount']);
			$query = "UPDATE users SET balance='$balance' WHERE id='$user';";
			mysql_query($query);
			
			$query = "INSERT INTO transactions (pp_trx_id, amount, user_id, account_id) VALUES ('".$response_array["payKey"]."', '-".floatval($_POST['amount'])."', '$user', '$user');";
			mysql_query($query);
			
			?>
			<head><meta http-equiv="refresh" content="2; index.php"></head>
			<center><b>Funds Sent!</b></center>
			<?php
		}else{
			echo "Funds transfer failed!<br/>Code: ".$response_array["error(0).errorId"]."<br/>Message: ".urldecode($response_array["error(0).message"]);
		}
	}catch(Exception $e){
		echo "Error Message: " .$e->getMessage();
	}
}else{
	echo "Mail address was malformed or amount was invalid!";
}

?>
