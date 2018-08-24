<?php

/**
 * Extension of the Zend_Mail class for the Mailer with Template module.
 * 
 * This class provides the following extentions to the Zend_Mail module:
 * 
 *  - mails are generated by templates with variables to fill out dynamic text
 *  - eMail addresses, server and credentials of the sender can be configured by
 *    the Zend_Config application configuration file
 * 
 * @author chof
 */
class Chof_Zend_Mail extends Zend_Mail
{
	
	protected $template = null;
	protected $config = null;
	
	public function __construct(Chof_Zend_Template $template = null, 
	                            $charset = 'iso-8859-1')
	#*****************************************************************************
	{
  	parent::__construct($charset);
		
  	$config = Zend_Registry::get('chofconfig');
		$this->config = (isset($config->mailer)) 
		  ? $config->mailer
		  : new Zend_Config(array());
		  		
  	if($template !== null) 
  	{
  		$this->setTemplate($template);
  	}
  	
  	$this->assignSenderFromConfig()
  	     ->assignCredentialsFromConfig()
  	     ->assignReturnAddressFromConfig();	
  }	          

  private function assignSenderFromConfig()
	#*****************************************************************************
  {
  	if(isset($this->config->sender->email->address))
  	{
  		$this->setFrom($this->config->sender->email->address,
  		               (isset($this->config->sender->email->address)) ?
  		                 $this->config->sender->email->name :
  		                 '');
  		                 
  	}
  	
  	return $this;
  }
  
  private function assignCredentialsFromConfig()
	#*****************************************************************************
  {
  	if ((isset($this->config->sender->email->server)) &&
  	    (isset($this->config->sender->email->username)) &&
  	    ((isset($this->config->sender->email->password))))
  	{
  		$config = array(
  		  'auth'     => (isset($this->config->sender->email->auth)) ?
  		                  $this->config->sender->email->auth : 'login',
  		  'username' => $this->config->sender->email->username,
  		  'password' => $this->config->sender->email->password,
  		  'ssl'      => (isset($this->config->sender->email->ssl)) ?
  		                  $this->config->sender->email->ssl : ''
  		);
  		
  		$transport = new Zend_Mail_Transport_Smtp(
  		  $this->config->sender->email->server, $config);
  		Zend_Mail::setDefaultTransport($transport);
  	}
  	return $this;
  }
  
  private function assignReturnAddressFromConfig()
	#*****************************************************************************
  {
  	if (isset($this->config->returnaddress->address))
  	{
  		$this->setReplyTo($this->config->returnaddress->address,
  		  (isset($this->config->returnaddress->name)) ? 
  		    $this->config->returnaddress->name : '');
  	}
  	return $this;
  }
	
	#-----------------------------------------------------------------------------
	# GETTER AND SETTER METHODS
	#-----------------------------------------------------------------------------
	
	/**
	 * disabled method setBodyText
	 * @throws InvalidMethod
	 */
	public function setBodyText($txt, $charset = null, 
	                            $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
	#*****************************************************************************
	{
		throw new InvalidMethod('setBodyText must not be called from outside!');
	}
	
	/**
	 * disabled method setBodyHtml
	 * @throws InvalidMethod
	 */
	public function setBodyHtml($html, $charset = null, 
	                            $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
	#*****************************************************************************
  {
		throw new InvalidMethod('setBodyHtml must not be called from outside!');
  }
  
  /**
   * Assigns a new template for this mailer object
   *  
   * @param Chof_Zend_Template $template
   * @return Chof_Zend_Mail a reference to itself for method chaining
   */
  public function setTemplate(Chof_Zend_Template $template)
	#*****************************************************************************
  {
  	$this->template = $template;
  	return $this;
  }
  
  /**
   * Provides a reference for the template object for this mailer
   * 
   * @return Chof_Zend_Template template of this mailer
   */
  public function getTemplate()
	#*****************************************************************************
  {
  	return $this->template;
  }
  
  /**
   * Enter description here ...
   * 
   * @param Chof_Zend_Template_FilloutInterface $filler
   *   The filler object used to replace the variables in the template text 
   *   with real values
   * @param  string $charset see Zend_Mail
   * @param  string $encoding see Zend_Mail
   * @return Chof_Zend_Mail reference to itself for method chaining 
   */
  public function setBody(Chof_Zend_Template_FilloutInterface $filler,
                          $charset = null, 
	                        $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
	#*****************************************************************************
  {
  	if ($this->template === null)
  	{
  		throw new TemplateRequiredException('A template object is required!');
  	}

  	//retrieve the text from the template by utilizing the filler object
  	$text = $this->template->getText($filler);
  	
  	if ($this->template->getType() == 'txt')
  	{
  	  parent::setBodyText($text, $charset, $encoding);
  	}
  	else 
  	{
  		parent::setBodyHtml($text, $charset, $encoding);
  	}
  	
  	return $this;
  }
   
}

class InvalidMethod extends Exception {}
class TemplateRequiredException extends Zend_Exception {}

?>