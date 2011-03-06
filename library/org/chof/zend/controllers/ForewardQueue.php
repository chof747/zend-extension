<?php
 
/**
 * Implements the data structure of a foreward queue
 * 
 * The foreward queue consists of an array containing entries with for different 
 * elements each, which are representing the parameters of a _foreward() call:
 *  - the name of the action --> 'action'
 *  - the name of the controller --> 'controller'
 *  - the name of the module where the controller sits --> 'module'
 *  - an associative array containing the parameter of the foreward call
 * 
 * The class has two main method to operate:
 * 
 *  - push() adds a foreward request to the list
 *  - pop() returns the next foreward request and removes it from the list
 *  
 * The following method can be used to determine the number of the foreward 
 * requests in the queue:
 * 
 *  - count() returns the number of requests in the queue
 * 
 * @author chris
 * @package org.chof.controller
 */
class Chof_Controller_ForewardQueue
{
  private $queue = null;

  /**
   * Initializes the foreward queue
   */
  function __construct()
  #*****************************************************************************
  {
    $this->queue = array();
  }
  
  /**
   * Adds a request to the queue
   * 
   * @param string action the name of the action
   * @param string controller the name of the controller
   * @param string module the name of the module the controller belongs to
   * @param array  params associative array with all the request parameters
   */
  public function push($action, 
                       $controller = null, $module = null, 
                       array $params = null)
  #*****************************************************************************
  {
    array_push($this->queue, array(
      'action'     => $action,
      'controller' => $controller,
      'module'     => $module,
      'params'     => $params
    )); 
    
    return count($this->queue);
  }
  
  /**
   * returns and removes the next request entry
   * 
   * @return array representing the next request. The array has the entries
   *               action, controller, module and params as described in
   *               push()
   */
  public function unqueue()
  #*****************************************************************************
  {
    return array_shift($this->queue);
  }
  
  /**
   * Gives the number of foreward requests waiting in the queue
   * 
   * @return integer the number of foreward requests in the queue
   */
  public function count()
  #*****************************************************************************
  {
    return count($this->queue);  
  }
}

?>