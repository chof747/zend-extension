<?php

class Chof_Model_Decorator_Message_Factory
{
  protected $namespaces;
  
  private function __construct()
  #*****************************************************************************
  {
    $this->namespaces = new Chof_Util_Namespace('Chof_Model_Decorator_Message');
  }
  
  public static function create($model, $format)
  #*****************************************************************************
  {
    $factory = self::getInstance();
    
    if ($msgClass = $factory->namespaces->getClassName($format))
    {
      $decorator = new $msgClass($model);
      return $decorator;
    }
    else
    {
      throw new Zend_Exception("No message decorator for $format registered!");
    }
  }
 
  
  /**
   * Static access functions
   */  
  static private $instance = null;
  
  private static function getInstance()
  {
    if (!self::$instance)
    {
      self::$instance = new Chof_Model_Decorator_Message_Factory();
    }
    
    return self::$instance;
  }
  
  public static function getNamespaces()
  #*****************************************************************************
  {
    return self::getInstance()->namespaces->getNamespaces();
  }

  public static function setNamespaces($namespace)
  #*****************************************************************************
  {
    return self::getInstance()->namespaces->setNamespaces($namespaxces);
  }

  public static function addNamespaces($namespaces)
  #*****************************************************************************
  {
    return self::getInstance()->namespaces->addNamespaces($namespaces);
  }
}

?>