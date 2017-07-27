<?php

class Chof_Util_Math 
{
  public static function average(array $a)
  #*****************************************************************************
  {
    return (array_sum($a) *1.0) / (count($a) * 1.0);
  }
  
  public static function sign($n)
  #*****************************************************************************
  {
    return ($n > 0) - ($n < 0);
  }
  
  public static function standardDev(array $a, $sample = false) 
  #*****************************************************************************
  {
    $n = count($a);
    if ($n === 0) {
      trigger_error("The array has zero elements", E_USER_WARNING);
      return false;
    }
    if ($sample && $n === 1) {
      trigger_error("The array has only 1 element", E_USER_WARNING);
      return false;
    }
    $mean = self::average($a);
    
    $carry = 0.0;
    foreach ($a as $val) 
    {
      $d = ((double) $val) - $mean;
      $carry += $d * $d;
    };
    
    return sqrt($carry / ($sample ? ($n-1) : $n));
  }
  
  public static function linInterpolation($x, $x0, $x1, $y0, $y1)
  #*****************************************************************************
  {
    return $y0 + ($y1 - $y0) * (($x - $x0) / ($x1 - $x0));
  }
  
  public static function logInterpolation($x, $x0, $x1, $y0, $y1)
  #*****************************************************************************
  {
    return self::linInterpolation(log10($x), log10($x0), log10($x1), $y0, $y1);
  }  
}

?>