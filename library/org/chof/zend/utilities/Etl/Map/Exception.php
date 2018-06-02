<?php
class Chof_Util_Etl_Map_Exception extends Zend_Exception
{
  public $column;
  public $originalException;

  public function __construct(Exception $original, $column)
  {
    $this->column = $column;
    parent::__construct($original->getMessage(), $original->getCode(), $original);
  }
}
?>