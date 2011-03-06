<?php
class Chof_View_Helper_BaseHelper extends Zend_View_Helper_Abstract
{
  private static $INDENTER = "                                                                                "; 
  private $indent = 2;
  
  protected $style = '';
  protected $classes = array();

  protected function readParams($params)
  #***************************************************************************
  {
    if (is_array($params))
    {
      foreach($params as $key => $value)
      {
        $this->$key = $value;
      }
    }
    
    //if a 'style' parameter is set, intepret its tokens as css classes
    if ($this->style != '')
    { 
      $this->classes = preg_split("/[\s,]+/", $this->style);
    }    
  }
  
  protected function indent()
  #***************************************************************************
  {
    return substr(Chof_View_Helper_BaseHelper::$INDENTER, 0, $this->indent);
  }
  
  public function getIndent()
  #***************************************************************************
  {
    return $this->indent();
  }
  
  public function hasClass($class)
  #***************************************************************************
  {
    return (array_search($class, $this->classes) !== false);
  }
  
  protected function output($lines)
  #***************************************************************************
  {
    $output = "\n";
    if (is_array($lines))
    {
      foreach($lines as $line)
      {
         $output .= $this->indent().$line."\n"; 
      }
    }
    else 
      $output .= $this->indent().$lines."\n";
      
    return $output;
  }
}
?>