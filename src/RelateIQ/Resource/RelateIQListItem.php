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
    public function setField($name, $value){
        $field = $this->list->lookupFieldName($name);
        if(!$field)
            return false;
        if(isset($field->id))
        $this->fieldValues[$field->id] = array(array('raw' => $field->resolveFieldValue($value) ));
    }

    public function save(){
        $request = new RelateIQRequest;
        if($this->id){
            return $request->newPut('lists/'.$this->listId.'/listitems/'.$this->id, $this);
        }else{
            return $request->newPost('lists/'.$this->listId.'/listitems/', $this);
        }
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

        $listItem->listId = $list->id;
        $listItem->list = $list;

        return $listItem;
    }

}
