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
class Chof_Model_Decorator_Resource extends Chof_Model_Decorator_Abstract
                                implements Zend_Acl_Resource_Interface
{
  public function getResourceId()
  #*****************************************************************************
  {
    return $this->model->getModelName();
  }
}
?>