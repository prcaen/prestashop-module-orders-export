<?php
class ExportToCSV
{
	private $_fileName	= 'export';
	private $_dirName		= 'modules/ordersexport/';
	private $_file			= NULL;
	private $_delimiter = ',';
	private $_enclosure = '';

	public function __construct($fileName, $dirName, $delimiter, $enclosure)
	{
		$this->_fileName	= $fileName;
		$this->_dirName		= $dirName;
		$this->_delimiter = $delimiter;
		$this->_enclosure = $enclosure;
	}

	public function open()
	{
		if($this->_file = fopen($this->_dirName . $this->_fileName, 'w'))
			return true;
		else
			return false;
	}

	public function setContent($datas)
	{
		foreach($datas AS $line)
			$this->_setLine($line);
	}

	public function close()
	{ 
		if(@fclose($this->_file))
			return true;
	}

	private function _setLine($line)
	{
		@fputcsv($this->_file, $line, $this->_delimiter, $this->_enclosure);
	}
}
?>