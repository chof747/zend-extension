<?php

class Chof_Util_TimeUtils 
{
  
  #****************************************************************************
  public static function returnTime($format, $time)
  #****************************************************************************
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
}
?>