<?php
class mad_mysqlconn
{
	public $connectionId = false;
	public $database;
	public $dbtype = 'mysqli';
	public $host;
	public $open;
	public $password;
	public $username;
	public $persistent;
	public $debug = false;
	public $debug_console = false;
	public $debug_output;
	public $forcenewconnection = false;
	public $createdatabase = false;
	public $socket = false;
	public $port = false;
	public $clientFlags = 0;
	public $sql;
	public $raiseErrorFn = false;
	public $query_count = 0;
	public $query_time_total = 0;
	public $query_list = array();
	public $query_list_time = array();
	public $query_list_errors = array();
	public $info;
	public $transaction_status = true;
	public $sysDate = 'CURDATE()';
	public $sysTimeStamp = 'NOW()';
	public $isoDates = true; 
	
	public function IsConnected()
	{
		return $this->connectionId === false || $this->connectionId == false;
	}

	public function Connect($host = "", $username = "", $password = "", $database = "", $forcenew = false)
	{		
		return $this->_connect(false, $forcenew, $host, $username, $password, $database);
	}

	public function &Execute($sql, $inputarr = false)
	{
		$rs = &$this->do_query($sql, -1, -1, $inputarr);
		return $rs;
	}

	public function &SelectLimit($sql, $nrows = -1, $offset = -1, $inputarr = false)
	{
		$rs = &$this->do_query($sql, $offset, $nrows, $inputarr);
		return $rs;
	}

	// public function outp($text, $newline = true)
	// {
	// 	global $ADODB_OUTP;
	// 	$this->debug_output = "(" . $this->dbtype . "): " . htmlspecialchars($text) . ". Error: " .
	// 	$this->ErrorMsg() . " (" . $this->ErrorNo() . ")";

	// 	if (defined('ADODB_OUTP')) {
	// 		$fn = ADODB_OUTP;
	// 	} else if (isset($ADODB_OUTP)) {
	// 		$fn = $ADODB_OUTP;
	// 	}

	// 	if (defined('ADODB_OUTP') || isset($ADODB_OUTP)) {
	// 		$fn($this->debug_output, $newline);
	// 		return;
	// 	}
	// }

	protected function _connect($persistent, $forcenew, $host = "", $username = "", $password = "", $database = "")
	{
		if (!function_exists('mysqli_real_connect')) {
			return false;
		}

		$this->host = $host;
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;
		$this->persistent = $persistent;
		$this->forcenewconnection = $forcenew;

		$this->connectionId = @mysqli_init();		
		@mysqli_real_connect($this->connectionId, $this->host, $this->username, $this->password, $this->database, $this->port, $this->socket, $this->clientFlags);		

		if (mysqli_connect_errno() != 0) {
			$this->connectionId = false;
		}

		if ($this->connectionId === false) {
			if ($fn = $this->raiseErrorFn) {
				$fn($this->dbtype, 'CONNECT', $this->ErrorNo(), $this->ErrorMsg(), $this->host, $this->database, $this);
			}

			return false;
		}

		if (!empty($this->database)) {
			if ($this->SelectDB($this->database) == false) {
				$this->connectionId = false;
				return false;
			}
		}

		return true;
	}

	public function SelectDB($dbname)
	{
		$this->database = $dbname;

		if ($this->connectionId === false) {
			$this->connectionId = false;
			return false;
		} else {
			$result = @mysqli_select_db($this->connectionId, $this->database);

			if ($result === false) {
				if ($this->createdatabase == true) {
					$result = @mysqli_query($this->connectionId, "CREATE DATABASE IF NOT EXISTS " . $this->database);
					if ($result === false) { 
						return false;
					}
					$result = @mysqli_select_db($this->connectionId, $this->database);
					if ($result === false) {
						return false;
					}
				} else {
					return false;
				}
			}
			return true;
		}
	}


	public function ErrorMsg()
	{
		if ($this->connectionId === false) {
			return @mysqli_connect_error();
		} else {
			return @mysqli_error($this->connectionId);
		}
	}


	public function ErrorNo()
	{
		if ($this->connectionId === false) {
			return @mysqli_connect_errno();
		} else {
			return @mysqli_errno($this->connectionId);
		}
	}

