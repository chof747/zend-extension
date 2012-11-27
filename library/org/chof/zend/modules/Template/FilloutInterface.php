<?php

/**
 * Interface to provide concrete information for variables within a template
 * 
 * @author chof
 *
 */
interface  Chof_Zend_Template_FilloutInterface
{
	
	/**
	 * Checks wether the fillout interface provides content for the variable with 
	 * the name $variable
	 * 
	 * @param string $variable the requested variable
	 * @return boolean true: if the variable is supported, false otherwise
	 */
	public function providesVariable($variable);
	
	/**
	 * This method is used by the template to retrieve the concrete information of
	 * a variable used in the template
	 * 
	 * @param string $variable
	 * @return string with the content for the variable
	 */
	public function fillOut($variable);
}

?>