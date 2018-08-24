<?php

class TimeUtilsTest extends TestCase_Base
{
  public function testPeriod()
  //****************************************************************************
  {
  	$date = new DateTime('2010-11-21 00:00:00');
  
  	$this->assertEquals(
  	  Chof_Util_TimeUtils::closestIntervalStart($date, 'Y'),
  	  new DateTime('2010-01-01 00:00:00'));
  	  
    $this->assertEquals(
  	  Chof_Util_TimeUtils::closestIntervalStart($date, 'H'),
  	  new DateTime('2010-07-01 00:00:00'));
  	  
    $this->assertEquals(
  	  Chof_Util_TimeUtils::closestIntervalStart($date, 'Q'),
  	  new DateTime('2010-10-01 00:00:00'));
  	  
    $this->assertEquals(
  	  Chof_Util_TimeUtils::closestIntervalStart($date, 'M'),
  	  new DateTime('2010-11-01 00:00:00'));
  	  
    $this->assertEquals(
  	  Chof_Util_TimeUtils::closestIntervalStart($date, 'W'),
  	  new DateTime('2010-11-15 00:00:00'));
  
  	$date = new DateTime('2010-11-20 00:00:00');
  
    $this->assertEquals(
  	  Chof_Util_TimeUtils::closestIntervalStart($date, 'W'),
  	  new DateTime('2010-11-15 00:00:00'));
  
  	$date = new DateTime('2010-11-15 00:00:00');
  
    $this->assertEquals(
  	  Chof_Util_TimeUtils::closestIntervalStart($date, 'W'),
  	  new DateTime('2010-11-15 00:00:00'));
  
  	$date = new DateTime('2010-07-01 00:00:00');
  
    $this->assertEquals(
  	  Chof_Util_TimeUtils::closestIntervalStart($date, 'H'),
  	  new DateTime('2010-07-01 00:00:00'));
  
    $this->assertEquals(
  	  Chof_Util_TimeUtils::closestIntervalStart($date, 'Q'),
  	  new DateTime('2010-07-01 00:00:00'));
  
  	$date = new DateTime('2010-07-01 00:00:00');
  
    $this->assertEquals(
  	  Chof_Util_TimeUtils::closestIntervalStart($date, 'H'),
  	  new DateTime('2010-07-01 00:00:00'));
  
  	$date = new DateTime('2010-06-30 00:00:00');
  
  	  $this->assertEquals(
  	  Chof_Util_TimeUtils::closestIntervalStart($date, 'Q'),
  	  new DateTime('2010-04-01 00:00:00'));
  }
  
  public function testFullPeriodWithSundayQ4()
  //****************************************************************************
  {
    $results= array(
    	'D' => array(
    	  -1 => array('2014-10-04', '2014-10-04'),
    	   0 => array('2014-10-05', '2014-10-05'),
    	   1 => array('2014-10-06', '2014-10-06')
        ),
      'W' => array(
          -1 => array('2014-09-22', '2014-09-28'),
           0 => array('2014-09-29', '2014-10-05'),
           1 => array('2014-10-06', '2014-10-12')
    	),
      'M' => array(
        -1 => array('2014-09-01', '2014-09-30'),
         0 => array('2014-10-01', '2014-10-31'),
         1 => array('2014-11-01', '2014-11-30')
      ),
        'Q' => array(
          -1 => array('2014-07-01', '2014-09-30'),
           0 => array('2014-10-01', '2014-12-31'),
           1 => array('2015-01-01', '2015-03-31')
        ),
        'H' => array(
          -1 => array('2014-01-01', '2014-06-30'),
           0 => array('2014-07-01', '2014-12-31'),
           1 => array('2015-01-01', '2015-06-30')
        ),
        'Y' => array(
          -1 => array('2013-01-01', '2013-12-31'),
           0 => array('2014-01-01', '2014-12-31'),
           1 => array('2015-01-01', '2015-12-31')
        )
      );
    
      $this->checkPeriods(new DateTime('2014-10-05'), $results);
  }
  
  public function testFullPeriodWithMondayQ1()
  //****************************************************************************
  {
    $results= array(
    	'D' => array(
    	  -1 => array('2014-03-16', '2014-03-16'),
    	   0 => array('2014-03-17', '2014-03-17'),
    	   1 => array('2014-03-18', '2014-03-18')
        ),
      'W' => array(
          -1 => array('2014-03-10', '2014-03-16'),
           0 => array('2014-03-17', '2014-03-23'),
           1 => array('2014-03-24', '2014-03-30')
    	),
      'M' => array(
        -1 => array('2014-02-01', '2014-02-28'),
         0 => array('2014-03-01', '2014-03-31'),
         1 => array('2014-04-01', '2014-04-30')
      ),
        'Q' => array(
          -1 => array('2013-10-01', '2013-12-31'),
           0 => array('2014-01-01', '2014-03-31'),
           1 => array('2014-04-01', '2014-06-30')
        ),
        'H' => array(
          -1 => array('2013-07-01', '2013-12-31'),
           0 => array('2014-01-01', '2014-06-30'),
           1 => array('2014-07-01', '2014-12-31')
        ),
        'Y' => array(
          -1 => array('2013-01-01', '2013-12-31'),
           0 => array('2014-01-01', '2014-12-31'),
           1 => array('2015-01-01', '2015-12-31')
        )
      );
    
      $this->checkPeriods(new DateTime('2014-03-17'), $results);
  }
  
  public function testReturnTimeDateTimeObject()
  //****************************************************************************
  {
    $dt = Chof_Util_TimeUtils::returnTime('datetime', '2017-01-01');
    $dt2 = Chof_Util_TimeUtils::returnTime('datetime', $dt);
    $dt->add(new DateInterval('P1Y'));
    $this->assertNotEquals($dt, $dt2);
  }
  
  public function checkPeriods($date, $periods)
  //****************************************************************************
  {
    foreach($periods as $period => $variants)
    {
      foreach($variants as $pcn => $expected)
      {
        $actuals = Chof_Util_TimeUtils::fullPeriod($date, $period, $pcn);
        $this->assertEquals($expected[0],
          Chof_Util_TimeUtils::returnTime('mysql-date', $actuals[0]), "Error in $period / $pcn begin");
        $this->assertEquals($expected[1],
          Chof_Util_TimeUtils::returnTime('mysql-date', $actuals[1]), "Error in $period / $pcn end");
      }
    }
  }
}

?>