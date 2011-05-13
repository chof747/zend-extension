<?php
class Chof_View_Helper_ControlledDojo extends Zend_Dojo_View_Helper_Dojo
{
  protected $controller = array();
  protected $basens = ''; 
  
  public function controlledDojo($basenamespace = '')
  #*****************************************************************************
  {
    if ($this->basens == '')
    {
      if ($basenamespace == '')
      {
        $config = Zend_Registry::get('chofconfig');
        $this->basens = $config->dojo->basenamespace;
      }
      else
      {
        $this->basens = $basenamespace;
      }
      
      $this->dojo()->requireModule("dijit.dijit");
      $this->dojo()->requireModule("dijit.dijit-all");
      $this->dojo()->requireModule("dojox.gfx");
    }
    return $this;
  }
  
  public function dojo()
  {
    return parent::dojo();
  }  
  public function registerController($controller, $constructorArguments = array())
  #*****************************************************************************
  {
    if (!isset($this->controller[$controller]))
    {
      $controller_module = strtolower($controller);
      $this->controller[$controller] = $constructorArguments;
      $this->dojo()->requireModule($this->basens.'.controller.'.$controller_module);
    }  
    return $this;
  }
  
  public function addOnLoad()
  #*****************************************************************************
  {
    $controllers = array();
    
    foreach ($this->controller as $controller => $constructorArguments)
    {
      $arguments = array();
      if (is_array($constructorArguments))
      {
        foreach($constructorArguments as $arg => $argType)
        {
          $arguments[] = ($argType == 'number') ? $arg : "'$arg'";
        }
      }
            
      $controllers[] = 
        "  ctrl$controller = new $this->basens.controller.$controller(".
        join(", ", $arguments).
        ");";
    }
    
    $this->view->headScript()->prependScript(
      "dojo.addOnLoad(function( ) {\n  ".
      ($this->dojo()->getDjConfigOption('parseOnLoad') ? 
         "" : "dojo.require('dojo.parser');\n  dojo.parser.parse( );\n  ").
      join("\n  ", $controllers)."\n});\n");
  
  return $this;
  }
}
?>