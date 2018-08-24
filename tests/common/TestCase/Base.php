<?php

require_once(dirname(__FILE__).'/../bootstrap_backend.php');
require_once('Zend/Test/PHPUnit/DatabaseTestCase.php');

class TestCase_Base extends Zend_Test_PHPUnit_DatabaseTestCase
{
  private $_connectionMock;
  private static $dbconnection = null;
  
  protected $asserter;
  protected $help;
  protected $application;
  
  public function __construct($name=null, $data=[], $dataName='')
  #*****************************************************************************
  {
    parent::__construct($name, $data, $dataName);
    $this->asserter = new TestCase_Helper_Asserts($this);
    $this->help = new TestCase_Helper_Help($this);
  }
   
  public function setUp()
  #*****************************************************************************
  {
    $this->application = initializeZendBootstrap(); 
//    $this->application->getBootstrap('session');
    Zend_Session::$_unitTestEnabled = true;
    $this->application->bootstrap();
    
    $conn = $this->getConnection();
    $conn->getConnection()->query("set foreign_key_checks=0");
    parent::setUp();
    $conn->getConnection()->query("set foreign_key_checks=1");    
  }
  
  public function tearDown()
	#*****************************************************************************
  {
    DataSetFixture::purgeTempFiles();
    parent::tearDown();
    $this->getConnection()->close();
    //ZLOG(exec('lsof | wc -l'));
    Zend_Registry::get('logger')->__destruct();
  }
  
  protected function withUser($user)
	#*****************************************************************************
  {
    $money20 = new  Zend_Session_Namespace('money20');
    $money20->storage = $user;
  }

  public function getConnection()
	#*****************************************************************************
  {
    if ($this->_connectionMock === null)
    {
      if (self::$dbconnection === null)
      {
        $db = Zend_Registry::get('db');
        self::$dbconnection = Zend_Db::factory($db);
        //  $this->bootstrap->getBootstrap()->getPluginResource('db')->getDBAdapter();
        Zend_Db_Table_Abstract::setDefaultAdapter(self::$dbconnection);
          
      }
      
      $this->_connectionMock = $this->createZendDbConnection(
      self::$dbconnection, 'money20test');

    }

    return $this->_connectionMock;
  }

  public function getDataSet()
  #*****************************************************************************
  {
    $standardset = DataSetFixture::getDataSet();
    $additionalset = $this->getAdditionalDataSet();
    
    $dataSet = new PHPUnit_Extensions_Database_DataSet_CompositeDataSet();
    $dataSet->addDataSet($standardset);
    $dataSet->addDataSet($additionalset);
    
    return new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet(
        $dataSet,
        array('NULL' => null,
            'true' => 1,
            'false' => 0));
    
  }	
  
  protected function getAdditionalDataSet()
  #*****************************************************************************
  {
    return new PHPUnit_Extensions_Database_DataSet_CsvDataSet();  
  }
  
  private static function callFrom($helper, $method, $arguments)
  #*****************************************************************************
  {
    if (array_search($method,
      get_class_methods(get_class($helper))) !== false)
    {
      return
        call_user_func_array(array($helper, $method), $arguments);
    }
    else
    {
      throw new PHPUnit_Framework_Exception(
        "Method $method not in the test case!");
    }
  }
  
  public function __call($method, $arguments)
  //****************************************************************************
  {
    if (substr($method, 0, 6) == 'assert')
    {
      return self::callFrom($this->asserter, $method, $arguments);
    }
    else if (substr($method, 0, 4) == 'help')
    {
      return self::callFrom($this->help, $method, $arguments);
    }

    throw new PHPUnit_Framework_Exception("Method $method not in the test case!");
  }
  
  public function _testBasics()
	#*****************************************************************************
  {
    $config = Zend_Registry::get('config');
  }

   
};

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
?>