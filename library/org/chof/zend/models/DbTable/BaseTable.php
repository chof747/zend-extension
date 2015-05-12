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
  //****************************************************************************
  {
    return $this->_name;
  }
  
  public function _setupDatabaseAdapter()
  //****************************************************************************
  {
     $this->_db = Chof_Util_DBCommander::getInstance()->db();
  }
  
  public function delete($where)
  //****************************************************************************
  {
    $tbName = (property_exists($this, 'deleteName')) ? $this->deleteName : $this->_name;
    
    $tableSpec = ($this->_schema ? $this->_schema . '.' : '') . $tbName;
        return $this->_db->delete($tableSpec, $where);
  }
  
  public function insert(array $data)
  //****************************************************************************
  {
    $oldname = $this->_name;
    $this->_name = (property_exists($this, 'updateName')) ? $this->updateName : $this->_name;
    $result = parent::insert($data);
    $this->_name = $oldname;
    
    return $result;
  }
  
  public function update(array $data, $where)
  //****************************************************************************
  {
    $oldname = $this->_name;
    $this->_name = (property_exists($this, 'updateName')) ? $this->updateName : $this->_name;
    $result = parent::update($data, $where);
    $this->_name = $oldname;
    
    return $result;
  }
  
  public function hasAutomatedSequence()
  //****************************************************************************
  {
  	return $this->_sequence;
  }
  
  
}