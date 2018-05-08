<?php

class Chof_Util_Queue_Message extends Zend_Queue_Message
{
  /**
   * Provides an update on the percentage of completion of this task
   * 
   * @param integer $complete
   */
  
  private $complete;
  
  private $status;
  
  private $adapter;
  
  public function __construct(array $options = array())
  //****************************************************************************
  {
    parent::__construct($options);
    $this->complete = 0;
    $this->adapter = null;
  }

  /**
   * Set the completion state and update the message status
   * @param integer $value
   */
  public function setCompletion($value)
  //****************************************************************************
  { 
    $this->complete = ($value < 0) ? 0 : (($value > 100) ? 100 : $value);
    $this->updateCompletion();
  }
  
  /**
   * Retrieve the completion status from the database
   * @param integer $value
   */
  public function getCompletion()
  //****************************************************************************
  { 
    $this->retrieveCompletion();
    return array($this->complete, $this->status);
  }
  
  protected function updateCompletion()
  //****************************************************************************
  {
    $this->__getAdapter()->updateStatus($this, $this->complete);
  }
  
  protected function retrieveCompletion()
  //****************************************************************************
  {
    list($this->complete, $this->status) = 
      $this->__getAdapter()->retrieveStatus($this); 
   
  }
  
  private function __getAdapter()
  //****************************************************************************
  {
    if ($this->adapter === null)
    {
      $this->adapter = $this->getQueue()->getAdapter();
    }
    
    return $this->adapter;
  }
}

?>