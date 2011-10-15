<?php

/**
 * Class representing a file received or to be received by the file receiver
 * 
 * The File class contains all information necessary to process and validate 
 * a received file.
 * 
 * @author chris
 *
 */
abstract class Chof_Util_FileReceiver_File
{
  /**
   * 
   * @var string contains the relative path of the file
   */
  protected $resourceID = '';
  
  /**
   * 
   * @var int contains the length of the file. 0 means nothing has been received
   */
  private $length = 0;
  
  /**
   * 
   * @var string contains the type of the file specified by its mime type
   */
  private $type = '';
  
  /**
   * 
   * @var object any file stream
   */
  private $stream = null;
  
  /**
   * 
   * @var int unix timestamp of the file
   */
  private $timestamp = 0;
  
  /**
   * Standard constructor receiving the information from a put request of a 
   * file
   * 
   * @param $options
   */
  function __construct(array $options = null)
  //****************************************************************************
  {
    if (is_array($options))
    {
      $this->setOptions($options);
    }
  }

  /**
   * @return array with the file parameter
   */
  public function toArray()
  //****************************************************************************
  {
    return array(
      'resourceID' => $this->resourceID,
      'length' => $this->$length,
      'type' => $this->$type,
      'stream' => $this->stream,
      'timestamp' => $this->timestamp
    );
  }
  
  private function setOptions($options)
  //****************************************************************************
  {
    $this->resourceID = $options['path'];
    $this->length = $options['content_length'];
    $this->type = $options['content_type'];
    $this->stream = $options['stream'];
    $this->timestamp = time();
  }
  
  public function getResourceID()
  //****************************************************************************
  {
    return $this->resourceID;
  }

  public function getLength()
  //****************************************************************************
  {
    return $this->length;
  }

  public function getType()
  //****************************************************************************
  {
    return $this->type;
  }

  public function getTimestamp()
  //****************************************************************************
  {
    return $this->timestamp;
  }
  
  /**
   * Method which is called by a stream function registered with this file
   * 
   * @param string $data the data received by the stream
   */
  abstract public function received($data);
}

?>