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
  
  /**
   * @var Chof_Util_Queue_Adapter_Feedbackdb_Error
   */
  protected $_errorTable = null;
  
  private $authSession = false;
  
  public function close()
  {
    $this->_messageTable->getAdapter()->closeConnection();
  }
    
  private function checkAuth()
  //****************************************************************************
  {
    $user = false;
    
    if ($this->authSession)
    {
      $checkAuth = new Chof_Controller_Helper_CheckAuth();
      $user = $checkAuth->direct(array(
          'session' => $this->authSession,
          'autologin' => false,
          'online'    => false
          ));
    }
    
    return $user;
  }
    
  public function __construct($options, Zend_Queue $queue = null)
  //****************************************************************************
  {
    parent::__construct($options, $queue);
    
    $this->_statusTable = new Chof_Util_Queue_Adapter_Feedbackdb_Status(array(
            'db' => $this->_messageTable->getAdapter()
        ));
    
    $this->_errorTable = new Chof_Util_Queue_Adapter_Feedbackdb_Error(array(
            'db' => $this->_messageTable->getAdapter()
        ));    
    
    if (isset($options['authSession']))
    {
      $this->authSession = $options['authSession'];
    }
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
    
    if ($user = $this->checkAuth())
    {
      $status->user_id = $user;
    }
    
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
      if (($status->complete < 100) || ($status === null)) 
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

  public function retrieveErrors(Chof_Util_Queue_Message $message)
  //****************************************************************************
  {
    $errorset = $this->_errorTable->fetchAll('message_id = '.$message->message_id);
    if ($errorset->count() > 0)
    {
      return $errorset->toArray();
    }
    else
    {
      return false;
    }
  }
  
  public function reportError(Chof_Util_Queue_Message $message, 
                              $errorCode, $errorMessage, $localizer)
  //****************************************************************************
  {
    $errorRow = $this->_errorTable->createRow();
    $errorRow->message_id = $message->message_id;
    $errorRow->timeoccured = time();
    $errorRow->errorcode = $errorCode;
    $errorRow->errormessage = substr($errorMessage, 0, 512);
    $errorRow->localizer = substr($localizer, 0, 80);
    
    try 
    {
      $errorRow->save();
    } catch (Exception $e) 
    {
      require_once 'Zend/Queue/Exception.php';
      throw new Zend_Queue_Exception($e->getMessage(), $e->getCode(), $e);
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
  
  public function messageStatusByUser($user)
  //****************************************************************************
  {
    return $this->_statusTable->fetchAll($this->_statusTable->select()
      ->where('user_id = ?', $user));
  }
}

?>