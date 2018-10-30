<?php
require_once 'org/chof/zend/init.php';

function ZLOG($text)
#*******************************************************************************
{
  if (APPLICATION_ENV != 'production')
  {
    if (!is_string($text))
    {
      $text = var_export($text, true);
    }
    Zend_Registry::get('logger')->info($text);
  }
}

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

  /**
   * Initializes all chof extension relevant topics
   */
  protected function _initChof()
  #*****************************************************************************
  {
    return initChof($this);
  }
  
  protected function _initConfDB()
  #*****************************************************************************
  {
    if (!Zend_Registry::isRegistered('db'))
    {
      //$config = Zend_Registry::get('config');
      Zend_Registry::set('db', new Zend_Config($this->getOptions()["resources"]["db"]));
    }
  }
  
  protected function _initAutoload()
  #*****************************************************************************
  {
    $autoloader = new Zend_Application_Module_Autoloader(array(
      'namespace' => 'Default_',
      'basePath'  => dirname(__FILE__),
    ));
    
    $autoloader->addResourceTypes(array(
      'utilities' => array(
        'path' => 'utilities',
        'namespace' => 'Util_')));
    
    return $autoloader;
  }  
  
  protected function _initValidators()
  #*****************************************************************************
  {
    Zend_Validate::addDefaultNamespaces(array('Default_Model_Validator'));
  }
  
  
  protected function _initLogger()
  #*****************************************************************************
  {
    $writer = new Zend_Log_Writer_Stream(dirname(__File__).'/../../logs/testlog.txt');
    $logger = new Chof_Util_Log($writer);
    //$logger->registerErrorHandler();
    
    Zend_Registry::set('logger', $logger);
    
    return $logger;
  }
}

