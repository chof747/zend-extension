<?php
class Chof_View_Helper_PageLinkHelper extends Chof_View_Helper_ActivityLinkHelper
{
  protected $controller = 'index';
  protected $action = 'index';
  protected $urlparameter = array();
  
  public function pageLinkHelper($linkText, $params)
  #***************************************************************************
  {
    
    $this->readParams($params);
    
    $baseUrl = new Zend_View_Helper_BaseUrl();
    
    $params = array();
    foreach($this->urlparameter as $param => $value)
    {
      $params []= "$param=$value";
    }
    
    $url  = $baseUrl->baseUrl(join('/', array($this->controller, $this->action)));
    $url .= (count($params)>0) ? '?'.join('&', $params) : '';

    return $this->activityLinkHelper($linkText,$url);
  }
  
}

?>