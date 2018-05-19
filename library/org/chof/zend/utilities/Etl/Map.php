<?php 

abstract class Chof_Util_Etl_Map
{
  protected static $instance = null;
  
  protected $targets;
  
  public static function map(array $input)
  //****************************************************************************
  {
    $class = get_called_class();
    self::$instance = new $class();
    
    $result = array();
    
    foreach($input as $row) 
    {
      $result[] = self::$instance->convertRow(self::$instance->mapRow($row));
    }
    
    return $result;
  }
  
  private static function simpleConversion($type, $value)
  //***************************************************************************
  {
    if ($type == 'integer')
    {
      return (int)$value;
    }
    else if ($type == 'number')
    {
       return (float)$value;
    }
    else if ($type == 'boolean')
    {
       return ($value) ? true : false;
    }
    else if ($type == 'date')
    {
       return new DateTime($value);
    }
    else if ($type == 'string')
    {
      return "$value";
    }
    else
    {
      throw new Chof_Util_Etl_Map_TypeDoesNotExist(
        "$type cannot be used as a target type");
    }
  }
  
  protected function __construct()
  //****************************************************************************
  {
    $this->targets = $this->defineTargetStructure();
  }
  
  protected function convertRow($row)
  //****************************************************************************
  {
    $converted = array();
    foreach($this->targets as $column => $def)
    {
      if (is_string($def))
      {
        $converted[$column] = self::simpleConversion($def, $row[$column]);
      }
      else if (is_array($def) && 
               (isset($def['type'])) && (isset($def['pattern'])))
      {
        if ($def['type'] == 'date')
        {
          $converted[$column] = Chof_Util_TimeUtils::returnTime(
            $def['pattern'], $row[$column]);
        }
        else
        {
          $converted[$column] = self::simpleConversion($def['type'],
            sprintf($def['pattrn'], $row['column']));
        }
      }
      else 
      {
        throw new Chof_Util_Etl_Map_WrongDefinition(
          "If you provde not a type as def you need to provide type + pattern");
      }
      
    }
    
    return $converted;
  }
  
  protected abstract function defineTargetStructure();
  protected abstract function mapRow($row);
}

class Chof_Util_Etl_Map_TypeDoesNotExist extends Zend_Exception
{
  
}

class Chof_Util_Etl_Map_WrongDefinition extends Zend_Exception
{
  
}

?>