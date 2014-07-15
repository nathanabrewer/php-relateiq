<?php
/**
 * @author Nathan A Brewer <nathan.a.brewer@dftz.org>
 * @link http://dftz.org/
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 */

namespace nathanabrewer\RelateIQ;
use nathanabrewer\RelateIQ\Resource;

class RelateIQ{

    function __construct($key=null, $secret=null, $listId=null){
        if($key && $secret) Resource\RelateIQConfig::setKey($key, $secret);
        if($listId) $this->listId = $listId;
    }


    public function getList($listId){
        return Resource\RelateIQList::fetch($listId);
    }

    public function getLists(){
        return Resource\RelateIQList::fetchAll();
    }

    public function getContact($cid){
        return Resource\RelateIQContact::fetch($cid);
    }

    public function getContacts(){
        return Resource\RelateIQContact::fetchAll();
    }

    public function newContact($name, $email=null, $phone=null, $address=null){
        return new Resource\RelateIQContact($name, $email, $phone, $address);
    }

}
