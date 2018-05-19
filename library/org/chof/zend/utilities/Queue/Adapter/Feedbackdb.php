<?php

class Chof_Util_Queue_Adapter_Feedbackdb extends Zend_Queue_Adapter_Db
{
    /**
     * @var Chof_Util_Queue_Adapter_Feedbackdb_Status
     */
    protected $_statusTable = null;

    /**
     * @var Zend_Db_Table_Row_Abstract
     */
    protected $_statusRow = null;
    
    
  public function __construct($options, Zend_Queue $queue = null)
  //****************************************************************************
  {
    parent::__construct($options, $queue);
    
    $this->_statusTable = new Chof_Util_Queue_Adapter_Feedbackdb_Status(array(
            'db' => $this->_messageTable->getAdapter()
        ));
  }
  
  /**
   * 
   * @see Zend_Queue_Adapter_DB::send()
   */
  public function send($message, Zend_Queue $queue = null)
  //****************************************************************************
  {
    $msg = parent::send($message, $queue);
    
    //setup the status of the new message
    $status = $this->_statusTable->createRow();
    $status->message_id = $msg->message_id;
    $status->created = $msg->created;
    $status->title = $msg->getTitle();
    
    try {
      $status->save();
    } catch (Exception $e) {
      require_once 'Zend/Queue/Exception.php';
      throw new Zend_Queue_Exception($e->getMessage(), $e->getCode(), $e);
    }
    
    return $msg;
  } 
  
  /**
   * Changes the status of the message to accepted and sets the accepted 
   * timestamp
   * 
   * @see Zend_Queue_Adapter_DB::receive()
   */
  public function receive($maxMessages = null, $timeout = null, Zend_Queue $queue = null)
  //****************************************************************************
  {
    $timestmp = time();
    $msgSet = parent::receive($maxMessages, $timeout, $queue);
    
    foreach($msgSet as $msg)
    {
      $this->changeStatus($msg, function(&$status) 
      {
        $status->accepted = time();
        $status->status = 'accepted';
      });
    }
    
    $msgSet->rewind();
    return $msgSet;
  }
  
  /**
   * If a message is deleted, the adapter sets the status to 'closed'
   * 
   * @see Zend_Queue_Adapter_DB::deleteMessage()
   */
  public function deleteMessage(Zend_Queue_Message $message)
  //****************************************************************************
  {
    $this->changeStatus($message, function(&$status) {
      $status->closed = time();
      if (($status->complete != 100) || ($status === null)) 
      {
        $status->status = 'terminated';
      }
      else
      {
        $status->status = 'closed';
      }
    });    
    return parent::deleteMessage($message);
  }
  
  public function updateStatus(Chof_Util_Queue_Message $message, $complete)
  //****************************************************************************
  {
    $this->changeStatus($message, function(&$status) use ($complete) {
      $timestmp = time();
      if ($status->firstupdate === null)
      {
        $status->firstupdate = $timestmp;
      }
      $status->latestupdate = $timestmp;
      $status->complete = $complete;
      if ($complete >= 100)
      {
        $status->status = 'done';
      }
      else if ($complete <= 0)
      {
        $status->status = 'accepted';
      }
      else
      {
        $status->status = 'working';
      }
    });  
  }
  
  public function retrieveStatus(Chof_Util_Queue_Message $message)
  //****************************************************************************
  {
    $statusset = $this->_statusTable->find($message->message_id);
    if ($statusset->count() == 1)
    {
      return array(
        $statusset->current()->complete,
          $statusset->current()->status);
    }
    else
    {
      return false;
    }
  }
  
  private function changeStatus($message, $callback)
  //****************************************************************************
  {
    $statusset = $this->_statusTable->find($message->message_id);
    foreach ($statusset as $status)
    {
      $callback($status);
    
      try {
        $status->save();
      } catch (Exception $e) {
        require_once 'Zend/Queue/Exception.php';
        throw new Zend_Queue_Exception($e->getMessage(), $e->getCode(), $e);
      }
    }
  }
}

?>