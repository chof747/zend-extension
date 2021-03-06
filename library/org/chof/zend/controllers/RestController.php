<?php

/**
 * A RESTable controller which implements the
 * 
 * GET, POST, PUT and DELETE operations of a restable service.
 * 
 * Just implement the initModelService() method and provide the model and id 
 * property of the controller within that method and you get a fully featured 
 * REST controller for a descendant of BaseModel.
 * 
 * @package org.chof.controller
 * @author chris
 * 
 * TODO: implement last-modified data as indicated in 
 * http://www.sitepen.com/blog/2009/01/26/new-in-jsonreststore-13-dates-deleting-conflict-handling-and-more/
 *
 */
abstract class Chof_Controller_RestController extends Zend_Rest_Controller
{
  protected $model   = null;
  protected $id      = 'id';
  
  private $params  = null;
  private $format  = 'json';
  
  /**
   * This is the method that has to be implemented by a concrete REST Controller
   * In this method you should at laest set the
   * 
   * $this->model parameter by creating and assigning to it a new instance of your
   * concrete BaseModel.
   *
   * Note: You should only call methods and execute commands which can be called
   * whenever a new model is needed in the process of the controller
   */
  abstract protected function initModelService();
  
  /**
   * A function that can be overridden by descendants of RestController to 
   * apply additional Model Decorators before the message decorator and the 
   * schema decorator are applied by makeMessageModel.
   * 
   * Use this method to generate specific decorations of your model class
   * especially for the getIndex() method.
   * 
   * @param Chof_Model_BaseModel $model
   */
  protected function decorateModel(Chof_Model_BaseModel $model)
  #*****************************************************************************
  {
    return $model;
  }

  /**
   * Applies the Chof_Model_Decorator_Schema and above the appropriate 
   * descendant of Chof_Model_Decorator_Message_Abstract to generate a
   * decorated model which will present its information in the requested format.
   * 
   * To apply additional decorators to the model before the messaging is done use
   * the decorateModel() method.
   * 
   * @param Chof_Model_BaseModel $model
   * @param mixed $format
   */
  protected function makeMessageModel(Chof_Model_BaseModel $model, $format = false)
  #*****************************************************************************
  {
    $format = ($format) ? $format: $this->format;
    
    return Chof_Model_Decorator_Message_Factory::create(
      new Chof_Model_Decorator_Schema($model), $format);
  }
  
  protected function getListFilter()
  #*****************************************************************************
  {
    return false;
  }
  
  protected function getUserRole()
  #*****************************************************************************
  {
    return null;
  }
  
  protected function getAcl()
  #*****************************************************************************
  {
    return Zend_Registry::get('acl');
  }
  
  protected function getResource($item)
  #*****************************************************************************
  {
    return new Chof_Model_Decorator_Resource($item);
  }
  
  protected function getCount()
  #*****************************************************************************
  {
    return $this->model->getCount($this->getListFilter());
  }
  
  protected function requestHasId()
  #*****************************************************************************
  {
    return $this->getRequest()->getParam($this->id);
  }
  
  protected function echoKey($key)
  {
  	$keystring = '';
  	if (is_array($key))
  	{
  		$keystring = "\n";
  		$fields = array_keys($key);
  		foreach($fields as $f)
  		{
  			$keystring .= "$f with ".$key[$f]."\n";
  		}
  	}
  	else
  	  $keystring = $key;
  	
  	return $keystring;
  }
  
  protected function modifyPostedData($data)
  {
    return $data;
  }
  
  protected function isAllowed($item, $action)
  #*****************************************************************************
  {
    $item = ($item !== null) ? $item : $this->model;
    
    $role = $this->getUserRole();
    $acl = $this->getAcl();
    
    $allowed = $acl->isAllowed($this->getUserRole(), 
                              $this->getResource($item), $action);

    if (!$allowed)
    {
      if ($item->getPrimary() !== null)
      {
        $this->getResponse()->appendBody("Not allowed to $action item with ID".
                                         $this->echoKey($item->getPrimary()))
                            ->setHttpResponseCode(401);
      }
      else
      {
        $this->getResponse()->appendBody("Not allowed to $action the new item.")
                            ->setHttpResponseCode(401);
      }  
      
      return false;
      
    }
    else
    {  
      return true;
    }
  }
  
