<?php

namespace Pyrobyte\Behance\Action;

use Pyrobyte\Behance\AbstractClientCookie;

class AbstractAction
{
    protected $client = null;

    function __construct()
    {
        $this->client = new AbstractClientCookie();
    }
}