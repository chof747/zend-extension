<?php

/*
 * Decorator handling message in/output in xls format and providing the csv schema
 * 
 * To use this you must have PHPExcel in the library folder of your Zend Application
 * and must have the PHPExcel Namespace autoloaded with:
 * 
 * The class inherits the composition of output from csv but exports it as 
 * an excel file
 */

class Chof_Model_Decorator_Message_Xls extends Chof_Model_Decorator_Message_Csv
{
  protected $QUOTATION_MARK = '';
  protected $DATEFMT = 'datetime';//'\x\l\s\-\d\a\t\e\: Y-m-d H:i:s';
  
  private $excel = null;
  private $worksheet = null;
  
  
  private function getName()
  #*****************************************************************************
  {
    $schema = $this->model->schema();
    
    return (isset($schema['name']))
      ? strtolower($schema['name'])
      : 'export';
  }
  
  private function getFileName()
  #*****************************************************************************
  {
    return $this->getName().
           Chof_Util_TimeUtils::returnTime('file', new DateTime()).
           '.xlsx';
  }
  
  /**
   * @see Chof_Model_Decorator_Message_Abstract::setMessage()
   */
  public function decompose(array $messageData)
  #*****************************************************************************
  {
    throw new XLSImportNotAllowed();
  }
  
  private function initExcel()
  #*****************************************************************************
  {
    $this->excel = new PHPExcel();
    $this->excel->setActiveSheetIndex(0); 
    $this->worksheet = $this->excel->getActiveSheet();
    $this->worksheet->setTitle($this->getName());
  }
  
  private function getCoordinate($col, $row)
  #*****************************************************************************
  {
    return $this->worksheet
                ->getCellByColumnAndRow($col, $row)
                ->getCoordinate();
  }
  
  private function setFilters($cols, $rows)
  #*****************************************************************************
  {
    $topleft     = $this->getCoordinate(0,1);
    $bottomright = $this->getCoordinate($cols-1, $rows);
        
    $this->worksheet->setAutoFilter($topleft.":".$bottomright);
  }
  
  private function formatHeader($cols)
  #*****************************************************************************
  {
    $left  = $this->getCoordinate(0,1);
    $right = $this->getCoordinate($cols-1, 1);
    
    $style = $this->worksheet->getStyle("$left:$right");
    
    $style->getFont()->getColor()
                     ->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
                     
    $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $style->getFill()->getStartColor()
                     ->setARGB(PHPExcel_Style_Color::COLOR_DARKBLUE);
  }
  
  private function autoSizeColumns($cols)
  #*****************************************************************************
  {
    for($c=0;$c<$cols;$c++)
    {
      $this->worksheet->getColumnDimensionByColumn($c)->setAutoSize(true);
    }
  }
  
  private function fillCells($data)
  #*****************************************************************************
  {
    $nRow = 0;
    $nCol = 0;
    
    foreach($data as $row)
    {
      $nRow++;
      $nCol = 0;
      
      foreach($row as $cell)
      {
        if($cell instanceof DateTime)
        {
          $value = PHPExcel_Shared_Date::PHPToExcel($cell);
          $cell = $this->worksheet->getCellByColumnAndRow($nCol, $nRow);
          $cell->setValue($value);
          
          $style = $this->worksheet->getStyle($cell->getCoordinate());
          $style->getNumberFormat()->setFormatCode(
            PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);          
        }
        else 
        {
          $this->worksheet->setCellValueByColumnAndRow($nCol, $nRow, $cell);
        }
        
        $nCol++;
      }
    }
    
    $this->setFilters($nCol, $nRow);
    $this->formatHeader($nCol);
    $this->autoSizeColumns($nCol);
  }
  
  private function closeExcel()
  #*****************************************************************************
  {
    $this->excel->disconnectWorksheets();
    unset($this->excel);
  }
  
  public function encode(array $messageData)
  #*****************************************************************************
  {
    array_unshift($messageData, $this->getHeader());
    
    $this->initExcel();
    $this->fillCells($messageData);
    
    //write into a temporary file
    $tmpfile = tempnam(sys_get_temp_dir(), "xls");
    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
    $objWriter->save($tmpfile);
    
    $this->closeExcel();
    
    //get content and delete the temporary file
    $content = file_get_contents($tmpfile);
    unlink($tmpfile);
    
    return $content;
  }
  
  public function decode($message)
  #*****************************************************************************
  {
    throw new XlsImportNotAllowed();
  }
  
  
  public function getContentType()
  #*****************************************************************************
  {
    return "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
  }
  
  public function getResponseHeaders()
  #*****************************************************************************
  {
    return array(
      'Content-Disposition' => " attachment; filename=".$this->getFileName()
    );
  }
}

class XLSImportNotAllowed extends Zend_Exception {}
?>