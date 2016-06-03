<?php

class IOSNotify{

public function sendNotification($device_id, $msg){

// Put your device token here (without spaces):
//ffc0959d6a8d4d1e5915d4418c0153d7f50e01576e85104156f96d678b75b7d9
$deviceToken = $device_id;

//echo $deviceToken."=".$msg;
// Put your private key's passphrase here:
$passphrase = 'hcentercoupons';

// Put your alert message here:
$message = $msg;

////////////////////////////////////////////////////////////////////////////////

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'webservices/class/ck_dev.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

// Open a connection to the APNS server
$fp = stream_socket_client(
	'ssl://gateway.sandbox.push.apple.com:2195', $err,
	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
	exit("Failed to connect: $err $errstr" . PHP_EOL);

//echo 'Connected to APNS' . PHP_EOL;

// Create the payload body
$body['aps'] = array(
	'alert' => $message,
	'sound' => 'default'
	);

// Encode the payload as JSON
$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));

//if (!$result)
//	echo 'Message not delivered' . PHP_EOL;
//else
//	echo  'Message \'' . $message.'\'successfully delivered to ' .$deviceToken. PHP_EOL;

// Close the connection to the server
fclose($fp);
}
}

?>