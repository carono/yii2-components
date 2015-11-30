<?php
namespace carono\components\murl;
/**
 * @property string $Accept;
 * @property string $Accept_Charset
 * @property string $Accept_Encoding
 * @property string $Accept_Language
 * @property string $Authorization
 * @property string $Content_Disposition
 * @property string $Expect
 * @property string $From
 * @property string $Host
 * @property string $Max_Forwards
 * @property string $Proxy_Authorization
 * @property string $Referer
 * @property string $User_Agent
 * @property string $X_Requested_With
 * @property string $Cookie
 * @property string $Location
 * @property string $Content_Type
 *
 */
class Header
{
	const RqH = 0;
	const RsH = 1;

	private $headers = array();

	public function __set($name, $value)
	{
		$this->headers[$this->_normalizeName($name)] = $value;
	}

	public function __get($name)
	{
		$name = $this->_normalizeName($name);
		if (array_key_exists($name, $this->headers)) {
			return $this->headers[$name];
		} else {
			return null;
		}
	}

	public function __construct($type = 0)
	{
		if ($type == 0) {
			$this->User_Agent = Agent::Opera();
		}
	}

	private function _normalizeName($name)
	{
		return strtolower(str_replace('_', '-', $name));
	}

	public function get($assoc = false)
	{
		if ($assoc) {
			$result = [];
			foreach ($this->headers as $name => $header) {
				$result[] = $name . ": " . $header;
			}
			return $result;
		} else {
			return $this->headers;
		}
	}

	public function clear()
	{
		$this->headers = array();
	}
}