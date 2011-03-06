<?php

class Chof_Util_ItemNotFoundException extends Zend_Exception
{
  protected $id = 0;
  protected $type = '';
  
  function __construct($id, $type)
  #*****************************************************************************
  {
    $this->id = $id;
    $this->type = $type;
  }
  
  public function getId()
  #*****************************************************************************
  {
    return $this->id;
  }
  public function getType()
  #*****************************************************************************
  {
    return $this->type;
  }
}
?>