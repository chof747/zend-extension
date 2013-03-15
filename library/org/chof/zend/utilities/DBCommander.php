<?php

class Chof_Util_DBCommander 
{
  static private $instance = null;
  
  private $transactionCount = 0;
  private $dbhandle = null;
  
  private function __construct()
  #*****************************************************************************
  {
    $front = Zend_Controller_Front::getInstance();
    $db = Zend_Registry::get('db');
    $this->dbHandle = Zend_Db::factory($db);
    $this->transactionCount = 0;
  }
  
  /**
   * retrieves the singelton instance of the DBCommander
   */
  static public function getInstance()
  #*****************************************************************************
  {
    if (Chof_Util_DBCommander::$instance === null)
    {
      Chof_Util_DBCommander::$instance = new Chof_Util_DBCommander();
    }
    
    
    return Chof_Util_DBCommander::$instance;
  }
  
  public function executeRoutine($routine, $params = null)
  #*****************************************************************************
  {
    $callparams = array();
    
    if (is_array($params))
    {
      foreach($params as $type => $value)
      {
        $type = strtolower($type);
        $param = '';
        
        if (($type == 'string') || ($type == 'datatime'))
          $param = "'$value'";
        else
          $param = $value;
        
        array_push($callparams, $param);
      }
    }
      
    $this->dbHandle->getConnection();
    $call = $this->dbHandle->prepare("CALL $routine(".join(', ', $callparams).');');
    $result = $call->execute();
    $this->dbHandle->closeConnection();    
    return $result;
  }
  
  /**
   * Enables careless beginTransaction and commits via a transaction counter
   */
  public function beginTransaction()
  #*****************************************************************************
  {
    if ($this->transactionCount == 0)
    {
      $this->dbHandle->beginTransaction();
    }
    
    ++$this->transactionCount;
    //echo $this->transactionCount;
    return $this;
  }
  
  public function commit()
  #*****************************************************************************
  {
    if ($this->transactionCount<=0) return;
    
    --$this->transactionCount;
    //echo $this->transactionCount;
    if($this->transactionCount == 0)
    {
      $this->dbHandle->commit();
    }
    
    return $this;
  }

  public function rollback()
  #*****************************************************************************
  {
  	if ($this->transactionCount > 0)
  	{
	    $this->dbHandle->rollback();
	    $this->transactionCount = 0;
  	}
  	    
    return $this;
  }
  
  public function db()
  #*****************************************************************************
  {
     $this->dbHandle->getConnection();
     return $this->dbHandle; 
  }
  
  public function quote($value)
  {
  	//$this->dbhandle->getConnection();
  	return $this->dbHandle->quote($value);
  }
}

?>