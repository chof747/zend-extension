<?php
/**
 * Exception class to indicate validation errors
 * 
 * 
 * @author chris
 * @package org.chof.zend.model
 */
class Chof_Model_ValidationException extends Zend_Exception
{
  /**
   * the details of errors
   * 
   * is an associative array of the field, causing the error, as key and the
   * error text es value.
   */
  private $details;
  
  /**
   * Constructor for an invalid field exception
   * 
   * @param $field the content of the invalid field
   * @param $fieldname the name of the invalid field
   * @param $code an optional error code
   */
  function __construct($code = -10000)
  #*****************************************************************************
  {
    $this->details = array();
    parent::__construct("Validation Error - see details", $code); 
  }
  
  /**
   * Autothrow detmermines whether the exception is worth to be thrown or not
   * 
   * Since a validation exception is only usefull if any error detail has been 
   * recorded, autoThrow checks if any detail has been recorded and throws
   * itself afterwards. 
   * 
   * @return true if the validation exception has no errors recorded
   */
  public function autoThrow()
  #*****************************************************************************
  {
    if (count($this->details) > 0)
    {
      throw $this;
    }
    else
      return true;
  }  
  
  #-----------------------------------------------------------------------------
  # Validation Details
  #-----------------------------------------------------------------------------
  
  public function getDetails()
  #*****************************************************************************
  {
    return $this->details;
  }
    
  public function addDetail($field, $message, $code)
  #*****************************************************************************
  {
    $this->details[$field] = array('message' => $message, 'code' => $code); 
  }
  
  public function hasError($field)
  #*****************************************************************************
  {
    return isset($this->details[$field]);
  }
  
  public function getError($field)
  #*****************************************************************************
  {
    return ($this->hasError($field)) ? $this->details[$field] : false;
  }
  
  public function getErrorMessage($field)
  #*****************************************************************************
  {
    return ($this->hasError($field)) 
      ? $this->details[$field]['message'] 
      : false;
  }
  
  public function getErrorCode($field)
  #*****************************************************************************
  {
    return ($this->hasError($field)) 
      ? $this->details[$field]['code'] 
      : false;
  }
}