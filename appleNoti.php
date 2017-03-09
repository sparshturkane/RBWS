<?php
 	function iphoneNotification()
    {
        $apnsHost = 'gateway.sandbox.push.apple.com';
        $apnsCert = '/var/www/html/rockabyteServicesV3Test/application/RockabyteDevpushcert.pem';
        $apnsPort = 2195;
        $apnsPass = 'Coders1234';
        $token = '51953e40362f0f643e16eebc8d6bb6087a5e5472a0288c60d105acba2a9fd972';

        $payload['aps'] = array(
            'alert' => 'works ...............', 
            'badge' => 1, 
            'sound' => 'default'
            );

        // prep the bundle
        // if($notificationTypeID==3){
        //     $payload['aps'] = array
        //     (
        //         'alert'   => $notification,
        //         'badge' => 1, 
        //         'sound' => 'default',
        //         'title'     => 'Rockabyte',
        //         'videoID'   => $videoID,
        //         // 'isWish'    => 0,
        //         'thumbnail' => $videoThumbnail,
        //         'notificationTypeID'  => '3', // this is for like video
        //     );
        // } elseif($notificationTypeID==2) {
        //     $msg = array
        //     (
        //         'alert'   => $notification,
        //         'badge' => 1, 
        //         'sound' => 'default',
        //         'title'     => 'Rockabyte',
        //         'videoID'   => $videoID,
        //         // 'isWish'    => 1,
        //         'thumbnail' => $videoThumbnail,
        //         'notificationTypeID'  => '1', // this is for wish videos
        //     );
        // } elseif($notificationTypeID==4){
        //     $msg = array
        //     (
        //         'alert'   => $notification,
        //         'badge' => 1, 
        //         'sound' => 'default',
        //         'title'     => 'Rockabyte',
        //         'videoID'   => '',
        //         // 'isWish'    => 0,
        //         'thumbnail' => $videoThumbnail,
        //         'notificationTypeID'  => '2', // this is for notification of follow
        //     );
        // }


        $output = json_encode($payload);
        $token = pack('H*', str_replace(' ', '', $token));
        $apnsMessage = chr(0).chr(0).chr(32).$token.chr(0).chr(strlen($output)).$output;

        $streamContext = stream_context_create();
        stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
        stream_context_set_option($streamContext, 'ssl', 'passphrase', $apnsPass);

        $apns = stream_socket_client('ssl://'.$apnsHost.':'.$apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
        fwrite($apns, $apnsMessage);
        fclose($apns);
    }