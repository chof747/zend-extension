<?php

class IndexTest extends TestCase_Base
{
  
  public function testConstruction()
  //****************************************************************************
  { 
    $index = new Default_Model_Index();
    $this->assertNull($index->getId());
    $this->assertEquals(Chof_Util_TimeUtils::returnTime('datetime', 0), $index->getDateStart());
    $this->assertEquals('', $index->getName());
    $this->assertEquals('', $index->getIssuer());
    $this->assertEquals('', $index->getDescription());
    $this->assertEquals('', $index->getExternalKey());
  }  
  
  /**
   * @group release
   */
  public function testReadFromDB()
  //****************************************************************************
  {
    $index = new Default_Model_Index();
    $index->retrieveFromID(1);
    
    $this->assertEquals(1, $index->getId());    
    $this->assertEquals('2005-12-31', $index->getDateStart('mysql-date'));
    $this->assertEquals('VPI 2005', $index->getName());
    $this->assertEquals('Statistik Austria', $index->getIssuer());
    $this->assertEquals('Verbraucherpreisindex 2005 der Statistik Austria', 
      $index->getDescription());
    $this->assertEquals('VPI_05',$index->getExternalKey());
    
    $index = $index->find(2);
    
    $this->assertEquals(2, $index->getId());
    $this->assertEquals('1945-12-31', $index->getDateStart('mysql-date'));
    $this->assertEquals('LHKI 45', $index->getName());
    $this->assertEquals('Historic Indexer', $index->getIssuer());
    $this->assertEquals('Pricing index since 1945',
      $index->getDescription());
    $this->assertEquals('',$index->getExternalKey());
    
  }
  
  /**
   * @group release
   */
  public function testSaveToDB()
  //****************************************************************************
  {
    $index = new Default_Model_Index();
    try
    {
      $index->setDateStart(d('1976-08-13'))
           ->setName('CHOF_IX')
           ->setIssuer('Christian')
           ->setDescription('Index seit Anbeginn')
           ->setExternalKey('TEST_KEY')
           ->save();
    }
    catch(Chof_Model_ValidationException $e)
    {
      var_dump($e->getDetails());
      throw $e;
    }
    
    
    $index = new Default_Model_Index();
    $this->assertNull($index->getId());
    
    $index->retrieveFromID(3);
    $this->assertEquals(3, $index->getId());
    $this->assertEquals('1976-08-13', $index->getDateStart('mysql-date'));
    $this->assertEquals('CHOF_IX', $index->getName());
    $this->assertEquals('Christian', $index->getIssuer());
    $this->assertEquals('Index seit Anbeginn',
      $index->getDescription());
    $this->assertEquals('TEST_KEY', $index->getExternalKey());
  }
  
  public function testValidation()
  //****************************************************************************
  {    
    $index = new Default_Model_Index();
    
    
    $this->assertValidation(array(
    ), $index->setDateStart(d('2018-08-01'))
    );
  }
 
  public function testResource()
  //****************************************************************************
  {
    $resource = new Chof_Model_Decorator_Resource(new Default_Model_Index());    
    $this->assertEquals('Index', $resource->getResourceId());
  }
  
  /**
   * @group release
   */
  public function testDelete()
  //****************************************************************************
  {
    $index = new Default_Model_Index();
    $index->retrieveFromID(2);
    $index->delete();
    try
    {
      $index->retrieveFromID(2);
      $this->assertFalse(true);
    }
    catch(Chof_Util_ItemNotFoundException $e)
    {
      $this->assertTrue(true);
      $this->assertCount(0, Default_Model_IndexValue::getValuesFor(2));
    }
    catch(Exception $e)
    {
      $this->assertFalse(true);
    }
  }
  
  /**
   * @group release
   */
  public function testIndexForDate()
  //****************************************************************************
  {
    $value = Default_Model_Index::getIndexForDate('2017-12-30', 'VPI 2005');
    $this->assertEquals(124.8,$value->getValue());
    $value = Default_Model_Index::getIndexForDate('2017-12-31', 'VPI 2005');
    $this->assertEquals(126.4,$value->getValue());
    $value = Default_Model_Index::getIndexForDate('2018-06-02', 'VPI 2005');
    $this->assertEquals(127.1,$value->getValue());
    
    //test the method getValueForDate in Index
    $index = new Default_Model_Index();
    $index->retrieveFromID(1);
    $this->assertEquals(124.8, $index->getValueForDate(d('2017-12-30')));
    $this->assertEquals(126.4, $index->getValueForDate(d('2017-12-31')));
    $this->assertEquals(127.1, $index->getValueForDate(d('2018-06-02')));
    $this->assertNull($index->getValueForDate(d('2017-05-31')));
    
  }
    
  /*
  
  public function testSchema()
  //****************************************************************************
  {
    $model = new Chof_Model_Decorator_Schema($this->account);
    $schema = $model->schema();
    
    //$schema = Zend_Json::decode($jsonSchema);
    
    $this->assertEquals('Account', $schema['name']);
    $this->assertEquals('NAME', $schema['label']);
    $this->assertEquals('ID', $schema['identifier']);
  }
*/
}
?>