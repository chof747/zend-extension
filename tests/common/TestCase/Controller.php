<?php

require_once(dirname(__FILE__).'/../bootstrap_backend.php');
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

abstract class TestCase_Controller extends Zend_Test_PHPUnit_ControllerTestCase
{
  
  private $db = null;
  private $connection = null;
  protected $asserter;
  protected $help;
  
  public function __construct($name=null, $data=[], $dataName='')
  #*****************************************************************************
  {
    parent::__construct($name, $data, $dataName);
    $this->asserter = new TestCase_Helper_Asserts($this);
    $this->help = new TestCase_Helper_Help($this);
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
  
  /**
   * Sets up bootstrap for the application.
   * This method is called before a test is executed.
   *
   * @return void
   */
  public function setUp()
  #*****************************************************************************
  {
    $this->bootstrap = initializeZendBootstrap();
    
    parent::setUp();
    $this->_setupDatabase();
  }
  
  public function tearDown()
  #*****************************************************************************
  {
    Zend_Registry::get('logger')->__destruct();
    $queue =  $this->bootstrap->getBootstrap()->getResource('messagequeue');
    $queue->getAdapter()->close();
    $this->getConnection()->close();
    parent::tearDown();
  }
  
  private function getDb()
  #*****************************************************************************
  {
    if ($this->db == null)
    {
      $db = Zend_Registry::get('db');
      $this->db = Zend_Db::factory($db);
      
      //$this->db = $this->bootstrap->getBootstrap()->getPluginResource('db')->getDbAdapter();
      Zend_Db_Table_Abstract::setDefaultAdapter($this->db);
    }
    
    return $this->db;
  }
  
  public function getConnection()
  #*****************************************************************************
  {
    if ($this->connection == null)
    {
      $this->connection = new Zend_Test_PHPUnit_Db_Connection(
        $this->getDb(), 'money20test');
    }
    
    return $this->connection;
  }

  /**
   * Setup test database and load fixture
   */
  protected function _setupDatabase()
  #*****************************************************************************
  {
    $dataSet = $this->getDataSet();

    if ($dataSet instanceof PHPUnit_Extensions_Database_DataSet_IDataSet) {
      $setupOperation = new PHPUnit_Extensions_Database_Operation_Composite(array(
            new Zend_Test_PHPUnit_Db_Operation_Truncate(),
            new Zend_Test_PHPUnit_Db_Operation_Insert()
        ));
      
      $conn = $this->getDb();
      $conn->getConnection()->query("set foreign_key_checks=0");
      
      $setupOperation->execute(new Zend_Test_PHPUnit_Db_Connection(
        $this->getDb(), 'money20test'), $dataSet);
      
      $conn->getConnection()->query("set foreign_key_checks=1");
      
    }
  }

  /**
   * Returns the test dataset.
   *
   * @return PHPUnit_Extensions_Database_DataSet_IDataSet
   */
  protected function getDataSet()
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
  
  protected function getResponseHeader($header)
  #*****************************************************************************
  {
    $headers = $this->response->sendHeaders();
    $header = strtolower($header);
    
    if (array_key_exists($header, $headers))
    {
      $matches = array();
      preg_match('/\w*?\:\s*(.*)/',$headers[$header],$matches);
      return $matches[1];
    }
  }
  
  protected function setAuthentication($method, $authstring)
  #*****************************************************************************
  {
    $this->getRequest()->setHeader('Authorization',
        $method.' '.$authstring);
  }
  
  protected function prepNextDispatch()
  #*****************************************************************************
  {
    $this->resetRequest();
    $this->resetResponse();
  }
}
?>