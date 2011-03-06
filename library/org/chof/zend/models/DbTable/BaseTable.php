<?php
class Chof_Model_DbTable_BaseTable extends Zend_Db_Table_Abstract 
{
  /**
   * Returns the field definition of the primary key
   * 
   * @return the field names comprising the primary key
   */
  public function getPrimaryKey()
  //****************************************************************************
  {
    if ((is_array($this->_primary)) && (count($this->_primary) == 1 ))
    {
      $this->_primary = array_shift($this->_primary);
    }
      
    return $this->_primary;
  }
  
  public function getTableName()
  {
    return $this->_name;
  }
  
  public function _setupDatabaseAdapter()
  {
     $this->_db = Chof_Util_DBCommander::getInstance()->db();
  }
}