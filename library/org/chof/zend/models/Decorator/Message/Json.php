<?php

/*
 * Decorator handling message in/output in json format and providing the json schema
 */

class Chof_Model_Decorator_Message_Json extends Chof_Model_Decorator_Message_Abstract
{
  /**
   * @see Chof_Model_Decorator_Message_Abstract::getMessage()
   */
  public function compose()
  #*****************************************************************************
  {
    $schema = $this->model->schema();
    $data = $this->model->toArray('json');
    
    $message = array();
    
    if ((isset($schema['properties'])) && (is_array($schema['properties'])))
    {
      foreach($schema['properties'] as $attribute => $definition)
      {
        $message[$attribute] = $data[$attribute];
      }
    }
    
    if ((isset($schema['links'])) && (is_array($schema['links'])))
    {
      foreach($schema['links'] as $link)
      {
        if ((isset($link['rel'])) && isset($link['href']) && isset($link['name']))
        {
          $message[$link['rel']] = array(
          	'$ref' => str_replace("{".$link['rel']."}", $data[$link['rel']], $link['href']),
     		"id"   => $data[$link['rel']]);
          
          $name = '';
          
          $getter = 'get'.$link['name'];
          if ($linkObj = $this->model->$getter())
          {
            $linkObj = new Chof_Model_Decorator_Schema($linkObj);
            $schema = $linkObj->schema();
            $label  = 'get'.ucfirst(strtolower($schema['label']));
            
            $name = $linkObj->$label();
          }
          else
          {
            if ((isset($link['required']) && ($link['required'])))
            {
              throw new Zend_Exception("The linked object ".$link['name']." is missing but required!");
            }
          }
          
          $message[$link['rel']]['name'] = $name;
        }
      }
    }
    
    return $message;
  }
  
  /**
   * @see Chof_Model_Decorator_Message_Abstract::setMessage()
   */
  public function decompose(array $messageData)
  #*****************************************************************************
  {
    $schema = $this->model->schema();
    $data = array();
    
    if ((isset($schema['properties'])) && (is_array($schema['properties'])))
    {
      foreach($schema['properties'] as $attribute => $definition)
      {
         if (isset($messageData[$attribute]))
         {
           $data[$attribute] = $messageData[$attribute];
           if ($definition['type'] == 'number')
           {
             $data[$attribute] =(float) $data[$attribute];
           }
         }
         else
         {
           $data[$attribute] = null;
         }
      }
    }
    
    if ((isset($schema['links'])) && (is_array($schema['links'])))
    {
      foreach($schema['links'] as $link)
      {
        if ((isset($link['rel'])) && isset($link['href']))
        {
          $attribute = $link['rel'];
          if (isset($messageData[$attribute]))
          {
            $linkdata = $messageData[$attribute];
            
            $ref = str_replace('{'.$link['rel'].'}', "", str_replace('\\', '', $link['href']));
            
            if ((is_numeric($linkdata)) || (is_string($linkdata)))
            {
            	$data[$attribute] = $linkdata;
            }
            else if (is_array($linkdata))
            {
	            $data[$attribute] = (isset($linkdata['id'])) ? $linkdata['id'] : 
	                                ((isset($linkdata['$ref'])) 
	                                  ? str_replace($ref, "", $linkdata['$ref']) 
	                                  : null);

	            $data[$attribute] = (empty($data[$attribute])) 
	                                  ? null 
	                                  : $data[$attribute];  
            }
            else
            {
            	$data[$attribute] = null;
            }
          }
        }
      }
    }
    
    $this->model->fromArray($data);
  }
  
  public function encode(array $messageData)
  #*****************************************************************************
  {
    return Zend_Json::encode($messageData);
  }
  
  public function decode($message)
  #*****************************************************************************
  {
    return Zend_Json::decode($message);  
  }
  
  
  public function getContentType()
  {
    return "application/json";
  }
}

?>