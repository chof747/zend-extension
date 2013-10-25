<?php 
  class Chof_Util_RegExp
{
	private $errors = array();
	protected $regexp = '';
	
  public function errorHandler($errno, $errstr, $errfile, $errline)
  //**************************************************************************** 
  {
  	$this->errors[] = array(
  	  'level' => $errno,
  	  'message' => str_replace('preg_match(): ', '',$errstr));
  	
  	return true;
  } 
  
  private static $classExpansion = array(
    '\d' => '[[:digit:]]',
    '\s' => '[[:blank:]]',
    '\w' => '[[:alnum:]]'
  );
  
  public static function expandClasses($regexp)
  //**************************************************************************** 
  {
  	foreach(self::$classExpansion as $old => $new)
  	{
  		$regexp = str_replace($old, $new, $regexp);
  	}	
  	
  	return $regexp;
  }
  
  /**
   * Constructs a syntax checked regular expression.
   * 
   * @param string $regexp the regular expression without delimiters
   */
  function __construct($regexp)
  //**************************************************************************** 
  {
    $this->setRegExp($regexp);
  }
  
  /**
   * Assigns and checks a regular expression
   * 
   * The regular expression handed over to the function is checked for validity.
   * If any errors occur, the method returns false and you can receive the
   * errors via getErrors() or getErrorMessages().
   * 
   * @param string $regexp regular expression without delimiters
   * @return true if the regular expression is valid, false otherwise
   */
  public function setRegExp($regexp)
  //**************************************************************************** 
  {
    $regexp = self::expandClasses($regexp);
		$this->errors = array();
	  $errorfunction = set_error_handler(array($this, 'errorHandler'));
	  preg_match("/$regexp/", 'mary had a little lamb');
		set_error_handler($errorfunction);

		if ($this->hasErrors())
		{
			$this->regexp = '';
			return false;
		}
		else
		{
			$this->regexp = $regexp;
			return true;
		}
  }
  
  /**
   * Returns the regular expression with delimiters
   * 
   * @return string regular expression with delimiters
   */
  public function getRegexp($delimiter = '/')
  //**************************************************************************** 
  {
  	return "$delimiter$this->regexp$delimiter";
  }
  
  public function getErrors()
  //**************************************************************************** 
  {
  	return $this->errors;
  }
  
  public function getErrorMessages()
  //**************************************************************************** 
  {
  	$map = function($error) { return $error['message']; };  	
  	return array_map($map, $this->errors);
  }
  
  public function hasErrors()
  //**************************************************************************** 
  {
  	return (sizeof($this->errors)>0);
  }
}

class InvalidRegExpException extends Zend_Exception { }

?>