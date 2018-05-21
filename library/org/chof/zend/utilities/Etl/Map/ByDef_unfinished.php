<?php

function array_map_assoc(callable $f, array $a) {
  return array_column(array_map($f, array_keys($a), $a), 1, 0);
}

abstract class Chof_Util_Etl_Map_ByDef extends Chof_Util_Etl_Map
{
  private static $MAPPING_KEY = 'mappings';
  
  private static $REGEXP_FUNC = '/([\w_]*?)\(([\w,#\s]*?)\)/';
  
  protected $mappings;
  
  protected function initialize($structure)
  //****************************************************************************
  {
    if (!empty($structure) &&
        array_key_exists(self::$MAPPING_KEY, $structure) &&
        is_array($structure[self::$MAPPING_KEY]))
    {
      $this->mappings = array_map_assoc(array($this, 'checkMapping'),
        $structure[self::$MAPPING_KEY]);
    }
    else
    {
      throw Chof_Util_Etl_MappingNotFound(
        "Mapping not found must be provided as ".self::$MAPPING_KEY.
        " in a specification structure");
    }
  }
  
  private function checkMapping($mapping, $column)
  //****************************************************************************
  {
    return [$column, $mapping];
  }
  
  protected function allowedFunctions()
  {
    return array(
      'abs' => 1, 
      'sin' => 1, 
      'cos' => 1, 
      'exp' => 1,
      'pow' => 2,
      'pi'  => 0
    );
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


class Chof_Util_Etl_Map_NotFound extends Zend_Exception
{
  
}
?>