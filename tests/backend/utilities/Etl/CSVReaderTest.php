<?php 

class CSVReaderTest extends BaseTestCase
{
  public function testMatches()
  //****************************************************************************
  {
    $this->assertEquals(2, preg_match_all('/"\;/', '"peter";"paul";"sophie"'));
    $this->assertEquals(2, preg_match_all('/"\,/', '"peter","paul","sophie"'));
    $this->assertEquals(2, preg_match_all('/'."'".'\,/', "'peter','paul','sophie'"));
    $this->assertEquals(2, preg_match_all('/"\|/', '"peter"|"paul"|"sophie"'));
  }
  
  private function comparecsv($expected, $file)
  //****************************************************************************
  {
    $csvfile = dirname(__FILE__)."/../../../files/etl/csvreader/$file";
    $data = Chof_Util_Etl_Read_CSV::read($csvfile,1);
    $this->assertArrayEquals($expected, $data);  
  }
  
  private function comparecsvExt($expected, $file, $headers, $delimiter, $enclosure)
  //****************************************************************************
  {
    $csvfile = dirname(__FILE__)."/../../../files/etl/csvreader/$file";
    $data = Chof_Util_Etl_Read_CSV::read($csvfile,$headers, $delimiter, $enclosure);
    $this->assertArrayEquals($expected, $data);  
  }
  public function testSimpleCSV()
  //****************************************************************************
  {
    $this->comparecsv(array(
        array(
            "A" => 10,
            "B" => 20,
            "C" => 'Peter'
        ),
        array(
            "A" =>  30,
            "B" => -40,
            "C" => 'Paul'
        )
    ), "simpletest.csv");
  }
  
  public function testEnclosureCSV()
  //****************************************************************************
  {
    $this->comparecsv(array(
       array(
         "Schlüssel" => 'xy',
         "Wert"      => -27.15,
         "Datum"     => '14.12.2018'
       ),
       array(
         "Schlüssel" => 'y|y',
         "Wert"      => 125.23,
         "Datum"     => '12.05.2018'
       ),
       array(
         "Schlüssel" => "'yx'",
         "Wert"      => 37.13,
         "Datum"     => '11.05.2018'
       )
    ), 'enclosuretest.csv');
  }
  
  public function testNoheader()
  {
    $this->comparecsvExt(array(
        array(
            0 => 10,
            1 => 20,
            2 => 'Peter'
        ),
        array(
            0 =>  30,0,
            1 => -40,
            2 => 'Paul'
        )), 'noheadertest.csv',0, ',', '"');
  }
}
?>