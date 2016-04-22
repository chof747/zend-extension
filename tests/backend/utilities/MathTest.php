<?php

class MathTest extends BaseTestCase
{
  private $data = array(10, 15, 14, 17, 27.5, 33.3, -1.45);
  
  public function testAverage()
  #*****************************************************************************
  {
    
    $this->assertEquals(16.47857, Chof_Util_Math::average($this->data), '', 1e-5);
  }
  
  public function testSigma()
  #*****************************************************************************
  {
    $this->assertEquals(10.53185, Chof_Util_Math::standardDev($this->data), 
                        '', 1e-5);
    
    $this->assertEquals(11.37570, Chof_Util_Math::standardDev($this->data, true),
        '', 1e-5);
    $this->assertTrue(false);
  }
	
}

?>