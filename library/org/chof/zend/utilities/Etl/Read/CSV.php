<?php

class Chof_Util_Etl_Read_CSV extends Chof_Util_Etl_Read
{
  
  private static $DELIMITERS = array(',', ';', '|', '\t');
  private static $ENCLOSURES = array('"', "'");
  
  
  public static function read($filename, $headers=1, 
                              $delimiter=null, $enclosure=null)
  #*****************************************************************************
  {
    $result = array();
    $fhandle = fopen($filename, 'r');
    if (is_resource($fhandle))
    {
      $linenumber = $headers;
      
      $columns = self::prepare($fhandle, 
                               $headers, $delimiter, $enclosure);
      $colcount = (count($columns) == 0) ? null : count($columns);
      while($line = fgets($fhandle))
      {
        $linenumber++;
        $line = trim($line," \t\n\r\0\x0B$delimiter");
        if (!empty($line))
        {
          $data = str_getcsv($line, $delimiter, $enclosure);
          //check that columns ar equal
          $colcount = ($colcount === null) ? count($data) : $colcount;
          if ($colcount != count($data))
          {
            throw new Chof_Util_Etl_FileReadError(sprintf(
              'columns not matching in line %s. Expected %d got %d',
              $linenumber,
              count($columns),
              count($data)));
          }
       
          $result[] = ($columns !== null) 
            ? array_combine($columns, $data)
            : $data;
        }
      }
      
      fclose($fhandle);
      return $result;
    }
    else
    {
      $error = error_get_last();
      throw new Chof_Util_Etl_FileOpenError($error['message'], $error['type']);
    }
  }
  
  private static function prepare($fhandle, $headers, 
    &$delimiter, &$enclosure)
  #*****************************************************************************
  {
    if ($headers<=0)
    {
      $delimiter = ($delimiter === null) ? ';'  : $delimiter;
      $enclosure = ($enclosure === null) ? '"'  : $enclosure;
      return null;
    }
    else
    {
      list($delimiter, $enclosure, $columns) = 
        self::processHeaders($fhandle, $headers);
      
      return $columns;
    }
  }
  
  private static function processHeaders($fhandle, $headers)
  #*****************************************************************************
  {
    //skip all lines except the last header line and preserve the last
    if ($headers>0)
    {
      $line = '';
      for($i=0;$i<$headers;$i++)
      {
        if(!($line = fgets($fhandle)))
        {
          throw new Chof_Util_Etl_FileReadError('less lines then headers');
        }
      }
      
      //check delimiters
      $delimiter = ';';
      $count = 0;      
      foreach(self::$DELIMITERS as $d)
      {
        $c = count(explode($d,$line));
        if ($c >= $count)
        {
          $count = $c;
          $delimiter = $d;
        }
      }
      
      //check for enclosures
      $count = 0;
      $enclosure = '"';
      foreach(self::$ENCLOSURES as $e)
      {
        $c = preg_match_all('/'.$e.'\\'.$delimiter.'/', $line) +
             preg_match_all('/'.'\\'.$delimiter.$e.'/', $line);
        if ($c >= $count)
        {
          $count = $c;
          $enclosure = $e;
        }
      }
      
      $columns = str_getcsv($line, $delimiter, $enclosure);
      return array($delimiter, $enclosure, $columns);
    }
    else
    {
      return array(';','"', false);
    }
    
  }
}

?>