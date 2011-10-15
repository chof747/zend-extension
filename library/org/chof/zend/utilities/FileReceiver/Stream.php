<?php

/**
 * A streamwrapper class which receives data into a private variable and passes
 * it over to a Chof_Util_FileReceiver_File instance where it gets processed 
 * upon completion.
 * 
 * @author chris
 *
 */
class Chof_Util_FileReceiver_Stream
{
  private $position = 0;
  private $data = '';
  private $closed = false;
  private $resourceID = '';
  
  public $context ;

  function stream_open($path, $mode, $options, &$opened_path)
  //****************************************************************************
  {
    $this->resourceID = $path;
    if ($mode != 'w')
    {
      throw new Zend_Exception("File Receiver Stream can only be opened for writing!");
    }  
    else
    {
      $this->position = 0;
      $this->closed = false;
      $this->data = '';
      
      return true;
    }
  }
  
  function stream_read($count)
  //****************************************************************************
  {
    throw new Zend_Exception("File Receiver Stream can only be opened for writing!");
  }
  
  function stream_write($data)
  //****************************************************************************
  {
    if (!$this->closed)
    {
      $left = substr($this->data, 0, $this->position);
      $right = substr($this->data, $this->position + strlen($data));
      $this->data = $left . $data . $right;
      $this->position += strlen($data);
      return strlen($data);
    }
  }
  
  function stream_tell()
  //****************************************************************************
  {
     return $thid-closed ? 0 : $this->position;
  }

  function stream_eof()
  //****************************************************************************
  {
    return true;
  }
  
  function stream_close()
  //****************************************************************************
  {
    $options = stream_context_get_options($this->context);
    
    $file = $options['freceive']['file'];
    
    if (isset($file) && 
        $file instanceof Chof_Util_FileReceiver_File)
    {
      if (strlen($this->data) > 0)
      {
        $file->received($this->data);
      } 
    }
    
    $this->closed = true;
  }
}

?>