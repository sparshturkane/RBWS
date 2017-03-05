<?php

function notification($reg_key,$notification){
	
// API access key from Google API's Console
define( 'API_ACCESS_KEY', 'AIzaSyDNk9wafP7C8TMNHVoT9TuuMAiF0_mh6LM' );
$registrationIds = array($reg_key);

// prep the bundle
$msg = array
(
	'message' 	=> $notification,
	'title'		=> 'Rockabyte',
	'subtitle'	=> 'notification',
	'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
	'vibrate'	=> 1,
	'sound'		=> 1,
	'largeIcon'	=> 'large_icon',
	'smallIcon'	=> 'small_icon'
);
$fields = array
(
	'registration_ids' 	=> $registrationIds,
	'data'			=> $msg
);
 
$headers = array
(
	'Authorization: key=' . API_ACCESS_KEY,
	'Content-Type: application/json'
);
 
$ch = curl_init();
curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
curl_setopt( $ch,CURLOPT_POST, true );
curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
$result = curl_exec($ch );
curl_close( $ch );
//echo $result;

return $result;
}

echo $abc=notification("fuoFXP_ZMf4:APA91bE9JAHTVYou69DHqOVXvGt0cnXVKE0FZe7SRTKi9rdOYHZOsX3o1UskmrKoixR_HVG7dpPwUS7hcDgkMu8cext_o5e5l02KgCTTslkRUZPKnpYqHxNM5wsY9OBNpZvqzP9iVqOt","hello gcm works");
?>

<!-- <!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<a href="#" onclick="notification('fgH6TGWARM8:APA91bESHk_aRGjr9rx0zX6m6C_YsWZGCnsGecVk4AV7z9enIifpdqr_BmQ6FQohdnDvYtt3yKaDZ_HvpgV50HfkDOQOB7ELORhZfVWdZ1P11Xb1GQbd44mh0ohyDlYNiL5gZiZ1Erqu','hello sparsh from gcm')"> click to send message</a>
</body>
</html> -->