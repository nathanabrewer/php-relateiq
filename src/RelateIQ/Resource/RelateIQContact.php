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

    public static function fetch($cid){
        $request = new RelateIQRequest();
        $contact = self::handleResponse( $request->newGet('contacts/'.$cid) );
        return $contact;
    }
    public static function fetchAll(){
        $request = new RelateIQRequest();
        $contact = self::handleResponse( $request->newGet('contacts') );
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
