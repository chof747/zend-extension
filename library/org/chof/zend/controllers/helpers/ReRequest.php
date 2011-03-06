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
  protected $registryKey = 'Chof_Controller_Helper_ReRequest';  

  public function init()
  #*****************************************************************************
  {
    if (Zend_Registry::isRegistered($this->registryKey))
    {
      $this->rerequest = Zend_Registry::get($this->registryKey);
    }
    else
    {
      $this->rerequest = null;
    }
  }
  
  public function register(Zend_Controller_Request_Abstract $request)
  #*****************************************************************************
  {
    $this->rerequest = clone $request;
    //echo $this->rerequest->getActionName(). " - ".$this->rerequest->getControllerName()."<br/>\n";
    Zend_Registry::set($this->registryKey, $this->rerequest); 
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
      $request->setModuleName($this->rerequest->getModuleName())
                ->setControllerName($this->rerequest->getControllerName())
                ->setActionName($this->rerequest->getActionName())
                ->setParams($this->rerequest->getParams());

      $request->setDispatched(false);
    }
    else
    {
      //echo "Huch!!\n";
    }
  }
  
}
?>