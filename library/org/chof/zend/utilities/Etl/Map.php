<?php 

abstract class Chof_Util_Etl_Map
{
  protected static $instance = null;
  public static $STRUCTURE_KEY = 'structure';
  
  protected $targets;
  protected $context;
  
  public static function map(array $input, $structure = null, 
                             Chof_Util_Etl_Map_Context $context = null)
  //****************************************************************************
  {
    $class = get_called_class();
    self::$instance = new $class($structure, $context);
    
    $result = array();
    $line = 1;
    
    foreach($input as $row) 
    {
      $mapped = false;
      try
      {
        $mapped = self::$instance->mapRow($row);
      }
      catch(Exception $e)
      {
        self::handleError($context, $e, $line, $input);
      }
      
      if ($mapped !== false)
      {
        try
        {
          $result[] = self::$instance->convertRow($mapped);
        }
        catch(Chof_Util_Etl_Map_ConversionError $e)
        {
          self::handleError($context, $e, $line, $input);
        }
      }

      $line++;
    }
    
    return $result;
  }
  
  private static function handleError(Chof_Util_Etl_Map_Context $context,
                                      Exception $e, $line, $input)
  //****************************************************************************
  {
    if (is_object($context))
    {
      $context->handleMappingError(
          $e,
          $line,
          ($e instanceof Chof_Util_Etl_Map_Exception) ? $e->column : null,
          $input);
    }
    else
    {
      throw $e;
    }  
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
  
  private static function simpleConversion($type, $value, $column)
  //***************************************************************************
  {
    if (array_search($type, array(
        'integer', 'number', 'boolean', 'date', 'string'))===false)
    {
      throw new Chof_Util_Etl_Map_TypeDoesNotExist(
          "$type cannot be used as a target type");
    }
    
    try
    {
      if (($type == 'integer') || ($type == 'number'))
      {
        if (is_numeric($value))
        { 
          return $value * (($type == 'integer') ? 1 : 1.0);
        }
        else
        {
          throw new UnexpectedValueException("$value is not a number");
        }
      }
      else if ($type == 'boolean')
      {
         return ($value) ? true : false;
      }
      else if ($type == 'date')
      {
         return new DateTime($value);
      }
      else 
      {
        return "$value";
      }
    }
    catch(Exception $e)
    {
      throw new Chof_Util_Etl_Map_ConversionError($e, $column);
    }
  }
  
  protected function __construct($structure = null, $context = array())
  //****************************************************************************
  {
    $this->context = $context;
    
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
        $converted[$column] = self::simpleConversion($def, $row[$column], $column);
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
            sprintf($def['pattern'], $row['column'], $column));
        }
      }
      else 
      {
        throw new Chof_Util_Etl_Map_WrongDefinition(
          "If you provide not a type as def you need to provide type + pattern");
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

class Chof_Util_Etl_Map_ConversionError extends Chof_Util_Etl_Map_Exception
{
}

?>