  public function init()
  #*****************************************************************************
  {        
    $this->_helper->params();
    $this->_helper->viewRenderer->setNoRender(true);

    $layout = Zend_Layout::getMvcInstance();
    $layout->disableLayout();
    
    $this->format = ($this->format = $this->getRequest()->getParam('format')) ? 
                     $this->format : 
                     'json';
                         
    $this->initModelService();
    $this->model = $this->makeMessageModel($this->model, $this->format);
    
    $this->getResponse()->setHeader('Content-type', $this->model->getContentType());
    
  }
  
  /**
   * Function determining if the put action can also create items if they are 
   * missing - i.e. if we do not have an autoId
   * 
   * @return boolean
   */
  protected function createInPut()
  #*****************************************************************************
  {
    return false;
  } 
  
  /**
   * Must be implemented by the subclasses to retrieve a valid schema of the 
   * provided data
   */
  protected function getSchema()
  {
    return $this->model->schema();
  }
  
  protected function save($item = null)
  #*****************************************************************************
  {
    $new = ($item === null);
    $data = $this->_helper->params();
    $result = array();
       
    try
    {      
      if (is_array($data))
      {
        $x = self::is_assoc($data);
        if((!$new) || ($x))
        {
          $item = $new ? $this->model : $item;
          $result = array($this->saveItem($item, $new, $data));
        }
        else
        {
          foreach($data as $entry)
          {
            $this->initModelService();
            $this->model = $this->makeMessageModel($this->decorateModel($this->model));
            $result[] = $this->saveItem($this->model, $new, $entry);
          }        
        }
        $this->composeOutput("", $new ? 201 : 200);        
      }
    }
    catch (Chof_Model_ValidationException $e)
    {
      $this->getResponse()->appendBody(Zend_Json::encode(
          $e->getDetails()))
          ->setHTTPResponseCode(400);
    }
    catch (Zend_Exception $e)
    {
      $this->getResponse()->appendBody("Item could note be saved. $e")
      ->setHttpResponseCode(500);
    }
    
    return $result;
  }

  private function saveItem($item, $new, $data)
  #*****************************************************************************
  {
    $data = $this->modifyPostedData($data);
    $item->decompose($data);
    
    if ($this->isAllowed($item, $new ? 'add' : 'write'))
    {
      if ($this->beforeSave($item, $new))
      {
        $item->save();
        $this->afterSave($item, $new);
        return $item->getId();
      }
      else 
        return false;
    }
    else
    {
      return false;
    }
  }
  
  protected function getIndex($range, $order, $listFilter)
  #*****************************************************************************
  {
    $models = $this->model->fetchAll($range[0], $range[1], $order, $listFilter);
    $result = array();
    
    foreach($models as $model)
    {
      $result[] = $this->makeMessageModel(
        $this->decorateModel($model));
    }
    
    return $result;
  }
  
  protected function getItem()
  #*****************************************************************************
  {
    $this->model->retrieveFromRequest($this->getRequest());
    return $this->model;
  }
  
  public function indexAction()
  #*****************************************************************************
  {
    if ($this->getRequest()->getParam('schema', false))
    {
      $this->_forward('schema');
    }
    else if ($this->getRequest()->getParam('filterlist', false))
    {
      $this->_forward('filterlist');
    }
    else
    {
      $range = $this->getRequest()->getParam('range');
      $range = ($range) ? $range : array(null, null);
        
      $items = $this->getIndex($range, 
        $this->getRequest()->getParam('order'),
        $this->getListFilter());
      
    	
      if (is_array($items))
      {
        if (count($items)>0)
        {
      	  if ($this->isAllowed($items[0], 'list'))
          {
  
            $this->getResponse()->setHeader('Content-Range', 
              'items '.$range[0].'-'.$range[1].'/'.$this->getCount());
            $this->composeOutput($items, 200);
          }
        }
        else 
        {
          $this->composeOutput($items, 200);
        }
      }
      else
      {
        $this->getResponse()->appendBody("Item not found")
                            ->setHttpResponseCode(404);  
      }
    }
  }

