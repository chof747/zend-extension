<?php

/**
 * Base class for complex message return types
 * 
 * @author chris
 * @package org.chof.model
 */
class Chof_Model_BaseType 
{
  public static $STRINGIFY_XMLLIKE = 0;
  public static $STRINGIFY_JSON = 1;
  /**
   * Standard constructor for base types. 
   * 
   * The constructor receives an array in the style of options and passes them
   * to the setOptions method to initialize the objects with values provided by
   * the client.
   * 
   * @param array $options 
   */
  function __construct(array $options = null)
  {
    if (is_array($options))
      $this->setOptions($options);
  }
  
  /**
   * Set object state by an option style array
   *
   * @param  array $options in the form of "param" => value
   * @return Chof_Model_BaseType the model with the new parameters set
   */
  private function setOptions(array $options)
  #****************************************************************************
  {
    $vars = array_keys(get_class_vars(get_class($this)));

    foreach ($options as $key => $value) 
    {
      if (in_array($key, $vars))
      {
        $this->$key = $value;
      }
    }
    return $this;
  }
  
  public function __toString()
  #****************************************************************************
  {
    return self::stringify($this);
  }
  
  public function toArray()
  #****************************************************************************
  {
    $representation = array();
    foreach(get_object_vars($this) as $key => $value)
    {
      $representation[$key]= $value;
    }
    
    return $representation;
  }
  
  public static function stringify($obj, $type = 0)
  #****************************************************************************
  {
    $representation = "";
    
    
    switch ($type)
    {
      case (self::$STRINGIFY_XMLLIKE) :
      {
        foreach(get_object_vars($obj) as $key => $value)
        {
         $representation .= "<$key>$value</$key>";
        }
        break;
      }
      case (self::$STRINGIFY_JSON) : 
      {
        $representation = Zend_Json::encode(get_object_vars($obj));
        break;
      }
    }
    
    return $representation;
  }
}
?>