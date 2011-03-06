<?php

/**
 * Standard Error Class
 * 
 * @author christian.hofbauer
 * @package org.chof.model.import
 */
class Chof_Model_Import_Error extends Chof_Model_Import_Event
{
  protected $position  = array();
  protected $eClass    = E_ERROR;
  
  function __construct($title, $msg, $eClass, $position = null, $object = null)
  #****************************************************************************
  {
    $this->setPosition($position)
         ->setErrorClass($eClass);
    
    parent::__construct($title, $msg, $object);
  }

  #-----------------------------------------------------------------------------
  # Presentation Methods
  #-----------------------------------------------------------------------------
  
  public function toArray($datetimefmt = '')
  #****************************************************************************
  {
    $a = parent::toArray($datetimefmt);
    return array_merge($a,
                       array('title' => $this->title,
                             'msg'   => $this->msg));
  }
  
  #-----------------------------------------------------------------------------
  # Getter & Setter
  #-----------------------------------------------------------------------------
  
  public function getPosition($part = '')
  #****************************************************************************
  {
    if ($part<>'')
      return (isset($this->position[$part])) ? $this->position[$part] : null;
    else
      return $this->position;
  }
  
  public function setPositionPart($part, $posid)
  #****************************************************************************
  {
    if ($part<>'')
      $this->position[$part] = $posid;
      
    return $this;
  }
  
  public function setPosition($position)
  #****************************************************************************
  {
    $this->position = (is_array($position)) ? $position : array(); 
    return $this;
  }
  
  public function getErrorClass()
  #****************************************************************************
  {
    return $this->eClass;
  }
  
  public function isAsCriticalAs($eclass)
  #****************************************************************************
  {
    if ($this->validEClass($eclass))
      return ($this->eClass <= $eclass);
    else
      return false;
  }
  
  public function setErrorClass($eclass)
  #****************************************************************************
  {
    $this->eClass = ($this->validEClass($eclass)) ? $eclass : $this->error;  
    return $this;
  }
  
  #-----------------------------------------------------------------------------
  # Private Functions
  #-----------------------------------------------------------------------------
  
  private function validEClass($eClass)
  #****************************************************************************
  {
    return in_array($eClass,array(E_ERROR, E_WARNING, E_NOTICE));
  }
}
?>