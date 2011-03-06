<?php

/**
 * Base iterator class for typesafe PHP iterators
 * 
 * @author christian
 * @package org.chof.model
 * 
 * The base iterator implements the main methods of the iterator interface
 * and allows the traversing of object collections in a type safe way
 */
abstract class Chof_Model_BaseIterator implements Iterator 
{
  
  protected $position      = 0;
  protected $array         = null;

  public function __construct(&$array)
  #****************************************************************************
  {
    $this->array       = &$array;
    $this->position    = 0;
  }
  
  /**
   * provides a special way to implement a count method
   * 
   * Override this method if your iterator implementation iterates over the 
   * items only in an expensive or intrusive way (e.g. fetches datasets from a
   * database or changes the elements during iteration), since the standard
   * counting algorithm calls for an iteration over the sequence represented
   * by the iterator.
   * 
   * @return number of elements in the sequence or null if standard method
   *                should be used.
   */
  function countElements()
  #****************************************************************************
  {
    return null;
  }

  function current()
  #****************************************************************************
  {
    return $this->array[$this->position];
  }

  function key() 
  #****************************************************************************
  {
    return $this->position;
  }

  function rewind() { }
  function next() { } 

  /**
   * checks if the current element is valid
   * 
   * The method performs two checks:
   *  - it checks if there is an element at $position in the array and
   *  - calls the checkType() method of the iterator to verify the type
   *  
   * The method throws an Chof_Model_IteratorTypeException if the element is 
   * of a different type
   */
  function valid() 
  #****************************************************************************
  {
    if (isset($this->array[$this->position]))
      if ($this->checkType($this->array[$this->position]))
        return true;
      else
        throw new Chof_Model_IteratorTypeException(
          (string) $this->checkType(), 
          is_object($this->array[$this->position]) ? 
          get_class($this->array[$this->position]) : gettype($this->array[$this->position]),
          $this->array[$this->position]);
    else
      return false;
  }
  
  /**
   * 
   * @param $element
   * @return 
   *  - true if the $element is of a type that can be handled by the 
   *    itarator, 
   *  - false if $element is not empty and cannot by handled by the iterator
   *  - the typename of $element is not given
   */
  abstract protected function checkType($element = null);
  

  /**
   * Static helper function to count iterators
   * 
   * The function checks of the iterator has the special <b>count()</b> method
   * implemented (i.e. checks if this method returns something different to
   * Null), takes either this value or iterates and counts the elements in the
   * sequence.
   * 
   * Be aware, that this function iterates once over the iterator sequence,
   * so if the iterator implements some expesive fetching functions (e.g. DB
   * operations) or changes its objects, this simple method to count elements
   * is strongly discouraged! - Please use other means to count those iterators
   * e.g. override the countElements() class method.
   * 
   * @param Iterator $it the iterator to count
   * @return number count of elements represented by the iterator
   */
  static public function count(Chof_Model_BaseIterator $it)
  #****************************************************************************
  {
    $c = $it->countElements();
    if ($c == null)
    {
      $c = 0;
      foreach($it as $v) ++$c;
    } 
    
    return $c;
      
  }
  
}
?>