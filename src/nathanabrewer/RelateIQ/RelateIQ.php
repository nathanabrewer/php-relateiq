<?php
/**
 * @author Nathan A Brewer <nathan.a.brewer@dftz.org>
 * @link http://dftz.org/
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 */


namespace nathanabrewer\RelateIQ;

class RelateIQ{

    protected $key = "";
    protected $secret = "";
    private $listId = "";

    protected $apiEndpoint = "https://api.relateiq.com/v2/";
    protected $listData = null;
    protected $data = array();
    private $debug = false;
    private $error = '';

    /**
     * @param null $key
     * @param null $secret
     * @param null $listId
     */
    function __construct($key=null, $secret=null, $listId=null){
        if($key && $secret) Resource\RelateIQConfig::setKey($key, $secret);
        if($listId) $this->listId = $listId;
    }

    /**
     * @param $debug
     */
    public function setDebug($debug){
        $this->debug = $debug;
    }

    /**
     * @return string
     */
    public function getError(){
        return $this->error;
    }

    /**
     * @param null $listId
     * @return RelateIQ
     */
    public static function getObject($listId=null){
        return new self(null, null, $listId);
    }

    /**
     * @param $listId
     */
    public function setListId($listId){
        $this->listId = $listId;
    }

    /**
     * @param $fieldName
     * @param $fieldValue
     */
    public function setFieldValue($fieldName, $fieldValue){
        $fieldId = $this->lookupFieldByName($fieldName);
        $this->data[$fieldId] = $fieldValue;
    }

    /**
     * @param $fieldName
     * @param $fieldOptionText
     */
    public function setFieldOption($fieldName, $fieldOptionText){
        if($this->debug)
            echo "Set Field Option $fieldName, $fieldOptionText\n";
        $fieldId = $this->lookupFieldByName($fieldName);
        $optionId = $this->lookupFieldOptionById($fieldId, $fieldOptionText);
        if($this->debug)
            echo "Resolved Field Id and Option Id $fieldId, $optionId\n";
        $this->data[$fieldId] = $optionId;
    }

    /**
     * @param $name
     * @return bool|int|string
     */
    public function lookupFieldByName($name){
        if(!$this->listData){
            $this->listData = $this->getList();
        }
        foreach($this->listData as $key => $field){
            if($field['name'] == $name) return $key;
        }
        return false;
    }

    /**
     * @param $name
     * @param $optionText
     * @return bool|int|string
     */
    public function lookupFieldOptionByName($name, $optionText){
        return $this->lookupFieldOptionById(    $this->lookupFieldByName($name), $optionText   );
    }

    /**
     * @param $id
     * @param $optionText
     * @return bool|int|string
     */
    public function lookupFieldOptionById($id, $optionText){
        if(!$this->listData){
            $this->listData = $this->getList();
        }
        if(!isset($this->listData[$id]) || !isset($this->listData[$id]['options']) )
            return false;
        foreach($this->listData[$id]['options'] as $key => $option){
            if($option == $optionText) return $key;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getList(){
        $temp = array();
        $results = $this->newGet('lists');
        foreach($results->fields as $field){
            $temp[$field->id]['name'] = $field->name;
            $temp[$field->id]['isMultiSelect'] = $field->isMultiSelect;
            $temp[$field->id]['isEditable'] = $field->isEditable;
            $temp[$field->id]['dataType'] = $field->dataType;
            foreach($field->listOptions as $option)
                $temp[$field->id]['options'][$option->id] = $option->display;
        }
        return $temp;
    }

    /* create contact, return Contact ID */
    /**
     * @param $name
     * @param null $email
     * @param null $phone
     * @param null $address
     * @return bool
     */
    public function createContact($name, $email=null, $phone=null, $address=null){
        $request =      array( 'name' =>   array(array('value'=> $name    )));
        if($email)   $request['email'] =   array(array('value'=> $email   ));
        if($phone)   $request['phone'] =   array(array('value'=> $phone   ));
        if($address) $request['address'] = array(array('value'=> $address ));
        $results = $this->newPost('contacts', array('properties' => $request));

        if(isset($results->errorMessage)){
            $this->error = $results->errorMessage;
            return false;
        }
        if(isset($results->id)) return $results->id;
        return false;
    }

    public function getContact($cid){
        $request = new Resource\RelateIQRequest();
        $contact = Resource\RelateIQContact::createFromResponse( $request->newGet('contacts/'.$cid) );
        print_r($contact);
        return $contact;
    }

    public function getContacts(){
        $request = new Resource\RelateIQRequest($this->key, $this->secret);
        $contact = Resource\RelateIQContact::createFromResponse( $request->newGet('contacts') );
        return $contact;
    }

    /* Alias for creating new listitem entry */
    /**
     * @param $contacts
     * @param null $values
     * @return bool
     */
    public function newListitem($contacts, $values=null){
        return $this->updateListitem(null, $contacts, $values);
    }

    /**
     * @param $id
     * @param $contacts
     * @param null $values
     * @return bool
     */
    public function updateListitem($id, $contacts, $values=null){
        //we might use setFieldValue/ setFieldOption to build the values array, so here...
        if(!$values) $values = $this->data;

        if(!is_array($values)) return false;

        $request = array(
            'listId' =>  $this->listId,
            'contactIds' => (array)$contacts,
            'accountId' => null
        );

        if($id){ $request['id'] = $id;  }

        foreach($values as $k => $v){
            $request['fieldValues'][$k] = array( array("raw" =>  (string)$v));
        }

        if($id){
            $results = $this->newPut('lists/'. $this->listId.'/listitems/'.$id, $request);
        }else{
            $results = $this->newPost('lists/'. $this->listId.'/listitems/', $request);
        }

        if(isset($results->id)){
            echo "return success... ".$results->id."\n";
            return $results->id;
        }
        return false;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function readListItem($id){
        return $this->newGet('lists/'. $this->listId.'/listitems/'.$id);
    }








    /**
     * @param $target
     * @param null $data
     * @return mixed
     */
    private function newPost($target,$data=null){
        return $this->send('POST', $target, $data);
    }

    /**
     * @param $target
     * @param null $data
     * @return mixed
     */
    private function newPut($target,$data=null){
        return $this->send('PUT', $target, $data);
    }

    /**
     * @param $target
     * @param null $data
     * @return mixed
     */
    private function newGet($target,$data=null){
        if($target=="lists") $target .= "/".$this->listId;
        return $this->send('GET', $target, $data);
    }

    /**
     * @param $requestType
     * @param $target
     * @param $data
     * @return mixed
     */
    private function send($requestType, $target, $data){
        $headers = array();
        $ch = curl_init($this->apiEndpoint.$target);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key . ':' . $this->secret);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);

        if($requestType == 'PUT' || $requestType == 'POST'){
            if(!$data) $data = $this->data;
            $payload = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $headers[] = 'Content-Length: '.strlen($payload);
            $headers[] = 'Content-Type: application/json';
        }
        $headers[] = 'Accept: application/json';

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        if($this->debug)
            curl_setopt($ch, CURLOPT_VERBOSE, true);

        $this->data = array();

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $response = explode("\r\n\r\n", $result, 2 + $info['redirect_count']);

        $body = array_pop($response);

        curl_close($ch);
        return json_decode($body);

    }

}