	public function Affected_Rows()
	{
		return @mysqli_affected_rows($this->connectionId);
	}

	public function Insert_ID()
	{
		return @mysqli_insert_id($this->connectionId);
	}

	public function qstr($string)
	{
		return "'" . mysqli_real_escape_string($this->connectionId, $string) . "'";
	}

	public function Concat()
	{
		$arr = func_get_args();
		$list = implode(', ', $arr);

		if (strlen($list) > 0) {
			return "CONCAT($list)";
		} else {
			return '';
		}

	}

	public function Close()
	{
		var_dump($this->connectionId);
		@mysqli_close($this->connectionId);
		$this->connectionId = false;
	}

	public function &GetArray($sql, $inputarr = false)
	{
		$data = false;
		$result = &$this->Execute($sql, $inputarr);
		if ($result) {
			$data = &$result->GetArray();
			$result->Close();
		}
		return $data;
	}

	public function &GetAll($sql, $inputarr = false)
	{
		$data = &$this->GetArray($sql, $inputarr);
		return $data;
	}

	public function GetOne($sql, $inputarr = false)
	{
		$ret = false;
		$rs = &$this->Execute($sql, $inputarr);
		if ($rs) {
			if (!$rs->EOF) {
				$ret = reset($rs->fields);
			}

			$rs->Close();
		}
		return $ret;
	}

	public function &do_query($sql, $offset, $nrows, $inputarr = false)
	{	

		//global $ADODB_FETCH_MODE;

		$false = false;

		$limit = '';
		if ($offset >= 0 || $nrows >= 0) {
			$offset = ($offset >= 0) ? $offset . "," : '';
			$nrows = ($nrows >= 0) ? $nrows : '18446744073709551615';
			$limit = ' LIMIT ' . $offset . ' ' . $nrows;
		}

		if ($inputarr && is_array($inputarr)) {
			$sqlarr = explode('?', $sql);
			if (!is_array(reset($inputarr))) {
				$inputarr = array($inputarr);
			}

			foreach ($inputarr as $arr) {
				$sql = '';
				$i = 0;
				foreach ($arr as $v) {
					$sql .= $sqlarr[$i];
					switch (gettype($v)) {
						case 'string':
							$sql .= $this->qstr($v);
							break;
						case 'double':
							$sql .= str_replace(',', '.', $v);
							break;
						case 'boolean':
							$sql .= $v ? 1 : 0;
							break;
						default:
							if ($v === null) {
								$sql .= 'NULL';
							} else {
								$sql .= $v;
							}

					}
					$i += 1;
				}
				$sql .= $sqlarr[$i];
				if ($i + 1 != sizeof($sqlarr)) {
					return $false;
				}

				$this->sql = $sql . $limit;
				$time_start = array_sum(explode(' ', microtime()));
				$this->query_count++;
				$resultId = @mysqli_query($this->connectionId, $this->sql);
				$time_total = (array_sum(explode(' ', microtime())) - $time_start);
				$this->query_time_total += $time_total;
				if ($this->debug_console) {
					$this->query_list[] = $this->sql;
					$this->query_list_time[] = $time_total;
					$this->query_list_errors[] = $this->ErrorMsg();
				}
				if ($this->debug) {
					$this->outp($sql . $limit);
				}
			}
		} else {
			$this->sql = $sql . $limit;
			$time_start = array_sum(explode(' ', microtime()));
			$this->query_count++;
			$resultId = @mysqli_query($this->connectionId, $this->sql);
			$time_total = (array_sum(explode(' ', microtime())) - $time_start);
			$this->query_time_total += $time_total;
			if ($this->debug_console) {
				$this->query_list[] = $this->sql;
				$this->query_list_time[] = $time_total;
				$this->query_list_errors[] = $this->ErrorMsg();
			}
			if ($this->debug) {
				$this->outp($sql . $limit);
			}
		}

		if ($resultId === false) { // Error handling if query fails
			if ($fn = $this->raiseErrorFn) {
				$fn($this->dbtype, 'EXECUTE', $this->ErrorNo(), $this->ErrorMsg(), $this->sql, $inputarr, $this);				
			}
			return $false;
		}

		if ($resultId === true) { // Return simplified recordset for inserts/updates/deletes with lower overhead
			$recordset = new MySqlRecordSetBase();
			return $recordset;			
		}

		$recordset = new MySqlRecordSet($resultId, $this->connectionId);

		$recordset->_currentRow = 0;

		$recordset->fetchMode = MYSQLI_BOTH;		
		
		$recordset->_numOfRows = @mysqli_num_rows($resultId);
		if ($recordset->_numOfRows == 0) {
			$recordset->EOF = true;
		}
		$recordset->_numOfFields = @mysqli_num_fields($resultId);
		$recordset->_fetch();

		return $recordset;
	}
}

