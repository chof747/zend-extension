<?php
interface Chof_Model_Interface_Validateable
{
  /**
   * Validates the specific object and returns true of the validation was correct
   * 
   * If the validation is not correct, the implementation should indicate the
   * errors by specific exceptions of the class Chof_Model_InvalidFieldException 
   */
  public function validate();
}