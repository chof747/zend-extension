<?php

interface Chof_Util_Etl_Map_Context
{
  public function handleMappingError(Exception $e, $line, $column, $inputRow);
}

?>