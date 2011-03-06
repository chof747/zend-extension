<?php
interface Chof_Model_Interface_Changeable
{
  /**
   * Resets the change tracking functionality assuming that the current fields
   * of the object represent the initial state. 
   */
  public function initialize();
  
  /**
   * @return true if the object or its related data has been changed
   */
  public function hasChanged();  
}