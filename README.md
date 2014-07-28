php-relateiq
============

Simple class to quickly read and write stuff in RelateIQ. I do not have anything that is in place for Accounts at this moment, only simple Contact and List/ListItem stuff.

Install with composer

    "require": {
            "nathanabrewer/php-relateiq": "0.0.*"
    }

Using Laravel4? Me too! ....In you config/app.php add to your aliases array

    'RelateIQ'          =>  '\nathanabrewer\RelateIQ\RelateIQ'

Example below will run two API Queries, one GET and one PUT.

    $riq = new RelateIQ($key, $secret);
    $contact = $riq->getContact('53c238d7e4b0d0612a7b84bd');
    $contact->properties->remove('email', 'nathan.a.brewer@gmail.com');
    $contact->properties->add('email', 'nathan.a.brewer@dftz.org');
    $contact->save();

Lookup the available Lists... Make sure the List is shared with you! This is runs a sinle GET Request

    $lists = $riq->getLists();
    foreach($lists as $list){
        echo "{$list->id} -- {$list->title}\n";
    }

Here, Rather than look at all the Lists, I am asking each List for List Items that contain this Contact. This will do a series of API GET requests
- GET Request for all avaliable Lists (If I have not done one yet)
- GET Request per List for all ListItems with contact

    $listItems = $riq->getAllListItemsForContact($contact);
    foreach($listItems as $listItem){
        echo "Contact {$contact->getName()} (cid {$contact->id} has a ListItem {$listItem->id} on List {$listItem->listId} {$listItem->getList()->title}\n";
    }

Alternatively, I could load the List, and then load the List Item. If I load an existing ListItem by its ListItem Id then we don't need to deal with the contactID

    $list = $riq->getList($list_id);
    $listItem = $list->getListItem($list_item_id);

Here I am interacting with my List Item. The idea here is already have the List Schema loaded, so I can define a value by its name. I will let the ListItem object determine if it is a Text/Number/List or Picklist, and set the value for the API call as appropriate.

    $listItem->setField('Status', 'Active');
    $listItem->setField('Drinks', array('Tea', 'Coffee', 'Water'));
    $listItem->save();

