<?php

class Chof_Util_SoapAutodetection extends Zend_Soap_AutoDiscover
{
    /**
     * Add a function to the WSDL document.
     *
     * @param $function Zend_Server_Reflection_Function_Abstract function to add
     * @param $wsdl Zend_Soap_Wsdl WSDL document
     * @param $port object wsdl:portType
     * @param $binding object wsdl:binding
     * @return void
     */
    protected function _addFunctionToWsdl($function, $wsdl, $port, $binding)
    {
    	$noPublish = false;
      if ($comment =$function->getDocComment())
      {
      	$noPublish = preg_match('/@nopublish/', $comment);
      }
      
    	if ((!$function->isStatic()) && (!$noPublish))
    	{
    		parent::_addFunctionToWsdl($function, $wsdl, $port, $binding);
    	}
    }
}

?>