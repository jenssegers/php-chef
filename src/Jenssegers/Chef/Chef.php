<?php namespace Jenssegers\Chef;

class Chef {

    protected $server;
    protected $key;
    protected $client;
    protected $version;

    // the number of seconds to wait while trying to connect
    protected $timeout = 10;

    /**
     * Create a new Chef instance.
     *
     * @param  string  $server
     * @param  string  $client
     * @param  string  $key
     * @param  string  $version
     * @return void
     */
    function __construct($server, $client, $key, $version) {
        $this->server = $server;
        $this->client = $client;
        $this->key = $key;
        $this->version = $version;
    }

    /**
     * API calls.
     *
     * @param  string  $endpoint
     * @param  mixed   $data
     * @param  string  $method
     * @return mixed
     */
    function api($endpoint, $method = 'GET', $data = FALSE) {
        // json encode data
        if ($data && !is_string($data))
            $data = json_encode($data);

        // basic header
        $header = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Chef-Version: ' . $this->version
        );

        // endpoint needs to start with forward slash
        if (substr($endpoint, 0, 1) != '/')
            $endpoint = '/' . $endpoint;

        // method always uppercase
        $method = strtoupper($method);

        // sign the request
        $this->sign($endpoint, $method, $data, $header);

        // initiate curl requset
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // add data to post en put requests
        if ($method == 'POST' || $method == 'PUT') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        // execute
        $response = curl_exec($ch);
        curl_close($ch);
        
        if ($response !== FALSE)
            return json_decode($response);

        return $response;
    }

    /**
     * Sign API calls with private key.
     *
     * @param  string  $endpoint
     * @param  string  $method
     * @param  json    $data
     * @param  array   $headers
     * @return void
     */
    private function sign($endpoint, $method, $data, &$header) {
        // generate timestamp
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");

        // add X-Ops headers
        $header[] = 'X-Ops-Sign: version=1.0';
        $header[] = 'X-Ops-UserId: ' . $this->client;
        $header[] = 'X-Ops-Timestamp: ' . $timestamp;
        $header[] = 'X-Ops-Content-Hash: ' . base64_encode(sha1($data, true));

        // create signature
        $signature = 
            "Method:" . $method . "\n" .
            "Hashed Path:" . base64_encode(sha1($endpoint, true)) . "\n" .
            "X-Ops-Content-Hash:" . base64_encode(sha1($data, true)) . "\n" .
            "X-Ops-Timestamp:" . $timestamp . "\n" .
            "X-Ops-UserId:" . $this->client;

        // encrypt signature with private key
        $key = openssl_get_privatekey("file://" . $this->key);
        openssl_private_encrypt($signature, $crypted, $key);
        $encoded = base64_encode($crypted);

        // add signature to header
        $shrapnel = explode("\n", chunk_split($encoded, 60));
        for ($i = 0; $i < count($shrapnel); $i++) {
            $header[] = "X-Ops-Authorization-" . ($i + 1) . ": " . trim($shrapnel[$i]);
        }
    }

}