<?php

class Default_Model_Map_IndexValue extends Chof_Model_BaseMapper
{
  /**
   * The constructor sets the database table class to Track. 
   */
  //****************************************************************************
  function __construct()
  {
    $this->setDbTable('Default_Model_DbTable_IndexValue'); 
  } 
  
  #-----------------------------------------------------------------------------
  # Concretization methods for the base mapper
  #-----------------------------------------------------------------------------
  
  public function saveData(Chof_Model_BaseModel $model, $datetimefmt = '')
  //****************************************************************************
  {
    $ixvalue = $model;
    
    $datetimefmt = ($datetimefmt == '') ? 'mysql' : $datetimefmt;
    
    $data = array(
      'FK_INDEX_ID'  => $ixvalue->getIndexId(),
      'DATE_VALUE'   => $ixvalue->getDateValue($datetimefmt),
      'VALUE'        => $ixvalue->getValue()*1.0);    

    return $data;

  }
  
  public function fillFromRow($row, Chof_Model_BaseModel $model, $datetimefmt = '')
  //****************************************************************************
  {
    $ixvalue = $model;
    $datetimefmt = ($datetimefmt == '') ? 'mysql' : $datetimefmt;
    
    $ixvalue->setIndexId($row['FK_INDEX_ID'])
            ->setDateValue($row['DATE_VALUE'])
            ->setValue($row['VALUE']*1.0);
    
    $ixvalue->initialize();
    
    return $ixvalue;
  }
  
  protected function createModel()
  //****************************************************************************
  {
    return new Default_Model_IndexValue();  
  }
  
}