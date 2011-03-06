<?php

class Chof_View_Helper_Gapi extends Chof_View_Helper_BaseHelper
{
  protected $enabled = false;
  protected $apis = array();
  protected $gapikey = '';
  private $initialized = false;
  
  public function gapi($params = array())
  #*****************************************************************************
  {
    if (!$this->initialized)
    {
      $this->readParams($params);
      $this->initialized = true;
    } 
    
    return $this;
  }
  
  /**
   * enables the google api helper for the page 
   */
  public function enable()
  #*****************************************************************************
  {
    $this->enabled = true;
    return $this;
  }

  /**
   * disables the google api helper for the page 
   */
  public function disable()
  #*****************************************************************************
  {
    $this->enabled = true;
    return $this;
  }
  
  public function requestApi($api, $version)
  #*****************************************************************************
  {
    if (!isset($this->apis[$api]))
    {
      $this->apis[$api] = $version;
    }

    return $this;
  }
  
  public function injectGMaps($version = null)
  #*****************************************************************************
  {
    $config = Zend_Registry::get('chofconfig');
    
    $version = ($version == null) ?  
      Zend_Registry::get('chofconfig')->gapi->gmapversion : $version;
    
    $this->requestApi("maps", $version);
  }
  
  public function initialize()
  #*****************************************************************************
  {
    if ($this->enabled)
    {
      if ($this->gapikey == '')
      {
        $this->gapikey = Zend_Registry::get('chofconfig')->gapi->gapikey;
      }
    
      $loads = array();
      foreach ($this->apis as $api => $version)
      {
        $loads[] = "  google.load(\"$api\", \"$version\", {other_params: \"sensor=false\"});";
      }
      
      $this->view->headScript()->prependScript($this->output($loads));
      $this->view->headScript()->prependFile("http://www.google.com/jsapi?key=$this->gapikey");
    }
    
    return $this;
  }
}
?>