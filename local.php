<?php

require_once('./src/nathanabrewer/RelateIQ/RelateIQ.php');
require_once('./src/nathanabrewer/RelateIQ/Resource/Request.php');
require_once('./src/nathanabrewer/RelateIQ/Resource/ListItem.php');
require_once('./src/nathanabrewer/RelateIQ/Resource/Contact.php');
require_once('./src/nathanabrewer/RelateIQ/Resource/ContactProperties.php');
require_once('./src/nathanabrewer/RelateIQ/Resource/List.php');
require_once('./src/nathanabrewer/RelateIQ/Resource/RelateIQConfig.php');


/* These are keys from my Trial Account... you won't find much here */

//test list
$key = "53c23842e4b0dd8125a7eab9";
$secret = "0UlIeso9k33jAVRvTssDixX8A3i";

$riq = new \nathanabrewer\RelateIQ\RelateIQ($key, $secret);


$lists = $riq->getLists();
    foreach($lists as $list){
        if($list->id == "53b1bf43e4b0f0eb6bc6ce74"){
            echo "{$list->id} -- {$list->title}\n";
            foreach($list->getListItems() as $listItem){
                echo $listItem->json();
                echo "\n\n\nOK SAVE\n";
                $listItem->setField('Distro', 'Good Test');
                echo $listItem->json();
                //$listItem->setField('Status', 'Active');
                $result = $listItem->save();

            }

        }
    }
die();

$contacts = $riq->getContacts();

$someRandomEmail = "nathan.a.brewer@gmail.com";

foreach($contacts as $contact){
    echo "\n\nContactId: {$contact->id}\n";

    foreach($contact->properties->get('name') as $name)
        echo "Name: $name\n";

    echo "Modified: {$contact->modifiedDate}\n";
    echo "Email Addresses: ";
    echo implode(", ", $contact->properties->get('email'));
    echo "\n";

    if(!$contact->properties->remove('email', $someRandomEmail)){
        echo "Was Unable to remove $someRandomEmail from contact... does not exist\n";
    }
    $newEmail = "bogus_email_{$contact->id}@gmail.com";
    if( $contact->properties->add('email', $newEmail) ){
        echo "Adding $newEmail to Contact";
    }
    $contact->save();
}


$contact = $riq->getContact('53c238d7e4b0d0612a7b84bd');
//echo json_encode($contact);
//$contact->properties->addEmail('nathan.a.brewer@gmail.com');
//$contact->save();
