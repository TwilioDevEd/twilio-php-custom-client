<?php

use Twilio\Http\CurlClient;
use Twilio\Http\Response;

// Custom HTTP Class
class MyRequestClass extends CurlClient {
    protected $http = null;
    protected $proxy = null;

    public function __construct($proxy = null) {
        $this->proxy = $proxy;
        $this->http = new CurlClient();
    }

    public function request($method, $url, $params = array(), $data = array(),
                            $headers = array(), $user = null, $password = null,
                            $timeout = null) {
        // Here you can change the URL, headers and other request parameters
        $options = $this->options($method, $url, $params, $data, $headers, 
                                  $user, $password, $timeout);
        
        $curl = curl_init($url);
        curl_setopt_array($curl, $options);
        curl_setopt($curl, CURLOPT_PROXY, $this->proxy);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, TRUE);
        $response = curl_exec($curl);

        $parts = explode("\r\n\r\n", $response, 3);
        list($head, $body) = ($parts[0] == 'HTTP/1.1 100 Continue'
                            || $parts[0] == 'HTTP/1.1 200 Connection established')
                            ? array($parts[1], $parts[2])
                            : array($parts[0], $parts[1]);

        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $responseHeaders = array();
        $headerLines = explode("\r\n", $head);
        array_shift($headerLines);
        foreach ($headerLines as $line) {
            list($key, $value) = explode(':', $line, 2);
            $responseHeaders[$key] = $value;
        }

        curl_close($curl);

        if (isset($buffer) && is_resource($buffer)) {
            fclose($buffer);
        }
        return new Response($statusCode, $body, $responseHeaders);
    }
}
