<?php

class Chof_Util_DBCommander 
{
  static private $instance = null;
  
  private $dbhandle = null;
  
  private function __construct()
  #*****************************************************************************
  {
    $front = Zend_Controller_Front::getInstance();
    $db = Zend_Registry::get('db');
    $this->dbHandle = Zend_Db::factory($db);
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
  
  public function beginTransaction()
  #*****************************************************************************
  {
    $this->dbHandle->beginTransaction();
  }
  
  public function commit()
  #*****************************************************************************
  {
    $this->dbHandle->commit();
  }

  public function rollback()
  #*****************************************************************************
  {
    $this->dbHandle->rollback();
  }
  
  public function db()
  #*****************************************************************************
  {
     $this->dbHandle->getConnection();
     return $this->dbHandle; 
  }
}

?>