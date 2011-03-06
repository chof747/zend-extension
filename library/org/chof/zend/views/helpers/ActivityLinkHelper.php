<?php

class Chof_View_Helper_ActivityLinkHelper extends Chof_View_Helper_BaseHelper
{
  protected $image = '';
  protected $both = false;
  
  public function activityLinkHelper($linkText, $url, $params = array())
  #***************************************************************************
  {
    
    $this->readParams($params);
    
    $style = ($this->style != '') ? 'class="'.$this->style.'"' : '';
    
    //the special style / css class icon indicates a link in windows icon style
    // - add a line break between an image and the linktext
    // - center the anchor tag
    $lb = '';
    $center = '';
    
    if ($this->hasClass('icon'))
    {
      $lb = '<br/>';
      $center = ' align="center"';
    }
      
    $linkText = htmlentities($linkText, ENT_COMPAT, 'UTF-8');
    $innerHtml = ($this->image != '')
      ? '<img src="'.$this->image.'" border=0 alt="'.$linkText.'">'.
        (($this->both) ? "$lb$linkText" : '') 
      : $linkText;
    
    $outputs = array("<div$center>".'<a href="'.$url.'" '.$style.">",
                     '  '.$innerHtml.'</a></div>');

    return $this->output($outputs);
  }
  
}


?>