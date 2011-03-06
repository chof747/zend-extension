<?php

class Chof_View_Helper_ParagraphHelper extends Chof_View_Helper_BaseHelper
{
  
  public function paragraphHelper($text, $params)
  #***************************************************************************
  {
    $this->readParams($params);
    
    $paragraphs  = explode("\n",$text);
    
    $style = ($this->style != '') ? 'class="'.$this->style.'"' : '';
     
    $outputs = array();
    foreach($paragraphs as $paragraph)
    {
       $outputs[] = "<p $style>";
       $outputs[] = "  ".htmlspecialchars($paragraph)."</p>";
    }

    return $this->output($outputs);
  }
  
}

?>