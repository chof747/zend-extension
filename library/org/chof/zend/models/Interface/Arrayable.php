<?php
/**
 * simple way to provide unified interface for property array export
 * 
 * the arrayable interface provides a unified way to transform any object
 * implementing this interface to provide its features within an associative
 * array.
 * 
 * The interface has one method: toArray()
 *
 * @author christian.hofbauer
 * @package org.chof.model.interface
 * 
 */
interface Chof_Model_Interface_Arrayable 
{
  /**
   * provides a unified way to transform an object into an array
   * 
   * The toArray function must generate an exhaustive array of the form
   * <code>
   *    { 
   *      property => value,
   *      ...
   *    }
   * </code>
   * 
   * @return an array containing all properties of the object/class
   */
  public function toArray($datetimefmt = '');
}