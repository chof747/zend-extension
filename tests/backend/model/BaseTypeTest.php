<?php

class BaseTypeTest extends TestCase_Base
{
  
  private function createStub()
  //****************************************************************************
  {
    require_once(dirname(__FILE__)."/stubs/TypeStub.php");
    return new TypeStub(array(
      'a' => 15,
      'b' => 'paul'
    ));
  }
  
  public function testCreate()
  //****************************************************************************
  {
    $bt = $this->createStub();
    $this->assertEquals(15, $bt->a);
    $this->assertEquals('paul', $bt->b);
  }
  
  public function testStringify()
  //****************************************************************************
  {
    $bt = $this->createStub();
    
    $stringified = Chof_Model_BaseType::stringify($bt,
      Chof_Model_BaseType::$STRINGIFY_XMLLIKE);
    $this->assertEquals('<a>15</a><b>paul</b>', $stringified);
    
    $stringified = Chof_Model_BaseType::stringify($bt,
      Chof_Model_BaseType::$STRINGIFY_JSON);
    $this->assertArrayEquals($bt->toArray(), Zend_Json::decode($stringified));
  }
}

?>