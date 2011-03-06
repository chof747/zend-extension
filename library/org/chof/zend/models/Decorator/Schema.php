<?php

/*
 * The schema decorator inspects the class name of the model, transforms it from
 * Something_Model_ModelName into Something_Model_Schema_Modelname and takes the
 * model schem from a class with that name and from a static method within this 
 * class called schema().
 * 
 * To use the schema decorator you should organize your models and schema in a
 * folder structure as follows:
 * 
 * Something (a module or application directory)
 *  |
 *  +--models
 *   |
 *   +--Schema
 *   |  |
 *   |  +--ModelName.php
 *   |     (the schema class containing the static schema() method
 *   |
 *   +--ModelName.php
 *      (the model class itself)
 */

class Chof_Model_Decorator_Schema extends Chof_Model_Decorator_Abstract
{
  protected $schema = null;
  
  function __construct($model , $schema = null)
  #*****************************************************************************
  {
    parent::__construct($model);
    
    $properties = array();
    $links = array();
    
    if (null === $this->schema = $schema)
    {
      $namecomps = array();
      $model = $this;
      do
      {
        $model = $model->model;
        $modelname = get_class($model);
        $namecomps = explode("_", $modelname);
        $result = array_search('Decorator', $namecomps);

        if ($model instanceof Chof_Model_Decorator_Schema_Interface)
        {
          $properties =array_merge($properties, $model->schemaProperties());
          $links = array_merge($links, $model->schemaLinks());
        }
        
      } while ($result!=false);
      
      $basename = array_pop($namecomps);
      array_push($namecomps, "Schema");
      array_push($namecomps, $basename);
      
      $schemaname = join("_", $namecomps);
      $this->schema = $schemaname::schema();

      if (isset($this->schema['properties']))
      {
        $this->schema['properties'] = array_merge($this->schema['properties'], $properties);
      }
      else if (count($properties) > 0)
      {
        $this->schema['properties'] = $properties;
      }
      if (isset($this->schema['links']))
      {
        $this->schema['links'] = array_merge($this->schema['links'], $links);
      }
      else if (count($links) > 0)
      {
        $this->schema['links'] = $links;
      }
    }
  }
  
  /**
   * Retrieves a schema of the model as an array.
   */
  public function schema()
  #*****************************************************************************
  {
    return $this->schema;
  }
}