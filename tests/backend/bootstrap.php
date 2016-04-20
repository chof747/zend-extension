<?php

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

defined('TEST_PATH')
    || define('TEST_PATH', realpath(dirname(__FILE__).'/..'));

defined('TESTFILES_PATH')
    || define('TESTFILES_PATH', realpath(dirname(__FILE__).'/../files'));
    
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(dirname(__FILE__) .'/../../library'),
    realpath(dirname(__FILE__) .'/../../vendor/zendframework/zendframework1/library'),
    get_include_path(),
)));

require_once 'common/BaseTestCase.php';
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
