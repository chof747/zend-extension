<?php
/**
 * Baseclass for importing functionality
 * 
 * The importing functionality of a web application must be able to support the
 * following steps:
 * 
 *  - reading and interpreting data provided either by file or in an object
 *    notation
 *  - offering user or software interaction between the interpretation and the 
 *    actual import of the data into the systen
 *  - Handling and remedy of all errors and warnings which occur during the input
 *  
 *  Thus the importer provides two different result queues / arrays which can be
 *  used by a proper controller to handle with all the events / data provided by
 *  the importer:
 *  
 *  - an array of interpreted objects together with metadata to identify the type
 *    etc.
 *  - an array of standardized error messages to display all warnings and errors
 *    the error messages can contain specific remedy options which can be chosen 
 *    and are then executed by the specific importer
 *
 * @author christian.hofbauer
 * @package org.chof.model.import
 *
 */
abstract class Chof_Model_Import_BaseImporter
{
  /**
   * contains list of objects which are ready for import
   * 
   * this is a list of type Chof_Model_Import_Data which contains the
   * importet data elements as identified by the 
   * {@link Chof_Model_Import_BaseImporter::read} method.
   * 
   * @var array 
   */
  protected $imports;
  
  /**
   * contains the list of errors identified during the import
   * 
   * this is a list of typ Chof_Model_Import_Error elements which have been
   * identified during the last call of the 
   * {@link Chof_Model_Import_BaseImporter::read} method.
   * 
   * @var array
   */
  protected $errors;
  
  function __construct()
  #****************************************************************************
  {
    $this->imports = array();
    $this->errors  = array();
  }
  
  /**
   * invokes a data import
   * 
   * This is the public interface for the invokation of the importing algorithm
   * implemented. It performs some housekeeping - 
   * - clearing of the {@link $imports} list 
   * - clearing of the {@link $errors} queue
   * 
   * before the {@link read} method is called to execute the actual importing
   * algorithm
   * 
   * @param any $data
   * @return array the import function returns an associative array containing
   *               the number of
   *               - imported data elements
   *               - errors total
   *               - errors per error class
   */
  public function import($data)
  #****************************************************************************
  {
    $result = array();
    
    $this->imports = array();
    $this->errors  = array();
    
    $result['elements'] = $this->read($data);
    
    foreach(array(E_ERROR, E_WARNING, E_NOTICE) as $e)
    {
      $result[$e] = Chof_Model_BaseIterator::count(
      $this->listErrors($e));
    }
    
    return $result;
  }
  
  /**
   * Reads a set of data from which element should be importet
   * 
   * This is the working horse of the importing functionality since here the 
   * actual reading algorithm must be implemented and is called from this 
   * routine. Since the type fo the $data variable varies depending on the 
   * implementation of the importer, no special type is required in the abstract
   * version of the base class.
   * 
   * The read method is not called directly from outside the class but must be
   * always invoked via the {@link import} 
   * method, since this method performs some house keeping of the underlying
   * data structures.
   * 
   * @param any $data
   * @return integer the number of imported items 
   */
  abstract protected function read($data);
  
  #-----------------------------------------------------------------------------
  # Getter and Setter - Object handling
  #-----------------------------------------------------------------------------
   
  /**
   * retrieves the number of imports
   * 
   * @return integer number of imports
   */
  public function countImports()
  #****************************************************************************
  {
    return count($this->imports);  
  }
  
  /**
   * retrieves the number of errors occured during the last import
   * 
   * @return integer number of errors
   */
  public function countErrors()
  #****************************************************************************
  {
    return count($this->errors);  
  }

  /**
   * retrieves the error with the index $ix
   * @param $ix the index of the error
   * @return any the error requested or null
   */
  public function getError($ix)
  #****************************************************************************
  {
    return (isset($this->errors[$ix])) ? $this->errors[$ix] : null;
  }
  
  
  /**
   * provides an iterator for the import errors of the last import run
   * 
   * The iterator can be requested in three ways:
   *  - if provided without any parameters a sequence of all errors is returned
   *  - providing all errors of the given class $eclass if $ascriticalas is
   *    set to false
   *  - providing all errors as critical as or more critical as the given class
   *    if $ascriticallas is set tot true
   *    
   * @param integer $eclass       the query error class class
   * @param boolean $ascriticalas if true effects a filtering of the errors so 
   *                              that all errors which are as critical as or 
   *                              more critical as the given error class are 
   *                              selected
   * @return Chof_Model_Import_ErrorIterator an iterator containing the 
   *                                         requested sequence of errors
   */
  public function listErrors($eclass = null, $ascriticalas = false)
  #****************************************************************************
  {
    if ($eclass !== null)
      return new Chof_Model_Import_ErrorIterator($this->errors, $eclass,
                   ($ascriticalas) ? 
                     Chof_Model_Import_ErrorIterator::$ASCRITICALAS : 
                     Chof_Model_Import_ErrorIterator::$EXACT);
    else
      return new Chof_Model_Import_ErrorIterator($this->error, E_NOTICE,
                   Chof_Model_Import_ErrorIterator::$NONE);
  }

  /**
   * retrieves the import with the index $ix
   * @param $ix the index of the imported object
   * @return any the import type 
   */
  public function getImport($ix)
  #****************************************************************************
  {
    return (isset($this->imports[$ix])) ? $this->imports[$ix] : null;
  }
  
  /**
   * provides an iterator of the data elements detected for importing during
   * the import execution.
   * 
   * The iterator can be requested in two ways:
   *  - if provided without any parameters a sequence of all imports is 
   *    returned
   *  - providing all imports of the given type $type 
   * 
   * @param string $type the identifier of the type which should be filtered
   * @return Chof_Model_Import_DataIterator an iterator containing the requested
   *                                        sequence
   */
  public function listData($type = null)
  #****************************************************************************
  {
    if ($type !== null)
      return new Chof_Model_Import_DataIterator($this->imports, 
                   Chof_Model_Import_DataIterator::$EXACT, $type);
    else
      return new Chof_Model_Import_DataIterator($this->imports, 
                   Chof_Model_Import_DataIterator::$NONE);
  }
  
}

?>