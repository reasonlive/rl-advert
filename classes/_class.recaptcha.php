<?php  

class ReCaptcha
{
    /**
     * Version of this client library.
     * @const string
     */
    const VERSION = 'php_1.1.2';

   
    private $secret;

    private $response;

    private $remoteIp;

    
    public function __construct($secret, $response = null, $remoteIp = null)
    {
        if (empty($secret)) {
            throw new \RuntimeException('No secret provided');
        }

        if (!is_string($secret)) {
            throw new \RuntimeException('The provided secret must be a string');
        }

        $this->secret = $secret;

        if (!is_null($response)) {
            $this->response = $response;
        }

        if (!is_null($remoteIp)) {
            $this->remoteIp = $remoteIp;
        }

        
    }

    public function toArray()
    {
        $params = array('secret' => $this->secret, 'response' => $this->response);

        if (!is_null($this->remoteIp)) {
            $params['remoteip'] = $this->remoteIp;
        }

        $params['version'] = self::VERSION;

        return $params;
    }

    public function toQueryString()
    {
        return http_build_query($this->toArray(), '', '&');
    }

    public function submit()
    {
        /**
         * PHP 5.6.0 changed the way you specify the peer name for SSL context options.
         * Using "CN_name" will still work, but it will raise deprecated errors.
         */
        $peer_key = version_compare(PHP_VERSION, '5.6.0', '<') ? 'CN_name' : 'peer_name';
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => $this->toQueryString(),
                // Force the peer to validate (not needed in 5.6.0+, but still works
                'verify_peer' => true,
                // Force the peer validation to use www.google.com
                $peer_key => 'www.google.com',
            ),
        );
        $context = stream_context_create($options);
        return file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
    }

    

    public function verify()
    {
        
        if (empty($this->response)) {
            $recaptchaResponse = "missing_input_response";
            return $recaptchaResponse;
        }

        $rawResponse = $this->submit();
        $responseData = json_decode($rawResponse, true);
        if(!$responseData)return false;
        if (isset($responseData['error-codes']) && is_array($responseData['error-codes']))return false;
        if(isset($responseData['success']) && $responseData['success'] == true)return true;
    }
}

?>