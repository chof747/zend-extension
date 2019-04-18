<?php 

class Chof_Util_ClassFinder
{
  static public function obtainClasses($indicator)
  //****************************************************************************
  {
    $nameComponents = explode('_', $indicator);
    $indicatorClass = array_pop($nameComponents);
    $baseName = join('_', $nameComponents);
    
    $classpath = self::getPathOfClass($indicator);
    
    if (!empty($classpath))
    {
    
      $classes = array();
      foreach(new DirectoryIterator($classpath) as $filename)
      {
        if (!$filename->isDir())
        {
          $className = $filename->getBasename('.php');
          if (($className <> $indicatorClass) && ($className != 'Abstract'))
          {
            $classes[] = $baseName.'_'.$className;
          }
        }
      }
    
      return $classes;
    }
    else
    {
      throw new NoClassHierarchyFound("$indicator cannot be found"); 
    }
  }
  
  static private function getPathOfClass($className)
  //****************************************************************************
  {
    $al = Zend_Loader_Autoloader::getInstance();
    $cal= $al->getClassAutoloaders($className);
    $pathinfo = pathinfo($cal[0]->getClassPath($className));
    return array_key_exists('dirname',$pathinfo) 
      ? $pathinfo['dirname']
      : null;
  } 
}

class NoClassHierarchyFound extends Exception {}

?>