<?php

/**
 * This class represents a data element found in an imported entity
 * 
 * It is derived from Chof_Model_Import_Event and adds an $object variable as 
 * well as an unique identifier to the event properties.
 * 
 * In addition to a basic event a data object stores also a unique ID of the
 * data element, a type denominator as well as the objects data.
 * 
 * @author christian
 * @package org.chof.model.import
 * 
 */
class Chof_Model_Import_Data extends Chof_Model_Import_Event
{
  protected $id = 0;
  protected $type = '';
  
  function __construct($id, $object = null, $type = 'general', $title = '')
  #****************************************************************************
  {
    $this->id     = $id;
    $this->type   = $type;
    
    parent::__construct($title, '' ,$object);
  }
  
  #-----------------------------------------------------------------------------
  # Presentation Methods
  #-----------------------------------------------------------------------------
  
  public function toArray($datetimefmt = '')
  #****************************************************************************
  {
    $a = parent::toArray();
    return array_merge($a,
                       array('id'     => $this->id,
                             'type'   => $this->type,
                             'object' => $this->object));
  }

  #-----------------------------------------------------------------------------
  # Getter & Setter
  #-----------------------------------------------------------------------------
  
  public function getId()
  #****************************************************************************
  {
    return $this->id;
  }
  
  public function setId($id)
  #****************************************************************************
  {
    $this->id = $id;
    return $this;
  }
  
  public function getType()
  #****************************************************************************
  {
    return $this->type;
  }
  
  public function setType($type)
  #****************************************************************************
  {
    $this->type = $type;
    return $this;
  }
} 

?>