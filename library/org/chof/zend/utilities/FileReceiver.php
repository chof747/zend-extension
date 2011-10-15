<?php

/**
 * File Receiver Controller using WebDAV
 * 
 * This controller requires the PEAR package: HTTP_WebDAV_Server
 */

include_once("HTTP/WebDAV/Server.php");

stream_wrapper_register('freceive', 'Chof_Util_FileReceiver_Stream');


/**
 * A file receiver server based on HTTP_WebDAV_Server
 * 
 * The purpose of the file receiver server is to provide a input channel in
 * URL form, where the client can save or deposit files, which will than be
 * handled by the server or a controller handling the server.
 * 
 * Architecture:
 * =============================================================================
 *  
 * - FileReceiver ... handles the HTTP Requests and stores received files within
 *                    an internal array
 *                    
 * - File         ... Contains the metainformation about a single file deposited 
 *                    to the receiver. Specific files, which should be received 
 *                    by the receiver must be implemented as descendants of 
 *                    Chof_Util_FileReceiver_File and should be registered via a
 *                    mime type and a regular expression parsing the filename
 *                    
 * - Stream       ... Receives the file content from the HTTP Stream and stores
 *                    it within a string variable. The stream is attached to a
 *                    File and calls the received($data) method of the File 
 *                    class to initiate the processing of the received content
 *
 * 
 * WebDAV Method Implementation:
 * =============================================================================
 * 
 * The base protocoll for the server is WebDAV but limited to the following
 * commands:
 * 
 * PROPFIND  : Mimics an empty directory ready to receive files
 * 
 * PUT       : Receives the file stores it in the temp directory of the system
 *             and notifies the controller as soon as the file has been received
 *             completely
 * 
 * PROPPATCH : does nothing
 * 
 * MKCOL     : does nothíng
 * 
 * GET       : returns false -> no get possible -> one way queue only
 * 
 * HEAD      : returns the basic options of a file handed over
 * 
 * PUT       : retrieves the file and hands it over to one of the registered
 *             file objects
 *             
 * DELETE    : deletes the specific file from the list of files
 * 
 * LOCK      : does nothing
 * 
 * UNLOCK    : does nothing
 * 
 * @author chris
 */
class Chof_Util_FileReceiver extends HTTP_WebDAV_Server
{
  private $files = array();
  private $filetypes = array();
  
  private $scriptname = "";
  
  /**
   * Registers a class as file receiver for a specific mime type and
   * a specific pattern of filenames
   * 
   * @param $mimeType      the mimetype of this receiver type
   * @param $nameRegExp    the regular expression which must be matching the 
   *                       name
   * @param $fileTypeClass the file type receiver class
   */
  public function registerFileType($mimeType, $nameRegExp, 
                                   $fileTypeClass)
  //**************************************************************************** 
  {
    $this->filetypes[$mimeType][$nameRegExp] = $fileTypeClass;
  }
  
  /**
   * Unregisters a class as file receiver for a specific mime type and
   * a specific pattern of filenmes.
   * 
   * 
   * @param $mimeType
   * @param $nameRegExp
   * 
   * @return true if a filetype class was registered and removed
   *         false otherwise.
   */
  public function unregisterFileType($mimeType, $nameRegExp)
  //**************************************************************************** 
  {
    if (isset($this->filetypes[$mimeType][$namedRegExp]))
    {
      unset ($this->filetypes[$mimeType][$namedRegExp]);
    }
  }
  
  /**
   * Private function to create a file instance of the file type receiver 
   * registered for the given name and mimetype 
   *  
   * @param array $options the options handed over from the Server class
   * @param string $mimeType
   * 
   * @return Chof_Util_FileReceiver_File  or null
   */
  private function createFileInstance($options)
  //**************************************************************************** 
  {
    $mimeType = $options['content_type'];
    $name     = $options['path'];
    
    if (isset($this->filetypes[$mimeType]))
    {
      foreach($this->filetypes[$mimeType] as $regexp => $fileTypeClass)
      {
        if (preg_match($regexp, $name))
        {
          return new $fileTypeClass($options);
        } 
      }
    }  
        
    return null;
  }
  
  function __construct($scriptname)
  //**************************************************************************** 
  {
    $this->scriptname = $scriptname;
    
    parent::__construct();
  }
  
