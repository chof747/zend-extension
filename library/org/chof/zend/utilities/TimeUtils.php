<?php

function threeAlternatives($pcn, $date, $prev,$current,$next)
#*******************************************************************************
{
  if ($pcn > 0)
  {
    return $date->add(new DateInterval($next));
  }
  else if ($pcn == 0)
  {
    return $date->sub(new DateInterval($current));
  }
  else
  {
    return $date->sub(new DateInterval($prev));
  }
}

class Chof_Util_TimeUtils 
{
  public static $MAP_INTERVAL_PERIOD = array(
    'P3M' => 'Q',
    'P1M' => 'M',
    'P1Y' => 'Y',
    'P1W' => 'W',
    'P7D' => 'W'
  );  
  
  public static function today()
  //****************************************************************************
  {
    $today = new DateTime();
    $today->setTime(0,0,0);
    
    return $today;
  }
  
  public static function compareTime(DateTime $a, DateTime $b)
  //****************************************************************************
  {
    if ($a->getTimeStamp() > $b->getTimeStamp()) {
      return -1;
    }
    elseif ($a->getTimeStamp() < $b->getTimeStamp()) {
      return 1;
    }
    else {
      return 0;
    }
  }
  
  public static function transformTime($newFormat, $time)
  //****************************************************************************
	{
		$dtime  = self::returnTime('datetime', $time);
		return self::returnTime($newFormat, $dtime);
	}
  
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
      if ($time instanceof DateTime)
      {
        //clone to avoid issues when changing afterwards
        return clone $time;
      }
      else
      {
        return null;
      }
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
        
        //format for file timestamps
        case  'file' : $format = 'YmdHis'; break;
        
        //specialing formats
        case 'date-long'       : $format = 'l, d.M.Y'; break;
        case 'date-short'      : $format = 'd.M.Y'; break;
        case 'datetime-long'   : $format = 'l, d.M.Y H:i:s e'; break;
        case 'datetime-short'  : $formst = 'd.M.Y H:i:s'; break;
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
  
  private static function periodBegin(DateTime $date, $period, $pcn = -1)
  {
    $date = clone $date;
    $date->setTime(0, 0, 0);
    $year = $date->format('Y');
    $month = $date->format('m');
    
    switch ($period)
    {
    	case 'D' : 
    	  return threeAlternatives($pcn, $date, 'P1D', 'P0D', 'P1D');
    	case 'W' : 
    	  $wd = ($date->format('w') + 6) % 7;
    	  return threeAlternatives($pcn, $date, 'P'.($wd+7).'D',
    	                                        'P'.($wd).'D',
    	                                        'P'.(7-$wd).'D');
    	                                        
    	case 'Y' : 
    	  $date->setDate($year, 1, 1);
    	  return threeAlternatives($pcn, $date, 'P1Y', 'P0Y', 'P1Y');
    	case 'H' :  
    	  $hm = floor(($month-1)/6) * 6 + 1;
    	  $date->setDate($year, $hm, 1);
    	  return threeAlternatives($pcn, $date, 'P6M', 'P0M', 'P6M');
    	   
    	case 'Q' : 
    	  $qm = floor(($month-1)/3) * 3 + 1;
    	  $date->setDate($year, $qm, 1);
    	  return threeAlternatives($pcn, $date, 'P3M', 'P0M', 'P3M');
    	
    	default:  
    	  $date->setDate($year, $month, 1);
    	  return threeAlternatives($pcn, $date, 'P1M', 'P0M', 'P1M');
    	   
    }
    
  }
  
  /**
   * Returns the start and end date of a specific period in time
   * 
   * 
   * @param DateTime $date
   * @param string $period
   * @param integer $pcn
   */
  public static function fullPeriod($date, $period, $pcn = -1)
  //****************************************************************************
  {
    $begin = self::periodBegin($date, $period, $pcn);
    $end = clone $begin;
    $end->add(new DateInterval(self::makePeriod($period)));
    $end->sub(new DateInterval('P1D'));
    
    return array($begin, $end);
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