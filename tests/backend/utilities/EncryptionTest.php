<?php

class EncryptionTest extends TestCase_Base
{
	
  public function testResource()
  //****************************************************************************
  {
  	//$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
  	$this->assertTrue($this->application->getBootstrap()->hasPluginResource('sslEncryption'));
  	
  }
/**
 * 
 */private function getSslResource()
  //****************************************************************************
  {
    $sslResource = $this->application->getBootstrap()->getPluginResource('sslEncryption');
  	return $sslResource->init();
  }

  
  public function testEncryption()
  //****************************************************************************
  {
  	$sslencryptor = $this->getSslResource();
  	
  	$this->assertFalse($sslencryptor->isReversed());
  	$this->encryptDecrypt($sslencryptor);
  }
  
  private function encryptDecrypt(Chof_Util_SslEncryptionEngine $sslencryptor)
  //****************************************************************************
  {	
  	$information = 'Mary has a little lamp';
    $encrypted = $sslencryptor->encrypt($information);
    
    $decoded = $sslencryptor->decrypt($encrypted);
    $this->assertEquals($information, $decoded);
    
  }
  
  public function testReverseEncryption()
  //****************************************************************************
  {
  	$sslencryptor = $this->getSslResource();
    
  	$sslencryptor->reverseEncryptionMode(true);
  	$this->assertTrue($sslencryptor->isReversed());
  	
  	$this->encryptDecrypt($sslencryptor);
  }
  
    /**
   * @expectedException SslEncryptionInformationTooLong
   */
  public function testTooLongData()
  //****************************************************************************
  {
  	$sslencryptor = $this->getSslResource();
    $string = randString($sslencryptor->getEncryptionLength() + 1);
  	
    $sslencryptor->encrypt($string);
  }

  public function _testExtras()
  //****************************************************************************
  {
  	$sslencryptor = $this->getSslResource();
    echo "\nUsername: [".base64_encode($sslencryptor->encrypt("mustermax"))."]";
  	echo "\nPassword: [".base64_encode($sslencryptor->encrypt("max4mu"))."]";
  	echo "\n";
  	echo "\nUsername: [".base64_encode($sslencryptor->encrypt("malerlilli"))."]";
  	echo "\nPassword: [".base64_encode($sslencryptor->encrypt("ma2lilli"))."]";
  	echo "\n";
  }
}

?>