<?php

abstract class TestCase_RestController extends TestCase_Controller
{
  protected $restpath = '';
  protected $readonly = false;
  
  /*
   * DEFAULT Test Methods
   */
  
  public function testPostAction()
  #*****************************************************************************
  {
    if ($this->readonly)
    {
      $this->assertFalse(false);
    }
    else 
    {
      $model = $this->crashtestdummy();
      $models = $this->dispatchPost($model);
    
      $this->assertModelsEquals($model,$models[0], $models[0]->getPrimary());
    }
  }
  
  public function testDeleteAction()
  #*****************************************************************************
  {
    if ($this->readonly)
    {
      $this->assertFalse(false);
    }
    else
    {
      $model = $this->crashtestdummy();
      $model->save();
  
      $this->dispatchDelete($model);
    }
  }
  
  /*
   * Test class methods
   */
  
  public function resetRequest()
  #*****************************************************************************
  {
    parent::resetRequest();
    $this->getRequest()->setHeader('Accept', 
                           'Accept:application/javascript, application/json')
                       ->setHeader('Content-Type', 'application/json');
  }
  
  protected function setRestPath($restpath)
  #*****************************************************************************
  {
    if (substr($restpath, -1) == '/') $restpath = substr($restpath, 0, -1);
    $this->restpath = $restpath;
  }
  
  abstract protected function newModel($tag = null);
  
  protected function makeModel($tag = null)
  #*****************************************************************************
  {
    return new Chof_Model_Decorator_Message_Json(
	  new Chof_Model_Decorator_Schema($this->newModel($tag))
    );
  }
  
  abstract protected function crashtestdummy();
  
