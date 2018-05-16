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
	 	   $queueOptions = array(
	 	       'name' => $options['name'],
	 	       'driverOptions' => $options['driverOptions']);
	 	   
	 	   if (!empty($options['adapterNamespace']))
	 	   {
	 	     $queueOptions['adapterNamespace'] = $options['adapterNamespace'];
	 	   }
	 	   
	 	   $queue = new Zend_Queue ($options['adapter'], new Zend_Config($queueOptions));
	 	   
	 	   if(!empty($options['messageClass']))
	 	   {
	 	     $queue->setMessageClass($options['messageClass']);
	 	   }

	 	 	 if(!empty($options['messageClassSet']))
	 	   {
	 	     $queue->setMessageClassSet($options['messageClassSet']);
	 	   }

	 	   return $queue;
	 	 }
	 	 else
	 	   return null;
  }
}

?>