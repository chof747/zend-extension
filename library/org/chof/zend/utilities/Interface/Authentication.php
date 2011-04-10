<?php
/**
 * Common interface for classes that provide credential validations
 * 
 * 
 * @author chris
 * @package org.chof.util.interface
 */

interface Chof_Util_Interface_Authentication
{
  /**
   * The validate method must take a login username and password and return a valid
   * identity if the credentials are valid or false otherwise
   * 
   * @param string $login
   * @param string $password
   * 
   * @return valid identity or false
   */
  public function validate($login, $password);
}