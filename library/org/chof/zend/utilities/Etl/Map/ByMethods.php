<?php

class Chof_Util_Etl_Map_ByMethods extends Chof_Util_Etl_Map
{
  public static $SIMPLE_KEY = 'mappings';
  
  protected $simples = array();
  protected $defaults = array();
  
  protected function initialize($structure)
  //****************************************************************************
  {
    if (!empty($structure) &&
        array_key_exists(self::$SIMPLE_KEY, $structure) &&
        is_array($structure[self::$SIMPLE_KEY]))
    {
      $mappings = $structure[self::$SIMPLE_KEY]; 
    }
    else
    {
      $mappings = $this->simpleMappings();
    }
    
    foreach($mappings as $column => $def)
    {
      if (substr($def, 0, 3) == 'cv=')
      {
        $this->defaults[$column] = substr($def, 3);
      }
      else
      {
        $this->simples[$column] = $def;
      }
    }
  }
  
  protected function simpleMappings()
  //****************************************************************************
  {
    return array();
  }
  
  protected function mapRow($input)
  //****************************************************************************
  {
    $mapped = array();
    foreach($this->targets as $column => $def)
    {

      if (array_key_exists($column, $this->defaults))
      {
        $mapped[$column] = $this->defaults[$column];
      }
      else if(array_key_exists($column, $this->simples))
      {
        $mapped[$column] = $input[$this->simples[$column]];
      }
      else
      {
        $method = 'map'.ucfirst(
          str_replace(' ', '_', 
            str_replace('-', '_', $column)
        ));
        
        if (method_exists($this, $method))
        {
          $mapped[$column] = $this->$method($input);
        }
        else if (is_object($this->context) && method_exists($this->context, $method))
        {
          $mapped[$column] = $this->context->$method($input);
        }
          
        else 
        {
          throw new Chof_Util_Etl_Map_MappingMethodNotFound(
            "No method found in ".get_class($this)." to map column $column");
        }
      }
    }
    
    return $mapped;
  }
}


class Chof_Util_Etl_Map_MappingMethodNotFound extends Zend_Exception
{
  
}
?>