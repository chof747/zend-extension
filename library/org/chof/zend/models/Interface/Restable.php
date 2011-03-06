<?php 

interface Chof_Model_Interface_Restable extends Chof_Model_Interface_Arrayable
{ 
  //enables the reading of data from a plain Json
  public function fromArray($array);
}
?>