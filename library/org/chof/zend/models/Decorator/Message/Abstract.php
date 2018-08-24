<?php

/*
 * An abstract class defining the interface for message format decorators like json or xml
 */

abstract class Chof_Model_Decorator_Message_Abstract extends Chof_Model_Decorator_Abstract
{  
  function __construct($model, $links = array())
  #*****************************************************************************
  {
    if (!method_exists($model, 'schema'))
    {
      throw new Zend_Exception(
        'The model must be decorated with a Chof_Model_Schema!');
    }
    
    parent::__construct($model);
  }
  
  /*
   * retrieves the data of the underlying model instance in the message format 
   * implemented by the concrete class
   */
  abstract public function compose();
  
  /**
   * Reads a message and updates the underlying model implementation
   * @param mixed $messageData
   */
  abstract public function decompose(array $messageData);

  /**
   * encodes the messagedata given as an array into the correct message format
   * 
   * @param array $messageData the message to be encoded as an array
   */
  abstract public function encode(array $messageData);
  
  /**
   * Decodes the message into a data array
   * 
   * @param String or object $message
   */
  abstract public function decode($message);
  
  /**
   * @return string the content type string for the message 
   */
  abstract public function getContentType();

  /**
   * Each format can specify specific response headers. Default is nothing
   * 
   * @return array an array of additional content header
   */
  public function getResponseHeaders()
  {
    return array();
  }
  
  /**
   * Retrieves a schema of the message format in an appropriate format.
   * 
   * e.g. retrieves XSD for xml or JSON-Schema for json data
   */
  public function sendSchema($schema = '')
  #*****************************************************************************
  {
    $schema = ($schema != '') ? $schema : $this->model->schema();
    return $this->encode($schema);
  }
}