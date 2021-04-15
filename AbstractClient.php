<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 01.02.21
 * Time: 16:33
 */
namespace Pyrobyte\Behance;
use GuzzleHttp\Client as GuzzleClient;

class AbstractClient
{
    protected $client = null;
    protected $headers = null;
    function __construct()
    {
        $this->client = new GuzzleClient();
    }

    public function setHeaders($headers)
    {
        $this->headers = array_merge([
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:66.0) Gecko/20100101 Firefox/66.0',
            'Accept' => '*/*',
            'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Connection' => 'keep-alive',
            'Content-Type' => 'application/json',
        ], $headers);
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function get($url)
    {
        try {
            $response = $this->client->get($url, [
                'headers' => $this->headers
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $response;
    }

    public function post($url, $body)
    {
        try {
            $response = $this->client->post($url, array_merge($body, [
                'headers' => $this->headers
            ]));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $response;
    }
}