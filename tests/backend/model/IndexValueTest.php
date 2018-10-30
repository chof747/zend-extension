<?php

class IndexValueTest extends TestCase_Base
{
  
  public function testConstruction()
  //****************************************************************************
  { 
    $ixvalue = new Default_Model_IndexValue();
    $this->assertNull($ixvalue->getIndexId());
    $this->assertEquals(Chof_Util_TimeUtils::today(), $ixvalue->getDateValue());
    $this->assertEquals(0, $ixvalue->getValue());
  }
  
  
  /**
   * @group release
   */
  public function testReadFromDB()
  //****************************************************************************
  {
    $ixvalue = new Default_Model_IndexValue();
    $ixvalue->retrieveFromID('1_2017-12-31');
    
    $this->assertEquals(1, $ixvalue->getIndexId());    
    $this->assertEquals('2017-12-31', $ixvalue->getDateValue('mysql-date'));
    $this->assertEquals(126.4, $ixvalue->getValue(), '', 1e-2);
    
    $this->assertEquals('VPI 2005', $ixvalue->getIndex()->getName());
    
    $ixvalue = $ixvalue->find(2, '2017-09-01');
    
    $this->assertEquals(2, $ixvalue->getIndexId());
    $this->assertEquals('2017-09-01', $ixvalue->getDateValue('mysql-date'));
    $this->assertEquals(5835.0, $ixvalue->getValue(), '', 1e-2);
    
    $this->assertEquals('LHKI 45', $ixvalue->getIndex()->getName());
    
  }
  
  /**
   * @group release
   */
  public function testSaveToDB()
  //****************************************************************************
  {
    $ixvalue = new Default_Model_IndexValue();
    try
    {
      $ixvalue->setDateValue(d('1976-08-13'))
           ->setIndexId(2)
           ->setValue(2616.8)
           ->save();
    }
    catch(Chof_Model_ValidationException $e)
    {
      var_dump($e->getDetails());
      throw $e;
    }
    
    
    $ixvalue = new Default_Model_IndexValue();
    $this->assertNull($ixvalue->getId());
    
    $ixvalue->find(2,'1976-08-13');
    $this->assertEquals(2, $ixvalue->getIndexId());
    $this->assertEquals('1976-08-13', $ixvalue->getDateValue('mysql-date'));
    $this->assertEquals(2616.8, $ixvalue->getValue(), '', 1e-2);
    
  }
  
  /**
   * @group release
   */
  public function testValidation()
  //****************************************************************************
  {    
    $ixvalue = new Default_Model_IndexValue();
    
    $this->assertValidation(array(
      'FK_INDEX_ID' => -2201,
    ), $ixvalue->setValue(0.0));
    
    $this->assertValidation(array(
      'DATE_VALUE' => -2203,
    ), $ixvalue->setIndexId(2)
               ->setDateValue(d('1945-12-30')));
    
    $this->assertValidation(array(
      'VALUE' => -2204
    ), $ixvalue->setDateValue(d('1945-12-31'))
               ->setValue(-0.01));
  }
 
  public function testResource()
  //****************************************************************************
  {
    $resource = new Chof_Model_Decorator_Resource(new Default_Model_IndexValue());    
    $this->assertEquals('IndexValue', $resource->getResourceId());
  }
  
  public function testDelete()
  //****************************************************************************
  {
    $ixvalue = new Default_Model_IndexValue();
    $ixvalue->retrieveFromID("2_2017-09-01");
    $ixvalue->delete();
    try
    {
      $ixvalue->retrieveFromID("2_2017-09-01");
      $this->assertFalse(true);
    }
    catch(Chof_Util_ItemNotFoundException $e)
    {
      $this->assertTrue(true);
    }
    catch(Exception $e)
    {
      $this->assertFalse(true);
    }
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