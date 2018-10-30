<?php 


class DataSetFixture
{
  private static $tmpFiles = array();
  
  public static function additionalFile($path)
  {
    return dirname(__FILE__).'/additional/'.$path;
  }
  
  public static function createTempFile($prefix)
  #*****************************************************************************
  {
    $filename = tempnam(sys_get_temp_dir(), $prefix);
    array_push(self::$tmpFiles, $filename);
    return $filename;
  }
  
  public static function purgeTempFiles()
  #*****************************************************************************
  {
    while(!empty(self::$tmpFiles))
    {
      $file = array_pop(self::$tmpFiles);
      if (file_exists($file))
      {
        unlink($file);
      }
    }
  }
  
  public static function getDataSet()
  #*****************************************************************************
  {
    $dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet();
    
    $dataSet->addTable('tindex',
      dirname(__FILE__).'/initial/index_test.csv');
    $dataSet->addTable('tindexvalue',
      dirname(__FILE__).'/initial/indexvalue_test.csv');
    
    
    /*
    $dataSet->addTable('queuetest.message',
        dirname(__FILE__).'/initial/queue_message.csv');
    $dataSet->addTable('queuetest.status',
        dirname(__FILE__).'/initial/queue_status.csv');
    $dataSet->addTable('queuetest.error',
        dirname(__FILE__).'/initial/queue_error.csv');
    $dataSet->addTable('histest.tperson',
        dirname(__FILE__).'/initial/vperson_test.csv');
    $dataSet->addTable('taccount',
        dirname(__FILE__).'/initial/account_test.csv');
    $dataSet->addTable('taccountanalytics',
        dirname(__FILE__).'/initial/accountanalytics_test.csv');
    $dataSet->addTable('taccounttype',
        dirname(__FILE__).'/initial/accountType_test.csv');
    $dataSet->addTable('ttaxposition',
        dirname(__FILE__).'/initial/taxposition_test.csv');
    $dataSet->addTable('tcategory',
        dirname(__FILE__).'/initial/category_test.csv');
    
    $dataSet->addTable('tstockexchange',
        dirname(__FILE__).'/initial/stockexchange_test.csv');
    $dataSet->addTable('tsecuritiespositiontype',
        dirname(__FILE__).'/initial/securitiespositiontype_test.csv');
    $dataSet->addTable('tsecuritiesposition',
        dirname(__FILE__).'/initial/securitiesposition_test.csv');
    $dataSet->addTable('tsecurityfixing',
        dirname(__FILE__).'/initial/securityfixing_test.csv');    
    
    $dataSet->addTable('ttransaction',
        dirname(__FILE__).'/initial/transaction_test.csv');
    $dataSet->addTable('ttransactionref',
        dirname(__FILE__).'/initial/counterTransaction_test.csv');
    
    $dataSet->addTable('tfixedpayment',
        dirname(__FILE__).'/initial/fixedpayment_test.csv');
    $dataSet->addTable('tfixedpaymentexecution',
        dirname(__FILE__).'/initial/fixedpaymentexecution_test.csv');
    
    $dataSet->addTable('tsecuritiessell',
        dirname(__FILE__).'/initial/securitiessell_test.csv');
    
    $dataSet->addTable('timporterror',
        dirname(__FILE__).'/initial/importerror_test.csv');
    
    $dataSet->addTable('trefinterestrate',
        dirname(__FILE__).'/initial/refinterestrate_test.csv');
    
    $dataSet->addTable('tpayslip',
        dirname(__FILE__).'/initial/payslip_test.csv');
    
    $dataSet->addTable('tinterestrate',
        dirname(__FILE__).'/initial/interestrate_test.csv');

    $dataSet->addTable('tcashflow',
        dirname(__FILE__).'/initial/cashflow_test.csv');
    
    $dataSet->addTable('trule',
        dirname(__FILE__).'/initial/rule_test.csv');
    
    $dataSet->addTable('tregulartransaction',
        dirname(__FILE__).'/initial/regulartransaction_test.csv');
    
    $dataSet->addTable('truleapplication',
        dirname(__FILE__).'/initial/ruleapplication_test.csv');
    
    $dataSet->addTable('tusersettings',
        dirname(__FILE__).'/initial/usersettings_test.csv');

    $dataSet->addTable('tuserpreferences',
        dirname(__FILE__).'/initial/userpreferences_test.csv');
    
    $dataSet->addTable('t1timelinktype',
        dirname(__FILE__).'/initial/onetimelinktype_test.csv');
    
    $dataSet->addTable('t1timelink',
        dirname(__FILE__).'/initial/onetimelink_test.csv');
    
    $dataSet->addTable('histest.tusercredential',
        dirname(__FILE__).'/initial/usercredentials_test.csv');   
    
    $dataSet->addTable('histest.tcurrencyexchangerates',
        dirname(__FILE__).'/initial/vcurrency_test.csv');
    
    $dataSet->addTable('histest.tlookup',
        dirname(__FILE__).'/initial/lookup_test.csv');
        */    
    return $dataSet;    
  }
  
  public static function insertIntoTable($connection, $tablename, $csvfile)
  #*****************************************************************************
  {
    $handle = fopen($csvfile, "r");
    //extract HEADER
    $columns = fgetcsv($handle, 2048, ',', '"');
    while (($data = fgetcsv($handle, 2048, ',', '"')) !== false)
    {
      $row = array();
      for($i=0;$i<count($columns);$i++)
      {
        if ($data[$i] != 'NULL')
        {
          $row[$columns[$i]] = $data[$i];
        }
      }
      //var_dump($row);
      $connection->insert($tablename, $row);
    }
  }
}

?>