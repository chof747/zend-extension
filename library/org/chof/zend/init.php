<?php

include_once dirname(__FILE__).'/functions/Math.php';

function initChof(Zend_Application_Bootstrap_Bootstrap $bootstrap)
{
      $chof_autoloader = new Zend_Application_Module_Autoloader(array(
      'namespace' => 'Chof_',
      'basePath'  => 'org/chof/zend/'
    ));
    
    $chof_autoloader->addResourceTypes(array(
      'modules'   => array(
        'path' => 'modules', 
        'namespace' => 'Zend_'),
      'utilities' => array(
        'path' => 'utilities',
        'namespace' => 'Util_'),
      'models' => array(
        'path' => 'models',
        'namespace' => 'Model_'),
      'controllers' => array(
        'path' => 'controllers',
        'namespace' => 'Controller_'),
      'controller_helper' => array(
        'path' => 'controllers/helpers',
        'namespace' => 'Controller_Helper'),
      'controller_plugins' => array(
          'path' => 'controllers/plugins/',
          'namespace' => 'Controller_Plugin')));
    
    $org = $bootstrap->getOption('org');
    
    if (!empty($org))
    {
      $config = new Zend_Config($org['chof']);
    }
    else
    {
      $config = array();
    } 
    
      Zend_Registry::set('chofconfig', $config);
    
    
    
    //setup controller helpers and plugins
    Zend_Controller_Action_HelperBroker::addPath(dirname(__FILE__).'/controllers/helpers',
                                                   'Chof_Controller_Helper');  
    
    Zend_Validate::addDefaultNamespaces(array('Chof_Model_Validator'));
    $pluginloader = $bootstrap->getPluginLoader();
    $pluginloader->addPrefixPath('Chof_Resource', dirname(__FILE__).'/resources');
    
    
    return $chof_autoloader;

    
    $bootstrap->setPluginLoader($pluginloader);
}

?>