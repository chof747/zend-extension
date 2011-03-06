<?php
class Chof_Controller_Helper_CheckAuth extends Zend_Controller_Action_Helper_Abstract
{
 
  public function direct($params)
  #***************************************************************************** 
  {
    $auth = Zend_Auth::getInstance();
    
    //set the auth session storage to a specific session
    if (isset($params['session']))
    {
      $auth->setStorage(new Zend_Auth_Storage_Session($params['session']));
    }
    
    //if specified set the storage to a specific authentication storage
    if (isset($params['storage']))
    {
      if ($params['storage'] instanceof Zend_Auth_Storage_Interface)
      {
        $auth->setStorage($params['storage']);
      }
    }
    
    if ($auth->hasIdentity()) 
    {
      return $auth->getIdentity();;
    }
    else
    {
      $request = $this->getRequest();
      
      $rerequest = $this->getActionController()->getHelper('reRequest');
      $rerequest->direct($request);
      
      if ((isset($params['autologin'])) ? $params['autologin'] : true )
      {
        $request
          ->setControllerName((isset($params['controller'])) ? $params['controller'] : 'login') 
          ->setActionName((isset($params['action'])) ? $params['action'] : 'login')
          ->setModuleName((isset($params['module'])) ? $params['module'] : 'default')
          ->setDispatched(false);
      } 

      return false;
    }
  }
  
}
?>