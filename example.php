<?php

require_once('php-relateiq.php');

/* These are keys from my Trial Account... you won't find much here */

$key = "53c23842e4b0dd8125a7eab9";
$secret = "0UlIeso9k33jAVRvTssDixX8A3i";
$listId = "53b1bf43e4b0f0eb6bc6ce74";

$riq = new RelateIQ($key, $secret);

//print_r($riq->getList());

$contactId = $riq->createContact("Nathan Brewer", "nathan.a.brewer@dftz.org", "970-205-9321");
// $riq->createContact($name, $email, $phone, $address);

if(!$contactId)
    die("Error: ".$riq->error);

echo "Created Contact: $contactId\n";

// If we have a contactId, then lets update and/or add a List Item
// Make sure This list is shared with the Integration API Key that you setup!
$riq->setListId($listId);

// Lookup Field by its Name, and Option
// This works with List type, Field name and Option name must match exactly
$riq->setFieldOption('Status', 'Ready');

//Lookup Field by its Name, set value
$riq->setFieldValue('OS', "Linux");
$riq->setFieldValue('Distro', "Ubuntu");
$riq->setFieldValue('Number Value', 500);

// Create new List Item (Again, Make sure you Shared this list with your API Integration!
    $listItemId = $riq->newListitem($contactId);
    echo "Created New List Item Entry: $listItemId\n";

// Now you need to make changes later?
    $riq = new RelateIQ($key, $secret);
    $riq->setListId($listId);
    $riq->setFieldValue('Distro', "CentOS");

    $riq->updateListitem($listItemId, $contactId);

// In your List item, you will see the Item was Created, and then that Distro was updated later

