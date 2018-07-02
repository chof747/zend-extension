<?php

class MapByMethodsTest extends BaseTestCase
{
  public function setUp()
  //****************************************************************************
  {
    parent::setUp();
    $this->basepath .= '/etl/map';
  }
  
  public function testByMethodsMapper()
  //****************************************************************************
  {  
    require_once(dirname(__FILE__)."/stubs/TestMapper.php");
    
    $input = Chof_Util_Etl_Read_Json::read(
      $this->pathto("bymethodssimple.json"));
    $expected = Chof_Util_Etl_Read_Json::read(
      $this->pathto('bymethodssimpleresult.json'));
    
    $this->assertArrayEquals($expected, TestMapper::map($input));
  } 
  
  public function testByMethodMapperWithJsonDefinition()
  //****************************************************************************
  {
    require_once(dirname(__FILE__)."/stubs/TestMapperWithoutDef.php");
    
    $json = Zend_Json::decode(
      file_get_contents($this->pathto('simplestructure.json')));

    $input = Chof_Util_Etl_Read_Json::read(
      $this->pathto("bymethodssimple.json"));
    $expected = Chof_Util_Etl_Read_Json::read(
      $this->pathto('bymethodssimpleresult.json'));
    
    $this->assertArrayEquals($expected, TestMapperWithoutDef::map($input, $json));
  }
  
  public function testByMethodMapperWithJsonDefinitionAndMapping()
  //****************************************************************************
  {
    require_once(dirname(__FILE__)."/stubs/TestMapperOnlyComplex.php");
    
    $json = Zend_Json::decode(
      file_get_contents($this->pathto('structurewithmapping.json')));

    $input = Chof_Util_Etl_Read_Json::read(
      $this->pathto("bymethodssimple.json"));
    $expected = Chof_Util_Etl_Read_Json::read(
      $this->pathto('bymethodssimpleresult.json'));
    
    $this->assertArrayEquals($expected, TestMapperOnlyComplex::map($input, $json));    
  }
  
  public function testByMethodMapperWithDefaultsAndJsonDefinition()
  //****************************************************************************
  {
    require_once(dirname(__FILE__)."/stubs/TestMapperOnlyComplex.php");
  
    $json = Zend_Json::decode(
      file_get_contents($this->pathto('structurewithmappinganddefaults.json')));

    $input = Chof_Util_Etl_Read_Json::read(
      $this->pathto("bymethodssimple.json"));
    $expected = Chof_Util_Etl_Read_Json::read(
      $this->pathto('bymethodsconstantsresult.json'));
    
    $this->assertArrayEquals($expected, TestMapperOnlyComplex::map($input, $json));
    
  }
  
  /**
   * @expectedException        Chof_Util_Etl_Map_WrongDefinition
   * @expectedExceptionMessage No target structure define, provide either json or array with definition!
   */
  public function testMapperWithoutAnyDefinition()
  //****************************************************************************
  {
    require_once(dirname(__FILE__)."/stubs/TestMapperWithoutDef.php");
    
    $input = Chof_Util_Etl_Read_Json::read(
        $this->pathto("bymethodssimple.json"));
    TestMapperWithoutDef::map($input);
  }
}
?>