<?php

class TestCase_Helper_Base
{
  protected $tc;
  
  /**
   * Setsup the additional assertion helper class
   *
   * @param TestCase_Base|TestCase_Controller| $testCase
   */
  public function __construct($testCase)
  #*****************************************************************************
  {
    $this->tc = $testCase;
  }
}
?>