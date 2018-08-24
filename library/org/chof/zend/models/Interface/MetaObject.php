<?php

/**
 * Defines the common interface of metaobjects (objects which are associated 
 * with point esembles)
 * 
 * They are basically providing methods giving:
 *  
 *   - the where element for the selections of the points
 * 
 * @author Christian
 * @package org.chof.model.interface
 */
interface Chof_Model_Interface_MetaObject
{
  /**
   * Retrieves the selection criteria to identify all positions belonging to the
   * meta object. The selection criteria has to be the part of a where statement
   * which will be executed upon the tPosition table
   * 
   * @return string the where part identifying all positions belonging to that
   *         metaobject or null if the metaobject is not associated to the
   *         database
   */
  public function getPositionSelect();
}