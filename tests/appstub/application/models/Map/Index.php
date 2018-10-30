<?php

class Default_Model_Map_Index extends Chof_Model_BaseMapper
{
  /**
   * The constructor sets the database table class to Track. 
   */
  //****************************************************************************
  function __construct()
  {
    $this->setDbTable('Default_Model_DbTable_Index'); 
  } 
  
  #-----------------------------------------------------------------------------
  # Concretization methods for the base mapper
  #-----------------------------------------------------------------------------
  
  public function saveData(Chof_Model_BaseModel $model, $datetimefmt = '')
  //****************************************************************************
  {
    $index = $model;
    
    $datetimefmt = ($datetimefmt == '') ? 'mysql-date' : $datetimefmt;
    
    $data = array(
      'DATE_START'   => $index->getDateStart($datetimefmt),
      'NAME'         => $index->getName(),
      'ISSUER'       => $index->getIssuer(),
      'DESCRIPTION'  => $index->getDescription(),
      'EXTERNAL_KEY' => $index->getExternalKey()
    );

    return $data;

  }
  
  public function fillFromRow($row, Chof_Model_BaseModel $model, $datetimefmt = '')
  //****************************************************************************
  {
    $data = $model;
    
    if (isset($row['ID']))
    {
      //will be used also for setting up new values
      $data->setId($row['ID']);
    }
    $datetimefmt = ($datetimefmt == '') ? 'mysql' : $datetimefmt;
    
    $data->setDateStart(Chof_Util_TimeUtils::returnTime('datetime',$row['DATE_START']))
           ->setName($row['NAME'])
           ->setIssuer($row['ISSUER'])
           ->setDescription($row['DESCRIPTION'])
           ->setExternalKey($row['EXTERNAL_KEY']);
    
    $data->initialize();
    
    return $data;
  }
  
  protected function createModel()
  //****************************************************************************
  {
    return new Default_Model_Index();  
  }
  
}