<?php
class TestContextWithErrors implements Chof_Util_Etl_Map_Context
{
  public $errors;
  
  public function __construct()
  {
    $this->errors = array();
  }
  
  public function mapName(array $input)
  //****************************************************************************
  {
    return eval('return $input["first_name"]." ".$input["second_name"];');
  }

  public function mapCountry_of_Residence(array $input)
  //****************************************************************************
  {
    throw new Exception('no country for old men', 42);
  }
  
  public function handleMappingError(Exception $e, $line, $column, $inputRow)
  //****************************************************************************
  {
    $this->errors[] = array(
       'line' => $line,
       'column' => $column,
       'message' => $e->getMessage()
    );
  }
}
?>