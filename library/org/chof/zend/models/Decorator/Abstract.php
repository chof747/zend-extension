<?php

/**
 * An abstract base class for model decorators.
 * 
 * The functionality of the abstract base decorator is the forewarding of all 
 * method calls, which are not explicitly implemented in a concrete decorator
 * to the underlying instance of the base model
 * 
 * @package org.chof.zend.model.decorator
 */
abstract class Chof_Model_Decorator_Abstract extends Chof_Model_BaseModel
{
  protected $model = null;
  
  function __construct($model)
  #*****************************************************************************
  {
    if (($model === null) || (!($model instanceof Chof_Model_BaseModel)))
    {
      throw new Zend_Exception('The Model decorator must be called with an actual Chof_Model_BaseModel instance');
    }
    
    $this->model = $model;
  }
  
  function __call($name, $arguments)
  #*****************************************************************************
  {
    return call_user_func_array(array($this->model, $name), $arguments);
  } 

  #-----------------------------------------------------------------------------
  # Method stubs for the BaseModel base class
  #-----------------------------------------------------------------------------

  /**
   * All method stubs are delegeting the call to the $model
   */
  
  protected function createMapper() {
    return $this->model->createMapper();
  }

  public function getPrimary() {
    return $this->model->getPrimary();
  }

  protected function setPrimary($primary) {
    return $this->model->setPrimary($primary);
  }
  
  public function getModelName() {
    return $this->model->getModelName();
  }

  public function save() {
    return $this->model->save();
  }
  
  public function retrieveFromRequest(Zend_Controller_Request_Abstract $request) {
    return $this->model->retrieveFromRequest($request);
  }

  public function retrieveFromID($id) {
    return $this->model->retrieveFromID($id);
  }
  
  public function toArray($datetimefmt = '') {
    return $this->model->toArray($datetimefmt);
  }

  public function delete() {
    return $this->model->delete();
  }

  public function find($id) {
    return $this->model->find($id);
  }

  public function fetchAll($from = null, $to = null, 
                           $order = false, $filter = false) {
    return $this->model->fetchAll($from, $to, $order, $filter);
  }

  public function fetch(array $fetchparams = null) {
    return $this->model->fetch($fetchparams);
  }
  
  protected function setMapper($mapper) {
    return $this->model->setMapper($mapper);
  }

  public function getMapper()  {
    return $this->model->getMapper();
  }

  protected function validateNumber($var, $max, $min)  {
    return $this->model->validateNumber($var, $max, $min);
  }
  
  protected function validateRegExp($var, $regexp)  {
    return $this->model->validateRegExp($var, $regexp);
  }
  
  public function validate() {
    return $this->model->validate();
  }
    
  public function setOptions(array $options)  {
    return $this->model->setOptions($options);
  }

  public function getLastID()  {
    return $this->model->getLastID();
  }  

  public function getCount()  {
    return $this->model->getCount();
  }     
}
?>