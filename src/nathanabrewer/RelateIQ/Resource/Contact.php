<?php

namespace nathanabrewer\RelateIQ\Resource;

Class RelateIQContact{

    public $id;
    public $modifiedDate;
    public $properties;

    function __construct($name=null, $email=null, $phone=null, $address=null){
        $contactProperty = new RelateIQContactProperties;
        if($name) foreach((array)$name as $v)
            $contactProperty->add('name', (is_object($v)) ? $v->value : $v);
        if($email) foreach((array)$email as $v)
            $contactProperty->add('email', (is_object($v)) ? $v->value : $v);
        if($email) foreach((array)$phone as $v)
            $contactProperty->add('phone', (is_object($v)) ? $v->value : $v);
        if($email) foreach((array)$address as $v)
            $contactProperty->add('address',(is_object($v)) ? $v->value : $v);
        $this->properties = $contactProperty;
    }

    public function save(){
        $request = new RelateIQRequest;
        if($this->id){
            $request->newPut('contacts/'.$this->id, $this);
        }else{
            $request->newPost('contacts', $this);
        }
    }

    public static function createFromResponse($response){
        if(isset($response->objects)){
            $objects = array();
            foreach($response->objects as $object){
                $objects[] = self::createFromResponseObject($object);
            }
            return $objects;
        } else {
            return self::createFromResponseObject($response);
        }
    }

    public static function createFromResponseObject($response){
        $contact = new RelateIQContact(
            isset($response->properties->name) ? $response->properties->name : null,
            isset($response->properties->email) ? $response->properties->email : null,
            isset($response->properties->phone) ? $response->properties->phone : null,
            isset($response->properties->address) ? $response->properties->address : null
        );
        $contact->modifiedDate = $response->modifiedDate;
        $contact->id = $response->id;
        return $contact;
    }


}

Class RelateIQContactProperties{
    public $name = array();
    public $email = array();
    public $phone = array();
    public $address = array();

    public function add($property, $value){
        if(property_exists($this, $property)){
            $this->{$property}[] = array('value' => $value);
            return true;
        }
        return false;
    }

    public function get($property){
        if(property_exists($this, $property)){
            $return = array();
            foreach($this->$property as $v)
                $return[] = $v['value'];
            return $return;
        }
        return false;
    }

    public function remove($property, $value){
        if(property_exists($this, $property)){
            foreach($this->$property as $index => $v){
                if($v['value'] == $value){
                    array_splice($this->$property, $index, 1);
                    return true;
                }
            }
        }
        return false;
    }

}