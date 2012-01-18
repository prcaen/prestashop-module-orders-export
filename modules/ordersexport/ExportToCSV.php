<?php
class ExportToCSV
{
  private $_fileName  = 'export';
  private $_fileDir   = 'modules/ordersexport/';
  private $_file      = NULL;
  private $_delimiter = ',';
  private $_enclosure = '';
  
  public function __construct($fileName, $fileDir, $delimiter, $enclosure)
  {
    $this->_fileName  = $fileName;
    $this->_fileDir   = $fileDir;
    $this->_delimiter = $delimiter;
    $this->_enclosure = $enclosure;
  }

  public function open()
  {
    if($this->_file = fopen($this->_fileDir . $this->_fileName, 'w'))
      return true;
    else
      return false;
  }
  public function setContent($datas)
  {
    @fputcsv($this->_file, $datas, $this->_delimiter, $this->_enclosure);
  }
  
  public function output()
  { 
	  if(@fclose($this->_file))
	    return true;
  }
}
?>