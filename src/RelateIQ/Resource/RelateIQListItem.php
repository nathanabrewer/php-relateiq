<?php

namespace nathanabrewer\RelateIQ\Resource;

Class RelateIQListItem{

    public $id;
    public $modifiedDate;
    public $listId;
    private $list;
    public $accountId;
    public $contactIds = array();
    public $name;
    public $fieldValues = array();

    public function json(){
        if(!$this->name)       unset($this->name);
        if(!$this->id)         unset($this->id);
        if(!$this->accountId)  unset($this->accountId);
        unset($this->modifiedDate);
        unset($this->createdDate);
        return json_encode($this);
    }


    //not supporting multiselect yet i guess....
    public function setField($name, $values){
        $field = $this->list->lookupFieldName($name);
        if(!$field)
            return false;
        //should check if field is multiSelect to throw an error.
        if(isset($field->id)){
            foreach((array)$values as $v)
            $this->fieldValues[$field->id][] = array('raw' => $field->resolveFieldValue($v) );
        }
    }

    public function save(){
        $request = new RelateIQRequest;
        if($this->id){
            return $request->newPut('lists/'.$this->listId.'/listitems/'.$this->id, $this);
        }else{
            return $request->newPost('lists/'.$this->listId.'/listitems/', $this);
        }
    }
    public function setList(RelateIQList $list){
        $this->list = $list;
        $this->listId = $list->id;
    }
    public function getList(){
        return $this->list;
    }
    public function setContact(RelateIQContact $contact){
        $this->contactId = array( $contact->id );
    }

    public static function fetch($list, $listItemId){
        $request = new RelateIQRequest();
        $listItem = self::handleResponse( $list, $request->newGet('lists/'.$list->id.'/listitems/'.$listItemId) );
        return $listItem;
    }

    public static function fetchAll($list){
        $request = new RelateIQRequest();
        $listItems = self::handleResponse( $list, $request->newGet('lists/'.$list->id.'/listitems/') );
        return $listItems;
    }

    public static function fetchContacts($list, $contacts){
        $request = new RelateIQRequest();
        $contactIds = array();
        if(is_array($contacts)){
            foreach((array)$contacts as $contact){
                $contactIds[] = (is_object($contact)) ? $contact->id : $contact;
            }
        }else{
            $contactIds[] = (is_object($contacts)) ? $contacts->id : $contacts;
        }
        $listItems = self::handleResponse( $list, $request->newGet('lists/'.$list->id.'/listitems/?_ids='.implode(',', $contactIds)) );
        return $listItems;
    }


    public static function handleResponse($list, $response){
        if(isset($response->objects)){
            $objects = array();
            foreach($response->objects as $object){
                $objects[] = self::parseResponseObject($list, $object);
            }
            return $objects;
        } else {
            return self::parseResponseObject($list, $response);
        }
    }

    public static function parseResponseObject($list, $response){
        $listItem = new self;
        $listItem->id = $response->id;
        $listItem->modifiedDate = $response->modifiedDate;
        $listItem->createdDate = $response->createdDate;
        $listItem->accountId = $response->accountId;
        $listItem->contactIds = $response->contactIds;
        foreach($response->fieldValues as $key => $fieldValue){
            $listItem->fieldValues[$key] = $fieldValue;
        }

        $listItem->setList($list);

        return $listItem;
    }

}
