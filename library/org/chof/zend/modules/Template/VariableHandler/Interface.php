<?php

/**
 * Standard interface for mailer template variable handlers.
 * 
 * The interface has only one method which gets a variable name and returns the
 * information to be filled into the variable.
 * 
 * The same variable handler can thus be registered for several variable names.
 * 
 * @example Chof_Zend_Mail_VariableHanlder_CurrentTime
 * 
 * @author chof
 *
 */
interface Chof_Zend_Template_VariableHandler_Interface
{
	public static function registerHandler();
	
	/**
	 * Method which receives the name of a variable and provides the content 
	 * formatted as a string to be pasted into the template text of an E-Mail
	 * 
	 * @param string $variable name of the variable
	 * @return string Text to be inserted instead of the variable into the
	 *                template
	 */
	public function provideInformation($variable);
}

?>