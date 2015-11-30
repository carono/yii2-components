<?php
namespace carono\components\murl;
/**
 * @property Header                $headers
 * @property Header                $response_headers
 * @property string                $cookie
 */
class MUrl
{
	public $tmpFolder;
	private $_cookie = [];
	public $headers;
	public $post = [];
	public $timeout = 300;
	public $ssl_verifyPeer = false;
	public $ssl_verifyHost = false;
	public $encoding = "";
	public $proxyHost = "";
	public $proxyPort = 3128;
	public $response_headers;
	public $request_headers = "method not implemented";
	public $HTTP_CODE = 0;
	public $postAsString = true;
	public $charset = null;
	public $postUrlEncode = true;
	public $followRedirect = 0;
	public $followCookie = 0;
	public $url;
	public $content;
	private $_redirection = 10;

	public function __get($name)
	{
		if ($name == 'cookie') {
			$cookie = [];
			foreach ($this->_cookie as $name => $value) {
				$cookie[] = $name . '=' . $value;
			}
			return join('; ', $cookie);
		} else {
			throw new \Exception("Property $name not found");
		}
	}

	public function __set($name, $value)
	{
		if ($name == "cookie") {
			if (!is_array($value)) {
				$value = [$value];
			}
			foreach ($value as $cookie) {
				$arr = explode('=', $cookie);
				$this->_cookie[$arr[0]] = $arr[1];
			}
		} else {
			throw new \Exception("Property $name not found");
		}
	}

	public function getPostData()
	{
		if ($this->postAsString) {
			if (is_array($this->post)) {
				$p = array();
				foreach ($this->post as $name => $value) {
					$name = ($this->postUrlEncode ? urlencode($name) : $name);
					$value = ($this->postUrlEncode ? urlencode($value) : $value);
					$p[] = $name . "=" . $value;
				}
				return join("&", $p);
			} else {
				return ($this->postUrlEncode ? urlencode($this->post) : $this->post);
			}
		} else {
			$post = $this->post;
			if ($this->postUrlEncode) {
				$post = [];
				foreach ($this->post as $name => $value) {
					$post[urlencode($name)] = urlencode($value);
				}
			}
			return $post;
		}
	}

	public function getContent($url, $charset = null)
	{
		$this->url = $url = $this->checkURL($url);
		$this->response_headers->clear();
		$this->HTTP_CODE = 0;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifyPeer);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->ssl_verifyHost);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

		if ($this->followCookie && $this->cookie) {
			$this->headers->Cookie = $this->cookie;
		}

		if ($this->encoding) {
			curl_setopt($ch, CURLOPT_ENCODING, $this->encoding);
		}

		if (count($this->headers->get())) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers->get(true));
		}

		if ($this->post) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getPostData());
		}

//        if ($this->followRedirect) {
//            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//            curl_setopt($ch, CURLOPT_MAXREDIRS, (int)$this->followRedirect <= 10 ? (int)$this->followRedirect : 10);
//        }


		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->headers->User_Agent);

		if ($this->proxyHost && $this->proxyPort) {
			curl_setopt($ch, CURLOPT_PROXY, $this->proxyHost . ":" . $this->proxyPort);
		}

		$verbose = fopen('php://temp', 'rw+');
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_STDERR, $verbose);

		$this->content = $content = curl_exec($ch);
		$this->HTTP_CODE = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		rewind($verbose);
		$verboseLog = stream_get_contents($verbose);
		$this->request_headers = $verboseLog;

		preg_match_all('|Set-Cookie: (.*);|U', $content, $results);
		$this->extractHeaders($this->response_headers, $content);
		$content = $this->deleteRawHeaders($content);
		$this->cookie = $results[1];
		if ($charset || $this->charset) {
			if ($charset) {
				$content = iconv($charset, 'UTF-8', $content);
			} else {
				$content = iconv($this->charset, 'UTF-8', $content);
			}
		}
		if (($this->HTTP_CODE == 302 || $this->HTTP_CODE == 301) && $this->followRedirect && $this->_redirection > 0) {
			$this->_redirection--;
			return $this->getContent(self::_fixUrl($url, $this->response_headers->Location), $charset);
		} else {
			$this->_redirection = 10;
		}
		return $content;
	}

	private static function _fixUrl($baseUrl, $url)
	{
		$arr = parse_url($baseUrl);
		if (strpos($url, 'http') !== 0 && strpos($url, '//') !== 0) {
			if (strpos($url, '/') === 0) {
				return $arr["scheme"] . "://" . $arr["host"] . $url;
			} else {
				$path = explode('/', $arr["path"]);
				array_pop($path);
				return $arr["scheme"] . "://" . $arr["host"] . "/" . join('/', $path) . "/" . $url;
			}
		} else {
			return $url;
		}
	}

	public function downloadFile($url)
	{
		$content = $this->getContent($url);
		if ($this->HTTP_CODE !== 200) {
			return [];
		}
		if ($this->tmpFolder) {
			$tmpName = tempnam($this->tmpFolder, "CParser");
			$fp = fopen($tmpName, 'w+');
		} else {
			$fp = tmpfile();
		}
		fwrite($fp, $content);
		$fData = stream_get_meta_data($fp);
		rewind($fp);
		$fileName = '';
		if ($this->response_headers->Content_Disposition) {
			preg_match('/filename=([^ ]+)/', $this->response_headers->Content_Disposition, $match);
			if (isset($match[1])) {
				$fileName = trim($match[1], '"');
			}
		}
		if (!$fileName) {
			$pathParts = pathinfo($url);
			$fileName = $pathParts["basename"];
		}
		$result = [];
		$result["name"] = $fileName;
		$fInfo = finfo_open(FILEINFO_MIME_TYPE);
		$result["type"] = finfo_file($fInfo, $fData["uri"]);
		$result["size"] = filesize($fData["uri"]);
		$result["tmp_name"] = $fData["uri"];
		$result["handle"] = $fp;
		finfo_close($fInfo);
		return $result;
	}

	/**
	 * @param Header $headers
	 * @param string $rawHeaders
	 */
	private function extractHeaders(&$headers, $rawHeaders)
	{
		$res = Array();
		preg_match_all('|(.*): (.*)' . chr(13) . chr(10) . '|U', $rawHeaders . chr(13) . chr(10), $res1);
		if (count($res1[1]) && count($res1[2])) {
			$res = array_combine($res1[1], $res1[2]);
		}
		foreach ($res as $header => $value) {
			$headers->$header = $value;
		}
	}

	private function deleteRawHeaders($content)
	{
		$content = substr($content, strpos($content, "\r\n\r\n") + 4);
		if (strpos($content, 'HTTP/') === 0) {
			return $this->deleteRawHeaders($content);
		}
		return $content;
	}

	public function __construct()
	{
		$this->headers = new Header();
		$this->response_headers = new Header(1);
	}

	private function checkURL($url)
	{
		if (strpos($url, "http") !== 0) {
			$url = "http://" . $url;
		}
		return $url;
	}
}