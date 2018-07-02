<?php
class Chof_Controller_Helper_CheckAuth extends Zend_Controller_Action_Helper_Abstract
{
  private function getAuthenticationHeader()
  #***************************************************************************** 
  {
    if ($this->getRequest() !== null)
    {
      $authorization = $this->getRequest()->getHeader('Authorization');
      if ($authorization)
      {
        list($method, $credentials) = explode(' ', $authorization, 2);
    
        switch ($method)
        {
          case 'Basic' : 
            $credentials = base64_decode($credentials);
            list($login,$password) = explode(':', $credentials);
            return array(
              'login' => $login,
              'password' => $password
            );
            break;
          case 'ApiKey' :
            $credentials = base64_decode($credentials);
            return array(
            	'login' => "",
              'password' => $credentials
            );
        }
      }
    }
    
    return false;
  }
 
  public function direct($params)
  #***************************************************************************** 
  {
    $auth = Zend_Auth::getInstance();
    $online = (isset($params['online']) ? $params['online'] : true);
   
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
    else if ($credentials = $this->getAuthenticationHeader())
    {
      $auth = (isset($params['authenticator']) ? $params['authenticator'] : null);
      if ($auth instanceof Chof_Util_Interface_Authentication)
      {
        return $auth->validate($credentials['login'], $credentials['password']);
      }
      else
      {
        throw new Zend_Exception('No valid authenticator provided');
      }
    }
    else if ($online)
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
    else
      return false;
  }
  
}
?>