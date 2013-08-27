<?php

class Chof_Util_Url
{
	public static function url($controller, $action, $params, $base = '')
	#*****************************************************************************
	{
		$p = array();
		foreach($params as $param => $value)
		{
			$p []= "$param=$value";
		}
		 
		$url  = join('/', array($base, $controller, $action));
		$url .= (count($p)>0) ? '?'.join('&', $p) : '';

		return $url;

	}

	public static function baseUrl($url)
	#*****************************************************************************
	{
		$controller = Zend_Controller_Front::getInstance();
		return $controller->getBaseUrl().'/'.$url;
	}

	public static function getClientIP()
	#*****************************************************************************
	{
		if ( isset($_SERVER["REMOTE_ADDR"]) ) 
		{
			return $_SERVER["REMOTE_ADDR"];
		} 
		else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )    
		{
			return $_SERVER["HTTP_X_FORWARDED_FOR"];
		} 
		else if ( isset($_SERVER["HTTP_CLIENT_IP"]) )    
		{
			return $_SERVER["HTTP_CLIENT_IP"];
		}
	}
}
	?>