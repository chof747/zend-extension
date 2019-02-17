<?php
class Chof_Model_ChangeObjectImpl
  implements Chof_Model_Interface_ChangeObject
{
  private $_changeTrackers = array();
  private $changed = false;
  
  /**
   * Initializes the change object structures (list of change trackers) 
   */
  function __construct()
  //***************************************************************************
  {
    $this->_changeTrackers = array();
  }
  
  /**
   * Releases the list of change trackers upon destruction 
   */
  function __destruct()
  //***************************************************************************
  {
    for ($i = 0; $i < count($this->_changeTrackers); $i++) 
      unset($this->_changeTrackers[$i]); 	
  }
  

  /**
   * @see Chof_Model_Interface_ChangeObject::registerChangeTracker($tracker) 
   */
  public function registerChangeTracker(Chof_Model_Interface_ChangeTracker $tracker)
  //***************************************************************************
  {
    if ($tracker instanceof Chof_Model_Interface_ChangeTracker)
    {
      $this->_changeTrackers[] = $tracker;
      return true;
    }
    else
    return false;
  }

  /**
   * @see Chof_Model_Interface_ChangeObject::unregisterChangeTracker($tracker) 
   */
  public function unregisterChangeTracker(Chof_Model_Interface_ChangeTracker $tracker)
  {
    for ($i = 0; $i < count($this->_changeTrackers); $i++) 
      if ($this->_changeTrackers[$i] === $tracker) 
        unset($this->_changeTrackers[$i]); 	
  }
  
  /*
   * @see Chof_Model_Interface_Changeable::initialize()
   */
  public function initialize()
  //****************************************************************************
  {
    $this->changed = false;
  }
  
  /*
   * @see Chof_Model_Interface_Changeable::hasChanged()
   */
  public function hasChanged()
  //****************************************************************************
  {
    return $this->changed;
  }

  /**
   * Notifies all change trackers from changes within this esemble
   */
  protected function notifyChange()
  //****************************************************************************
  {
    $this->changed = true;
    if (count($this->_changeTrackers)>0)
      foreach($this->_changeTrackers as $tracker)
        $tracker->elementChanged($this);
  }
  
}
?>
