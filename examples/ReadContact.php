<?php
require "../vendor/autoload.php";


/* These are keys from my Trial Account... you won't find much here */
$key = "53c23842e4b0dd8125a7eab9";
$secret = "0UlIeso9k33jAVRvTssDixX8A3i";

$riq = new \nathanabrewer\RelateIQ\RelateIQ($key, $secret);

//get all contacts
$contacts = $riq->getContacts();

foreach($contacts as $contact){
    echo "\n\nContactId: {$contact->id}\n";

    foreach($contact->properties->get('name') as $name)
        echo "Name: $name\n";

    echo "Modified: {$contact->modifiedDate}\n";
    echo "Email Addresses: ";
    echo implode(", ", $contact->properties->get('email'));
    echo "\n";

}


$contact = $riq->getContact('53c238d7e4b0d0612a7b84bd');

