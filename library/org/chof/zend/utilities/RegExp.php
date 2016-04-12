<?php 
  class Chof_Util_RegExp
{
	private $errors = array();
	protected $regexp = '';
	private $mysql = false;
	
	public static $REGEXP_DELIMITER = '/';
	
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
  
  public static function makeSimilarityRegexp($reference, array $samples)
  //**************************************************************************** 
  {
    $common = Chof_Util_String::computeCommonStringParts($reference, $samples, '|');
    return self::transformSegmentsToRegexp($common, '|');
  }
  
  public static function transformSegmentsToRegexp($segmentedstring, $delimiter)
  //**************************************************************************** 
  {
    return join('.*', array_map(function($token) {
      return preg_quote($token, self::$REGEXP_DELIMITER);
    }, explode($delimiter, $segmentedstring)));
  }
  
  public static function expandClasses($regexp)
  //**************************************************************************** 
  {
  	foreach(self::$classExpansion as $old => $new)
  	{
  		$regexp = str_replace($old, $new, $regexp);
  	}	
  	
  	return $regexp;
  }
  
  public static function makeMySQLConform($regexp)
  //**************************************************************************** 
  {
    return //preg_replace('/\.\*\?(.)/', '[^${1}]*${1}', $regexp);
           preg_replace('/\.\*\?/', '.*', $regexp);
  }
  
  /**
   * Constructs a syntax checked regular expression.
   * 
   * @param string $regexp the regular expression without delimiters
   * @param bool $mysql set this regexp as a mysql regexp
   */
  function __construct($regexp, $mysql = false)
  //**************************************************************************** 
  {
    $this->mysql = $mysql;
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
    if ($this->mysql)
    {
      $regexp = self::makeMySQLConform($regexp);
    }
 
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