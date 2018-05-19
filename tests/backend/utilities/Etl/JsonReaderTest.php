<?php 

class JsonReaderTest extends BaseTestCase
{
  public function testJsonRead()
  //****************************************************************************
  {
    $data = Chof_Util_Etl_Read_Json::read(
      dirname(__FILE__)."/../../../files/etl/jsonreader/simpletest.json");
    
    $this->assertArrayEquals(array(
      "first_name"=> "Winston",
      "surname"   => "Churchill",
      "Offices"   => ["First Sealord", "Prime Minister"],
      "Quote"     => array(
        "source"     => "https://quoteinvestigator.com/2014/08/27/drink-it/",
        "occasision" => "Quarrel with Nancy Astor",
          "text"     => "If I Were Your Husband I’d Drink It"
      )), $data);
  }
}
?>