<?php

abstract class Chof_Model_BaseMapper
{
  /**
   *
   * @var name of the corresponding data table
   */

  protected $_dbTable;
  
  protected $model = null;

  #-----------------------------------------------------------------------------
  # Manipulation methods
  #-----------------------------------------------------------------------------

  protected function getPrimarySearchString($id)
  {
    $tablePrimKey = $this->getDbTable()->getPrimaryKey();
    $primdef = array();
    
    if (is_array($tablePrimKey))
    {
      $primary_search = array();
      foreach($tablePrimKey as $pk)
      $primary_search[] = "$pk = ?";
      
      $id = (is_array($id)) ? $id : array($id);
      return array_combine($primary_search, $id);
    }
    else
      return array("$tablePrimKey = ?" => $id);

  }

  /**
   * Abstract method definition for saving the model to the mapped entity
   *
   * @param $model the model to be saved
   */
  public function save()
  //****************************************************************************
  {
    if ($this->model === null)
    {
      throw new Exception("no model set!");
    }
    
    $data = $this->saveData($this->model);
    $tablePrimKey = $this->getDbTable()->getPrimaryKey();

    if (null === ($id = $this->model->getPrimary()))
    {
      if (is_array($tablePrimKey))
        foreach($tablePrimKey  as $pk) unset($data[$pk]);
      else
        unset($data[$tablePrimKey]);

      $newkey = $this->getDbTable()->insert($data);
      
      $this->model->setPrimary($newkey);
    }
    else
      $this->getDbTable()->update($data, $this->getPrimarySearchString($id));
  }

  /**
   * 
   * @param $key the primary key of the record to be deleted
   * @return unknown_type
   */
  public function delete($key)
  //****************************************************************************
  {
    $tablePrimKey = $this->getDbTable()->getPrimaryKey();

    if (null !== ($id = $this->model->getPrimary()))
      return ($this->getDbTable()->delete($this->getPrimarySearchString($id)) > 0);
    else
      return false;    
  }

  /**
   * Method to search for a model based on the primary key
   *
   * @param $id the primary key
   * @return the model matching the primary key
   */
  public function find($id)
  //****************************************************************************
  {
    $result = $this->getDbTable()->find($id);
    
    $this->model = ($this->model) ? $this->model : $this->createModel();
    
    if (0 == count($result))
      return;
    else
      return $this->fillFromRow($result->current(), $this->model);
  }

  /**
   * Returns all models in the mapped entity
   *
   * @return array of all models in the mapped entity
   */
  public function fetchAll(Zend_Db_Table_Select $select = null)
  //****************************************************************************
  {
    $resultSet = $this->getDbTable()->fetchAll($select);
    $entries   = array();

    foreach ($resultSet as $row)
    {
      $entry = $this->createModel();
      $this->fillFromRow($row, $entry);
      $entries[] = $entry;
    }

    return $entries;
  }

  /**
   * Fetch a set of objects based on specific fetch parameters
   *
   * This method allows the fetching of models from the mapped entity based.
   * The fetchparams can contain the following parameters:
   *  - where -- containing an SQL like where statement (w/o the where)
   *  - order -- containing an SQL like order by statement (w/o the order)
   *  - limit -- the number of records to be retrieved
   *
   *  but basically all method names from Zend_Db_Table_Select which can work
   *  with one parameter only.
   *
   * @param array $fetchparams
   * @return list of matching models
   */
  public function fetch(array $fetchparams = null)
  #****************************************************************************
  {
    $select = $this->_dbTable->select();
    $methods = get_class_methods($select);

    foreach ($fetchparams as $param => $value)
    {
      //echo "\n- DEBUG -\n";
      //echo " $param : $value\n";
      //echo "- DEBUG -\n";
      
      $param = strtolower($param);
      if (in_array($param, $methods))
      $select->$param($value);
    }

    return $this->fetchAll($select);
  }

  #-----------------------------------------------------------------------------
  # Abstract methods to be concretized by the specific model
  #-----------------------------------------------------------------------------

  /**
   * Abstract method to be concretized by descendant classes to provide a
   * mechanism to fill a model object from the content of a datatable row
   *
   * @param $row the datatable row
   * @param $model the model to be filled by the row
   * @param $datetimefmt the date and time format of provided date fields
   * @return unknown_type
   */
  abstract protected function fillFromRow($row, Chof_Model_BaseModel $model, $datetimefmt = '');

  /**
   * Abstract helping method to define the concrete mapping from model to entity
   *
   * @param  $model the model to be saved
   * @return an array containing pairs of columns and values to be saved from
   *         the model
   */
  abstract protected function saveData(Chof_Model_BaseModel $model, $datetimefmt = '');

  /**
   * Checking method to validate if a base model reference is an instance of a
   * concrete model class
   *
   * @param $model the base model class which should be checked for concrete type
   * @return a reference to model if the type is the concrete type
   */
  protected function getSaveReference(Chof_Model_BaseModel $model)
  {
    return $model;
  }
  abstract protected function createModel();

  #-----------------------------------------------------------------------------
  # Getter and Setter Methods
  #-----------------------------------------------------------------------------

  public function setDbTable($dbTable)
  //****************************************************************************
  {
    if (is_string($dbTable)) {
      $dbTable = new $dbTable();
    }
    if (!$dbTable instanceof Chof_Model_DbTable_BaseTable)
    {
      throw new Exception('Invalid table data gateway provided');
    }
    $this->_dbTable = $dbTable;
    return $this;
  }

  public function getDbTable()
  //****************************************************************************
  {
    if (null === $this->_dbTable)
    throw new Exception(
        'Data table class not set - do this in the constructor of the concrete class!');

    return $this->_dbTable;
  }

  /**
   * Retrieves last used ID of current table (mapped entity)
   *
   * @return last used ID of mapped entity
   */
  public function getLastID()
  #****************************************************************************
  {
    $primary = $this->_dbTable->getPrimaryKey();
    $order =
      ((is_array($primary)) ? join (' DESC, ', $primary) : $primary). ' DESC';

    $select = $this->_dbTable->select()
      ->order($order)
      ->limit(1);

    $row = $this->_dbTable->fetchRow($select);

    if (is_array($primary))
      return $row->toArray();
    else
      return $row->$primary;
  }
  
  //TODO: Add getNextID / getLastID to enable browsing throw data sets with id 
  //      next and prev, first and last 

  /**
   * Retrieves last used ID of current table (mapped entity)
   *
   * @return last used ID of mapped entity
   */
  public function getCount($filter = false)
  #****************************************************************************
  {
    $db = $this->_dbTable->getAdapter();
    
    $select = $db->select()->from($this->_dbTable->getTableName(), 'COUNT(*) AS C');
    
    if ($filter)
    {
      $select->where($filter);
    }
    
    $rowset =  $db->query($select)->fetchAll();
    return $rowset[0]['C'];
  }
  
  public function getModel()
  #****************************************************************************
  {
    return $this->model;
  }
  
  public function setModel(Chof_Model_BaseModel $model)
  #****************************************************************************
  {
    $this->model = $model;
    return $this;
  }
}