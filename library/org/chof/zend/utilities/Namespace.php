<?php

class Chof_Util_Namespace 
{
  
  protected $namespaces;
  
  function __construct($namespaces = array())
  #*****************************************************************************
  {
    $this->namespaces = array();
    $this->setNamespaces($namespaces);
  }
  
  /**
   * Returns the set of namespaces
   *
   * @return array
   */
  public function getNamespaces()
  #*****************************************************************************
  {
     return $this->namespaces;
  }

  /**
   * Sets new default namespaces
   *
   * @param array|string $namespace
   * @return null
   */
  public function setNamespaces($namespaces)
  #*****************************************************************************
  {
      if (!is_array($namespaces)) {
          $namespaces = array((string) $namespaces);
      }

      $this->namespaces = $namespaces;
  }

  /**
   * Adds a new default namespace
   *
   * @param array|string $namespace
   * @return null
   */
  public static function addNamespaces($namespaces)
  #*****************************************************************************
  {
    if (!is_array($namespaces)) {
        $namespaces = array((string) $namespaces);
    }

    $this->namespaces = array_unique(array_merge($this->namespaces, $namespaces));
  }

  /**
   * Returns true when defaultNamespaces are set
   *
   * @return boolean
   */
  public function hasNamespaces()
  #*****************************************************************************
  {
    return (!empty($this->namespaces));
  }

  public function getClassName($classname)
  #*****************************************************************************
  {
    if ($this->hasNamespaces())
    {
      $classname = ucfirst($classname);
      foreach($this->namespaces as $ns)
      {
        $fullname = $ns.'_'.$classname;
        if (class_exists($fullname))
          return $fullname;
      }
    }

    return false;
  }
}
?>