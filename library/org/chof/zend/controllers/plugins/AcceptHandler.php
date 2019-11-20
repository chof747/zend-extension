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
    if (!($this->getRequest()->getParam('format')))
    {
      $header = $this->getRequest()->getHeader('Accept');
      $format = 'json';
    
      if ($header)
      {
        $format = (strstr($header, 'application/xml') && (!strstr($header, 'html'))) 
          ? 'xml' 
          : 'json';
      }
    
      $this->getRequest()->setParam('format', $format);    
    }
    
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
       preg_match('/sort\((.*)\)/i', $querystr,$sorting);
       if ($sorting)
       {
         $sorts = explode(',', $sorting[1]);
         $order = array();
         foreach($sorts as $sort)
         {
           $dir = substr($sort,0,1);
           $field = substr($sort,1,strlen($sort)-1);
           ZLOG("$dir => $field");
           $order[] = $field.(( $dir == '-') ? " DESC" : " ASC");
         }
         
         $this->getRequest()->setParam('order', $order);
       }
  }
}

?>