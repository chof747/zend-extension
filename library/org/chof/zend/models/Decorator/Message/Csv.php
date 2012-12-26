<?php

/*
 * Decorator handling message in/output in csv format and providing the csv schema
 */

class Chof_Model_Decorator_Message_Csv extends Chof_Model_Decorator_Message_Abstract
{
  protected $QUOTATION_MARK = '"';
  protected $SEPARATOR = ',';
  protected $EOL_MARK = "\n";
  protected $DATEFMT = "mysql-date";
  
  /**
   * @see Chof_ModelDecorator_Message_Abstract::getMessage()
   */
  public function compose()
  #*****************************************************************************
  {
    $schema = $this->model->schema();
    $data = $this->model->toArray();
    
    $message = array();
    
    if ((isset($schema['properties'])) && (is_array($schema['properties'])))
    {
      foreach($schema['properties'] as $attribute => $definition)
      {
        if (!$this->isReadOnly($definition))
        {
          $message[$attribute] = $data[$attribute];

          if ($definition['type'] == 'string')
          {
            $message[$attribute] = 
              $this->QUOTATION_MARK.$message[$attribute].$this->QUOTATION_MARK;
          }
          else if ($definition['type'] == 'date')
          {
            $message[$attribute] = 
              Chof_Util_TimeUtils::returnTime($this->DATEFMT,
                                              $message[$attribute]);
          }
        }
      }
    }
    
    if ((isset($schema['links'])) && (is_array($schema['links'])))
    {
      foreach($schema['links'] as $link)
      {
        if ((isset($link['rel'])) && isset($link['href']) && isset($link['name']))
        {
          $name = '';
          
          $getter = 'get'.$link['name'];
          if ($linkObj = $this->model->$getter())
          {
            $linkObj = new Chof_Model_Decorator_Schema($linkObj);
            $schema = $linkObj->schema();
            $label  = 'get'.ucfirst(strtolower($schema['label']));
            
            $name = $linkObj->$label();
          }
          else
          {
            if ((isset($link['required']) && ($link['required'])))
            {
              throw new Zend_Exception("The linked object ".
                          $link['name']." is missing but required!");
            }
          }
          
          $message[$link['name']] = 
            $this->QUOTATION_MARK.$name.$this->QUOTATION_MARK;
        }
      }
    }
    
    return $message;
  }
  
  private function isReadOnly(array $definition)
  {
    if (isset($definition['readonly']))
    {
      return ($definition['readonly'] == true);
    }
    else
    {
      return false;
    }
  }
  
  protected function getHeader()
  #*****************************************************************************
  {
    $schema = $this->model->schema();
    $header = array();
    
    if ((isset($schema['properties'])) && (is_array($schema['properties'])))
    {
      foreach($schema['properties'] as $attribute => $definition)
      {
        if (!$this->isReadOnly($definition))
        {
          $header[] = (isset($definition['title']))
            ? $definition['title'] 
            : $attribute;
        }
      }
    }
    
    if ((isset($schema['links'])) && (is_array($schema['links'])))
    {
      foreach($schema['links'] as $link)
      {
        $header[] = (isset($link['title']))
          ? $link['title'] 
          : $linnk['rel'];
      }
    }
    
    return $header;
  }
  
  /**
   * @see Chof_Model_Decorator_Message_Abstract::setMessage()
   */
  public function decompose(array $messageData)
  #*****************************************************************************
  {
    throw new CSVImportNotAllowed();
  }
  
  public function encode(array $messageData)
  #*****************************************************************************
  {
    $output = array();
    array_unshift($messageData, $this->getHeader());
    
    foreach($messageData as $line)
    {
      $output[] = join($this->SEPARATOR, array_values($line));
    }
    
    return utf8_decode(join($this->EOL_MARK, $output));
  }
  
  public function decode($message)
  #*****************************************************************************
  {
    throw new CSVImportNotAllowed();
  }
  
  
  public function getContentType()
  #*****************************************************************************
  {
    return "text/CSV; charset=iso-8859-1";
  }
  
  public function getResponseHeaders()
  #*****************************************************************************
  {
    $schema = $this->model->schema();
    
    $fileBaseName = (isset($schema['name']))
      ? strtolower($schema['name'])
      : 'export';
      
    $timestamp = Chof_Util_TimeUtils::returnTime('file', new DateTime());
    
    return array(
      'Content-Disposition' => " attachment; filename=$fileBaseName$timestamp.csv"
    );
  }
}

class CSVImportNotAllowed extends Zend_Exception {}
?>