<?php 
class Chof_Model_Validator_ModelLink extends Zend_Validate_Abstract
{
  const MSG_REQUIRED = 'msgRequired'; 
  const MSG_DAMN     = 'msgDamn';
  const MSG_MUSTEXIST = 'msgMustExist';
  
  public $model;
  public $required;
  
  protected $_messageVariables = array(
    'link' => 'value',
    'model' => 'model',
    'required' => 'required'
  );
  
  protected $_messageTemplates = array(
    self::MSG_REQUIRED => 'The %link% is required',
    self::MSG_DAMN => '%link% damn!',
    self::MSG_MUSTEXIST => 'The %link% must be valid and must exist.' 
  );
          
  public function __construct($options = array())
  {
    if (is_array($options))
    {
      $this->model = (isset($options['model'])) ? $options['model'] : "";
      $this->required = (isset($options['required'])) ? $options['required'] : false;
    }
  }
  
 public function isValid($value)
  #****************************************************************************
  {
    if ((($value == 0) || ($value === null) || ($value == ''))  && ($this->required))
    {
      $this->_error(self::MSG_REQUIRED);
      return false;
    }
    else if (($value<>0) || ($value<>''))
    {
      try
      {
        $modelName = $this->model;
        $model = new $modelName();
        $model->retrieveFromID($value);
      }
      catch(Chof_Util_ItemNotFoundException $e)
      {
        $this->_error(self::MSG_MUSTEXIST);
        return false;
      }
    }
    
    return true;
  }
  
 
}

?>