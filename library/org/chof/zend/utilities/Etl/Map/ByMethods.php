<?php

abstract class Chof_Util_Etl_Map_ByMethods extends Chof_Util_Etl_Map
{
  private static $SIMPLE_KEY = 'mappings';
  
  protected $simples;
  
  protected function initialize($structure)
  //****************************************************************************
  {
    if (!empty($structure) &&
        array_key_exists(self::$SIMPLE_KEY, $structure) &&
        is_array($structure[self::$SIMPLE_KEY]))
    {
      $this->simples = $structure[self::$SIMPLE_KEY]; 
    }
    else
    {
      $this->simples = $this->simpleMappings();
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

      
      if(array_key_exists($column, $this->simples))
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