<?php

interface Chof_Model_Interface_ChangeObject extends Chof_Model_Interface_Changeable
{
  /**
   * Adds a change tracker object to the list of trackers to be notified if the
   * elements of the esemble change.
   *
   * @param $tracker Chof_Model_Interface_ChangeTracker the change tracker object
   * @return true if the change tracker could be registered false otherwise
   *         (e.g. the tracker provided does not implement the
   *         Default_Model_Interface_ChangeTracker interface.
   */
  public function registerChangeTracker(Chof_Model_Interface_ChangeTracker $tracker);
  
  /**
   * Removes a change tracker from the list of trackers to be notified
   *
   * @param $tracker Chof_Model_Interface_ChangeTracker the tracker to be removed
   * @return true if the tracker could be removed, false otherwise (e.g. tracker
   *         is not in the list of registered trackers.
   */
  public function unregisterChangeTracker(Chof_Model_Interface_ChangeTracker $tracker);
  
}

?>