<?php

namespace Pyrobyte\Anticaptcha;

include_once('AdminAnticaptcha' . DIRECTORY_SEPARATOR . 'anticaptcha.php');
include_once('AdminAnticaptcha' . DIRECTORY_SEPARATOR . 'imagetotext.php');
include_once('AdminAnticaptcha' . DIRECTORY_SEPARATOR . 'customcaptcha.php');

class Anticaptcha
{
    public static $driver = 'anticaptcha';

    /**
     * Тип картинки, с которой работаем
     * @var string
     */
    public static $imageType = self::IMAGE_TYPE_URL;

    /**
     * Время ожидания распознавания капчи в секундах
     * @var int
     */
    public static $timeLimit = 60;

    protected $token;
    protected $engine;

    const IMAGE_TYPE_URL = 'url';
    const IMAGE_TYPE_PATH = 'path';
    const IMAGE_TYPE_BASE64 = 'base64';

    public function __construct($token = null)
    {
        if ($token) {
            $this->initEngine($token);
        }
    }

    public function initEngine($token)
    {
        $this->token = $token;

        switch (static::$imageType) {
            case self::IMAGE_TYPE_URL:
                $engine = new \CustomCaptcha();
                break;
            case self::IMAGE_TYPE_PATH:
                $engine = new \ImageToText();
                break;
            default:
                throw new \Exception('Unknouwn type is ' . static::$imageType);
        }

//        $engine->setVerboseMode(true);
        $engine->setKey($this->token);

        $this->engine = $engine;
    }

    /**
     * Распознавание капчи. Возвращает в качестве
     * @param $image
     * @return bool
     */
    public function recognize($image)
    {
        switch (static::$imageType) {
            case self::IMAGE_TYPE_URL:
                $this->engine->setImageUrl($image);
                break;
            case self::IMAGE_TYPE_PATH:
                $this->engine->setFile($image);
        }

        if (!$this->engine->createTask()) {
            $this->engine->debout("API v2 send failed - " . $this->engine->getErrorMessage(), "red");
            throw new RequestException('Anticaptcha error creating task');
        }

        if (!$this->engine->waitForResult(Config::getItem('time_limit'))) {
            $this->engine->debout("could not solve captcha", "red");
            $this->engine->debout($this->engine->getErrorMessage());
            throw new RequestException('Anticaptcha recognize error: ' . $this->engine->getErrorMessage());
        }

        $recaptchaToken = $this->engine->getTaskSolution();
        if (!$recaptchaToken) {
            throw new RequestException('Anticaptcha empty recognize response');
        }

        return $recaptchaToken;
    }

    /**
     * Возвращает баланс счета
     */
    public function getBalance()
    {
        $balance = $this->engine->getBalance();
        if (!is_numeric($balance)) {
            throw new RequestException('Error getting anticaptcha balance');
        }

        return (float)$balance;
    }

    public static function setImageType($type)
    {
        self::$imageType = $type;
    }
}