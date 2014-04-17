<?php

/**
 * The default base model is the mother class for all models in GPS-Tracks and
 * provides common functionality for all models. E.g. 
 *  - the access to the corresponding mapper class,
 *  - the base data access methods like find, fetchAll, fetch and save
 *   
 * @author Christian
 * @package org.chof.model
 */

abstract class Chof_Model_BaseModel extends Chof_Model_ChangeObjectImpl
                                    implements Chof_Model_Interface_Arrayable,
                                               Chof_Model_Interface_Validateable
{
  /**
   * @var reference to the corresponding mapper variable
   */
  protected $_mapper;
  
  #-----------------------------------------------------------------------------
  # Constructor
  #-----------------------------------------------------------------------------

  /**
   * Standard constructor for base model. 
   * 
   * The constructor receives an array in the style of options and passes them
   * to the setOptions method to initialize the objects with values provided by
   * the client.
   * 
   * @param $options 
   */
  function __construct(array $options = null)
  #****************************************************************************
  {
    if (is_array($options))
      $this->setOptions($options);

    $this->initialize();
  }
 
  #-----------------------------------------------------------------------------
  # Abstract methods to be implemented for concretization
  #-----------------------------------------------------------------------------
 
  /**
   * The concrete class has to implement this factory function to provide the
   * correct data mapper class for the model.
   * 
   * @return the corresponding data mapper
   */
  abstract protected function createMapper();

  /**
   * Primary key retrieval
   * 
   * @return the primary key of the model object
   */
  abstract public function getPrimary();
  
  /**
   * Retrieves a one-field ID of the object
   * 
   * @return string of the id
   */
   public function getId()
   #****************************************************************************
   {
     return $this->getPrimary();
   }
  
  /**
   * Retrieves the fields of the model object which are comprising the unique,
   * primary key for the object.
   * 
   * This method is used by the retrieveFromRequest method to determine the
   * request fields which are uniquely describing an object
   * 
   * The default implementation asumes that id is the name of the single-field
   * primary key.
   * 
   * @return array with the name of the primary fields
   */
  public function getPrimaryFields()
  #****************************************************************************
  {
    return array('id');
  }
  
  protected function getPrimaryFromId($id)
  #****************************************************************************
  {
    return array($id);
  }
  
  protected function getReference($id, $class)
  #*****************************************************************************
  {
    if (!empty($id))
    {
      $ref = new $class();
      $ref->retrieveFromID($id);
  
      return $ref;
    }
    else
    {
      return null;
    }
  }
  
  /**
   * Primary key setup
   */
  abstract protected function setPrimary($primary);
  
  /**
   * Returns the class name or unique identifier of all models
   * 
   * @return the name of the model
   */
  abstract public function getModelName();
  
  
  #-----------------------------------------------------------------------------
  # Manipulation Methods
  #-----------------------------------------------------------------------------

  /**
   * Save the current entry
   *
   * @return void
   */
  public function save()
  #****************************************************************************
  {
    if ($this->hasChanged())
    {
      if ($this->validate())
      {
        $this->getMapper()->save();
        $this->initialize();
        return true;
      } 
      else
      {
        return false;
      }
    }
  }
  
  /**
   * method to conviniently pull a model from the database and pass it
   * a controller action method directly from the http request object.
   * 
   * Calls retrieveFromID with the "id" parameter of the request
   * 
   * @param Zend_Controller_Request_Http $request
   * @return unknown_type
   */
  public function retrieveFromRequest(Zend_Controller_Request_Abstract $request)
  #****************************************************************************
  {    
    return $this->retrieveFromID($request->getParam('id', false));
  }

  /**
   * Retrieves a model from an ID
   * 
   * if the id parameter in the request is set to c the function tries to 
   * retrieve the current track cached to the gpstracks-cache within the registry.
   * Upon successfull retrieval the track is written to the gpstracks-cache
   * 
   * 
   * @param $id
   * @return unknown_type
   */
  public function retrieveFromID($id)
  #****************************************************************************
  {
    $model = null;
     
    if ($id == 'c')
    {
      if (Zend_Registry::isRegistered('model-cache'))
      {
        $model = Zend_Registry::get('model-cache')->getModel($this);
      }
      else 
        $model = null;
    }
    else
    {
      $id = $this->getPrimaryFromId($id);
      $model = call_user_func_array(array($this, "find"), $id);
    }
    
    if ($model === null)
    {
      throw new Chof_Util_ItemNotFoundException($id, 'model');
    } 
    else
    {
      if (Zend_Registry::isRegistered('model-cache'))
      {
        Zend_Registry::get('model-cache')->setModel($model);
      }
      return $model;
    }
  }
  
  /**
   * Provides the track information in form of a mapped array 
   * @return Array containing the fields of the track in form of a map (i.e.
   *               'title' => track.title, 'description' => track.description
   *               ...)
   */
  public function toArray($datetimefmt = '')
  #****************************************************************************
  {
    $a = $this->getMapper()->saveData($this, $datetimefmt);
    $a['ID'] = $this->getId();
    
    return $a;
  }
  
  public function fromArray($array)
  #*****************************************************************************
  {
    $this->getMapper()->fillFromRow($array, $this);
    $this->notifyChange();
  }

  

  /**
   * Deletes the object from the mapped database table and unsets the primary 
   * key
   */
  public function delete()
  #****************************************************************************
  {
    $result = $this->getMapper()->delete($this->getPrimary());
    $this->setPrimary(null);
    
    return $result;
  }

  /**
   * Find an entry
   *
   * Resets entry state if matching id found.
   *
   * @param  int $id
   * @return Default_Model_Guestbook
   */
  public function find()
  #****************************************************************************
  {
    return call_user_func_array(array($this->getMapper(), "find"), 
                                func_get_args());
  }

  /**
   * Fetch all entries
   *
   * @return array of all models in the mapper table
   */
  public function fetchAll($from = null, $to = null, 
                           $order = false, $filter = false)
  #****************************************************************************
  {
    $select = $this->getMapper()->getDbTable()->select();
    
    if ($from !== null)
    {
      //slice has been defined
      $from = ($from >= 0) ? $from : 0;
      $to = ($to !== null) ? $to : $from;

      $select->limit($to - $from + 1, $from);   
    }
    
    if (is_string($order))
    {
      $select->order($order);
    }
    
    if (is_string($filter))
    {
      $select->where($filter);
    }
    
    return $this->getMapper()->fetchAll($select);
  }

  /**
   * Fetch a set of objects based on specific fetch parameters
   * 
   * @see Default_Model_BaseMapper::fetch for further details
   * 
   * @param array $fetchparams
   * @return list of matching models
   */
  public function fetch(array $fetchparams = null)
  #****************************************************************************
  {
    return $this->getMapper()->fetch($fetchparams);
  }
  
  #-----------------------------------------------------------------------------
  # GETTER AND SETTER METHODS
  #-----------------------------------------------------------------------------

  /**
   * Performs the get function of a datetime field based on the given format
   *  
   * @param string $format as used by the utility class Chof_Util_TimeUtils
   * @param unknown_type $datetime the datetime property
   */
  protected function getDateTime($format, $datetime)
  #****************************************************************************
  {
    $format = ($format == 'mysql') ? 'mysql-date' : $format;
    return Chof_Util_TimeUtils::returnTime($format, $datetime);
    
  }
  
  public function __call($name, $arguments)
  #****************************************************************************
  {
    $prefix = substr($name, 0, 3);
    if (($prefix == 'get') || 
        (($prefix == 'set') && (count($arguments)==1)))
    {
      $property = lcfirst(substr($name, 3));
      if (property_exists($this,$property))
      {
        if ($prefix == 'get')
        {
          if ($this->$property instanceof DateTime)
          {
            $format = (isset($arguments[0])) ? $arguments[0] : 'datetime';
            return Chof_Util_TimeUtils::returnTime($format, $this->$property);
          }
          else
          {
            return $this->$property;
          }
        }
        else
        {
          if ($this->$property instanceof DateTime)
          {
            //use utility function in case of datetime property
            $this->$property = 
              Chof_Util_TimeUtils::returnTime('datetime', $arguments[0]);
          }
          else
          {
            $this->$property = $arguments[0];
          }
          
          if ($property != 'id') 
          {
            $this->notifyChange();
          }
          return $this;
        }
      }
      else
      {
        throw new Exception("Invalid property specified $property");
      }
    }
    else
    {
      throw new Exception("Invalid method specified $name");
    }
    
  }

  /**
   * Set data mapper
   *
   * @param  mixed $mapper
   * @return Default_Model_Guestbook
   */
  protected function setMapper($mapper)
  #****************************************************************************
  {
    $this->_mapper = $mapper;
    return $this;
  }

  /**
   * Get data mapper
   *
   * Lazy loads Default_Model_GuestbookMapper instance if no mapper registered.
   *
   * @return Default_Model_GuestbookMapper
   */
  public function getMapper()
  #****************************************************************************
  {
    if (null === $this->_mapper)
    { 
      $this->setMapper($this->createMapper());
      $this->_mapper->setModel($this);
    } 

    return $this->_mapper;
  }  
  
  protected function validateNumber($var, $max, $min)
  #****************************************************************************
  {
    if (is_numeric($var))
    {
      return (($var >= $min) && ($var <= $max));
    }
    
    return false;
  }
  
  protected function validateRegExp($var, $regexp)
  #****************************************************************************
  {
    if (is_string($var))
    {
      return (preg_match($regexp, $var)>0);
    }
        
    return false;
  }
  
  /**
   * @see Chof_Model_Interface_Validateable::validate()
   * 
   * The standard implementation of validate() simply returns true
   */
  public function validate()
  #****************************************************************************
  {
    return true;
  }
    
  /**
   * Set object state by an option style array
   *
   * @param  array $options in the form of "param" => value
   * @return the model with the new parameters set
   */
  public function setOptions(array $options)
  #****************************************************************************
  {
    $vars = array_keys(get_class_vars(get_class($this)));

    foreach ($options as $key => $value) 
    {
      if (in_array($key, $vars)) 
      {
        $method = 'set'.ucfirst($key);
        $this->$method($value);
      }
    }
    return $this;
  }
  
  /**
   * Retrieves the last primary ID of the entity
   * 
   * @return last primary id of the entity
   */
  public function getLastID()
  #****************************************************************************
  {
    return $this->getMapper()->getLastID();
  }  

  /**
   * Retrieves the last primary ID of the entity
   * 
   * @return last primary id of the entity
   */
  public function getCount($filter = false)
  #****************************************************************************
  {
    return $this->getMapper()->getCount($filter);
  }  
}
?>