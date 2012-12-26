<?php

/**
 * 
 * Class providing encryption and decryption facilities with open ssl encryption
 * 
 * The class receives a pair of public and private keys which are then used
 * to encrypt or decrypt pieces of information (strings) up to the maximal
 * encryption length of the keys.
 * 
 * After setting up the class, information (strings) can be encrypted with the
 * function encrypt(string) and decrypted with the function decrypt(string). For
 * encryption the public key is used and for decrpytion the private. If this 
 * behaviour has to be reversed use the reverseEncryptionMode() function which
 * will switch the keys.
 * 
 * If more information than the possible encryption length is provided, the
 * methods will throw an SslEncryptionInformationTooLong exception.
 * 
 * The keys can be set upon creation of the object or with the setPrivateKey() 
 * and setPublicKey() method.
 * 
 * 
 * @author chof
 *
 */
class Chof_Util_SslEncryptionEngine 
{
	protected $reverseKeys = false;
	
	protected $publicKey;
	protected $privateKey;
	
	protected $privateKeyDetails;
	protected $publicKeyDetails;

	/**
	 * Creates a ssl ecnryption/decryption engine with a given private and public
	 * key pair
	 * 
	 * @param resource $publicKey
	 * @param resource $privateKey
	 */
	public function __construct($publicKey, $privateKey)
	#****************************************************************************
	{
		$this->setPublicKey($publicKey);
		$this->setPrivateKey($privateKey);
	}
	
	public function __destruct()
	#****************************************************************************
	{
		if (!empty($this->privateKey))
		{
			openssl_free_key($this->privateKey);
			$this->privateKey = null;
		}

		if (!empty($this->publicKey))
		{
			openssl_free_key($this->publicKey);
			$this->publicKey = null;
		}
	}
	
	/**
	 * Sets the private key and stores the key details for further processing
	 * 
	 * @param resource $privateKey the new private key
	 * @return a reference to itself for method chaining
	 */
	public function setPrivateKey($privateKey)
	#****************************************************************************
	{
		if ($privateKey !== null)
		{
			$this->privateKey = $privateKey;
			$this->privateKeyDetails = openssl_pkey_get_details($privateKey);
		}
		else
		{
			$this->privateKey = null;
			$this->privateKeyDetails = array('bits' => 0);
		}
		
		return $this;
	}

	/**
	 * Sets the public key and stores the key details for further processing
	 * 
	 * @param resource $publicKey the new pulbic key
	 * @return a reference to itself for method chaining
	 */
	public function setPublicKey($publicKey)
	#****************************************************************************
	{
		if ($publicKey !== null)
		{
			$this->publicKey = $publicKey;
			$this->publicKeyDetails = openssl_pkey_get_details($publicKey);
		}
		else
		{
			$this->publicKey = null;
			$this->publicKeyDetails = array('bits' => 0);
		}
		
		return $this;
	}
	
	/**
	 * Exchanges the public with the private key
	 * 
	 * @param boolean $reverse true if public or private keys must be reversed
	 */
	public function reverseEncryptionMode($reverse = true)
	#****************************************************************************
	{
		$this->reverseKeys = $reverse;
	}
	
	public function getEncryptionLength()
	#****************************************************************************
	{
		return $this->getMaxLength(false);
	}
	
	protected function getMaxLength($public = true, $full = false)
	#****************************************************************************
	{
		$bits = ($public) ? $this->publicKeyDetails['bits']
		                  : $this->privateKeyDetails['bits'];

		$bytes = ceil($bits / 8) - (($full) ? 0 : 11);
		return ($bytes > 0) ? $bytes : 0;
	}
	
	/**
	 * @return true if the private and public keys are reversed for encryption or
	 *              decryption, false otherwise 
	 */
	public function isReversed() 
	#****************************************************************************
	{
		return $this->reverseKeys;
	}
	
	/**
	 * Encrypts information
	 * 
	 * The information is either encrypted via the public (default) or private key
	 * To switch this behaviour call reverseEncryptionMode(true).
	 * 
	 * @param unknown_type $information
	 * @return true if the encryption was successfull, false otherwise
	 * @throws SslEncryptionInformationTooLong if the information was too long
	 */
	public function encrypt($information)
	#****************************************************************************
	{
		$public = !$this->reverseKeys;
		$maxLength = $this->getMaxLength($public);
		
		if (strlen($information) > $maxLength)
		{
			throw new SslEncryptionInformationTooLong($maxLength, strlen($information));
		}
		
		$encrypted = '';
		
		if ($public)
		{
			$ok = openssl_public_encrypt($information, $encrypted, $this->publicKey);
		}
		else
		{
			$ok= openssl_private_encrypt($information, $encrypted, $this->privateKey);
		}
		
		if ($ok)
		{
			return $encrypted;
		}
		else
		{
			return false;
		}
	
	}
	
	/**
	 * Decrypts information
	 * 
	 * The information is either decrypted via the pprivate (default) or public key
	 * To switch this behaviour call reverseEncryptionMode(true).
	 * 
	 * @param string $encrypted
	 * @return true if the decryption was successfull, false otherwise
	 * @throws SslEncryptionInformationTooLong if the information was too long
	 */
	public function decrypt($encrypted)
	{
		$private = !$this->reverseKeys;
		$length = $this->getMaxLength($private, true);
		
		if (strlen($encrypted) != $length)
		{
			throw new SslEncryptionInformationTooLong($length, sizeof($encrypted));
		}
		
		$information = '';
		
		if ($private)
		{
			$ok= openssl_private_decrypt($encrypted, $information, $this->privateKey);
		}
		else
		{
			$ok = openssl_public_decrypt($encrypted, $information, $this->publicKey);
		}
		
		if ($ok)
		{
			return $information;
		}
		else
		{
			return false;
		}
	}
}

/**
 * Exception if a provided information is too long for encryption
 * 
 * @author chof
 *
 */
class SslEncryptionInformationTooLong extends Zend_Exception
{
	protected $keyLength = 0;
	protected $infoLength = 0;
	
	public function __construct($keyLength, $infoLength,  
	                            $code = 0, Exception $previous = null)
	#****************************************************************************
	{
  	$this->keyLength = $keyLength;
  	$this->infoLength = $infoLength;
  	
  	parent::__construct(
  	"The information ($infoLength bytes) is too long for the encryption key ".
  	"($keyLength bytes)", $code, $previous);
  }
  
  public function getKeyLength() { return $this->keyLength; }
  public function getInfoLength() { return $this->infoLength; }
}

?>