  private function composeMessage($model)
  #*****************************************************************************
  {
    if ($model instanceof Chof_Model_Decorator_Message_Json)
    {
      return $model->compose();
    }
    else
    {
      throw new \InvalidArgumentException('
              models must be decorated with a Json Message');
    }
  }
  
  private function makeBody($model)
  #*****************************************************************************
  {
    $body = array();
    if (is_array($model))
    {
      foreach ($model as $m)
      {
        $body[] = $this->composeMessage($m);
      }
    }
    else
    {
      $body = $this->composeMessage($model);
    }
    
    $encoder = $this->makeModel();
    return $encoder->encode($body); 
  }
  
  private function readModel($id)
  #*****************************************************************************
  {
    $model = $this->newModel();
    $model->retrieveFromID($id);

    return $model;
  }

  protected function dispatchPost($model)
  #*****************************************************************************
  {
    $this->getRequest()->setMethod('POST')
         ->setRawBody($this->makeBody($model));

    $this->dispatch($this->restpath);
    $this->assertResponseCode(201);
    $this->assertNotRedirect();
    $id = $this->getInsertId();
    
    if (!is_array($id))
    {
      $id = array($id);
    }
  
    $locations = array();
    $models = array();
    
    foreach($id as $i)
    {
      $locations[] = "$this->restpath/$i";
      $models[] = $this->readModel($i);
    }

    $this->assertHeaderContains('Location', join(',',$locations));
    
    return $models;
  }
  
  protected function dispatchPut($model)
  #*****************************************************************************
  {
    $body = $this->makeBody($model);
    $this->getRequest()->setMethod('PUT')
         ->setRawBody($body);
    
    $this->dispatch("$this->restpath/".$model->getId());  
    $this->assertResponseCode(200);
  }
  
  protected function dispatchDelete($reference)
  #*****************************************************************************
  {
    $this->getRequest()->setMethod('DELETE');
    $this->dispatch($this->restpath.'/'.$reference->getId());
    $this->assertResponseCode(204);

    $model = $this->newModel();
    try
    {
      $model->retrieveFromID($reference->getId());
      $this->assertFalse(true); 
    }
    catch(Chof_Util_ItemNotFoundException $e)
    {
      $id = $reference->getPrimary();
      $id = !(is_array($id)) ? array($id) : $id;
      $this->assertEquals($reference->getId(), $e->getId());
    }
  }
  
  protected function dispatchGet($id = '', $query = array())
  #*****************************************************************************
  {
    $this->getRequest()->setMethod('GET')
                       ->setQuery($query);
    
    $this->dispatch("$this->restpath/$id");
    $this->assertResponseCode(200);
    
    return $this->getModelsFromResponse($this->getResponse());
  }
  
  protected function getFilterListModel($id)
  #*****************************************************************************
  {
    $model = $this->makeModel();
    $model->retrieveFromID($id);
    return $model;
  }
  
  protected function assertFilterList($tag, array $expected, $label)
  #*****************************************************************************
  {
    $this->getRequest()->setMethod('GET')
    ->setQuery(array(
        'filterlist' => $tag
    ));
    $this->dispatch("$this->restpath");
    
    $expected_data = array(
        'identifier' => 'identifier',
        'items' => array(),
        'label' => 'label'
    );
    
    foreach($expected as $exp)
    {
      
      $data = $this->getFilterListModel($exp)->toArray();
      $expected_data['items'][] = array(
        'identifier' => $data['ID'],
        'label'      => $data[$label]
      );
    }
    
    usort($expected_data['items'], function($a, $b) {
      if ($a['label'] != $b['label'])
        return ($a['label'] > $b['label']) ? 1 : -1;
      else
        return 0;
    });
    
    $this->assertArrayEquals(
      $expected_data, 
      Zend_Json::decode($this->getResponse()->getBody()));
  }
    
  protected function getInsertId()
  #*****************************************************************************
  {
    $locations = $this->getResponseHeader('Location');
    $ids = str_replace($this->restpath.'/', '', $locations);
    
    $ids = explode(',',$ids);
    return (count($ids) == 1) ? $ids[0] : $ids;
  }
  
  private function getModelsFromResponse($response)
  #*****************************************************************************
  {
    //echo $response->getBody();
    $decoder = $this->makeModel();
    $models = array();
    try
    {
      $data = $decoder->decode($response->getBody());
    }
    catch(Zend_Json_Exception $e)
    {
      ZLOG($response->getBody());
      throw $e;
    }
    
    foreach($data as $d)
    {
      $model = $this->makeModel();
      $model->decompose($d);
      $models[] = $model;
    }

    return $models;
  }
  
  private function getModelsFromDB($ids)
  #*****************************************************************************
  {
    $models = array();
    
    foreach($ids as $id)
    {
      $model = $this->makeModel();
      $model->retrieveFromID($id);
      $models[] = $model;
    }
    
    return $models;
  }
        
  
  public function assertRestResultSet($expectedids, $actuals, $full = true)
  #*****************************************************************************
  {
    $sorter = function($a, $b) {
      return ($a->getId() > $b->getId())
        ? 1 
        : (($a->getId() < $b->getId())
          ? -1
          : 0);
    }; 
  
    $this->assertCount(count($expectedids), $actuals);

    if ($full)
    {
      $expectedModels = $this->getModelsFromDB($expectedids);
      usort($expectedModels, $sorter);
    }

    usort($actuals, $sorter);
    
    for($i=0;$i<count($expectedids);++$i)
    {
      $actual = $actuals[$i];
      
      if ($full)
      {
        $expected = $expectedModels[$i];
        $this->assertJsonStringEqualsJsonString(
        	$expected->encode($expected->compose()),
          $actual->encode($actual->compose()));
      }
      else 
      {
        $this->assertEquals($expectedids[$i], $actual->getId());        
      }
    }
  }
  
  protected function assertModelsEquals(Chof_Model_BaseModel $expected,
                                        Chof_Model_BaseModel $actual,
                                        $expectedId = null)
  #*****************************************************************************
  {
    if ($expectedId !== null)
    {
      $expected->setPrimary($expectedId);
    }
    
    $this->assertArrayEquals($expected->toArray(), $actual->toArray());                                            
  }
}
?>