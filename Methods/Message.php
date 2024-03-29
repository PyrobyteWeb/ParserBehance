<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 14.04.21
 * Time: 18:26
 */

namespace Pyrobyte\Behance\Methods;


use Pyrobyte\Behance\Action\Message as MessageAction;
use Pyrobyte\Behance\Service\Message as MessageService;

class Message
{
    protected $action;
    function __construct($token)
    {
        $this->action = new MessageAction($token);
    }

    /**
     * Получает диалоги
     * @return array
     * @throws \Exception
     */
    public function getDialogs()
    {
        return MessageService::formattingDialogs($this->action->getDialogs());
    }

    /**
     * Получает сообщения диалога
     * @param $dialogsId - id диалога
     * @param $onlyUnread
     * @return array
     * @throws \Exception
     */
    public function getDialogMessages($dialogsId, $onlyUnread = false)
    {
        $messages = MessageService::formattingMessages($this->action->getMessages($dialogsId), $onlyUnread);
        return $messages;
    }

    /**
     * Получает все сообщения распределенные по диалогам
     * @param $onlyUnread
     * @return array
     * @throws \Exception
     */
    public function getAllMessages($onlyUnread = false)
    {
        $messages = [];
        $threads = $this->getDialogs();
        foreach ($threads as $item) {
            $messages[$item['id']] = [
                'name' => $item['recipient']['name'],
                'login' => $item['recipient']['login'],
                'message' => $this->getDialogMessages($item['id'], $onlyUnread),
            ];
        }
        return $messages;
    }

}