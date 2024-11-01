<?php

namespace Z4Money;

/**
 * Exceptions class for HTTP conections
 *
 * @category Class
 */
class Environment
{
    private $api;

    private function __construct($api)
    {
        $this->api = $api;
    }

    public static function production()
    {
        $api = 'https://api.zsystems.com.br/';

        return new Environment($api);
    }

    public static function sandbox()
    {
        $api = 'https://sandbox.zsystems.com.br/';

        return new Environment($api);
    }

    public function getApiURL()
    {
        return $this->api;
    }

}
