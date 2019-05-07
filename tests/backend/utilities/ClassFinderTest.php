<?php

class ClassFinderTest extends TestCase_Base
{
  
  public function testClassFinderStandard()
  //****************************************************************************
  {
    
    $classlist = Chof_Util_ClassFinder::obtainClasses(
      Default_Model_Hierarchy_Empty::class);
   
    $this->assertArrayEquals(
      array(
        'Default_Model_Hierarchy_Empty',
        'Default_Model_Hierarchy_StrategyA',
        'Default_Model_Hierarchy_StrategyB'
      ), $classlist);
  }
  
  /**
   * @expectedException NoClassHierarchyFound
   */
  public function testClassFinderInvalid()
  //****************************************************************************
  {
    $classlist = Chof_Util_ClassFinder::obtainClasses(
      'Default_Model_Library');
    
  }
}

?>