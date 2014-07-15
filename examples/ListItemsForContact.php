<?php
require "../vendor/autoload.php";

/* These are keys from my Trial Account... you won't find much here */
$key = "53c23842e4b0dd8125a7eab9";
$secret = "0UlIeso9k33jAVRvTssDixX8A3i";

$riq = new \nathanabrewer\RelateIQ\RelateIQ($key, $secret);

$contact = $riq->getContact('53c238d7e4b0d0612a7b84bd');

$listItems = $riq->getAllListItemsForContact($contact);
foreach($listItems as $listItem){
    echo "Contact {$contact->getName()} (cid {$contact->id} has a ListItem {$listItem->id} on List {$listItem->listId} {$listItem->getList()->title}\n";
}
