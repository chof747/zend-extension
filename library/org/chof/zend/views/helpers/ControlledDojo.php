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
      
      //$this->dojo()->requireModule("dijit.dijit");
      //$this->dojo()->requireModule("dijit.dijit-all");
      //$this->dojo()->requireModule("dojox.gfx");
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
      //$this->dojo()->requireModule($this->basens.'.controller.'.$controller_module);
    }  
    return $this;
  }
  
  public function addOnLoad()
  #*****************************************************************************
  {
    $controllers = array();
    $controllerModule = array();
    $controllerVar = array();
    
    $basePath = str_replace('.', '/', $this->basens);
    
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
        "  ctrl$controller = new $controller(".
        join(", ", $arguments).
        ");";
        
      $controllerModule[] = strtolower("'$basePath/controller/$controller'");
      $controllerVar[] = $controller;
    }
    
    array_unshift($controllerModule, "'dojo/ready'");
    array_unshift($controllerVar, 'ready');
    
    $this->view->headScript()->prependScript(
      "require([".join(',', $controllerModule)."], ".
      "function(".join(",", $controllerVar).") {\n".
      "  ready(function( ) {\n    ".
      ($this->dojo()->getDjConfigOption('parseOnLoad') ? 
         "" : "dojo.require('dojo.parser');\n  dojo.parser.parse( );\n  ").
      join("\n  ", $controllers)."\n".
      
      "    if(typeof(dojo.layoutOnLoad) == 'function')\n".
      "    {\n".
      "      dojo.layoutOnLoad();\n".
      "    }\n".
      "  });\n".
      "});");
  
  return $this;
  }
}
?>