<?php

class Chof_Util_TimeUtils 
{
  
  public static function returnTime($format, $time)
  //****************************************************************************
  {
    if (is_string($time))
    {
      $time = new DateTime(($time == '') ? '1970-01-01' : $time);
      $time->setTimeZone(new DateTimeZone(date_default_timezone_get()));
    }
    else if (is_numeric($time))
    {
      $time = new DateTime();
      $time->setTimeStamp($time);
    }
    
    $format = ($format == '') ? 'mysql' : $format;
    
    if ($format == 'datetime')
    {
      return $time;
    }
    elseif ($format == 'number')
    {
      return $time->getTimeStamp();
    }
    else
    {
      switch ($format)
      {
        //special formats for json communication and mysql queries
        case  'json'  : $format = 'Y-m-d\TH:i:s'; break;
        case  'xml'  : $format = 'Y-m-d\TH:i:s'; break;
        case  'mysql' : $format = 'Y-m-d H:i:s'; break;
        case  'mysql-date' : $format = 'Y-m-d'; break;
      }

      return $time->format($format);
    }
  }    
  
  private static function makePeriod($period)
  //****************************************************************************
  {
  	switch ($period)
  	{
  		case 'Y' : return 'P1Y'; 
  		case 'H' : return 'P6M';
  		case 'Q' : return 'P3M';
  		case 'W' : return 'P1W';
  		case 'D' : return 'P1D';
  		default:   return 'P1M';
  	}
  }
  
  public static function printPeriodsByTemplate($template)
  //****************************************************************************
  {
  	$periods = array(
  	  'Y' => 'yearly',
  	  'H' => 'half-yearly',
  	  'Q' => 'quaterly',
  	  'M' => 'monthly',
  	  'W' => 'weekly',
  	  'D' => 'daily' );
  	
  	$results = array();
  	
  	foreach($periods as $symbol => $name)
  	{
  		$results[$symbol] = sprintf($template, $symbol, $name);
  	}
  	
  	return $results;
  }
  
  /**
   * retrieves the date interval corresponding to the provided period identifier
   * 
   * Y - yearly
   * H - half-yearly
   * Q - quaterly
   * M - monthly
   * W - weekly
   * D - daily
   * 
   * @param string $period
   * @return DateInterval the corresponding date interval object
   */
  public static function intervalFromPeriod($period)
  //***************************************************************************
  {  	
  	return new DateInterval(Chof_Util_TimeUtils::makePeriod($period));
  }
  
  public static function closestIntervalStart(DateTime $startDate, $period)
  //***************************************************************************
  {
  	$start = clone $startDate;
  	$dateparts = getdate($start->getTimeStamp());
  	
  	switch($period)
  	{
  		case 'Y' : 
  			$start->setDate($dateparts['year'], 1, 1); 
  			break;
  		
  		case 'H' :
  			$start->setDate($dateparts['year'],
  			                ($dateparts['mon'] <= 6) ? 1 : 7,
  			                1);
  			break;
  		
  	  case 'Q' : 
  			$start->setDate($dateparts['year'],
  			                floor(($dateparts['mon'] - 1) / 3) * 3 + 1,
  			                1);
  			break;
  	  
  	  case 'M' :
  	  	$start->setDate($dateparts['year'],
  	  	                $dateparts['mon'],
  	  	                1);
  	  	break;

  	  case 'W' :
  	    $daydiff = ($dateparts['wday'] == 0) ? 6 : $dateparts['wday'] - 1;
  	    $start->sub(new DateInterval('P'.$daydiff.'D'));
  	    break; 
  	}
  	
  	return $start;
  }
}
?>