<?php

class CSVData {
	
	const ROW_SIZE = 4096;
	private $f;
	private $delimiter;
	private $columnMap;
	
	public function __construct($file, &$columnMap, $delimiter=',', $skipHeader = false) {
		if (!is_readable($file)) {
			throw new Exception("文件不存在：\n" .$file. "\n");
		}
		$this->f = fopen($file, 'r');
		$this->columnMap = $columnMap;
		$this->delimiter = $delimiter;
		if ($skipHeader) {
			$this->readLine();
		}
	}
	
	public function getNext() {
		$row = $this->readLine();
		if ($row == false) {
			return false;
		}
		foreach ($this->columnMap as $colNum => $name) {
			$dataRow[$name] = $row[$colNum];
		}
		return $dataRow;
	}
	
	function readLine () {
		$row = fgetcsv($this->f, self::ROW_SIZE, $this->delimiter);
		return $row;
	}
	
}

?>
