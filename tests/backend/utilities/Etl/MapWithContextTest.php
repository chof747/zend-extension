<?php

class MapWithContextTest extends BaseTestCase
{
  public function setUp()
  //****************************************************************************
  {
    parent::setUp();
    $this->basepath .= '/etl/map';
  }
  
  public function testByMethodsWithContext()
  //****************************************************************************
  {  
    require_once(dirname(__FILE__)."/stubs/TestContext.php");
    
    $input = Chof_Util_Etl_Read_Json::read(
      $this->pathto("bymethodssimple.json"));
    $expected = Chof_Util_Etl_Read_Json::read(
      $this->pathto('bymethodssimpleresult.json'));
    
    $json = Zend_Json::decode(
        file_get_contents($this->pathto('structurewithmapping.json')));
    
    $this->assertArrayEquals($expected, Chof_Util_Etl_Map_ByMethods::map(
      $input, $json, new TestContext()));
  } 
  
  public function testByMethodsWithContextErrors()
  //****************************************************************************
  {
    require_once(dirname(__FILE__)."/stubs/TestContextWithErrors.php");
    $context = new TestContextWithErrors();
    
    $input = Chof_Util_Etl_Read_Json::read(
      $this->pathto("bymethodssimple.json"));
    $json = Zend_Json::decode(
        file_get_contents($this->pathto('structurewithmapping.json')));
    
    $this->assertArrayEquals(array(), Chof_Util_Etl_Map_ByMethods::map(
      $input, $json, $context));
    
    $this->assertArrayEquals(array(
      array(
        'line'    => 1,
        'column'  => 'Country of Residence',
        'message' => 'no country for old men'
      ),
      array(
          'line'    => 2,
          'column'  => 'Country of Residence',
          'message' => 'no country for old men'
      )
    ), $context->errors);
    
  }
  
  public function testByMethodsWithContextConversionErrors()
  //****************************************************************************
  {
    require_once(dirname(__FILE__)."/stubs/TestContext.php");
    $context = new TestContext();
  
    $input = Chof_Util_Etl_Read_Json::read(
        $this->pathto("bymethodssimple_error.json"));
    $expected = Chof_Util_Etl_Read_Json::read(
        $this->pathto('bymethodssimpleresult.json'));
    $expected = array($expected[1]);
    
    $json = Zend_Json::decode(
        file_get_contents($this->pathto('structurewithmapping.json')));
    
    $this->assertArrayEquals($expected, Chof_Util_Etl_Map_ByMethods::map(
        $input, $json, $context));
    
    $this->assertArrayEquals(array(
        array(
            'line'    => 1,
            'column'  => 'Cars',
            'message' => 'a is not a number'
        )
    ), $context->errors);
  }
}