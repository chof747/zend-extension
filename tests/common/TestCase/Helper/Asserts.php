<?php

class TestCase_Helper_Asserts extends TestCase_Helper_Base
{
  /**
   * @param expectedErrors the list of errors
   * @param e the validation error
   */
  public function checkValidationError($expectedErrors, $e)
  #*****************************************************************************
  {
    if (count($expectedErrors) == count($e->getDetails()))
    {
      foreach($expectedErrors as $field => $code)
      {
        $this->tc->assertTrue($e->hasError($field), 
          "$field does not have an error");
        $this->tc->assertEquals($code, $e->getError($field)['code']);
      }
    }
    else
    {
      var_dump($e->getDetails());
      $this->tc->assertTrue(false);
    }
  }
  
  public function assertValidation(array $expectedErrors,
    Chof_Model_BaseModel $model)
  #*****************************************************************************
  {
    try
    {
      $model->save();
      $this->tc->assertTrue(count($expectedErrors)==0);
    }
    catch(Chof_Model_ValidationException $e)
    {
      $this->checkValidationError($expectedErrors, $e);
    }
  }
   
  
  
  public function assertArrayEquals(array $expected, array $actual, 
                                    $msg = '', $precision = 1e-6)
  #*****************************************************************************
  {
    
    //ignore xdebug_message keys in the array as they are not comparable
    if (array_key_exists('xdebug_message', $actual))
    {
      unset($actual['xdebug_message']);
    }
    $this->tc->assertCount(count($expected), $actual);
    
    $i = 0;
    foreach($expected as $key => $value)
    {
      $i++;
      $this->tc->assertArrayHasKey($key, $actual);
      if (is_array($value))
      {
        $this->assertArrayEquals($value, $actual[$key], $msg, $precision);
      }
      else
      {
        if ($value instanceof DateTime )
        {
          $this->tc->assertEquals($value, $actual[$key],
            (!empty($msg) ? "$msg: " : '')."$key in row $i");
        }
        else
        {
          $this->tc->assertEquals($value, $actual[$key],
            (!empty($msg) ? "$msg: " : '')."$key in row $i", $precision);
        }
      }
    }
  }
  
  public function assertModelArray(array $expected, array $actual)
  #*****************************************************************************
  {
    $this->tc->assertCount(count($expected), $actual);
    foreach($actual as $a)
    {
      $this->tc->assertEquals(array_shift($expected), $a->getId());
    }
  }
  
  public function assertModelNotExisting(Chof_Model_BaseModel $model, $id)
  #*****************************************************************************
  {
    try
    {
      $model->retrieveFromID($id);
      $this->tc->assertFalse(true);
    }
    catch(Chof_Util_ItemNotFoundException $e)
    {
      $this->tc->assertTrue(true);
    }
  }
  
  
  
}

?>