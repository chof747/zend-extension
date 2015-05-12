<?php

class Chof_Resource_Messagequeue extends 
  Zend_Application_Resource_ResourceAbstract
{
	private $queue = null;
	
	
	 public function init()
	 #****************************************************************************
	 {
	 	 $options = $this->getOptions();
	 	 
	 	 if ((!empty($options['adapter'])) &&
	 	     (!empty($options['name'])) &&
	 	     (!empty($options['driverOptions'])) && (is_array($options['driverOptions'])))
	 	 {
	 	   $queue = new Zend_Queue ($options['adapter'], new Zend_Config(array(
	 	       'name' => $options['name'],
	 	       'driverOptions' => $options['driverOptions'])));

	 	   return $queue;
	 	 }
	 	 else
	 	   return null;
  }
}

?>