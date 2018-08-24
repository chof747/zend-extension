<?php

class TestCase_Helper_Help extends TestCase_Helper_Base
{

  public function helpCompareDS($file, $dbTable, $where = '1=1', $orderby = null)
  #*****************************************************************************
  {
    $select = "SELECT * FROM $dbTable WHERE $where";
    $select = ($orderby !== null) ? $select." ORDER BY $orderby" : $select;
    
    $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet($this->tc->getConnection());
    $ds->addTable($dbTable, $select);
    
    $ads = new PHPUnit_Extensions_Database_DataSet_CsvDataSet();
    $ads->addTable($dbTable,($file[0] == '/') ? $file : DataSetFixture::additionalFile($file));
    
    $ads = new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet(
      $ads,
      array('NULL' => null,
        'true' => 1,
        'false' => 0));
      
      $this->tc->assertDataSetsEqual($ads,$ds, "Error comparing $file against $dbTable");
  }
  
  public function helpLoadCSVIntoDB($csvfile, $table)
  #*****************************************************************************
  {
    $query = "LOAD DATA INFILE '$csvfile' INTO TABLE $table ".
      "FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' ".
      "LINES TERMINATED BY '\\n' IGNORE 1 ROWS";
    
    $this->tc->getConnection()->getConnection()->query($query);
    
  }
  
  public function helpMassUpdate($table, $set, $where)
  #*****************************************************************************
  {
    $db = $this->tc->getConnection()->getConnection();
    $db->update($table, $set, $where);
  }
  
  public function helpSaveDBTableToCSV($table, $csvfile)
  #*****************************************************************************
  { 
    $query = "SELECT * FROM `$table`";
    
    $result = $this->tc->getConnection()->getConnection()->fetchall($query);
    
    if (count($result)>0)
    {
      $handle = fopen($csvfile, 'w');

      fputcsv($handle, array_keys($result[0]), ",", '"');
      foreach($result as $row)
      {
        fputcsv($handle, $row, ",", '"');
      }
      
      fclose($handle);
    }
  }
  
  public function helpCompareTextWithFile($text, $filename)
  #*****************************************************************************
  {
    $testFile = DataSetFixture::createTempFile('textfile');
    $fh = fopen($testFile, 'w');
    fwrite($fh, $text);
    fclose($fh);
    
    $this->tc->assertFileEquals(
      DataSetFixture::additionalFile($filename),
      $testFile);
    
  }
  
  public function helpLoadCSVIntoArray($csvfile)
  #*****************************************************************************
  {
    $csv = array_map('str_getcsv', file($csvfile));
    array_walk($csv, function(&$a) use ($csv) {
      array_walk($a, function(&$v) use($a) {
        if (is_numeric($v)) {
          $v = $v * 1;
        }
      });
        $a = array_combine($csv[0], $a);
    });
      
      array_shift($csv);
      return $csv;
  }
  
  public function helpIsConnected($skip = false)
  //****************************************************************************
  {
    $connected = false;
    
    $sock = @fsockopen('www.google.com', 80);
    if ($sock)
    {
      $connected = true;
      fclose($sock);
    }
    else if ($skip)
    {
      $this->tc->markTestSkipped('No Internet Connection.');
    }
    
    return $connected;
  }
}

?>