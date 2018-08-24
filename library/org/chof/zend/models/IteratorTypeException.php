<?php

/**
 * Iterator type exception derived from Zend_Exception
 * 
 * The iterator type exception is thrown by child classes of the 
 * Chof_Model_BaseIterator class to indicate that an array without
 * the correct type or an element within this array has been passed to 
 * the iterator. 
 *
 * @package org.chof.model
 */
class Chof_Model_IteratorTypeException extends Zend_Exception
{
  /**#@+
   * 
   * @access private
   */
  
  /**
   * the expected type
   */
  private $expected_type = '';
  /**
   * the found type
   */
  private $found_type = '';
  /**
   * the faulty element
   */
  private $element = null;
  
  /**#@-*/
    
  /**
   * Creates an iterator type exception.
   * 
   * The exception is created with the expected type, the found data type and 
   * the element which has the faulty type and caused the exception
   * 
   * @param $expected_type string a string denoting the expected type
   * @param $found_type string a string denoting the found type
   * @param $element mixed the element which caused the problem
   */
  function __construct($expected_type, $found_type, $element = null)
  #*****************************************************************************
  {
    $this->expected_type = $expected_type;
    $this->found_type = $found_type;
    $this->element = $element;
    
    parent::__construct();
  }
  
  #*****************************************************************************
  # GETTER and SETTER Methods
  #*****************************************************************************
  
  public function getExpectedType()
  #*****************************************************************************
  {
    return $this->expected_type;
  }
  public function getFoundType()
  #*****************************************************************************
  {
    return $this->found_type;
  }

  public function getElement()
  #*****************************************************************************
  {
    return $this->element;
  }
  
}


?>