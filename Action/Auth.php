<?php

namespace Pyrobyte\Behance\Action;

use Pyrobyte\Anticaptcha\AdminAnticaptcha\RecaptchaV3Enterprise;

class Auth extends AbstractAction
{
    protected $password = null;
    protected $username = null;
    private $stateId = null;
    function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        parent::__construct();
    }

    /**
     * Первый способ авторизации в биханс (полный). Используя антикапчу
     * @param $anticaptchaKey
     * @return string
     * @throws \Exception
     */
    public function authV1($anticaptchaKey)
    {
        $i = 0;
        do {
            if ($i > 10) {
                break;
            }
            $this->audit();
            sleep(1);
            $this->accounts();
            sleep(1);
            $this->audit();
            sleep(1);
            $recaptchaToken = $this->getAnticaptchata($anticaptchaKey);
            sleep(2);
            $resultStepOne = $this->authenticationStepOne($recaptchaToken);
            $i++;
        } while (!$resultStepOne);
       $tokenOne = $this->authenticationStepTwo();
       $tokenTwo = $this->getAdobeTokens($tokenOne);
       $refreshUrl=  $this->loginWithJWT($tokenTwo);
       $authToken = $this->getAuthToken($refreshUrl);
       return $authToken;

    }

    /**
     * Второй способ авторизации в биханс. Параметр stateId берется из web
     * @param $stateId
     * @return string
     */
    public function authV2($stateId)
    {
        $this->stateId = $stateId;
        $tokenOne = $this->authenticationStepTwo();
        $tokenTwo = $this->getAdobeTokens($tokenOne);
        $refreshUrl = $this->loginWithJWT($tokenTwo);
        $authToken = $this->getAuthToken($refreshUrl);
        return $authToken;
    }

    /**
     * Метод который, получает токен из антикапчи, в случаи ошибки возрващает false
     * @return bool | string
     */
    private function getAnticaptchata($anticaptchaKey)
    {
        $anticaptcha = new RecaptchaV3Enterprise();
        $anticaptcha->setWebsiteURL("https://auth.services.adobe.com/ru_RU/index.html?callback=https%3A%2F%2Fims-na1.adobelogin.com%2Fims%2Fadobeid%2FBehanceWebSusi1%2FAdobeID%2Ftoken%3Fredirect_uri%3Dhttps%253A%252F%252Fwww.behance.net%252F%253Fisa0%253D1%2523from_ims%253Dtrue%2526old_hash%253D%2526api%253Dauthorize%2526rctx%253D%25257B%252522intent%252522%25253A%252522signIn%252522%25252C%252522csrf%252522%25253A%252522d2f76851-fd41-450f-83b1-850caa4655ea%252522%25252C%252522version%252522%25253A1%25257D%26state%3D%257B%2522ac%2522%253A%2522behance.net%2522%252C%2522csrf%2522%253A%2522d2f76851-fd41-450f-83b1-850caa4655ea%2522%257D%26code_challenge_method%3Dplain%26use_ms_for_expiry%3Dtrue&client_id=BehanceWebSusi1&scope=AdobeID%2Copenid%2Cgnav%2Csao.cce_private%2Ccreative_cloud%2Ccreative_sdk%2Cbe.pro2.external_client%2Cadditional_info.roles&denied_callback=https%3A%2F%2Fims-na1.adobelogin.com%2Fims%2Fdenied%2FBehanceWebSusi1%3Fredirect_uri%3Dhttps%253A%252F%252Fwww.behance.net%252F%253Fisa0%253D1%2523from_ims%253Dtrue%2526old_hash%253D%2526api%253Dauthorize%2526rctx%253D%25257B%252522intent%252522%25253A%252522signIn%252522%25252C%252522csrf%252522%25253A%252522d2f76851-fd41-450f-83b1-850caa4655ea%252522%25252C%252522version%252522%25253A1%25257D%26response_type%3Dtoken%26state%3D%257B%2522ac%2522%253A%2522behance.net%2522%252C%2522csrf%2522%253A%2522d2f76851-fd41-450f-83b1-850caa4655ea%2522%257D&state=%7B%22ac%22%3A%22behance.net%22%2C%22csrf%22%3A%22d2f76851-fd41-450f-83b1-850caa4655ea%22%7D&relay=c51a4b8e-f696-429b-9151-233a153bf9e8&locale=ru_RU&flow_type=token&dctx_id=bhnc_22989526-955d-49e3-9a7d-f093e8f3dbf5&idp_flow_type=login#/");
        $anticaptcha->setKey($anticaptchaKey);
        $anticaptcha->setWebsiteKey("6LcGE-4ZAAAAAG2tFdbr7QqpimWAPqhLjI8_O_69");
        $anticaptcha->setPageAction("ob6vwc5vq0n3");
        $anticaptcha->setMinScore(0.7);
        $anticaptcha->createTask();
        if (!$anticaptcha->waitForResult()) {
            $anticaptcha->debout("could not solve captcha", "red");
            $anticaptcha->debout($anticaptcha->getErrorMessage());
            return false;
        } else {
            $recaptchaToken = $anticaptcha->getTaskSolution();
            return $recaptchaToken;
        }
    }

    /**
     * Метод для иммулирования нормального поведения
     * @throws \Exception
     */
    private function accounts()
    {
        $this->client->setHeaders(
            [
                'X-IMS-CLIENTID' => 'BehanceWebSusi1',
                'X-DEBUG-ID' => '0c0c3abc-90cb-40c4-9de2-ff3aab67a11f',
            ]
        );

        $this->client->post('https://auth.services.adobe.com/signin/v2/users/accounts',
            [
                'json' => [
                    'username' => $this->username,
                ],
            ]
        );
    }

    /**
     * Метод для иммулирования нормального поведения
     * @throws \Exception
     */
    private function audit()
    {
        $this->client->setHeaders(
            [
                'X-IMS-CLIENTID' => 'BehanceWebSusi1',
                'X-DEBUG-ID' => 'c51a4b8e-f696-429b-9151-233a153bf9e8',
            ]
        );

        $this->client->post('https://auth.services.adobe.com/signin/v2/users/accounts',
            [
                'json' => [
                    'username' => $this->username,
                ],
            ]
        );
    }

    /**
     * Первый запрос аунтификации, в который передается только username, так же передается токен полученный из рекапчи
     * В данном методе устанавливается stateId, при первом способе авторизации
     * @param $authToken - токен полученный из рекапчи
     * @return bool
     * @throws \Exception
     */
    private function authenticationStepOne($authToken)
    {
        $this->client->setHeaders(
            [
                'X-IMS-CLIENTID' => 'BehanceWebSusi1',
                'X-DEBUG-ID' => 'c51a4b8e-f696-429b-9151-233a153bf9e8',
                'x-ims-captcha-token' => $authToken
            ]
        );

        $response = $this->client->post('https://auth.services.adobe.com/signin/v1/authenticationstate',
            [
                'json' => [
                    'username' => $this->username,
                    'accountType' => "individual",
                ],
            ]
        );
        $body = json_decode($response->getBody(), true);
        $confirmationRequired = $body['confirmationRequired'];
        if (!$confirmationRequired) {
            $this->stateId = $body['id'];
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Второй запрос аунтификации, в запросе используется уже username и password вместе
     * Так же первый раз используется stateId. С этого запроса начинается 2 способ авторизации
     * @return mixed
     * @throws \Exception
     */
    private function authenticationStepTwo()
    {
        $this->client->setHeaders(
            [
                'X-IMS-CLIENTID' => 'BehanceWebSusi1',
                'X-DEBUG-ID' => '0c0c3abc-90cb-40c4-9de2-ff3aab67a11f',
                'X-IMS-Authentication-State' => $this->stateId // изменяемый токен
            ]
        );

        $response = $this->client->post('https://auth.services.adobe.com/signin/v2/tokens?credential=password',
            [
                'json' => [
                    'username' => $this->username,
                    'password' => $this->password,
                    'accountType' => 'individual',
                ],
            ]
        );
        $body = json_decode($response->getBody(), true);
        $tokenOne = $body['token'];
        return $tokenOne;
    }

    /**
     * Получение токена адобе, на вход метода передаем токен полученный после цепочки аунтификации
     * @param $tokenOne
     * @return mixed
     * @throws \Exception
     */
    private function getAdobeTokens($tokenOne)
    {
        $this->client->setHeaders(
            [
                'Authorization' => 'Bearer ' . $tokenOne,
                'X-IMS-CLIENTID' => 'BehanceWebSusi1',
                'X-DEBUG-ID' => '0c0c3abc-90cb-40c4-9de2-ff3aab67a11f',
                'X-IMS-NONCE' => $this->stateId,
                'X-IMS-Authentication-State' => $this->stateId // изменяемый токен
            ]
        );

        $response = $this->client->post('https://auth.services.adobe.com/signin/v1/adobeidptokens',[]);
        $body = json_decode($response->getBody(), true);
        $tokenTwo = $body['token'];
        return $tokenTwo;
    }

    /**
     * На вход передаем токен адобе и получаем ссылочку которая вернет нам уже токен авторизации в бихансе :)
     * @param $tokenTwo
     * @return string
     * @throws \Exception
     */
    private function loginWithJWT($tokenTwo)
    {
        $this->client->setHeaders(
            [
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:66.0) Gecko/20100101 Firefox/66.0',
                'Accept' => '*/*',
                'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Connection' => 'keep-alive',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            ]
        );

        $response = $this->client->post('https://adobeid-na1.services.adobe.com/renga-idprovider/pages/login_with_jwt',
            [
                'form_params' => [
                    'token' => $tokenTwo,
                    'remember_me' => 'true',
                    'client_id' => 'BehanceWebSusi1',
                    'flow_type' => 'token',
                    'callback' => 'https://ims-na1.adobelogin.com/ims/adobeid/BehanceWebSusi1/AdobeID/token?redirect_uri=https%3A%2F%2Fwww.behance.net%2F%3Fisa0%3D1%23from_ims%3Dtrue%26old_hash%3D%26api%3Dauthorize&state=%7B%22ac%22%3A%22behance.net%22%2C%22csrf%22%3A%2274cee365-f867-4a01-8dae-a5d58b3faaeb%22%2C%22intent%22%3A%22signIn%22%2C%22version%22%3A1%7D&code_challenge_method=plain&use_ms_for_expiry=true',
                    'scope' => 'AdobeID,openid,gnav,sao.cce_private,creative_cloud,creative_sdk,be.pro2.external_client,additional_info.roles',
                    'state' => '{"ac":"behance.net","csrf":"74cee365-f867-4a01-8dae-a5d58b3faaeb","intent":"signIn","version":1}',
                    'locale' => 'ru_RU',
                    'relay' => '3a21a1c6-a008-4f02-87a9-5443f8ca99f7',
                    'dctx_id' => 'bhnc_22989526-955d-49e3-9a7d-f093e8f3dbf5',
                    'denied_callback' => 'https://ims-na1.adobelogin.com/ims/denied/BehanceWebSusi1?redirect_uri=https%3A%2F%2Fwww.behance.net%2F%3Fisa0%3D1%23from_ims%3Dtrue%26old_hash%3D%26api%3Dauthorize&response_type=token&state=%7B%22ac%22%3A%22behance.net%22%2C%22csrf%22%3A%2274cee365-f867-4a01-8dae-a5d58b3faaeb%22%2C%22intent%22%3A%22signIn%22%2C%22version%22%3A1%7D',
                    'flow' => 'true'
                ],
            ]
        );
        $body = $response->getBody()->getContents();
        if (preg_match('/(?<=url=)(.*)(?=">)/imu', $body, $matches)) {
            $result = $matches[0];
        } else {
            $result = "";
        }
        return $result;
    }

    /**
     * Таки получаем токен авторизации в бихансе
     * @param $refreshUrl - ссылочка :)
     * @return string
     * @throws \Exception
     */
    private function getAuthToken($refreshUrl)
    {
        $response = $this->client->get($refreshUrl);
        $body = $response->getBody()->getContents();
        if (preg_match('/(?<=access_token=)(.*)(?=&state)/imu', $body, $matches)) {
            $result = $matches[0];
        } else {
            $result = "";
        }
        return $result;
    }
}