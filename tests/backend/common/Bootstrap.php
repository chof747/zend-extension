<?php

require_once 'org/chof/zend/init.php';

function ZLOG($text) {
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
  public function __construct($application)
  #*****************************************************************************
  {
		 //initChof($this);
		 
		 //var_dump($this->getPluginLoader());
		 
		 parent::__construct($application);
  }
  
  /**
   * Initializes all chof extension relevant topics
   */
  protected function _initChof()
  #*****************************************************************************
  {
  	$chof = initChof($this);
  	
  	$this->bootstrap('sslencryption');
  	$encryption = $this->getResource('sslencryption');
    
  	if (!empty($encryption))
  	{
  		Zend_Registry::set('sslEncryption', $encryption);
  	}
  	
    return $chof;
  }  
  
  protected function _initLogger()
  #*****************************************************************************
  {
    $writer = new Zend_Log_Writer_Stream($this->getOption('logfile'));
    $logger = new Chof_Util_Log($writer);
    $logger->registerErrorHandler();
    
    Zend_Registry::set('logger', $logger);   
  }
  
}  
  

