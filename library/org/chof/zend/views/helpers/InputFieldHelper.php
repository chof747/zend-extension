<?php

class Chof_View_Helper_InputFieldHelper extends Chof_View_Helper_BaseHelper
{  
	private static $TYPE_MAP = array(
	  'text' => 'dijit.form.TextBox',
	  'submit' => 'dijit.form.Button',
	  'password' => 'dijit.form.TextBox',
	  'radio'    => 'dijit.form.RadioButton',
	  'checkbox' => 'dijit.form.CheckBox',
	);
	
	private $htmltype = 'text';
	private $dojotype = 'dijit.form.TextBox';
	
  public function inputFieldHelper($label, $variable, $type, 
                                   Chof_Model_ValidationException $errors, 
                                   $params = array())
  #***************************************************************************
  {
  	$buttontext = '';
  	
  	$this->readParams($params);
  	
  	$this->htmltype = $type;
  	$this->dojotype = self::$TYPE_MAP[$type];
  	
  	$value = (isset($_POST[$variable])) ? $_POST[$variable] : '';
  	
  	/**
  	 * id = Variable Name prepended by if - for input field - but in CamelCaps
  	 * instead of the _ as word seperator in the variable name
  	 */
  	$id = 'if' . 
  	      str_replace(' ', '', 
  	        ucwords(
  	          str_replace('_', ' ', $variable)));
  	          
  	$error = ($errors->hasError($variable)) 
  	  ? htmlspecialchars($errors->getErrorMessage($variable))
  	  : '&nbsp;';

  	if ($type == 'submit')
  	{
  	  $value = htmlspecialchars($label);
  	  $buttontext = $value;
  	  $label = '';
  	  
  	}
  	else
  	{
  	  $buttontext = '';
  	  $label = htmlspecialchars($label);
  	}
  	  
  	return $this->output(array( 
  	'<div class="formItem">',
  	  '  <div class="caption">',
      '     <label for="'.$id.'">'.$label.'</label></div>',
  	  '  <div class="inputField">',
  	  '    <input id="'.$id.'"',
  	  '           type="'.$this->htmltype.'"',
  	  '           dojoType="'.$this->dojotype.'"',
  	  '           name="'.$variable.'"',
  	  ($buttontext != '') 
  	    ? ('           label="'.$buttontext.'"')
  	    :'',
  	  '           value="'.$value.'"></input>',
  	  '    <br/>',
      '    <div class="errorIndicator" >'.$error.'</div></div></div>'));
  }  
}

?>