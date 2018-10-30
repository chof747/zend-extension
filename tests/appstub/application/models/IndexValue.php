<?php
class Default_Model_IndexValue extends Chof_Model_BaseModel 
  implements Chof_Model_Interface_Restable
{
  protected $indexId;
  protected $dateValue;
  protected $value;
  
  #-------------------------------------- ---------------------------------------
  # STATIC Methods
  #-----------------------------------------------------------------------------
  
  public static function getValuesFor($index)
  #****************************************************************************
  {
    $index = Default_Model_Index::provideIndex($index);
    
    $value = new Default_Model_IndexValue();
    return $value->fetch(array(
      'where' => "FK_INDEX_ID = $index"
    ));
  }
  
  /**
   * Constructor
   *
   * @param  array|null $options
   * @return void
   */
  #****************************************************************************
  function __construct(array $options = null)
  #****************************************************************************
  {
    $this->indexId = null;
    $this->dateValue = Chof_Util_TimeUtils::today();
    $this->value = 0;
    
    parent::__construct($options);
  }
  
  public function validate()
  #****************************************************************************
  {
    $validationException = new Chof_Model_ValidationException();
    $index = null;
    
    if (!Zend_Validate::is($this->indexId, 'Chof_Model_Validator_ModelLink', array(
      'required' => true,
      'model'    => 'Default_Model_Index',
      
    )))
    {
      $validationException->addDetail(
        'FK_INDEX_ID',
        'The value must be linked to a valid index',
        -2201);
    }
    else
    {
      $index = $this->getIndex();
    }
    
    if (!($this->dateValue instanceof DateTime))
    {
      $validationException->addDetail(
        'DATE_VALUE',
        'The date of the index value must be a valid date object',
        -2202);
    }
    else if (($index !== null) && ($index->getDateStart() > $this->getDateValue()))
    {
      $validationException->addDetail(
        'DATE_VALUE',
        'The date of the index value must be at or after the start date of the index',
        -2203);
    }
    
    if ((!(is_numeric($this->value))) || ($this->value < 0))
    {
      $validationException->addDetail(
        'VALUE',
        'The value must be a valid number and must be larger than zero',
        -2204);
    }
    
    return $validationException->autoThrow();
  }
  
  /***
   *  Concrete implementation for the creation of the model mapper object
   *  @return Default_Model_Map_IndexValue
   */
  protected function createMapper()
  #****************************************************************************
  {
    return new Default_Model_Map_IndexValue();
  }
  
  /**
   * @see src/gps-tracks-zend/application/models/Chof_Model_BaseModel#getModelName()
   */
  public function getModelName()
  #*****************************************************************************
  {
    return "IndexValue";
  }

  #-----------------------------------------------------------------------------
  # GETTER AND SETTER METHODS
  #-----------------------------------------------------------------------------  
  
  public function getIndex()
  #****************************************************************************
  {
    return $this->getReference($this->indexId, Default_Model_Index::class);
  }
   
  public function getPrimaryFields()
  #****************************************************************************
  {
    return array('indexId', 'dateValue');
  }
  
  protected function getPrimaryFromId($id)
  #****************************************************************************
  {
    return explode('_', $id);
  }
  
  public function getPrimary()
  #**************************************************************************
  {
    return array(
      $this->indexId,
      Chof_Util_TimeUtils::returnTime('mysql-date',$this->dateValue));
  }
  
  public function getId()
  #**************************************************************************
  {
    if ($this->indexId !== null)
    {
      return $this->indexId.'_'.
             Chof_Util_TimeUtils::returnTime('mysql-date',$this->dateValue);
    }
    else
    {
      return null;
    }
  }

  public function setPrimary($primary)
  #****************************************************************************
  {
    if(is_array($primary))
    {
      $this->indexId = 
        (isset($primary['FK_INDEX_ID'])) ? $primary['FK_INDEX_ID'] : $primary[0];
      $this->dateValue = 
        (isset($primary['DATE_VALUE'])) ? $primary['DATE_VALUE'] : $primary[1];
    }  
    return $this;
  }
  
  
  #-----------------------------------------------------------------------------
  # Interface Methods
  #-----------------------------------------------------------------------------
}
?>