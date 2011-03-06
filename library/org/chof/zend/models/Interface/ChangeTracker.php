<?php

/**
 * A composite is a business object that contains several sub elements.
 * This interface summarizes and declares the methods a composite should offer
 * its sub elements
 * 
 * @author Christian
 *
 */
interface Chof_Model_Interface_ChangeTracker extends Chof_Model_Interface_Changeable
{
  /**
   * Notifies a composite object that an element has changed
   * 
   * @param element the element that has changed
   * @return true if the composite has acknowledged the element change
   */
  public function elementChanged($element);
  
} 