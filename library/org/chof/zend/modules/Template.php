<?php

/**
 * Template class for mail templates:
 * 
 * - Templates are text files in a specific directory � stored by topics in 
 *   subdirectories as the system requests it.
 * - Variables in templates are inclosed by <? VARIABLE[=�DEFAULT�] ?> tags, 
 *   where DEFAULT is a possibly quoted default value if the variable has not 
 *   been provided by the calling code 
 * 
 * @author chof
 *
 */
class Chof_Zend_Template
{
	const STD_LANGUAGE = 'en';
	
  /**
   * Regular expression description to extract variables and defaults:
   * 1. Look for <?                         --> <\?
   * 2. skip any white space                --> \s*
   * 3. Extract all consecutive characters  --> (\w*)  => subpattern 1
   * 4. Conditional subpattern for def. val --> (?(?=[=])... 
   * 5. If = follows variable name extract
   *    all charactes incl. white spaces 
   *    between simple quotes               --> \'(.*?)\'
   * 6. skip any white space                --> \s*
   * 7. Look for ?>                         --> \?>
 	 *    
 	 */
	const VARIABLE_REGEXP = '/<\?\s*(\w*)(?(?=[=])=\'(.*?)\')\s*\?>/';
	
  protected $templateFileName = '';
  protected $templateText = '';
  protected $language;
  protected $type;
  
  protected $variables  = array();
  
  protected $config = null;
  
  
  /**
   * Constructor for the template. The constructor needs the name of the
   * template as it is stored in the template directory structure and optionally 
   * you can provide a type of text and a language.
   * 
   * @param string $templateName
   * @param string $type
   * @param string $language
   */
  public function __construct($templateName,
                              $type = 'txt',
                              $language = '')
	#*****************************************************************************
  {
		$config = Zend_Registry::get('chofconfig');
		$this->config = (isset($config->templates)) 
		  ? $config->templates
		  : new Zend_Config(array());

		$this->type = ($type == 'txt') ? 'txt' : 'html';
  	
  	$this->setLanguage($language)
         ->assignTemplateFile($templateName);
  	
  	$this->parseTemplate();  	
  }
  
  private function parseTemplate()
	#*****************************************************************************
  {
  	//look for variablenames and store them in a temporary array
  	
  	$matchings = array();
  	preg_match_all(self::VARIABLE_REGEXP,
  	               $this->templateText,
  	               $matchings, PREG_SET_ORDER);
  	               
  	$varHandlerReg = Chof_Zend_Template_VariableHandler_Registry::getInstance();
  	               
  	foreach ($matchings as $match)
  	{
  		$this->variables[] = new __templateVariable(
  		  $match[1],
  	    (sizeof($match) > 2) ? $match[2] : '',
  	    $varHandlerReg->getHandlerFor($match[1]));
  	}
  }
  
  private function regexpFor($variable = '')
	#*****************************************************************************
  {
  	if ($variable == '')
  	{
  		return self::VARIABLE_REGEXP;
  	}
  	else
  	{
  		return str_replace('\w*', $variable, self::VARIABLE_REGEXP);
  	}
  }
  
	#-----------------------------------------------------------------------------
	# GETTER AND SETTER METHODS
	#-----------------------------------------------------------------------------
	
  /**
   * Fills out the template text, thereby replacing the variables with either
   * the value provided by a variableHandler or by the concrete information
   * provided by the filler object
   * 
   * @param Chof_Zend_Template_FilloutInterface $filler interface object
   *        providing the concrete content for the variables
   * @return string text of the template with filled out variables
   */
  public function getText(Chof_Zend_Template_FilloutInterface $filler)
	#*****************************************************************************
  {
  	$text = $this->templateText;
  	
  	foreach($this->variables as $var)
  	{
  		$value = $this->obtainVariableValue($var, $filler);
  		$count = 0;
  		
  		$newtext = preg_replace($this->regexpFor($var->name), $value, $text, 
  		                        -1, $count);
  		                        
  	  if (($newtext === null) || ($count == 0))
  	  {
  	  	throw new TemplateFilloutException(
  	  	  "Error replacing $var->name with '$value'!",
  	  	  $var,
  	  	  $value);
  	  }
  	  else
  	  {
  	  	$text = $newtext;
  	  }
  	}
  	
  	return $text;
  }
  
  /**
   * Returns the type of the template text (html|text)
   */
  public function getType()
  {
  	return $this->type;
  }
  
  private function obtainVariableValue(__templateVariable $var, 
    Chof_Zend_Template_FilloutInterface $filler)
	#*****************************************************************************
  {
  	if ($var->handler !== null)
  	{
 			return $var->handler->provideInformation($var->name);
 		}
  	else
  	{
  		return ($filler->providesVariable($var->name)) ?
 			  $filler->fillOut($var->name) :
 			  $var->default;
  	}
  	
  }
  
  /**
   * Sets the language for the template. The language is either directly set by
   * the caller or set to the value provided in the configuration file under
   * org.chof.templates.language. If nothing is provided either way, language i
   * set to the constant STD_LANGUAGE of this class (usually 'en') 
   * 
   * @param string $language
   * @return Chof_Zend_Template a reference to itself for method chaining
   */
  protected function setLanguage($language)
	#*****************************************************************************
  {
  	if (($language == '') && (isset($this->config->language)))
  	{
  		$this->language = $this->config->language;
  	}
  	else if ($language == '')
  	{
  		$this->language = self::STD_LANGUAGE;
  	}
  	else 
  	{
  	  $this->language = $language;	
  	}
  	
  	return $this;
  }
  
  /**
   * Method which builds the filename out of the defined templates root in
   * org.chof.templates.root, the template name, type and language as set by
   * the constructor.
   * 
   * If no root is provided by the configuration the same directory as the files
   * directory is used
   * 
   * @param  string $templateName
   * @return Chof_Zend_Template a reference to itself for method chaining
   */
  protected function assignTemplateFile($templateName)
	#*****************************************************************************
  {
  	if (!isset($this->config->root))
  	{
  	  $root = dirname(__FILE__);	
  	}
    else
    {
  	  $root = $this->config->root;
    }
    
    $this->templateFileName = 
      "$root/$templateName.$this->type.$this->language.template";
    
    $file = file($this->templateFileName);
    $this->templateText = join("", $file);
    
    return $this;
  }
}

/**
 * Exception class if a variable replacement fails
 * 
 * Provides 2 additional pieces of information:
 *  - the name of the variable (getVariable())
 *  - the value which was tried to be filled into the variable (getValue())
 * 
 * @author chof
 *
 */
class TemplateFilloutException extends Zend_Exception 
{ 
	private $value;
	private $variable; 
	
	public function __construct($message, $variable, $value)
	#*****************************************************************************
	{
		parent::__construct($message);
		$this->variable = $variable;
		$this->value = $value;
	}
	
	public function getValue()    { return $this->value; }
	public function getVariable() { return $this->variable; }
}

class __templateVariable
{
	public $name = '';
	public $default = '';
	public $handler = null;
	
	public function __construct($name, $default, $handler)
	{
		$this->name = $name;
		$this->default = $default;
		$this->handler = $handler;
	}
}

?>