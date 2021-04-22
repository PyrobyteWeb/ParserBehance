<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 14.04.21
 * Time: 18:26
 */

namespace Pyrobyte\Behance\Methods;

use Pyrobyte\Behance\Action\Auth as AuthAction;

class Auth
{
    const AUTH_TYPE_ONE = "v1";
    const AUTH_TYPE_TWO = "v2";

    protected $action;

    function __construct($username, $password)
    {
        $this->action = new AuthAction($username, $password);
    }

    /**
     * Метод авторизации в биханс
     * @param string $type
     * @param null $param
     * @return string
     * @throws \Exception
     */
    public function auth($type, $param)
    {
        if ($type != self::AUTH_TYPE_ONE && $type != self::AUTH_TYPE_TWO) {
            throw new \Exception("Передайте правильный тип авторизации: v1 или v2. Или не передавайте вовсе, поумолчанию v1");
        }

        if ($type == self::AUTH_TYPE_ONE) {
            return $this->action->authV1($param);
        }

        if ($type == self::AUTH_TYPE_TWO) {
           return $this->action->authV2($param);
        }
    }

}
