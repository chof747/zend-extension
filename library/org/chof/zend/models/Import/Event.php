<?php

/**
 * Base class for importing events
 * 
 * Importing events are used by the interactive import module of the org.chof 
 * extension to the Zend Framework.
 * 
 * Import events store a title e.g. "Object A", together with a message e.g.
 * "Object identified". These title and message have to be set during the
 * creation of the object.
 * 
 * Currently there are two different desendants defined:
 *  - data elements (instantiated or derived from {@link Chof_Model_Import_Data})
 *  - errors (instantiated or derived from {@link Chof_Model_Import_Error})
 * 
 * @author christian.hofbauer
 * @package org.chof.model.import
 */
class Chof_Model_Import_Event implements Chof_Model_Interface_Arrayable
{
  protected $title  = '';
  protected $msg    = '';
  protected $object = null;
  
  function __construct($title, $msg, $object = null)
  #****************************************************************************
  {
    $this->title  = $title;
    $this->msg    = $msg;
    $this->object = $object;
  }
  
  #-----------------------------------------------------------------------------
  # Presentation Methods
  #-----------------------------------------------------------------------------
  
  /**
   * transfers the data elements of the event to an array.
   * 
   * This method is overwritten by all sub classes of event to add the specific
   * elements of the subclasses. The toArray is used by severa
   * 
   * @return array containing the values of the event
   */
  public function toArray($datetimefmt = '')
  #****************************************************************************
  {
    return array('title' => $this->title,
                 'msg'   => $this->msg);
  }

  #-----------------------------------------------------------------------------
  # Getter & Setter
  #-----------------------------------------------------------------------------
  
  /**
   * retrieves the title of the event
   * 
   * @return string title of the event
   */
  public function getTitle()
  #****************************************************************************
  {
    return $this->title;
  }
  
  /**
   * retrieves the message associated with the event
   * 
   * @return string a message text associated with the event
   */
  public function getMessage()
  #****************************************************************************
  {
    return $this->msg;
  }  
  
  public function getObject()
  #****************************************************************************
  {
    return $this->object;
  }
  
  public function setObject($object)
  #****************************************************************************
  {
    $this->object   = (is_object($object))   ? $object   : null;
    return $this;
  }
  
}
?>