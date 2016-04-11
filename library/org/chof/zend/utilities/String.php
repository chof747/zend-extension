<?php

class Chof_Util_String
{
  public static function computeDiff($from, $to)
  #*****************************************************************************
  {
    $diffValues = array();
    $diffMask = array();
  
    $dm = array();
    $n1 = strlen($from);
    $n2 = strlen($to);
  
    for ($j = -1; $j < $n2; $j++) $dm[-1][$j] = 0;
    for ($i = -1; $i < $n1; $i++) $dm[$i][-1] = 0;
    for ($i = 0; $i < $n1; $i++)
    {
      for ($j = 0; $j < $n2; $j++)
      {
        if ($from[$i] == $to[$j])
        {
          $ad = $dm[$i - 1][$j - 1];
          $dm[$i][$j] = $ad + 1;
        }
        else
        {
          $a1 = $dm[$i - 1][$j];
          $a2 = $dm[$i][$j - 1];
          $dm[$i][$j] = max($a1, $a2);
        }
      }
    }
  
    $i = $n1 - 1;
    $j = $n2 - 1;
    while (($i > -1) || ($j > -1))
    {
      if ($j > -1)
      {
        if ($dm[$i][$j - 1] == $dm[$i][$j])
        {
          $diffValues[] = $to[$j];
          $diffMask[] = 1;
          $j--;
          continue;
        }
      }
      if ($i > -1)
      {
        if ($dm[$i - 1][$j] == $dm[$i][$j])
        {
          $diffValues[] = $from[$i];
          $diffMask[] = -1;
          $i--;
          continue;
        }
      }
      {
        $diffValues[] = $from[$i];
        $diffMask[] = 0;
        $i--;
        $j--;
      }
    }
  
    $diffValues = array_reverse($diffValues);
    $diffMask = array_reverse($diffMask);
  
    return array('values' => $diffValues, 'mask' => $diffMask);
  }
  
}

?>