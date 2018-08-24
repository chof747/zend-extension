<?php

class MapByMethodsTest extends TestCase_Base
{
  private static function transformDates($expected)
  {
    $transformed = array();
    foreach($expected as $row)
    {
      foreach($row as $key => $value)
      {
        if ($key == 'Graduation Date')
        {
          $row[$key] = Chof_Util_TimeUtils::returnTime('datetime', $value);
        }
      }
      
      $transformed[] = $row;
    }
    
    return $transformed; 
  }
  
  public function testByMethodsMapper()
  //****************************************************************************
  {  
    require_once(dirname(__FILE__)."/stubs/TestMapper.php");
    
    $input = Chof_Util_Etl_Read_Json::read(
      DataSetFixture::additionalFile("etl/map/bymethodssimple.json"));
    $expected = self::transformDates(Chof_Util_Etl_Read_Json::read(
      DataSetFixture::additionalFile("etl/map/bymethodssimpleresult.json")));
    ZLOG($expected);
    //$expected=Chof_Util_Etl_Read_Json::read(
    //  DataSetFixture::additionalFile("etl/map/bymethodssimpleresult.json")); 
    $this->assertArrayEquals($expected, TestMapper::map($input));
  } 
  
  public function testByMethodMapperWithJsonDefinition()
  //****************************************************************************
  {
    require_once(dirname(__FILE__)."/stubs/TestMapperWithoutDef.php");
    
    $json = Zend_Json::decode(
      file_get_contents(
        DataSetFixture::additionalFile("etl/map/simplestructure.json")));

    $input = Chof_Util_Etl_Read_Json::read(
      DataSetFixture::additionalFile("etl/map/bymethodssimple.json"));
    $expected = self::transformDates(Chof_Util_Etl_Read_Json::read(
      DataSetFixture::additionalFile("etl/map/bymethodssimpleresult.json")));
    
    $this->assertArrayEquals($expected, TestMapperWithoutDef::map($input, $json));
  }
  
  public function testByMethodMapperWithJsonDefinitionAndMapping()
  //****************************************************************************
  {
    require_once(dirname(__FILE__)."/stubs/TestMapperOnlyComplex.php");
    
    $json = Zend_Json::decode(
      file_get_contents(
        DataSetFixture::additionalFile("etl/map/structurewithmapping.json")));

    $input = Chof_Util_Etl_Read_Json::read(
      DataSetFixture::additionalFile("etl/map/bymethodssimple.json"));
    $expected = self::transformDates(Chof_Util_Etl_Read_Json::read(
      DataSetFixture::additionalFile("etl/map/bymethodssimpleresult.json")));
    
    $this->assertArrayEquals($expected, TestMapperOnlyComplex::map($input, $json));    
  }
  
  public function testByMethodMapperWithDefaultsAndJsonDefinition()
  //****************************************************************************
  {
    require_once(dirname(__FILE__)."/stubs/TestMapperOnlyComplex.php");
  
    $json = Zend_Json::decode(
      file_get_contents(
        DataSetFixture::additionalFile("etl/map/structurewithmappinganddefaults.json")));

    $input = Chof_Util_Etl_Read_Json::read(
      DataSetFixture::additionalFile("etl/map/bymethodssimple.json"));
    $expected = self::transformDates(Chof_Util_Etl_Read_Json::read(
      DataSetFixture::additionalFile("etl/map/bymethodsconstantsresult.json")));
    
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
      DataSetFixture::additionalFile("etl/map/bymethodssimple.json"));
    TestMapperWithoutDef::map($input);
  }
}
?>