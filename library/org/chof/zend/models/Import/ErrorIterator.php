<?php
/**
 * The error iterator provides then following functionalities:
 * 
 * It expects an array of Chof_Model_Import_Error objects and privides an 
 * iterator that can traverse the array filtered by
 * 
 *  - nothing     -- plain array iterator
 *  - error class -- retrieves only those errors which are of a specific
 *                   error class
 *  - criticality -- traverses all errors which are as critical as a specific
 *                   error class
 * 
 * @author christian
 * @package org.chof.model.import  */
class Chof_Model_Import_ErrorIterator extends Chof_Model_BaseIterator 
{
  public static $NONE         = 0;
  public static $EXACT        = 1;
  public static $ASCRITICALAS = 2;
  
  
  private $eClass   = E_ERROR;
  private $type     = 0;
  
  private function checkError()
  #****************************************************************************
  {
    if (!$this->checkType($this->current())) return true;
    
    switch ($this->type)
    {
      case Chof_Model_Import_ErrorIterator::$EXACT :
        return $this->current()->getErrorClass() == $this->eClass;
      case Chof_Model_Import_ErrorIterator::$ASCRITICALAS :
        return $this->current()->isAsCriticalAs($this->eClass);
      default : 
        return true;
    }
  }
  
  protected function checkType($element = null)
  #****************************************************************************
  {
    if ($element === null)
      return 'Chof_Model_Import_Error';
    else 
      return ($element instanceof Chof_Model_Import_Error);
  }

  /**
   * Constructor setting up a specific error iterator
   * 
   * For the type of filter, specified by $type use the following static 
   * class constants:
   * 
   * - Chof_Model_Import_ErrorIterator::$NONE  -- no filtering
   * - Chof_Model_Import_ErrorIterator::$EXACT -- only errors of the $eClass error
   *                                            class
   * - Chof_Model_Import_ErrorIterator::$ASCRITICALAS -- errors which have at
   *                                                   least an error class as 
   *                                                   critical as $eClass
   *    
   * 
   * @param $errors the array of errors
   * @param $eClass the error class
   * @param $type   the type of filter
   */
  public function __construct(&$errors, $eClass = E_ERROR, $type = 1) 
  #****************************************************************************
  {
    $this->eClass = $eClass;
    $this->type = $type;
    
    parent::__construct($errors);
  }

  function next() 
  #****************************************************************************
  {
    do
    {
      ++$this->position;
      if ($this->position >= count($this->array))
        return;
      else if ($this->checkError())
        return;
        
    } while (true);
  }

  function rewind() 
  #****************************************************************************
  {
    $this->position = -1;
    $this->next();
  }
}

?>