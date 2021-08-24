<?php
/*
To send an overcharged message use the following function
*/

$MM_Connector->addMessage(new MM_Premium_Message(
    'Thankyou for your purchase!', // Text messages
    array('4512345678'), // The receiver
    '1245', // The sendername (Always a shortcode when overcharged message
    '150', // The amount in cents, e.g. 150 for 1,50 DKK
    '1', // The type, Possible types: 1 = Service, 2 = Donation, 3 = Goods
    '45', // The country code
    '1245' // The shortcode
));
?>