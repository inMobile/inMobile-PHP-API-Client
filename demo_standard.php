<?php
require_once 'mm_apiclient.class.php';

$apiKey = 'INSERT APIKEY';// Found at the top of the documentation page

// Instantiate an API client object
$MM_Connector = new MM_Connector(
        $apiKey, 
        'https://mm.inmobile.dk', // Server root address
        'http://mywebsite.com/example/messagestatus' // Optional for status callbacks
        );

/*
Prepare some messages to be sent. You can repeat this step multiple times
to send multiple messages in a single http call
*/
$msg = new MM_Message(
        'Hello world', // Message text
        array('4512345678'), // Msisdn (phonenumber with countrycode) for the receiver
        '1245'); // The sendername. This could be a phonenumber or your company name

// Optionally a send time can be specified
// $msg->setSendTime('2020-01-20 18:30:00');

// Optionally flash can be specified
// $msg->setFlash(true);

// Optionally an expire time in seconds can be specified
// $msg->setExpireInSeconds(60);

// Optionally respect blacklist can be specified
// $msg->setRespectBlacklist(false);

$MM_Connector->addMessage($msg);

/* Send the payload */
$success = $MM_Connector->send();
if($success)
{
    /* Read the message ids */
    $messageIds = $MM_Connector->getMessageIds();

    /*
    $messageIds contains an array with message id's and its corresponding msisdn

    Example:
    Array
    (
        [0] => Array
            (
                [msisdn] => 4512345678
                [id] => fd0ab916-e960-49d0-bb2e-361771818393
            )
    )
    */

    print_r($messageIds);
    echo 'Success!';
}
else
{
    /*
    This function returns the remote error code
    */
    print_r($MM_Connector->getError());
    echo 'Error!';
}
?>