<?php
class Default_Model_Index extends Chof_Model_BaseModel 
      implements Chof_Model_Interface_ChangeTracker,
                 Chof_Model_Interface_Restable
{  
  protected $id;
  protected $name;
  protected $issuer;
  protected $dateStart;
  protected $description;
  protected $externalKey;
  
  
  #-------------------------------------- ---------------------------------------
  # STATIC Methods
  #-----------------------------------------------------------------------------

  private static function provideDate($date)
  //****************************************************************************
  {
    if (! $date instanceof DateTime)
    {
      $date = Chof_Util_TimeUtils::returnTime('datetime', $date);
    }
    
    return Chof_Util_DBCommander::getInstance()->quote(
      Chof_Util_TimeUtils::transformTime('mysql-date', $date));
  }
  
  public static function provideIndex($index)
  //****************************************************************************
  {
    if (is_string($index))
    {
      $index = Default_Model_Index::getIndexByName($index)->getId();
    }
    else if ($index instanceof Default_Model_Index)
    {
      $index = $index->getId()*1;
    }
    else if (!is_numeric($index))
    {
      $index = 0;
    }
    
    return $index;
  }
  
  public static function getIndexForDate($date, $index)
  //****************************************************************************
  {
    $date = self::provideDate($date);
    $index = self::provideIndex($index);
    
    $indexvalues = new Default_Model_IndexValue();
    $results = $indexvalues->fetchAll(0,0,'DATE_VALUE DESC', 
                                  "FK_INDEX_ID = $index AND DATE_VALUE <= $date");
    
    if (count($results) == 1)
    {
      return $results[0];
    } 
    else
    {
      return null;
    }
  }
  
  public static function getIndexByName($name)
  {
    
    $index = new Default_Model_Index();
    $indices = $index->fetch(array(
      'where' => 'NAME = '.Chof_Util_DBCommander::getInstance()->quote($name)
    ));
    
    if (count($indices) == 1)
    {
      return $indices[0];
    }
    else
    {
      return null;
    }
  }
  
  #-----------------------------------------------------------------------------
  # Construction & Process
  #-----------------------------------------------------------------------------
  
    
  function __construct(array $options = null)
  #****************************************************************************
  {
    $this->id = null;
    $this->dateStart = new DateTime();
    $this->dateStart->setTimestamp(0);
    $this->name= '';
    $this->issuer= '';
    $this->description = '';
    $this->externalKey = '';
    
    parent::__construct($options);
  }
  
  /***
   *  Concrete implementation for the creation of the model mapper object
   *  @return Default_Model_Map_Index
   */
  protected function createMapper()
  #****************************************************************************
  {
    return new Default_Model_Map_Index();
  }
  
  /**
   * @see src/gps-tracks-zend/application/models/Chof_Model_BaseModel#getModelName()
   */
  public function getModelName()
  #****************************************************************************
  {
    return "Index";
  }
  
  public function validate()
  #****************************************************************************
  {
    $validationException = new Chof_Model_ValidationException();

    if (!($this->dateStart instanceof DateTime))
    {
      $validationException->addDetail(
          'DATE_START',
          'The start date of the period must be a valid date object',
          -2101);
    }
        
    return $validationException->autoThrow();
  }
  
  #-----------------------------------------------------------------------------
  # GETTER AND SETTER
  #-----------------------------------------------------------------------------
  
  public function getPrimary()
  #****************************************************************************
  {
    return $this->id;
  }
  
  /**
   * 
   * @param DateTime $date
   * @return double
   */
  public function getValueForDate(DateTime $date)
  #****************************************************************************
  {
    $value = self::getIndexForDate($date,$this);
    return ($value === null) ? null : $value->getValue();  
  }
  
  public function setPrimary($primary)
  #****************************************************************************
  {
    //ensure that $primary is an integer
    $primary += 0;
    
    if ((is_integer($primary)) || ($primary === null))
      $this->id = $primary;
      
      return $this;
  }
  
  #-----------------------------------------------------------------------------
  # Interface Methods
  #-----------------------------------------------------------------------------
  
  public function fromJson($json)
  #*****************************************************************************
  {
    return $this->fromArray(Zend_Json::decode($json));
  }
  
  /**
   * @see models/Interface/Default_Model_Interface_ChangeTracker#elementChanged($element)
   */
  public function elementChanged($element)
  #*****************************************************************************
  {
    return false;
  }  
}