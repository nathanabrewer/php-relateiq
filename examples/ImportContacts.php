<?php

require "../vendor/autoload.php";


/* These are keys from my Trial Account... you won't find much here */
$key = "53c23842e4b0dd8125a7eab9";
$secret = "0UlIeso9k33jAVRvTssDixX8A3i";

$riq = new \nathanabrewer\RelateIQ\RelateIQ($key, $secret);

$csv = array_map('str_getcsv', file('surnamereps.csv'));
foreach($csv as $rep){
    $name = $rep[1]." ".$rep[0];
    $name = $rep[1]." ".$rep[0];
    $email = null;
    $phone = null;
    $address = null;

    $contact = $riq->newContact($name, $email, $phone, $address);
    $contact = $contact->save();

    echo "Saved $name as {$contact->id}\n";
}





