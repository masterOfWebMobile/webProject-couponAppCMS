<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCM
 *
 * @author Ravi Tamada
 */
class GCM {

    //put your code here
    // constructor
    function __construct() {
        
    }

    /**
     * Sending Push Notification
     */
    public function send_notification($registatoin_ids, $message) {
        // include config
        include_once 'config.php';


        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

	//$message = json_encode($message);

        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message
        );

        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            echo "<p style='color:red;'> push notification for android sent failed for 1000 devices </p>";
            die('Curl failed: ' . curl_error($ch));
        }
        //else
            //echo " <p style='color:green;'> push notification for android  sent successfully for 1000 devices </p>";

        // Close connection
        curl_close($ch);
        //echo $result;
    }

}

?>