  public function ServeRequest()
  //**************************************************************************** 
  {
    $this->_SERVER['SCRIPT_NAME'] = $this->scriptname;
    parent::ServeRequest();
  }
  
  /**
   * PROPFIND method handler
   *
   * @param  array  general parameter passing array
   * @param  array  return array for file properties
   * @return bool   a dummy record suggesting an empty root directory
   */
  function PROPFIND(&$options, &$files) 
  //**************************************************************************** 
  {
    if ($options['path'] == '/')
    {
      $props = array();
      
      $props[] = $this->mkprop('displayname','/');
      $props[] = $this->mkprop('creationdate',0);//time());
      $props[] = $this->mkprop('getlastmodified',0);//time());
      $props[] = $this->mkprop('lastaccessed',0);//time());
      $props[] = $this->mkprop('ishidden',false);
      $props[] = $this->mkprop('resourcetype','collection');
      $props[] = $this->mkprop('getcontenttype','httpd/unix-directory');

    
      $files["files"][] = array('path' => '/', 'props' => $props);
      
      return true;
    }
    else
    {
      return false;
    }
  } 
  
  /**
   * PROPPATCH is a dummy method for the file receiver returning an empty string
   * 
   * @param $options
   */
  function PROPPATCH($options)
  //**************************************************************************** 
  {
    return "";
  }
  
  /**
   * MKCOL is a dummy method for the file receiver returning a HTTP status of
   * 405 - method not allwoed, since only file deposition is allowed here.
   * 
   * @param $options
   */
  function MKCOL($options)
  //**************************************************************************** 
  {
    return "405 Method not allowed";
  }
  
  /**
   * GET is not implemented for the file receiver, since no files can be
   * retrieved from the server.
   * 
   * GET therefore always returns false
   * 
   * @param $options
   */
  function GET(&$options)
  //**************************************************************************** 
  {
    return false;
  }
  
  /**
   * The HEAD method retreives file information from a file that has been 
   * uploaded or has been created for upload.
   * 
   * @param $options
   */
  function HEAD(&$options)
  //**************************************************************************** 
  {
    if (isset($this->files[$options['path']]))
    {
      $file = $this->files[$options['path']];

      $options['mimetype'] = $file->getType();
      $options['mtime'] = $file->getTimestamp();
      $options['size'] = $file->getSize();
       
      return true;
    }   
    else
      return false;
  }
  
  function PUT(&$options)
  //****************************************************************************
  {
    $filetype = $this->createFileInstance($options);
    
    
    if ($filetype !== null)
    {
      $ctx = array('freceive' => array(
          'file' => $filetype
        ) 
      );
      
      $this->files[$options['path']] = $filetype;
      $options["new"] = true;
      
      return fopen("freceive://".$filetype->getResourceID(), 'w', false, 
                   stream_context_create($ctx));
    }
    else
    {
      return "403 Forbidden";
    }
  }

  /**
   * DELETE removes the file, if present from the list of received files
   * 
   * @param $options
   */
  function DELETE($options)
  //****************************************************************************
  {
    if (isset($this->files[$options["path"]]))
    {
      unset ($this->files[$options["path"]]);
      return "204 No Content";
    }
    else
    {
      return "404 Not found";
    }
  }
  
  /**
   * COPY is a dummy method for the file receiver returning a HTTP status of
   * 405 - method not allwoed, since only file deposition is allowed here.
   * 
   * @param $options
   */
  function COPY($options, $del = false)
  //**************************************************************************** 
  {
    return "405 Method not allowed";
  }
  
  /**
   * MOVE is a dummy method for the file receiver returning a HTTP status of
   * 405 - method not allwoed, since only file deposition is allowed here.
   * 
   * @param $options
   */
  function MOVE($options)
  //**************************************************************************** 
  {
    return "405 Method not allowed";
  }
  
  /**
   * LOCK method is a dummy and always returns true
   * @param $options
   */
  function LOCK(&$options) 
  {
    return true;
  }
  
  /**
   * UNLOCK method is a dummy and always returns true
   * @param $options
   */
  function UNLOCK(&$options) 
  {
    return true;
  }
  
  function checkLock($path) 
  {
    return false;
  }
  
}

?>