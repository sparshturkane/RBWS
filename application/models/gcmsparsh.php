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
	'videoID'	=> '406',
	'thumbnail'	=> 'http://faarbetterfilms.com/rockabyteServicesV3Test/uploads/video/thumbnail/e3a049147dde78422bfcc515d15b87ba.jpg',
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

echo $abc=notification("ftueqiRN8KY:APA91bFacfgx34AVccYdAarmIonlfePkAVo7JBWKbQEtz61UiNrWegzIjTb7nV-_t9qf_iSFs_5Kj_WS77-A-bsmIfy4QAccOzWShOtn6wXIfznCTzsApQo5AVAPyRGAkQS-O7sMnzOI","hello gcm works");
?>

<!-- <!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<a href="#" onclick="notification('c5tz_kY42u8:APA91bEZoIp01gLR5ALdl-hoHk13xe7k702HOsE632Yb43ENnQlmkcYVLEXF0hbsvcHVUTy4D-whN92LmAcoI4YPpyrzGfZRXqJFZ9PkuJjH4jWhI1M3VzOjqrqwUAF4LiLX7AuEih3G','hello sparsh from gcm')"> click to send message</a>
</body>
</html> -->
