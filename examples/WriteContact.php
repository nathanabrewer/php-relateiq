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

$myApplicationList = $riq->getList('53b1bf43e4b0f0eb6bc6ce74');

//foreach($myApplicationList->getListItems() as $listItem){
//    echo $listItem->id."\n";
//}


/*
    we can getContact, or we can getContact without an API call... i.e. just a container for simple updates..
    saves an API call
*/
$listItem = $myApplicationList->getListItem('53c23b82e4b0d0612a7b85c5');

$listItem->setField('Status', 'Active');
$listItem->setField('Drinks', array('Tea', 'Coffee', 'Water'));
$listItem->save();

//If I want to avoid an API call, because I already have a contact....
$listItem = $myApplicationList->listItemContainer();
$listItem->setContact($contact);
$listItem->setField('Status', 'Active');
$listItem->save();






