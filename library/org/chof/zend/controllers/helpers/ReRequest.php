<?php
class Chof_Controller_Helper_ReRequest extends Zend_Controller_Action_Helper_Abstract
{
  /**
   *  @var Request to be redispatched
   */
  protected $rerequest;

  /**
   * Registry key under which actions are stored
   * @var string
   */
  static protected $registryKey = 'Chof_Controller_Helper_ReRequest';
  private $sessionkey = '';  

  public function init()
  #*****************************************************************************
  {
    $this->sessionkey = Zend_Registry::get('appname').'_'.self::$registryKey;
    //ZLOG($_SESSION);
    if (isset($_SESSION[$this->sessionkey]))
    {
      $this->rerequest = $_SESSION[$this->sessionkey];
      //ZLOG($this->rerequest->getActionName().' - '.$this->rerequest->getParam('id', false));
    }
    else
    {
      $this->rerequest = null;
    }
  }
  
  private function register(Zend_Controller_Request_Abstract $request)
  #*****************************************************************************
  {
    if ($request !== null)
    { 
      $this->rerequest = array(
        'module' => $request->getModuleName(),
        'controller' => $request->getControllerName(),
        'action' => $request->getActionName(),
        'params' => $request->getParams());
      
      if (array_search('getRequestUri', get_class_methods(get_class($request))))
      {
        $this->rerequest['uri'] = $request->getRequestUri();
      }
      
      $_SESSION[$this->sessionkey] = $this->rerequest;
    } 
  }
  
  public function direct(Zend_Controller_Request_Abstract $request = null)
  #*****************************************************************************
  {
    if ($request !== null)
    {
      $this->register($request);
    } 
    else if ($this->rerequest != null)
    { 
      //echo $this->rerequest->getActionName(). " - ".$this->rerequest->getControllerName()."<br/>\n";
      $request = $this->getActionController()->getRequest();
      $request->clearParams();
      $request->setModuleName($this->rerequest['module'])
              ->setControllerName($this->rerequest['controller'])
              ->setActionName($this->rerequest['action'])
              ->setParams($this->rerequest['params']);
      
      if (array_search('setRequestUri', get_class_methods($request)))
      {
        $request->setRequestUri($this->rerequest['uri']);
      }

      $request->setDispatched(false);
      unset($_SESSION[$this->sessionkey]);
    }
    else
    {
      //echo "Huch!!\n";
    }
  }
  
}
?>