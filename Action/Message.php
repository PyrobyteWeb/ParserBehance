<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 14.04.21
 * Time: 17:11
 */

namespace Pyrobyte\Behance\Action;


class Message extends AbstractAction
{
    protected $token = null;
    function __construct($token)
    {
        $this->token = $token;
        parent::__construct();
    }

    /**
     * Получает массив диалогов биханса
     * @return array
     * @throws \Exception
     */
    public function getDialogs()
    {
        $this->client->setHeaders(
            [
                'Authorization' => "Bearer " . $this->token,
            ]
        );
        $response = $this->client->get('https://www.behance.net/v2/inbox/threads');
        $body = json_decode($response->getBody(), true);
        $dialogs = $body['threads'];
        return $dialogs;
    }

    /**
     * Получает сообщения определенного диалога
     * @param $dialogsId - id диалога
     * @return array - возвращает массив сообщений
     * @throws \Exception
     */
    public function getMessages($dialogsId)
    {
        $this->client->setHeaders(
            [
                'Authorization' => "Bearer " . $this->token,
            ]
        );
        $response = $this->client->get('https://www.behance.net/v2/inbox/threads/' . $dialogsId . '/messages');
        $body = json_decode($response->getBody(), true);
        $messages = $body['messages'];
        return $messages;
    }

}