  public function putAction()
  #*****************************************************************************
  {
    if ($this->requestHasId())
    {
      try 
      {
        $result = $this->save($this->getItem());
        
        if (count($result) == 1)
        {
          if (!$result[0])
          {
            $this->getResponse()->appendBody("Not allowed to update item with ID".
                $this->echoKey($this->getItem()->getPrimary()))
                ->setHttpResponseCode(401);
            
          }
        }
      }
      catch (Chof_Util_ItemNotFoundException $e)
      {
        if ($this->createInPut())
        {
          $this->postAction();
        }
        else
        {
          $this->getResponse()->appendBody("Item not found")
                              ->setHttpResponseCode(404);  
        }
      }
    }
  }
  
  public function getAction()
  #*****************************************************************************
  {
    if ($this->requestHasId())
    {
      try 
      {
        $item = $this->getItem();
        if ($this->isAllowed($item, 'read'))
        {
          $this->composeOutput($item, 200);
        }
      }
      catch (Chof_Util_ItemNotFoundException $e)
      {
        $this->getResponse()->appendBody("Item not found")
                            ->setHttpResponseCode(404);  
      }
    }
  }
  
  public function postAction()
  #*****************************************************************************
  {
    if ($results = $this->save())
    {
      $ids = array();
      foreach($results as $id)
      {
        $ids[] = $this->view->url(array(
            'module'     => $this->getRequest()->getModuleName(),
            'controller' => $this->getRequest()->getControllerName(),
            'id'         => $id), 'rest', true);
      }
      $this->getResponse()->setHeader('Location', join(',',$ids));
    }    
  }
  
  public function deleteAction()
  #*****************************************************************************
  {
    if ($this->requestHasId())
    {
      try 
      {
        $item = $this->model->retrieveFromRequest($this->getRequest());
        if ($this->isAllowed($item, 'delete'))
        {
          if ($this->beforeDelete($item))
          {
            $id = $item->getPrimary();
            $item->delete();
            
            //perform call of after delete with the key still set
            $item->setPrimary($id);
            $this->afterDelete($item);
            $item->setPrimary(null);
            
            $this->composeOutput("", 204);
          }
          else
          {
            $this->getResponse()
              ->appendBody("Delete action was aborted by delegated controller")
              ->setHttpResponseCode(406);  
          }
        }
      }
      catch (Chof_Util_ItemNotFoundException $e)
      {
        $this->getResponse()->appendBody("Item not found")
                            ->setHttpResponseCode(404);  
      }
      catch (Zend_Exception $e)
      {
        $this->getResponse()->appendBody("Item could note be deleted.")
                            ->setHttpResponseCode(500);       
      }
    }
  }
  
  public function schemaAction()
  #*****************************************************************************
  {
    $this->composeOutput($this->model->sendSchema($this->getSchema()), 200, false);
  }
  
  private function prepareFilterlist($filtertag)
  #*****************************************************************************
  {
    $schema = $this->getSchema();

    if ((isset($schema['label'])) && (isset($schema['identifier'])))
    {
      $id = $schema['identifier'];
      $label = $schema['label'];
      
      $select = new Zend_Db_Select($this->model->getMapper()->getDbTable()->getAdapter());
      $select->order($label);
      if ('' != $filter = $this->getListFilter())
        $select->where($filter);
      
      $select = $this->filterlistSelect($filtertag, $select, array(
                         'identifier' => $id,
                         'label'      => $label));
      
      return $select;
    }
  }
  
  protected function filterlistSelect($filtertag, $select, $columns)
  //****************************************************************************
  {
    $select->from(
      array($this->model->getMapper()->getDbTable()->getTableName()),
      $columns);
    
    return $select;
  }

