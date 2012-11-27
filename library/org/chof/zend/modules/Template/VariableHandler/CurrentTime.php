<?php

/**
 * 
 * Variable handler which inserts the current date and time into a mail template
 * 
 * The variables supported are as follows:
 * 
 * <? CURRENTDATE-LONG ?>:
 * The current date in the long date format: "l, d.M.Y"
 * 
 * <? CURRENTDATE-SHORT ?>
 * The current date in the short date format: "d.M.Y"
 * 
 * <? CURRENTTIME ?>
 * The current date in the format: 'H:i:s'
 * 
 * <? CURRENTSTAMP-LONG ?>
 * The current timestamp in the format: "l, d.M.Y H:i:s e"
 * 
 * <? CURRENTSTAMP-SHORT ?>
 * The current timestamp int the format: 'd.M.Y H:i:s'
 * 
 * @author chof
 *
 */
class Chof_Zend_Template_VariableHandler_CurrentTime 
      implements Chof_Zend_Template_VariableHandler_Interface
{
	/**
	 * Registers the handler for all variables
	 */
	public static function registerHandler()
  #*****************************************************************************
	{
	  $registry = Chof_Zend_Template_VariableHandler_Registry::getInstance();
    $handler = new Chof_Zend_Template_VariableHandler_CurrentTime();
	  
		$registry->registerHandler('CURRENTDATE-LONG', $handler);
		$registry->registerHandler('CURRENTDATE-SHORT', $handler);
		$registry->registerHandler('CURRENTTIME', $handler);
		$registry->registerHandler('CURRENTSTAMP-LONG', $handler);
		$registry->registerHandler('CURRENTSTAMP-SHORT', $handler);
	}
	     	
	public function provideInformation($variable)
  #*****************************************************************************
	{
		$now = new DateTime();
		
		switch($variable)
		{
			case 'CURRENTDATE-LONG'   : return $now->format('l, d.M.Y');
			case 'CURRENTDATE-SHORT'  : return $now->format('d.M.Y');
			case 'CURRENTTIME'        : return $now->format('H:i:s');
			case 'CURRENTSTAMP-LONG'  : return $now->format('l, d.M.Y H:i:s e');
			case 'CURRENTSTAMP-SHORT' : return $now->format('d.M.Y H:i:s');
		}
	}
}
  
?>
