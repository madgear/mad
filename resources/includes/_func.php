<?php

namespace php_sys\system;

function get_route()
{
	$get_uri = $_SERVER['REQUEST_URI'];
	$x_path = explode("/", $get_uri);
	$path_cnt = count($x_path);
	if ($path_cnt > 1) {
		$route_uri = str_replace('/' . $x_path[1], '', $get_uri);
	} else {
		$route_uri = '/';
	}
	return $route_uri;
}

function re_route($route, $kword)
{
	$find = array("/" . $kword, $kword);
	$rw = str_replace($find, "", $route);
	return $rw;
}

function ExecuteJSON($sqlcode, $postid = "")
{
	$rows = ExecuteRows($sqlcode);
	if (!$rows) {
		_msg(400, "No Record Found!");
		exit;
	}
	$dataset = array(
		"status" => 200,
		"message" => 'success',
		"totalrecords" => count($rows),
		"id" => $postid,
		"data" => $rows
	);
	echo json_encode($dataset);
}


function ExecuteQuery($query, $msg = "")
{
	$_SESSION[SESSION_FAILURE_MESSAGE] = "";
	Execute($query);
	if ($_SESSION[SESSION_FAILURE_MESSAGE] <> "") {
		_msg(500, $_SESSION[SESSION_FAILURE_MESSAGE]);
		$_SESSION[SESSION_FAILURE_MESSAGE] = "";
	} else {
		_msg(200, $msg);
		exit;
	}
}

function fix_tags($text)
{
	$find = array("+", "$", "|", "&", "!", "@", ",", ".", ":", ";", "'", "=", "/", chr(34), "*", "#", "-", "%", chr(92), ">", "<", "[", "]", "{", "}", "(", ")", "`", "~");
	$text = str_replace($find, "", $text);
	return $text;
}

function check_char($text)
{
	$find = array("+", "$", "|", "&", "!", "@", ",", ".", ":", ";", "'", "=", "/", chr(34), "*", "#", "-", "%", chr(92), ">", "<", "[", "]", "{", "}", "(", ")", "`", "~");
	$tmp_txt = str_replace($find, "", $text);
	if ($tmp_txt <> $text) {
		return true;
	} else {
		return false;
	}
}

function _msg($c, $m)
{
	$r = array("status" => $c, "message" => $m);
	echo json_encode($r);
}

