<?php

namespace nathanabrewer\RelateIQ\Resource;

Class RelateIQList{

    public $id;
    public $modifiedDate;
    public $title;
    public $listType;
    public $fields = array();

    public function getListItem($listItemId){
        return RelateIQListItem::fetch($this, $listItemId);
    }
    public function getListItems(){
        return RelateIQListItem::fetchAll($this);
    }

    public function lookupFieldName($name){
        foreach($this->fields as $field){
            if($field->name == $name)
                return $field;
        }
        return false;
    }

    public static function fetch($listId){
        $request = new RelateIQRequest();
        $contact = self::handleResponse( $request->newGet('lists/'.$listId) );
        return $contact;
    }
    public static function fetchAll(){
        $request = new RelateIQRequest();
        $contact = self::handleResponse( $request->newGet('lists') );
        return $contact;
    }

    public static function handleResponse($response){
        if(isset($response->objects)){
            $objects = array();
            foreach($response->objects as $object){
                $objects[] = self::parseResponseObject($object);
            }
            return $objects;
        } else {
            return self::parseResponseObject($response);
        }
    }

    public static function parseResponseObject($response){
        $list = new self;
        $list->id = $response->id;
        $list->title = $response->title;
        $list->listType = $response->listType;
        foreach($response->fields as $fieldResponse){
            $list->fields[] = new RelateIQListField($fieldResponse);
        }

        return $list;
    }

}

Class RelateIQListField{
    public $id;
    public $name;
    public $listOptions = array();

    function __construct($field){
        $this->id = $field->id;
        $this->name = $field->name;
        $this->isMultiSelect = $field->isMultiSelect;
        $this->isEditable = $field->isEditable;
        $this->dataType = $field->dataType;
        $this->listOptions = $field->listOptions;
    }
    public function resolveFieldValue($input){
        if(!isset($this->listOptions) || count($this->listOptions) < 1) return $input;

        foreach($this->listOptions as $option){
            if($option->display == $input) return (string)$option->id;
        }
    }


}

Class RelateIQListFieldOptions{
    public $id;
    public $display;
}