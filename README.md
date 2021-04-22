# Авторизация в behance
Для авторизации используется класс Pyrobyte\Behance\Methods\Auth

Есть два способа авторизации
* Но оба начинаются с того, что в класс Auth передаем имя и пароль. Далее:
1) Используя сервис anti-captcha.com. 
* Вызываем метод класса Auth - auth(), без параметров. Метод вернет токен, который
будет использовать для авторизации.
2) Используя параметр взятый с запроса в браузере.
* Во время авторизации после того, как ввели логин и нажали продолжить найти запрос 
"authenticationstate" из его ответа взять параметр "id".
* Вызываем метод класса Auth с двумя параметрами - auth(Auth::AUTH_TYPE_TWO, {сюда передаем id из прошлого шага}).
Метод так же как и в прошлом способе авторизации вернет токен.
# Парсинг сообщений
Для парсинга используется класс Pyrobyte\Behance\Methods\Message
* В класс Message передаем токен, полученый при авторизации. 
* С помощью метода getDialogs() - получаем все диалоги
* С помощью метода getDialogMessages($dialogsId) - получаем сообщения определенного диалога
где $dialogsId - это идентификатор диалога, его можно получить из метода выше.
* С помощью метода getAllMessages() - получаем все сообщения распределенные по диалогам.
* Необезательный параметр "onlyUnread" (логического типа) для методов
 getAllMessages и getDialogMessages, если передать true, то будут получены только
  непрочитанные сообщения.
  
Параметры сообщений:
* id - идентификатор сообщения
* text - текст сообщения
* is_read - было ли прочитано сообщение, если false, то не прочитано.

Параметры диалогов 
* id - идентификатор диалога
* creator - данные пользователя с кем диалог
    * name - имя пользователя
    * website - вебсайт пользователя

Пример
     $auth = new \Pyrobyte\Behance\Methods\Auth("ivanbein@outlook.com", "TotKtoKaiden123");
     $token = $auth->authV1();
     $messageClass = new \Pyrobyte\Behance\Methods\Message($token);
     $allMessages = $messageClass->getAllMessages();
     
