<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 01.02.21
 * Time: 18:23
 */
namespace Pyrobyte\Behance;
use GuzzleHttp\Client as GuzzleClient;

class AbstractClientCookie extends AbstractClient
{
    public $cookieJar  = null;
    function __construct()
    {
        $this->cookieJar = new \GuzzleHttp\Cookie\CookieJar();
        $this->client = new GuzzleClient(
            [
                'headers' => $this->headers,
                'cookies' => $this->cookieJar
            ]
        );
    }
}