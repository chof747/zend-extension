<?php

class Chof_Util_Etl_Read_Json extends Chof_Util_Etl_Read
{
  public static function read($filename)
  #*****************************************************************************
  {
    if ($jsonContent = file_get_contents($filename))
    {
    }
    else
    {
      throw new Chof_Util_Etl_FileOpenError("Could not read file $filename");
    }
    
    return Zend_Json::decode($jsonContent);
  }
}  
?>