/**
 * Class MySqlRecordSet
 */
class MySqlRecordSet extends MySqlRecordSetBase
{
	public $connectionId;
	public $resultId;
	public $_currentRow = 0;
	public $_numOfRows = -1;
	public $_numOfFields = -1;
	public $fetchMode;

	public function __construct($resultId, $connectionId)
	{
		$this->fields = array();
		$this->connectionId = $connectionId;
		$this->record = array();
		$this->resultId = $resultId;
		$this->EOF = false;
	}

	public function Close()
	{
		@mysqli_free_result($this->resultId);
		$this->fields = array();
		$this->resultId = false;
	}

	public function Fields($field)
	{
		if (empty($field)) {
			return $this->fields;
		} else {
			return $this->fields[$field];
		}
	}

	public function RecordCount()
	{
		return $this->_numOfRows;
	}

	public function FieldCount()
	{
		return $this->_numOfFields;
	}

	public function MoveNext()
	{
		$this->fields = @mysqli_fetch_array($this->resultId, $this->fetchMode);
		if ($this->fields) {
			$this->_currentRow += 1;
			return true;
		}
		if (!$this->EOF) {
			$this->_currentRow += 1;
			$this->EOF = true;
		}
		return false;
	}

	public function MoveFirst()
	{
		if ($this->_currentRow == 0) {
			return true;
		}

		return $this->Move(0);
	}

	public function MoveLast()
	{
		if ($this->EOF) {
			return false;
		}

		return $this->Move($this->_numOfRows - 1);
	}

	public function Move($rowNumber = 0)
	{
		if ($rowNumber == $this->_currentRow) {
			return true;
		}

		$this->EOF = false;
		if ($this->_numOfRows > 0) {
			if ($rowNumber >= $this->_numOfRows - 1) {
				$rowNumber = $this->_numOfRows - 1;
			}
		}

		if ($this->_seek($rowNumber)) {
			$this->_currentRow = $rowNumber;
			if ($this->_fetch()) {
				return true;
			}
			$this->fields = false;
		}
		$this->EOF = true;
		return false;
	}

	public function _seek($row)
	{
		if ($this->_numOfRows == 0) {
			return false;
		}

		return @mysqli_data_seek($this->resultId, $row);
	}

	public function _fetch()
	{
		$this->fields = @mysqli_fetch_array($this->resultId, $this->fetchMode);
		return is_array($this->fields);
	}

	public function EOF()
	{
		if ($this->_currentRow < $this->_numOfRows) {
			return false;
		} else {
			$this->EOF = true;
			return true;
		}
	}

	public function &GetArray($nRows = -1)
	{
		$results = array();
		$cnt = 0;
		while (!$this->EOF && $nRows != $cnt) {
			$results[] = $this->fields;
			$this->MoveNext();
			$cnt++;
		}
		return $results;
	}

	public function &GetRows($nRows = -1)
	{
		$arr = &$this->GetArray($nRows);
		return $arr;
	}

	public function &GetAll($nRows = -1)
	{
		$arr = &$this->GetArray($nRows);
		return $arr;
	}

	public function FetchField()
	{
		return @mysqli_fetch_field($this->resultId);
	}
}

class MySqlRecordSetBase
{
	public $fields = false;
	public $EOF = true;

	public function MoveNext()
	{
		return;
	}

	public function RecordCount()
	{
		return 0;
	}

	public function FieldCount()
	{
		return 0;
	}

	public function EOF()
	{
		return true;
	}

	public function Close()
	{
		return true;
	}
}