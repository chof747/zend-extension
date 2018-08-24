<?php

/**
 * 
 * Singleton class providing the registry for variable handlers
 * 
 * @author chof
 *
 */
class Chof_Zend_Template_VariableHandler_Registry
{
	private static $instance = null;
	
  /**
   * retrieves the singelton instance of the 
   * Chof_Zend_Mail_VariableHanlder_Registry
   */
  static public function getInstance()
  #*****************************************************************************
  {
    if (Chof_Zend_Template_VariableHandler_Registry::$instance === null)
    {
      Chof_Zend_Template_VariableHandler_Registry::$instance = 
        new Chof_Zend_Template_VariableHandler_Registry();
    }
    
    
    return Chof_Zend_Template_VariableHandler_Registry::$instance;
  }
	

	private $handlers;
	
	private function __construct()
  #*****************************************************************************
	{
		$this->handlers = array();
	}
	
	/**
	 * Registers the provided variable handler for the given variable name.
	 * 
	 * If a handler has been associated before with the variable, the mechanism
	 * overrides the old hanlder with the new one but returns the instance of the
	 * old one.
	 * 
	 * @param string $variable name of the variable
	 * @param Chof_Zend_Template_VariableHandler_Interface $handler instance of the
	 *                                                          handler
	 * @return Chof_Zend_Template_VariableHandler_Interface instance of the old 
	 *         variable handler if present. Null otherwise                                                         
	 */
	public function registerHandler($variable,
	  Chof_Zend_Template_VariableHandler_Interface $handler)
  #*****************************************************************************
	{
		$oldHandler = null;
	  if (isset($this->handlers[$variable]))
	  {
	  	$oldHandler = $this->handlers[$variable];
	  }
	  
	  $this->handlers[$variable] = $handler;
	  return $oldHandler;
	}
	
	/**
	 * Removes a variable handler from the registry and returns the handler. If
	 * no handler was registered return Null
	 * 
	 * @param string $variable
	 * @return Chof_Zend_Template_VariableHandler_Interface instance of the old 
	 *         handler or null otherwise
	 */
	public function unregisterHandler($variable)
  #*****************************************************************************
	{
		$oldHandler = null;
		if ($this->hasHandlerFor($variable))
		{
			$oldHandler = $this->handlers[$variable];
		  unset($this->handler[$variable]);
		}
		
		return $oldHandler;
	}
	
	/**
	 * Checks wether a handler is registered for the given variable
	 * 
	 * @param string $variable
	 * @return true if the handler exists for the variable, false otherwise
	 */
	public function hasHandlerFor($variable)
  #*****************************************************************************
	{
		return isset($this->handlers[$variable]);
	}
	
	/**
	 * Returns the instance of the variable handler registered under the name of 
	 * the variable
	 * 
	 * @param string $variable
	 * @return Chof_Zend_Template_VariableHandler_Interface instance of the variable 
	 *                                                  handler, null otherwise
	 *                                                  
	 */
	public function getHandlerFor($variable)
  #*****************************************************************************
	{
		if ($this->hasHandlerFor($variable))
		{
			return $this->handlers[$variable];
		}
		else
		{
			return null;
		}
	}
}

?>