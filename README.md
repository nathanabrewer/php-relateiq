php-relateiq
============

Simple class to quickly read and write stuff in RelateIQ. Use at your own risk

Laravel4 add to composer

    "require": {
            "nathanabrewer/php-relateiq": "0.0.*"
    }

In you config/app.php add to your aliases array

    'RelateIQ'          =>  '\nathanabrewer\RelateIQ\RelateIQ'

A quick look, working with a contact
    $riq = new RelateIQ($key, $secret);
    $contact = $riq->getContact('53c238d7e4b0d0612a7b84bd');
    $contact->properties->remove('email', 'nathan.a.brewer@gmail.com');
    $contact->properties->add('email', 'nathan.a.brewer@dftz.org');
    $contact->save();

Now I need to modify some stuff in a list for a contact... ohh, what list was that?
    $lists = $riq->getLists();
    foreach($lists as $list){
        echo "{$list->id} -- {$list->title}\n";
    }

Still not sure... hmm
    $listItems = $riq->getAllListItemsForContact($contact);
    foreach($listItems as $listItem){
        echo "Contact {$contact->getName()} (cid {$contact->id} has a ListItem {$listItem->id} on List {$listItem->listId} {$listItem->getList()->title}\n";
    }

Ah, here we go
    $listItem->setField('Status', 'Active');
    $listItem->setField('Drinks', array('Tea', 'Coffee', 'Water'));
    $listItem->save();

Sorry for the lame documentation... its just a start
