<?php

class TStatisticTest extends BaseTestCase
{
  public function testTDistributionDoubleSided()
  //****************************************************************************
  {
    $cases = array(
        array( 'expected' => 1.895, 'p' => 0.90, 'df' =>  7),
        array( 'expected' => 2.083, 'p' => 0.92, 'df' =>  7),
        array( 'expected' => 2.789, 'p' => 0.97, 'df' =>  7),
        array( 'expected' => 1.691, 'p' => 0.90, 'df' =>  35),
        array( 'expected' => 2.305, 'p' => 0.97, 'df' =>  35),
    );
    
    array_walk($cases, function($case) {
      $this->assertEquals(
        $case['expected'], 
        Chof_Util_Math_TStatistic::t($case['p'], $case['df']),
        '',
        9e-4);
    });
  }

  public function testTDistributionSingleSided()
  //****************************************************************************
  {
    $cases = array(
        array( 'expected' => 0.741, 'p' => 0.75, 'df' =>  4)
    );
    
    array_walk($cases, function($case) {
      $this->assertEquals(
        $case['expected'], 
        Chof_Util_Math_TStatistic::t($case['p'], $case['df'], true),
        '',
        9e-4);
    });
  }
  
  public function testTValue()
  //****************************************************************************
  {
    $this->assertEquals(
      1.353, 
      Chof_Util_Math_TStatistic::tValue(30.0, $this->sample()), '', 9e-4);
  }
  
  public function testTEqual()
  //****************************************************************************
  {
    $this->assertTrue(
      Chof_Util_Math_TStatistic::ttestEqual($this->sample(), 30, 0.05));
    $this->assertTrue(
      Chof_Util_Math_TStatistic::ttestEqual($this->sample(), 30, 0.10));
    $this->assertFalse(
      Chof_Util_Math_TStatistic::ttestEqual($this->sample(), 32, 0.10));
  }

  public function testTSmaller()
  //****************************************************************************
  {
    $this->assertFalse(
      Chof_Util_Math_TStatistic::ttestSmaller($this->sample(), 31, 0.025));
    $this->assertTrue(
      Chof_Util_Math_TStatistic::ttestSmaller($this->sample(), 31, 0.10));
    $this->assertTrue(
      Chof_Util_Math_TStatistic::ttestSmaller($this->sample(), 32, 0.05));
  }

  public function testTLarger()
  //****************************************************************************
  {
    $this->assertTrue(
      Chof_Util_Math_TStatistic::ttestLarger($this->sample(), 29, 0.05));
    $this->assertTrue(
      Chof_Util_Math_TStatistic::ttestLarger($this->sample(), 30, 0.10));
    $this->assertFalse(
      Chof_Util_Math_TStatistic::ttestLarger($this->sample(), 32, 0.025));
  }
  
  public function testGCrit()
  //****************************************************************************
  {
    $cases = array(
        array( 'expected' => 2.708, 'a' => 0.050, 'df' =>  20),
        array( 'expected' => 2.729, 'a' => 0.075, 'df' =>  25),
        array( 'expected' => 2.972, 'a' => 0.050, 'df' =>  35),
        array( 'expected' => 2.760, 'a' => 0.120, 'df' =>  35),
        array( 'expected' => 3.199, 'a' => 0.010, 'df' =>  28)
    );
    
    array_walk($cases, function($case) {
      $this->assertEquals(
          $case['expected'],
          Chof_Util_Math_TStatistic::gcrit($case['df'], $case['a']),
          '',
          9e-4);
    });
    
  }
  
  public function testOutlierSimple()
  //****************************************************************************
  {
    //no outlier
    $a = $this->sample();
    list($sample, $outliers) = Chof_Util_Math_TStatistic::extractAllOutlier($a);
    $this->assertEmpty($outliers);
    $this->assertArrayEquals($a, $sample);
    
    //outlier
    $a[16] = 500;
    $a[] = 31;
    list($sample, $outliers) = Chof_Util_Math_TStatistic::extractAllOutlier($a);
    
    unset($a[16]);
    $this->assertArrayEquals($a, $sample);
    $this->assertArrayEquals(array(16 => 500), $outliers); 
  }
  
  public function testOutlierComplex()
  //****************************************************************************
  {
  //2nd outlier with 2 large and 2 small
    $outs = $this->outs();
    
    list($sample, $outliers) = Chof_Util_Math_TStatistic::
      extractAllOutlier($this->sample() + $outs);    
    ksort($outliers);
    $this->assertArrayEquals($this->sample(), $sample);
    $this->assertArrayEquals($outs, $outliers);
  }

  public function testSimpleArray()
  //****************************************************************************
  {
    $sample = array(5,4,5,4,5,4.5, 5,15,4,4);
    list($sample, $outliers) = Chof_Util_Math_TStatistic::extractAllOutlier($sample);
    $this->assertArrayEquals(array(
        7 => 15
    ), $outliers);
  }
  
  public function testSmallestOutlier()
  {
    $outs = array(
      70 => -5  
    );
    
    list($sample, $outliers) = Chof_Util_Math_TStatistic::
      extractFirstOutlier($this->sample() + $outs);
    
    $this->assertArrayEquals(array(70 => -5), $outliers);
  }
  
  private function sample()
  //****************************************************************************
  {
    return array(
        0 => 33,  1 => 32,  2 => 33,  3 => 33,  4 => 30,  5 => 30,  6 => 29,
        7 => 30,  8 => 28,  9 => 30, 10 => 33, 11 => 29, 12 => 33, 13 => 29,
        14 => 31, 15 => 31, 16 => 31, 17 => 30, 18 => 30, 19 => 28, 20 => 30,
        21 => 31, 22 => 28, 23 => 33, 24 => 28, 25 => 33, 26 => 29, 27 => 28,
        28 => 30);
  }
  
  private function outs()
  {
    return array(
        50 => 1,
        51 => 670,
        70 => -5,
        75 => 800
    );
  }
  
}

?>