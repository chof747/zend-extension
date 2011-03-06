<?php

class Chof_Controller_Plugin_AcceptHandler extends Zend_Controller_Plugin_Abstract
{
  public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
  #*****************************************************************************
  {
    if ($request instanceof Zend_Controller_Request_Http) 
    {
      $this->initAcceptFormat();
      $this->initRange();
      $this->initSorting();
    }
  }
  
  public function preDispatch(Zend_Controller_Request_Abstract $request)
  #*****************************************************************************
  {
    $this->getResponse()->setHeader('Vary', 'Accept');
  }
  
  private function initAcceptFormat()
  #*****************************************************************************
  {
    $header = $this->getRequest()->getHeader('Accept');
    $format = 'json';
    
    if ($header)
    {
      $format =  (strstr($header, 'application/xml') && (!strstr($header, 'html'))) ? 'xml' : 'json';
    }
    
    $this->getRequest()->setParam('format', $format);    
  }
  
  private function initRange()
  #*****************************************************************************
  {
    $header = $this->getRequest()->getHeader('Range');
    preg_match('/^items=(\d+)\-(\d+)$/', $header, $range);
    
    if ($range)
    {
      $this->getRequest()->setParam('range', array($range[1], $range[2]));
    }
  }
  
  private function initSorting()
  #*****************************************************************************
  {
    $querystr = $this->getRequest()->getServer('QUERY_STRING');
        preg_match('/sort\(([\+\-])(\w+)\)/i', $querystr, $sorting);
        
     if ($sorting) 
     {
       $this->getRequest()->setParam('order', 
        ("$sorting[2] ".(($sorting[1] == '-') ? "DESC" : "ASC")));
     }
  }
}

?>