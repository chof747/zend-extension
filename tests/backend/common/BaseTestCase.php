<?php

//require_once('PHPUnit/Extensions/Database/DataSet/CsvDataSet.php');
//require_once('PHPUnit/Extensions/Database/DataSet/ReplacementDataSet.php');
require_once('Zend/Test/PHPUnit/DatabaseTestCase.php');
require_once('Zend/Application.php');



class BaseTestCase extends PHPUnit_Framework_TestCase
{
  protected $bootstrap;
   
  public function setUp()
  #*****************************************************************************
  {
    // Assign and instantiate in one step:
    $this->bootstrap = new Zend_Application(
            'testing', 
    TESTFILES_PATH . '/application.ini'
    );
    $this->bootstrap->bootstrap();

    parent::setUp();
  }
  
  public function tearDown()
	#*****************************************************************************
  {
    Zend_Registry::get('logger')->__destruct();
    //Zend_Registry::set('logger', null);
    parent::tearDown();
  }

  protected function assertArrayEquals(array $expected, array $actual)
  //****************************************************************************
  {
    $this->assertCount(count($expected), $actual);
  
    foreach($expected as $key => $value)
    {
      $this->assertArrayHasKey($key, $actual);
      if (is_array($value))
      {
        $this->assertArrayEquals($value, $actual[$key]);
      }
      else
      {
        $this->assertEquals($value, $actual[$key]);
      }
    }
  }
  
  protected function helpCompareTextWithFile($text, $filename)
	#*****************************************************************************
  {
    $testFile = "testFile.txt";
    $fh = fopen($testFile, 'w');
    fwrite($fh, $text);
    fclose($fh);
    
    $this->assertFileEquals(
      dirname(__FILE__).'/../files/'.$filename, 
      $testFile);
  	
  }
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

?>