function _token($length = 10, $ch = '01234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
	$characters = $ch;
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function check_input($data)
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

function _fv($str)
{
	$fv = filter_var($str, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
	return $fv;
}

function _fp($str)
{
	$fp = filter_input(INPUT_POST, $str, FILTER_SANITIZE_STRING);
	return $fp;
}

function &Conn($dbid = 0)
{
	$db = &Db($dbid);
	if ($db && $db["conn"] == NULL)
		ConnectDb($db);
	if ($db)
		$conn = &$db["conn"];
	else
		$conn = FALSE;
	return $conn;
}

function &Db($dbid = 0)
{
	global $CONNECTIONS;
	if (EmptyString($dbid))
		$dbid = 0;
	if (array_key_exists($dbid, $CONNECTIONS))
		$db = &$CONNECTIONS[$dbid];
	else
		$db = FALSE;
	return $db;
}

function GetConnectionType($dbid = 0)
{
	$db = Db($dbid);
	if ($db) {
		return $db["type"];
	} elseif (SameText($dbid, "MYSQL")) {
		return "MYSQL";
	}
	return FALSE;
}
function ErrorFunc($dbType, $errorType, $errorNo, $errorMsg, $param1, $param2, $obj)
{
	if ($errorType == 'CONNECT') {

		$msg = "Failed to connect to $param2 at $param1. Error: " . $errorMsg . " (" . $errorNo . ")";
	} elseif ($errorType == 'EXECUTE') {
		if (DEBUG_ENABLED) {
			$msg = "Failed to execute SQL: $param1. Error: " . $errorMsg . " (" . $errorNo . ")";
		} else {
			//$msg = "Failed to execute SQL. Error1: " . $errorMsg . " (" . $errorNo . ")";
			$msg = "(" . $errorNo . "): " . $errorMsg . ".";
		}
	}

	$_SESSION[SESSION_FAILURE_MESSAGE] = $msg;
}

function &GetConnection($dbid = 0)
{
	return Conn($dbid);
}

function ConnectDb(&$info)
{
	global $DATE_FORMAT;
	Database_Connecting($info);
	$dbid = @$info["id"];
	$dbtype = @$info["type"];

	if ($dbtype == "MYSQL") {
		$conn = new \mad_mysqlconn();
	}
	$conn->info = $info;
	$conn->debug = DEBUG_ENABLED;
	if ($dbtype == "MYSQL")
		$conn->port = (int)@$info["port"];

	$conn->raiseErrorFn = (DEBUG_ENABLED) ? $GLOBALS["ERROR_FUNC"] : "";

	if ($dbtype == "MYSQL") {
		if ($dbtype == "MYSQL")
			$conn->Connect(@$info["host"], @$info["user"], @$info["pass"], @$info["db"], @$info["new"]);
		else
			$conn->Connect(@$info["host"], @$info["user"], @$info["pass"], @$info["db"]);
		$timezone = @$info["timezone"] ?: DB_TIME_ZONE;
		if ($dbtype == "MYSQL") {
			if (MYSQL_CHARSET <> "")
				$conn->Execute("SET NAMES '" . MYSQL_CHARSET . "'");
			if ($timezone <> "")
				$conn->Execute("SET time_zone = '" . $timezone . "'");
		}
	}
	Database_Connected($conn);
	$info["conn"] = &$conn;
}

function CloseConnections()
{
	global $Conn, $CONNECTIONS;
	foreach ($CONNECTIONS as $dbid => &$db) {
		if ($db["conn"]) $db["conn"]->Close();
		$db["conn"] = NULL;
	}
	$Conn = NULL;
}

function Database_Connecting(&$info)
{

	// Example:
	//var_dump($info);
	//if ($info["id"] == "DB" && CurrentUserIP() == "127.0.0.1") { // Testing on local PC
	//	$info["host"] = "locahost";
	//	$info["user"] = "root";
	//	$info["pass"] = "";
	//}

}

function Database_Connected(&$conn)
{
}
?>

<?php
class Tea
{
	private static function _long2str($v, $w)
	{
		$len = count($v);
		$s = [];
		for ($i = 0; $i < $len; $i++) {
			$s[$i] = pack("V", $v[$i]);
		}
		if ($w) {
			return substr(join('', $s), 0, $v[$len - 1]);
		} else {
			return join('', $s);
		}
	}
	private static function _str2long($s, $w)
	{
		$v = unpack("V*", $s . str_repeat("\0", (4 - strlen($s) % 4) & 3));
		$v = array_values($v);
		if ($w) {
			$v[count($v)] = strlen($s);
		}
		return $v;
	}

	// Encrypt
	public static function encrypt($str, $key = RANDOM_KEY)
	{
		if ($str == "")
			return "";
		$v = self::_str2long($str, true);
		$k = self::_str2long($key, false);
		$cntk = count($k);
		if ($cntk < 4) {
			for ($i = $cntk; $i < 4; $i++) {
				$k[$i] = 0;
			}
		}
		$n = count($v) - 1;
		$z = $v[$n];
		$y = $v[0];
		$delta = 0x9E3779B9;
		$q = floor(6 + 52 / ($n + 1));
		$sum = 0;
		while (0 < $q--) {
			$sum = self::_int32($sum + $delta);
			$e = $sum >> 2 & 3;
			for ($p = 0; $p < $n; $p++) {
				$y = $v[$p + 1];
				$mx = self::_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ self::_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
				$z = $v[$p] = self::_int32($v[$p] + $mx);
			}
			$y = $v[0];
			$mx = self::_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ self::_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$z = $v[$n] = self::_int32($v[$n] + $mx);
		}
		return self::_urlEncode(self::_long2str($v, false));
	}

	// Decrypt
	public static function decrypt($str, $key = RANDOM_KEY)
	{
		$str = self::_urlDecode($str);
		if ($str == "")
			return "";
		$v = self::_str2long($str, false);
		$k = self::_str2long($key, false);
		$cntk = count($k);
		if ($cntk < 4) {
			for ($i = $cntk; $i < 4; $i++) {
				$k[$i] = 0;
			}
		}
		$n = count($v) - 1;
		$z = $v[$n];
		$y = $v[0];
		$delta = 0x9E3779B9;
		$q = floor(6 + 52 / ($n + 1));
		$sum = self::_int32($q * $delta);
		while ($sum != 0) {
			$e = $sum >> 2 & 3;
			for ($p = $n; $p > 0; $p--) {
				$z = $v[$p - 1];
				$mx = self::_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ self::_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
				$y = $v[$p] = self::_int32($v[$p] - $mx);
			}
			$z = $v[$n];
			$mx = self::_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ self::_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$y = $v[0] = self::_int32($v[0] - $mx);
			$sum = self::_int32($sum - $delta);
		}
		return self::_long2str($v, true);
	}
	private static function _int32($n)
	{
		while ($n >= 2147483648) $n -= 4294967296;
		while ($n <= -2147483649) $n += 4294967296;
		return (int)$n;
	}
	private static function _urlEncode($string)
	{
		$data = base64_encode($string);
		return str_replace(['+', '/', '='], ['-', '_', '.'], $data);
	}
	private static function _urlDecode($string)
	{
		$data = str_replace(['-', '_', '.'], ['+', '/', '='], $string);
		return base64_decode($data);
	}
} //END CLASS TEA


// Encrypt
function Encrypt($str, $key = RANDOM_KEY)
{
	return Tea::encrypt($str, $key);
}

// Decrypt
function Decrypt($str, $key = RANDOM_KEY)
{
	return Tea::decrypt($str, $key);
}

/**
 * Class for encryption/decryption with php-encryption
 */
class PhpEncryption
{
	protected $Key;

	// Constructor
	public function __construct($encodedKey, $password = "")
	{
		if ($password) { // Password protected key
			$key = \Defuse\Crypto\KeyProtectedByPassword::loadFromAsciiSafeString($encodedKey);
			$this->Key = $key->unlockKey($password);
		} else { // Random key
			$this->Key = \Defuse\Crypto\Key::loadFromAsciiSafeString($encodedKey);
		}
	}

	// Create random password protected key
	public static function CreateRandomPasswordProtectedKey($password)
	{
		$protectedKey = \Defuse\Crypto\KeyProtectedByPassword::createRandomPasswordProtectedKey($password);
		return $protectedKey->saveToAsciiSafeString();
	}

	// Create new random key without password
	public static function CreateNewRandomKey()
	{
		$key = \Defuse\Crypto\Key::createNewRandomKey();
		return $key->saveToAsciiSafeString();
	}

	// Encrypt with password
	public static function encryptWithPassword($plaintext, $password)
	{
		return \Defuse\Crypto\Crypto::encryptWithPassword($plaintext, $password);
	}

	// Decrypt with password
	public static function decryptWithPassword($plaintext, $password)
	{
		return \Defuse\Crypto\Crypto::decryptWithPassword($plaintext, $password);
	}

	// Encrypt
	public function encrypt($plaintext)
	{
		return \Defuse\Crypto\Crypto::encrypt($plaintext, $this->Key);
	}

	// Decrypt
	public function decrypt($plaintext)
	{
		return \Defuse\Crypto\Crypto::decrypt($plaintext, $this->Key);
	}
} // END CLASS PHP ENCRYPTION

// Encrypt with php-encryption
function PhpEncrypt($str, $key = RANDOM_KEY)
{
	return PhpEncryption::encryptWithPassword($str, $key);
}

// Decrypt with php-encryption
function PhpDecrypt($str, $key = RANDOM_KEY)
{
	return PhpEncryption::decryptWithPassword($str, $key);
}

// Check empty string
function EmptyString($value)
{
	$str = strval($value);
	if (preg_match('/&[^;]+;/', $str)) // Contains HTML entities
		$str = @html_entity_decode($str, ENT_COMPAT | ENT_HTML5, PROJECT_ENCODING);
	$str = str_replace(SameText(PROJECT_ENCODING, "UTF-8") ? "\xC2\xA0" : "\xA0", " ", $str);
	return (trim($str) == "");
}

// Execute UPDATE, INSERT, or DELETE statements
function Execute($sql, $fn = NULL, $c = NULL)
{
	if ($c == NULL && (is_string($fn) || is_object($fn) && method_exists($fn, "execute")))
		$c = $fn;
	if (is_string($c))
		$c = &Conn($c);
	$conn = $c ?: @$GLOBALS["conn"] ?: Conn();
	$conn->raiseErrorFn = $GLOBALS["ERROR_FUNC"];
	$rs = $conn->execute($sql);
	$conn->raiseErrorFn = '';
	if (is_callable($fn) && $rs) {
		while (!$rs->EOF) {
			$fn($rs->fields);
			$rs->moveNext();
		}
		$rs->moveFirst();
	}
	return $rs;
}

// Executes the query, and returns the first column of the first row
function ExecuteScalar($sql, $c = NULL)
{
	$res = FALSE;
	$rs = LoadRecordset($sql, $c);
	if ($rs && !$rs->EOF && $rs->FieldCount() > 0) {
		$res = $rs->fields[0];
		$rs->Close();
	}
	return $res;
}

// Executes the query, and returns the first row
function ExecuteRow($sql, $c = NULL)
{
	$res = FALSE;
	$rs = LoadRecordset($sql, $c);
	if ($rs && !$rs->EOF) {
		$res = $rs->fields;
		$rs->Close();
	}
	return $res;
}

// Executes the query, and returns all rows
function ExecuteRows($sql, $c = NULL)
{
	$res = FALSE;
	$rs = LoadRecordset($sql, $c);
	if ($rs && !$rs->EOF) {
		$res = $rs->GetRows();
		$rs->Close();
	}
	return $res;
}

function ExecuteHtml($sql, $options = NULL, $c = NULL)
{
	$getFieldCaption = function ($key) use ($options) {
		$caption = "";
		if (!is_array($options))
			return $key;
		$tableName = @$options["tablename"];
		$lang = @$options["language"] ?: $GLOBALS["Language"];
		$useCaption = (array_key_exists("fieldcaption", $options) && $options["fieldcaption"]);
		if ($useCaption) {
			if (is_array($options["fieldcaption"])) {
				$caption = @$options["fieldcaption"][$key];
			} elseif (isset($lang)) {
				if (is_array($tableName)) {
					foreach ($tableName as $tbl) {
						$caption = @$lang->FieldPhrase($tbl, $key, "FldCaption");
						if ($caption <> "")
							break;
					}
				} elseif ($tableName <> "") {
					$caption = @$lang->FieldPhrase($tableName, $key, "FldCaption");
				}
			}
		}
		return $caption ?: $key;
	};
	$options = is_array($options) ? $options : [];
	$horizontal = (array_key_exists("horizontal", $options) && $options["horizontal"]);
	$rs = LoadRecordset($sql, $c);
	if (!$rs || $rs->EOF || $rs->fieldCount() < 1)
		return "";
	$html = "";
	$class = @$options["tableclass"] ?: "table table-bordered ew-db-table"; // Table CSS class name
	if ($rs->RecordCount() > 1 || $horizontal) { // Horizontal table
		$cnt = $rs->fieldCount();
		$html = "<table class=\"" . $class . "\">";
		$html .= "<thead><tr>";
		$row = &$rs->fields;
		foreach ($row as $key => $value) {
			if (!is_numeric($key))
				$html .= "<th>" . $getFieldCaption($key) . "</th>";
		}
		$html .= "</tr></thead>";
		$html .= "<tbody>";
		$rowcnt = 0;
		while (!$rs->EOF) {
			$html .= "<tr>";
			$row = &$rs->fields;
			foreach ($row as $key => $value) {
				if (!is_numeric($key))
					$html .= "<td>" . $value . "</td>";
			}
			$html .= "</tr>";
			$rs->moveNext();
		}
		$html .= "</tbody></table>";
	} else { // Single row, vertical table
		$html = "<table class=\"" . $class . "\"><tbody>";
		$row = &$rs->fields;
		foreach ($row as $key => $value) {
			if (!is_numeric($key)) {
				$html .= "<tr>";
				$html .= "<td>" . $getFieldCaption($key) . "</td>";
				$html .= "<td>" . $value . "</td></tr>";
			}
		}
		$html .= "</tbody></table>";
	}
	return $html;
}

function &LoadRecordset($sql, $c = NULL)
{
	if (is_string($c))
		$c = &Conn($c);
	$conn = $c ?: @$GLOBALS["conn"] ?: Conn();
	$rs = $conn->Execute($sql);
	return $rs;
}

function SameText($str1, $str2)
{
	return strcasecmp(trim($str1), trim($str2)) === 0;
}

function &DbHelper($dbid = 0)
{
	$dbHelper = new DbHelperBase($dbid);
	return $dbHelper;
}

class DbHelperBase
{

	public $Connection;

	public function __construct($dbid = 0)
	{
		$this->Connection = &GetConnection($dbid);
	}

	public function execute($sql, $fn = NULL)
	{
		return Execute($sql, $fn, $this->Connection);
	}

	public function executeScalar($sql)
	{
		return ExecuteScalar($sql, $this->Connection);
	}

	public function executeRow($sql)
	{
		return ExecuteRow($sql, $this->Connection);
	}

	public function executeRows($sql)
	{
		return ExecuteRows($sql, $this->Connection);
	}

	public function executeHtml($sql, $options = NULL)
	{
		return ExecuteHtml($sql, $options, $this->Connection);
	}

	public function &loadRecordset($sql)
	{
		return LoadRecordset($sql, $this->Connection);
	}
}
?>