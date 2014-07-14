<?php

namespace nathanabrewer\RelateIQ\Resource;

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