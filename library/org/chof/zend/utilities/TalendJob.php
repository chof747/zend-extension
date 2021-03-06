<?php

class TalendJob implements Zend_Acl_Resource_Interface
{
  public function getResourceId()
  #*****************************************************************************
  {
    return 'ExecuteJob';
  }
  
  public $version = "";
  public $jobURL = "";
}

class Chof_Util_TalendJob
{
  private static $LOGPREFIX = '[TalendJob]';
  
  private $jobRoot = "";
  private $jobFileExtension = "sh";
  
  private $jobs = null;
  private $log  = null;
  
  private static $ERROR_EXCEPTION_MATRIX = array(
    101 => array( 
        'exception' => 'MaximumTimeExceededFailure',
        'message'  => 'Execution of %s took too long!'
  ));
  
  private $OUTPUT_TOKENS = array('output', 'input', 'problems', 'message');
  
  public function __construct($root = "", $extension = "sh")
  #*****************************************************************************
  {
    $chofconfig = Zend_Registry::get('chofconfig');
    
    if ($root != "") 
    {
      $this->jobRoot = $root;
      $this->jobFileExtension = $extension;
      
    }
    else if (isset($chofconfig->talend->jobroot))
    {
      $this->jobRoot = $chofconfig->talend->jobroot;
      $this->jobFileExtension = $chofconfig->talend->jobextension;
    }
    else
    {
      throw new Zend_Exception("No job root directory for Talend ETL jobs provided!");
    }
    
    if ($log = Zend_Registry::get('logger'))
    {
      $this->log = $log;
    }

    $this->jobs = array();
    $this->readJobsFromRoot();
  }
  
  private function readJobsFromRoot()
  #*****************************************************************************
  {
    if ($handle = opendir($this->jobRoot))
    {
      $this->jobs = array();
      
      while (false !== ($file = readdir($handle))) 
      {
        $path = $this->jobRoot.'/'.$file;
        if ((is_dir($path)) && ($file != '.') && ($file != '..')) 
        {
          $this->addJob($file, $path);
        }
      }
      
    }
    else
      throw new Zend_Exception("Talend Job root directory $this->jobroot does not exist or is not a directory!");
  }
  
  private function addJob($directory, $path)
  #*****************************************************************************
  {
    $job = new TalendJob();
    $tokens = explode('_', $directory);
    if (count($tokens) >1 )
    {
      $job->version = array_pop($tokens);
    }
    else
    {
    	$job->version = "";
    }
    $jobName = implode('_', $tokens);
    $job->jobURL = $path.'/'.$jobName.'/'.$jobName.'_run.'.$this->jobFileExtension; 
    
    if (file_exists($job->jobURL))
    {
      $this->jobs[$jobName] = $job;
    }
    else
    {
      trigger_error("The runfile for $jobName: '$job->jobURL' could not be found",
                    E_USER_WARNING);
    }
  }
  
  private function parseParams($params)
  #*****************************************************************************
  {
    $parameter = '';
    foreach($params as $key => $value)
    {
      $parameter .= "--context_param $key=$value ";
    }  
    return $parameter;
  }
  
  public function executeJob($jobname, $params = array())
  #*****************************************************************************
  {
    if (isset($this->jobs[$jobname]))
    {
      $output = array();
      $ret = 0;
      
      $call = "\"".
           $this->jobs[$jobname]->jobURL.
           "\" ".
           $this->parseParams($params);
                 
      $this->logInfo("Call to $jobname: $call");
      
      exec("$call 2>&1", $output, $ret);
           
      if ($ret == 0)
      {
        $this->logInfo("Call to $jobname returned OK");
        $output = $this->parseOutput($output);
        return $output;
      }
      else
      {
        $exceptionClass = 'ExcecutionFailure';
        $exceptionMessage = "Talend Job Execution for: $jobname failed!";
        if (array_key_exists($ret, self::$ERROR_EXCEPTION_MATRIX))
        {
          $exceptionClass = self::$ERROR_EXCEPTION_MATRIX[$ret]['exception'];
          $exceptionMessage = sprintf(
            self::$ERROR_EXCEPTION_MATRIX[$ret]['message'], $jobname);
        }
        $this->logError($exceptionMessage.join("\n", $output));
        $output = $this->parseOutput($output, true);
        $e = new $exceptionClass($exceptionMessage);
        $e->output = $output;
        throw $e;
      }
    }
  }
  
  public function parseOutput($output,  $asError = false)
  #*****************************************************************************
  {
    if ($this->jobFileExtension == "bat")
    {
      //special treatment for batch files in windows:
      //eliminate repetition of shell commands in batch script 
      do 
      {
        $line = array_shift($output);
      } while ((sizeof($output) > 0) && (!preg_match("/.*?java.*?--context.*?/", $line)));
    }
      
    $info = array();
    $error = array();
    
    //parse rest of output to see if structured information is provided
    foreach($output as $line)
    {
      if (preg_match('/^(\w*)\s*?:\s*?(.*)$/', $line, $matches))
      {
        if ((sizeof($matches) == 3) && (in_array($matches[1], $this->OUTPUT_TOKENS)))
        {
          $info[$matches[1]] = trim($matches[2]);
        }
      }
      
      if (!preg_match('/^\s*?at\s/', $line))
      {
        $error[] = $line;
      }
    }
    
    if ($asError)
    {
      return array('problems' => implode("\n", $error),
                   'output' => "0",
                   'input'  => "0");
    }
    else
    {
      return (sizeof($info) > 0) ? $info : array('problems' => implode("\n", $output),
                                                 'output' => "0",
                                                 'input' => "0");
    }
  }
  
  public function getJobs()
  #*****************************************************************************
  {
    $result = array();
    
    foreach($this->jobs as $name => $job)
    {
      $result[$name] = clone $job;
    }
    
    return $result;
  }
  
  public function getJobResource($jobname)
  #*****************************************************************************
  {
    return ($this->hasJob($jobname)) ? $this->jobs[$jobname] : null;
  }
  public function hasJob($jobname)
  #*****************************************************************************
  {
    return (isset($this->jobs[$jobname]));
  }
  
  #-----------------------------------------------------------------------------
  # GETTER AND SETTER METHODS
  #-----------------------------------------------------------------------------

  public function getJobVersion($jobname)
  #*****************************************************************************
  {
    if (isset($this->jobs[$jobname]))
    {
      return $this->jobs[$jobname]->version + 0.0;
    }
    else
    {
      return 0;
    }
  } 
  
  public function getJobURL($jobname)
  #*****************************************************************************
  {
    if (isset($this->jobs[$jobname]))
    {
      return $this->jobs[$jobname]->jobURL + 0.0;
    }
    else
    {
      return '';
    }
  }

  public function getJobFileExtension()
  #*****************************************************************************
  {
    return $this->jobFileExtension;
  }
  
  public function setJobFileExtension($extension)
  #*****************************************************************************
  {
    if (($extension != $this->jobFileExtension) && 
        (in_array($extension, array('sh', 'bat'))))
    {
      $this->jobFileExtension = $extension;
      $this->readJobsFromRoot();
    }
  }
  
  private function logInfo($text)
  #*****************************************************************************
  {
    if ($this->log)
    {
      $this->log->info(self::$LOGPREFIX.' '.$text);
    }
  }
  private function logError($text)
  #*****************************************************************************
  {
    if ($this->log)
    {
      $this->log->err(self::$LOGPREFIX.' '.$text);
    }
  }
}

class ExcecutionFailure extends Zend_Exception
{
  public $output;
}

class MaximumTimeExceededFailure extends ExcecutionFailure
{
  
}


?>