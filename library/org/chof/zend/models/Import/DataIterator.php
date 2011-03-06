<?php
/** 
 * The data iterator provides then following functionalities:
 * 
 * It expects an array of Chof_Model_Import_Data objects and privides an 
 * iterator that can traverse the array filtered by
 * 
 *  - nothing     -- plain array iterator
 *  - type        -- retrieves only those data elements which are of a specific
 *                   data type (as specified by the 
 *                   {@link Chof_Model_Import_Data::$type value})
 *                    
 * 
 * @author christian
 * @package org.chof.model.import 
 */
class Chof_Model_Import_DataIterator extends Chof_Model_BaseIterator 
{
  public static $NONE         = 0;
  public static $EXACT        = 1;
  
  
  private $type       = 'general';
  private $filter     = 0;
  
  private function checkData()
  #****************************************************************************
  {
    if (!$this->checkType($this->current())) return true;
    
    switch ($this->filter)
    {
      case Chof_Model_Import_ErrorIterator::$EXACT :
        return $this->current()->getType() == $this->type;
      default : 
        return true;
    }
  }
  
  protected function checkType($element = null)
  #****************************************************************************
  {
    if ($element === null)
      return 'Chof_Model_Import_Data';
    else 
      return ($element instanceof Chof_Model_Import_Data);
  }

  /**
   * Constructor setting up a specific error iterator
   * 
   * For the type of filter, specified by $type use the following static 
   * class constants:
   * 
   * - Chof_Model_Import_DataIterator::$EXACT -- only data elements of the specified
   *                                             $type
   *    
   * 
   * @param array   $data   the array of data elements
   * @param integer $filter the type of filter
   * @param string  $type   the required type of the data elements
   */
  public function __construct(&$data, $filter = 0, $type = 'general') 
  #****************************************************************************
  {
    $this->filter = $filter;
    $this->type = $type;
    
    parent::__construct($data);
  }

  /**
   * @see Chof_Model_BaseIterator::next()
   */
  function next() 
  #****************************************************************************
  {
    do
    {
      ++$this->position;
      if ($this->position >= count($this->array))
        return;
      else if ($this->checkData())
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