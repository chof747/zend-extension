<?php 
#require_once(realpath(dirname(__FILE__)."/../vendor/autoload.php"));

// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../appstub/application'));

error_reporting( E_ALL | E_STRICT );
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

define('APPLICATION_ENV', 'testing');
define('TESTS_PATH', realpath(dirname(__FILE__)));

//Define the application name
defined('APPLICATION_NAME')
|| define('APPLICATION_NAME', 'appstub');

defined('TESTFILES_PATH')
|| define('TESTFILES_PATH', realpath(dirname(__FILE__).'/../data/'));

//echo(get_include_path()."\n");
$includePaths = array(
  realpath(dirname(__FILE__) .'/../../library/'),    
  get_include_path());
set_include_path(implode(PATH_SEPARATOR, $includePaths));
//echo(get_include_path()."\n");

require_once(dirname(__FILE__).'/../../vendor/autoload.php');
require_once(dirname(__FILE__) .'/../data/DataSetFixture.php');

function d($str) {
  return Chof_Util_TimeUtils::returnTime('datetime', $str);
}

function initializeZendBootstrap()
{
  // Ensure library/ is on include_path
  require_once "Zend/Loader/Autoloader.php";
  Zend_Loader_Autoloader::autoload('Zend_Application');
  
  // Create application, bootstrap, and run
  return new Zend_Application(
    APPLICATION_ENV,
    new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 
      APPLICATION_ENV)
    );
}
?>
