<?php

class MapWithContextTest extends TestCase_Base
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
  
  public function testByMethodsWithContext()
  //****************************************************************************
  {  
    require_once(dirname(__FILE__)."/stubs/TestContext.php");
    
    $input = Chof_Util_Etl_Read_Json::read(
      DataSetFixture::additionalFile("etl/map/bymethodssimple.json"));
    $expected = self::transformDates(Chof_Util_Etl_Read_Json::read(
      DataSetFixture::additionalFile("etl/map/bymethodssimpleresult.json")));
    
    $json = Zend_Json::decode(file_get_contents(
      DataSetFixture::additionalFile("etl/map/structurewithmapping.json")));
    
    $this->assertArrayEquals($expected, Chof_Util_Etl_Map_ByMethods::map(
      $input, $json, new TestContext()));
  } 
  
  public function testByMethodsWithContextErrors()
  //****************************************************************************
  {
    require_once(dirname(__FILE__)."/stubs/TestContextWithErrors.php");
    $context = new TestContextWithErrors();
    
    $input = Chof_Util_Etl_Read_Json::read(
      DataSetFixture::additionalFile("etl/map/bymethodssimple.json"));
    $json = Zend_Json::decode(file_get_contents(
      DataSetFixture::additionalFile("etl/map/structurewithmapping.json")));
    
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
        DataSetFixture::additionalFile("etl/map/bymethodssimple_error.json"));
    $expected = self::transformDates(Chof_Util_Etl_Read_Json::read(
        DataSetFixture::additionalFile("etl/map/bymethodssimpleresult.json")));
    $expected = array($expected[1]);
    
    $json = Zend_Json::decode(file_get_contents(
      DataSetFixture::additionalFile("etl/map/structurewithmapping.json")));
    
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