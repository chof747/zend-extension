<?php
class Chof_View_Helper_ControlledDojo extends Zend_Dojo_View_Helper_Dojo
{
  protected $controller = array();
  protected $basens = ''; 
  
  protected $configJs = '';
  protected $configObject = '';
  
  protected $layers = array();
  protected $layervars = array();
  
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
    return $this;
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
  
  public function getController($controller = "")
  #*****************************************************************************
  {
  	if (empty($controller))
  	{
  		$controller = array_shift(array_keys($this->controller));
  	}
  	return "ctrl$controller";
  }
  
  private function extractLayerVar($layer)
  #*****************************************************************************
  {
  	$comps = preg_split("/\//", $layer);
  	return array_pop($comps);
  }
  
  public function prependLayer($layer)
  #*****************************************************************************
  {
  	array_unshift($this->layers, "'$layer'");
  	array_unshift($this->layervars, $this->extractLayerVar($layer));
  	return $this;	
  }
  
  public function addLayer($layer)
  #*****************************************************************************
  {
  	$this->layers[] = "'$layer'";
  	$this->layervars[] = $this->extractLayerVar($layer);
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
      "require([".join(',', array_merge($this->layers, $controllerModule))."], ". 
      "function(".join(",", array_merge($this->layervars, $controllerVar)).") {\n".
      "  ready(function( ) {\n    ".
      join("\n  ", $controllers)."\n".
      
      "    if(typeof(dojo.layoutOnLoad) == 'function')\n".
      "    {\n".
      "      dojo.layoutOnLoad();\n".
      "    }\n".
      "  });\n".
      "});");
  
  return $this;
  }
  
  public function defineConfig($configJs, $configObject)
  {
  	$this->configJs = $configJs;
  	$this->configObject = $configObject;
  	
  	if (empty($this->configObject) && !empty($this->configJs))
  	{
  		$this->configObject = "dojo";
  	}
  	
    return $this;
  }
  
  public function __toString()
  {
  	$html = $this->_container->__toString();
  	$config = "";
  	
  	if (!empty($this->configJs))
  	{
  		$config =
  		  '<script type="text/javascript" src="'.$this->configJs.'"></script>'.
  		  "\n".
  		  '<script type="text/javascript">'."\n".
  		  "  dojoConfig = $this->configObject.config('".
  		     $this->view->baseUrl() . "');\n".
  		  "</script>"; 
  	}
  	
  	return "$config\n$html";
  }
}
?>