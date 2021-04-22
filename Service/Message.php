<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 14.04.21
 * Time: 18:13
 */

namespace Pyrobyte\Behance\Service;


class Message
{
    /**
     * Приводит массив диалогов к нужному виду
     * @param $data
     * @return array
     */
    public static function formattingDialogs($data)
    {
        $formattedDialogs = [];
        foreach ($data as $item) {
            $formattedDialogs[] =
                [
                    'id' => $item['id'],
                    'recipient' => [
                        'name' => $item['recipients'][0]['display_name'],
                        'login' => $item['recipients'][0]['username'],
                        'website' => $item['recipients'][0]['website'],
                    ]
                ];
        }
        return $formattedDialogs;
    }

    /**
     * Приводит массив сообщений к нужному виду
     * @param $data
     * @param $onlyUnread
     * @return array
     */
    public static function formattingMessages($data, $onlyUnread = false)
    {
        $formattedMessages = [];
        foreach ($data as $item) {
            if ($onlyUnread && $item['is_read']) {
                continue;
            }
            $formattedMessages[] =
                [
                    'id' => $item['message_id'],
                    'text' => $item['message'],
                    'is_read' => $item['is_read']
                ];
        }
        return $formattedMessages;
    }

}