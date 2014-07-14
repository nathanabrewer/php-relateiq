<?php

require "../vendor/autoload.php";


/* These are keys from my Trial Account... you won't find much here */
$key = "53c23842e4b0dd8125a7eab9";
$secret = "0UlIeso9k33jAVRvTssDixX8A3i";

$riq = new \nathanabrewer\RelateIQ\RelateIQ($key, $secret);


$contact = $riq->getContact('53c238d7e4b0d0612a7b84bd');

$contact->properties->remove('email', 'nathan.a.brewer@gmail.com');
$contact->properties->add('email', 'nathan.a.brewer@dftz.org');
$contact->save();






