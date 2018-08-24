<?php

define('FILES_ROOT', dirname(__FILE__).'/../../data/additional/');

class ServiceParameters
{
  public $endpoint = '';
  public $queryParameter = array();
  public $subfolder = '';
  public $requiresAuth = false;
  
  
  public function __construct($endpoint, 
                              $queryParameter = array(),
                              $subfolder = '',
                              $requireAuth = false)
  //****************************************************************************
  {
    $this->endpoint = $endpoint;
    $this->queryParameter = $queryParameter;
    $this->subfolder = $subfolder;
    $this->requiresAuth = $requireAuth;
  }
  
  public function getPath()
  {
    return join('/', array($this->subfolder, $this->endpoint)).'/';
  }
}

class ServiceScenario
{
  public $name = '';
  
  protected $accuracy = 1e-6;
  
  protected $httpHeaders = array();
  protected $jsonRPCBody = array();
  
  protected $expectedReturnCode = 0;
  protected $expectedResponseBody = array();
  
  private $testCase = null;
  
  public function __construct($name, $path)
  //****************************************************************************
  {
    $this->name = $name;
    $this->loadFromPath($path);  
  }
  
  private function loadFromPath($path)
  //****************************************************************************
  {
    $inJson = Zend_Json::decode(file_get_contents($path.$this->name.'.in.json'));
    $this->httpHeaders = $inJson['http-header'];
    $this->jsonRPCBody = $inJson['json-body'];
    
    $expectedJson = Zend_Json::decode(file_get_contents($path.$this->name.'.expected.json'));
    $this->expectedReturnCode = $expectedJson['http-return-code'];
    $this->expectedResponseBody = $expectedJson['response-body'];

    if (array_key_exists('accuracy', $expectedJson))
    {
      $this->accuracy = $expectedJson['accuracy'];
    }
      
  }
  
  public function prepareRequest(Zend_Controller_Request_HttpTestCase $request)
  //****************************************************************************
  {
    foreach($this->httpHeaders as $header => $value)
    {
      $request->setHeader($header, $value);
    }
    $request->setRawBody(Zend_Json::encode($this->jsonRPCBody));
    
    return $request;
  }
  
  public function prepare(TestCase_Service $testCase)
  //****************************************************************************
  {
    $this->testCase = $testCase;
    $this->callScenarioMethod('prepare');
  }
  
  
  private function assertResponse()
  //****************************************************************************
  {
    if (!empty($this->expectedResponseBody))
    {
      try 
      {
        $actual = Zend_Json::decode($this->testCase->getResponse()->getBody());
        //ZLOG($actual);
        $this->testCase->assertArrayEquals($this->expectedResponseBody, $actual, 
            "$this->name - Response compare", $this->accuracy);
      }
      catch (Zend_Exception $e)
      {
        echo "Not a valid response:\n".$this->testCase->getResponse()->getBody();
        $this->testCase->assertTrue(false);
      }
    }
  }
  
  private function assertSpecificForScenario()
  //****************************************************************************
  {
    $this->callScenarioMethod('assert');
  }
  
  private function callScenarioMethod($prefix)
  //****************************************************************************
  {
    $methodName = $prefix.join('', array_map(function($w) {
      return ucfirst($w); } , explode('-', $this->name)));
      if (array_search($methodName, 
                       get_class_methods(get_class($this->testCase)))!== false)
      {
        return $this->testCase->$methodName();
      }
      else
        return true;
  }
  
  public function executeScenarioAssertions()
  //****************************************************************************
  {
    $this->testCase->assertResponseCode($this->expectedReturnCode);
    $this->assertResponse();
    return $this->assertSpecificForScenario(); 
  }
  
  public function getExpectedReturnCode()
  //****************************************************************************
  {
    return $this->expectedReturnCode;
  }
  
  public function getExpectedResponseBody()
  //****************************************************************************
  {
    return $this->expectedResponseBody;  
  }
}


abstract class TestCase_Service extends TestCase_Controller
{  
  const SERVICE_ROOT = FILES_ROOT.'services/';
  
  private $currentScenario = null;
  
  protected abstract function serviceParameters();
  
  private static function retrieveScenarios($servicePath)
  //****************************************************************************
  {
    $files = scandir($servicePath,SCANDIR_SORT_DESCENDING);
    
    $in = array();
    $out = array();
    $scenarios = array();
    
    foreach($files as $key => $file)
    {
      $extensions = explode('.', $file);
      if (count($extensions) == 3)
      {
        list($name, $direction, $type) = $extensions;
        if ($type == 'json')
        {
          if ($direction == 'in')
          {
            $in[] = $name;
          }
          else if ($direction == 'expected')
          {
            $out[] = $name;
          }
        }
      }
    }
    
    return array_intersect($in, $out);
  }
  
  /**
   * Service Scenarios are documented as a pair or tripple of files in a specifc
   * folder which is determined by the service name and subfolder. The root is 
   * [test root]/backend/files/services/, under this the structure is as follows:
   * 
   * [service file root]
   *   |
   *   +-- [service name] (folder)
   *         |
   *         +-- [scenario name].in.json       (containing the json/rpc body 
   *                                            and http header)
   *         +-- [scenario name].expected.json (containing the http return code
   *                                            and the response body)
   *                                            
   *         ... further file pairs for further scenarios ... 
   * 
   */
  public function serviceScenarios()
  //****************************************************************************
  {
    $servicePath = self::SERVICE_ROOT.$this->serviceParameters()->getPath();
    
    $scenarios = array();
    
    foreach(self::retrieveScenarios($servicePath) as $scenario)
    {
      $scenarios[] = array(new ServiceScenario($scenario, $servicePath));
    }
    
    return $scenarios;
  }
  
  public function getRequest()
  //****************************************************************************
  {
    if ($this->currentScenario !== null)
    {
      $this->_request = $this->currentScenario->prepareRequest(parent::getRequest());
      return $this->_request->setHeader('Content-Type', 'application/json')
                            ->setMethod('POST');
    }
    else
    {
      return parent::getRequest();
    }
  }
  
  /**
   * @dataProvider serviceScenarios
   */
  public function testServiceScenario(ServiceScenario $scenario)
  //****************************************************************************
  {
    $this->prepNextDispatch();
    
    //familiarize test case with Scenario and prepare the scenario
    $this->currentScenario = $scenario;
    $scenario->prepare($this);
    
    $parameters = $this->serviceParameters();
    $address = '/service/'.$parameters->endpoint.(
      count($parameters->queryParameter) > 0 
      ? '?'.join('&', 
          array_walk($parameters->queryParameters,function(&$parameter, $key) {
            $parameter = '$key='.urlencode($parameter);
          }))
      : ''
    );
    $this->dispatch($address);
    $scenario->executeScenarioAssertions($this);
    $this->assertTrue(true);
  }
}

?>