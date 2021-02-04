<?php
# Class SocketIO
# develope by psinetron (slybeaver)
# Git: https://github.com/psinetron
# web-site: http://slybeaver.ru

class SocketIO {
	# @param null $host - $host of socket server
	# @param null $port - port of socket server
	# @param string $action - action to execute in sockt server
	# @param null $data - message to socket server
	# @param string $address - addres of socket.io on socket server
	# @param string $transport - transport type
	# @return bool

	public function send($host = null, $port = null, $action= "message",  $data = null, $address = "/socket.io/?EIO=3", $transport = 'websocket') {
		$fp = @fsockopen($host, $port, $errno, $errstr);
		if(!$fp) {
			return "$errstr ($errno)";
		}else{
			$key = $this->generateKey();
			$protocol = (isset($_SERVER["HTTPS"]) | (isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && strtolower(trim($_SERVER["HTTP_X_FORWARDED_PROTO"])) == "https")) ? "https://" : "http://";

			$out = "GET $address&transport=$transport ".(isset($_SERVER["SERVER_PROTOCOL"]) ? $_SERVER["SERVER_PROTOCOL"] : "HTTP/1.1")."\r\n";
			$out.= "Host: ".$_SERVER["HTTP_HOST"].":$port\r\n";
			$out.= "Connection: Upgrade\r\n";
			$out.= "Origin: ".$protocol.$_SERVER["HTTP_HOST"]."\r\n";
			$out.= "User-Agent: ".$_SERVER["HTTP_USER_AGENT"]."\r\n";

			$out.= isset($_SERVER["HTTP_ACCEPT"]) ? "Accept: ".$_SERVER["HTTP_ACCEPT"]."\r\n" : false;
			$out.= isset($_SERVER["HTTP_ACCEPT_ENCODING"]) ? "Accept-Encoding: ".$_SERVER["HTTP_ACCEPT_ENCODING"]."\r\n" : false;
			$out.= isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) ? "Accept-Language: ".$_SERVER["HTTP_ACCEPT_LANGUAGE"]."\r\n" : false;

			//$out.= isset($_SERVER["HTTP_COOKIE"]) ? "Cookie: ".$_SERVER["HTTP_COOKIE"]."\r\n" : false;
			//$out.= isset($_SESSION) ? "Cookie: ".session_name()."=".session_id()."\r\n" : false;

			$out.= "Cache-Control: no-cache\r\n";
			$out.= "Pragma: no-cache\r\n";

			$out.= "Sec-WebSocket-Key: $key\r\n";
			$out.= "Sec-WebSocket-Version: 13\r\n";
			$out.= "Upgrade: websocket\r\n\r\n";

			fwrite($fp, $out);
			$result = fread($fp, 10000);
			preg_match('#Sec-WebSocket-Accept:\s(.*)$#mU', $result, $matches);
			$keyAccept = trim($matches[1]);
			$expectedResonse = base64_encode(pack('H*', sha1($key.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
			$handshaked = ($keyAccept === $expectedResonse) ? true : false;

			$data = is_array($data) ? json_encode($data) : $data;

			if($handshaked && trim($data)) {
				fwrite($fp, $this->hybi10Encode('42["'.$action.'", "'.(!get_magic_quotes_gpc() ? addslashes(str_ireplace("'", "", $data)) : $data).'"]'));
				fread($fp, 1000000);
				return true;
			}else{
				return "Error sent to socket or data=false";
			}
			fclose($fp);
		}
	}

	private function generateKey($length = 16) {
		$c = 0; $tmp = '';
		while ($c++ * 16 < $length) {
			$tmp.= md5(mt_rand(), true);
		}
		return base64_encode(substr($tmp, 0, $length));
	}

	private function hybi10Encode($payload, $type = 'text', $masked = true) {
		$frameHead = array();
		$payloadLength = strlen($payload);

		switch($type) {
			case 'text':  $frameHead[0] = 129; break;
			case 'close': $frameHead[0] = 136; break;
			case 'ping':  $frameHead[0] = 137; break;
			case 'pong':  $frameHead[0] = 138; break;
		}

		if($payloadLength > 65535) {
			$payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
			$frameHead[1] = ($masked === true) ? 255 : 127;

			for($i = 0; $i < 8; $i++) {
				$frameHead[$i + 2] = bindec($payloadLengthBin[$i]);
			}

			if($frameHead[2] > 127) {
				$this->close(1004);
				return false;
			}

		}elseif($payloadLength > 125) {
			$payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
			$frameHead[1] = ($masked === true) ? 254 : 126;
			$frameHead[2] = bindec($payloadLengthBin[0]);
			$frameHead[3] = bindec($payloadLengthBin[1]);
		}else{
			$frameHead[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
		}

		foreach(array_keys($frameHead) as $i) {
			$frameHead[$i] = chr($frameHead[$i]);
		}

		if($masked === true) {
			$mask = array();
			for($i = 0; $i < 4; $i++) {
				$mask[$i] = chr(rand(0, 255));
			}
			$frameHead = array_merge($frameHead, $mask);
		}

		$frame = implode('', $frameHead);
		for($i = 0; $i < $payloadLength; $i++) {
			$frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];
		}

		return $frame;
	}
}
?>