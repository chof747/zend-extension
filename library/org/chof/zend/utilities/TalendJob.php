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
  private $jobRoot = "";
  private $jobFileExtension = "sh";
  
  private $jobs = null;
  
  private $OUTPUT_TOKENS = array('output', 'input', 'problems', 'message');
  
  public function __construct()
  #*****************************************************************************
  {
    $chofconfig = Zend_Registry::get('chofconfig');
    if (isset($chofconfig->talend->jobroot))
    {
       $this->jobRoot = $chofconfig->talend->jobroot;
       $this->jobFileExtension = $chofconfig->talend->jobextension;
       $this->jobs = array();
       
       $this->readJobsFromRoot();
    }
    else
      throw new Zend_Exception("No job root directory for Talend ETL jobs provided!");
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
    $job->version = array_pop($tokens);
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
  
  public function executeJob($jobname)
  #*****************************************************************************
  {
    if (isset($this->jobs[$jobname]))
    {
      $output = array();
      $ret = 0;
      
      exec("\"".$this->jobs[$jobname]->jobURL."\" 2>&1", $output, $ret);
      
      if ($ret == 0)
      {
        $output = $this->parseOutput($output);
        return $output;
      }
      else
      {
        $output = $this->parseOutput($output, true);
        $e = new ExcecutionFailure("Talend Job Execution for: $jobname failed!");
        $e->output = $output;
        throw $e;
      }
    }
  }
  
  public function parseOutput($output,  $asError = false)
  #*****************************************************************************
  {
    //eliminate repetition of shell commands in batch script 
    do 
    {
      $line = array_shift($output);
    } while ((sizeof($output) > 0) && (!preg_match("/.*?java.*?--context.*?/", $line)));
    
    $info = array();
    $error = array();
    
    //parse rest of output to see if structured information is provided
    foreach($output as $line)
    {
      if (preg_match('/^(\w*)\s*?:\s*?(.*)$/', $line, $matches))
      {
        if ((sizeof($matches) == 3) && (in_array($matches[1], $this->OUTPUT_TOKENS)))
        {
          $info[$matches[1]] = $matches[2];
        }
      }
      
      if (!preg_match('/^\s*?at\s/', $line))
      {
        $error[] = $line;
      }
    }
    
    if ($asError)
    {
      return implode("\n", $error);
    }
    else
    {
      return (sizeof($info) > 0) ? $info : implode("\n", $output);
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
}

class ExcecutionFailure extends Zend_Exception
{
  public $output;
}


?>