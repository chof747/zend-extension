<?php 

abstract class Chof_Util_Etl_Map
{
  protected static $instance = null;
  private static $STRUCTURE_KEY = 'structure';
  
  protected $targets;
  
  public static function map(array $input, $structure = null)
  //****************************************************************************
  {
    $class = get_called_class();
    self::$instance = new $class($structure);
    
    $result = array();
    
    foreach($input as $row) 
    {
      $result[] = self::$instance->convertRow(self::$instance->mapRow($row));
    }
    
    return $result;
  }
  
  private static function extractStructure(array $structure)
  //****************************************************************************
  {
    if (array_key_exists(self::$STRUCTURE_KEY, $structure) && 
        is_array($structure[self::$STRUCTURE_KEY]))
    {
      return $structure[self::$STRUCTURE_KEY];
    }
    else
    {
      return $structure;
    }
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
  
  protected function __construct($structure = null)
  //****************************************************************************
  {
    if ($structure === null)
    {
      $this->targets = $this->defineTargetStructure();
    }
    else if (is_string($structure))
    {
      $this->targets = self::extractStructure(Zend_Json::decode($structure));
    }
    else if (is_array($structure))
    {
      $this->targets = self::extractStructure($structure);
    }
    else 
    {
      throw new Chof_Util_Etl_Map_WrongDefinition(
        "Provided structure is not a supported mapping specification!");
    }
    
    $this->initialize($structure);
  }
  
  protected function defineTargetStructure()
  //****************************************************************************
  {
    throw new Chof_Util_Etl_Map_WrongDefinition(
       "No target structure define, provide either json or array with definition!");
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
  
  protected abstract function initialize($structure);
  protected abstract function mapRow($row);
}

class Chof_Util_Etl_Map_TypeDoesNotExist extends Zend_Exception
{
  
}

class Chof_Util_Etl_Map_WrongDefinition extends Zend_Exception
{
  
}

?>