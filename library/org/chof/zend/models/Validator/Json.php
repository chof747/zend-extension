<?php 
class Chof_Model_Validator_Json extends Zend_Validate_Abstract
{
  const MSG_SYNTAX_ERROR = 'msgSyntaxError';

  protected $_messageVariables = array(
    'string' => 'value',
  );
  
  protected $_messageTemplates = array(
    self::MSG_SYNTAX_ERROR => 'The string "%string%" is not a valid Json',
  );
          
  public function __construct($options = array())
  {
  }
  
 public function isValid($value)
  #****************************************************************************
  {
    try
    {
      Zend_Json::decode($value);
    }
    catch(Zend_Json_Exception $e)
    {
      $this->_error(self::MSG_SYNTAX_ERROR);
      return false;
    }
    
    return true;
  }
  
 
}

?>