  public function filterlistAction()
  #*****************************************************************************
  { 
    try
    {
      $select = $this->prepareFilterlist(
        $this->getRequest()->getParam('filterlist', 1));
      
      if ($select)
      {
      
        $stmt = $select->query();
        $resultSet = $stmt->fetchAll();
        
        $entries = array();
        
        foreach ($resultSet as $row)
        {
          $entries[] = array('identifier' => $row['identifier'],
                             'label'      => $row['label']);
          
        }
        
        $data = new Zend_Dojo_Data('identifier', $entries);
        $data->setLabel('label');
        
        $this->getResponse()->appendBody($data->toJson())
                            ->setHttpResponseCode(200);
      }
    }
    catch (Zend_Exception $e)
    {
      $this->getResponse()->appendBody($e->getMessage())
                          ->setHttpResponseCode(500);                
      
    }
  }
  
  private function composeOutput($data = null, $OKResponseCode = 200, 
                                 $htmlEncode = true)
  #*****************************************************************************
  {
    try
    {
      if ($data !== null)
      {
        foreach ($this->model->getResponseHeaders() as $header => $content)
        {
          $this->getResponse()->setHeader($header, $content);
        }
        $this->getResponse()->appendBody(
          (is_string($data)) ? ($htmlEncode) ? htmlentities($data) : $data 
                             : $this->formatOutput($data));
      }
      
      $this->getResponse()->setHttpResponseCode($OKResponseCode);
    }
    catch (WrongResultType $e)
    {
      $this->getResponse()->appendBody("Internal Server error: Handling wrong data types")
                          ->setHttpResponseCode(500);    
    }
    catch (WrongResultFormat $e)
    {
      $this->getResponse()->appendBody("Format ".$this->getRequest()->getParams('format')." not supported!")
                          ->setHttpResponseCode(400);    
    }
  }
  
  private function formatOutput($object)
  #*****************************************************************************
  {
    $a = array();
    if ($object instanceof Chof_Model_Decorator_Message_Abstract) 
    {
      $a = $object->compose();
    }
    else if (is_array($object))
    {
      //deal with arrays of objects separately
      foreach($object as $key => $item)
      {
        if ($item instanceof Chof_Model_Decorator_Message_Abstract)
        {
          $a[$key] = $item->compose();
        } 
        else
        {
          $a[$key] = $item;
        }
      }
    }
    else
      throw new WrongResultType();

    try
    { 
      return $this->model->encode($a);
    }      
    catch (Exception $e)
    {
      throw new WrongResultFormat();
    }
  }  
  
  public function headAction()
  #*****************************************************************************
  {
      $this->getResponse()->setBody(null);
  }

  public function optionsAction()
  #*****************************************************************************
  {
      $this->getResponse()->setBody(null);
      $this->getResponse()->setHeader('Allow', 'OPTIONS, HEAD, INDEX, GET, POST, PUT, DELETE');
  }

  private static function is_assoc(array $a)
  #*****************************************************************************
  {
    $keys = array_keys($a);
    return ($keys !== array_keys($keys));
  }
  
  /**
   * Extension hook which is triggered before an item is saved
   * 
   * @param Chof_Model_BaseModel $model
   * @param mixed $new
   * @returen boolean, true if it should be continued, false otherwise
   */
  protected function beforeSave(Chof_Model_BaseModel $model, $new)
  #*****************************************************************************
  {  
    return true;
  }
  
  /**
   * Extension hook which is triggered after an item is saved
   * 
   * @param Chof_Model_BaseModel $model
   * @param mixed $new
   */
  protected function afterSave(Chof_Model_BaseModel $model, $new)
  #*****************************************************************************
  {  
  }
  
  /**
   * Extension hook triggered before model has been deleted
   * 
   * @param Chof_Model_BaseModel $model
   * @return boolean true if it should be continued, false otherwise
   */
  protected function beforeDelete(Chof_Model_BaseModel $model)
  #*****************************************************************************
  {
    return true;
  }
  
  /**
   * Extension hook which is triggered after an item is saved
   * 
   * @param Chof_Model_BaseModel $model
   */
  protected function afterDelete(Chof_Model_BaseModel $model)
  {
    
  }
}

class WrongResultType extends Zend_Exception { }
class WrongResultFormat extends Zend_Exception { }