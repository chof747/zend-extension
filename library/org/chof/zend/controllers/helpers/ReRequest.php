<?php
class Chof_Controller_Helper_ReRequest extends Zend_Controller_Action_Helper_Abstract
{
  /**
   *  @var Zend_Controller_Request_Abstract request to be redispatched
   */
  protected $rerequest;
  protected $session;

  /**
   * Registry key under which actions are stored
   * @var string
   */

  public function init()
  #*****************************************************************************
  {
    $this->session = new Zend_Session_Namespace(Zend_Registry::get('appname'));
    if (isset($this->session->Chof_Controller_Helper_ReRequest))
    {
      $this->rerequest = $this->session->Chof_Controller_Helper_ReRequest;
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
      
      $this->session->Chof_Controller_Helper_ReRequest = $this->rerequest;
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
      #ZLOG($this->rerequest->getActionName(). " - ".$this->rerequest->getControllerName());
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
      unset($this->session->Chof_Controller_Helper_ReRequest);
    }
    else
    {
      //echo "Huch!!\n";
    }
  }
  
}
?>