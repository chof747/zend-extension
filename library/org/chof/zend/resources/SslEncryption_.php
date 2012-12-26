<?php

class Chof_Resource_SslEncryption extends 
  Zend_Application_Resource_ResourceAbstract
{
	private $encryption = null;
	
	
	 private function getKey($path)
	 #****************************************************************************
	 {
	 	 return file_get_contents(str_replace('\\', '/', $path));
	 }
	
	 public function init()
	 #****************************************************************************
	 {
	 	 $privateKey = null;
	 	 $publicKey = null;
	 	 
	 	 	 	 
	 	 $options = $this->getOptions();
	 	 
	 	 if ((!empty($options['keyfile'])) && (is_array($options['keyfile'])))
	 	 {
	 	 	 $keyfiles = $options['keyfile'];
	 	 	 
	 	 	 if (!empty($keyfiles['private']))
	 	 	 {
	 	 	 	 $privateKey = 
	 	 	 	   openssl_pkey_get_private($this->getKey($keyfiles['private']));
	 	 	 }
	 	 	 if (!empty($keyfiles['public']))
	 	 	 {
	 	 	 	 $publicKey = 
	 	 	 	   openssl_pkey_get_public($this->getKey($keyfiles['public']));
	 	 	 }
	 	 	 
	 	 	 if ((!empty($publicKey)) || (!empty($privateKey)))
	 	 	 {
	 	 	   return new Chof_Util_SslEncryptionEngine($publicKey, $privateKey);
	 	 	 }
	 	 	 else
	 	 	 {
	 	 	 	 return null;
	 	 	 }
	 	 }
	 	 else
	 	   return null;
  }
}

?>