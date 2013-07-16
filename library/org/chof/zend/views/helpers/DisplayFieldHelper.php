<?php

class Chof_View_Helper_DisplayFieldHelper extends Chof_View_Helper_BaseHelper
{  
	
	private $htmltype = 'text';
	
  public function displayFieldHelper($label, $value, $printEmpty = false, 
                                   $params = array())
  #***************************************************************************
  {
  	$this->readParams($params);
  	$output = array();
  	
  	if (($printEmpty) || !(empty($value)))
  	{
  		$output[] = '<tr class="form">';
  		$output[] = '  <td class="label">';
      $output[] = '    '.htmlentities($label).':</td>';
      $output[] = '  <td class="field">';
      $output[] = '    '.htmlentities($value).'</td></tr>';
  	}  	 
  	
  	return $this->output($output);
  }  
}

?>