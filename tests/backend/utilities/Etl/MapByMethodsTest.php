<?php

class MapByMethodsTest extends BaseTestCase
{
  public function testByMethodsMapper()
  //****************************************************************************
  {
    require(dirname(__FILE__)."/stubs/TestMapper.php");
    
    $input = Chof_Util_Etl_Read_Json::read(
      dirname(__FILE__)."/../../../files/etl/map/bymethodssimple.json");
    $expected = Chof_Util_Etl_Read_Json::read(
        dirname(__FILE__)."/../../../files/etl/map/bymethodssimpleresult.json");
    
    $this->assertArrayEquals($expected, TestMapper::map($input));
    
  } 
}
?>