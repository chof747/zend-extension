<?php

/**
 * 
 * Returns the signum of a number:
 * +1 if the number is larger than 0
 *  0 if the number is equal to 0
 * -1 if the number is smaller than 0
 * 
 * @param double $number
 */
function signum($number)
//******************************************************************************
{
	if (!is_numeric($number))
	{
		throw new InvalidArgumentException(
		  'parameter passed to signum must be a real number got: $number');
	}
	
	if ($number > 0)
	{
		return 1;
	}
	else if ($number < 0)
	{
		return -1;
	}
	else
	{
		return 0;
	}
}

function randString($length)
//******************************************************************************
{
  $chars = "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789";
  $randString = '';	

	$size = strlen($chars);
	for($i=0;$i<$length;$i++) 
	{
		$randString .= $chars[rand(0, $size-1)];
	}

	return $randString;
}