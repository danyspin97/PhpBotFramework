<?php

/*
 * This file is part of the PhpBotFramework.
 *
 * PhpBotFramework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, version 3.
 *
 * PhpBotFramework is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PhpBotFramework\Core;

use \Monolog\Logger;

use \Monolog\Handler\StreamHandler;

use PhpBotFramework\Exceptions\BotException;

use PhpBotFramework\Entities\File as TelegramFile;

use PhpBotFramework\Entities\InlineKeyboard;

/**
 * \mainpage
 * \section Documentation
 * This is the PhpBotFramework documentation for classes and methods.
 * You can find the source on [Github](https://github.com/DanySpin97/PhpBotFramework).
 * Guides are avaiable at [ReadTheDocs](http://phpbotframework.rtfd.io/)
 */
mb_internal_encoding('UTF-8');

/**
 * \class CoreBot
 * \brief Core of the framework
 * \details Contains data used by the bot to works, curl request handling, and all api methods (sendMessage, editMessageText, etc).
 */
class CoreBot
{
    use Updates,
        Send,
        Edit,
        Inline,
        Chat;

    /** \brief Chat_id of the user that interacted with the bot. */
    public $chat_id;

    /** @internal
      * \brief Bot id. */
    protected $_bot_id;

    /** @internal
      * \brief API endpoint (containing $token). */
    protected $_api_url;

    /** @internal
      * \brief Implements interface for execute HTTP requests. */
    public $_http;

    /** @internal
      * \brief Object of class PhpBotFramework\Entities\File that contain a path or resource to a file that has to be sent using Telegram API Methods. */
    protected $_file;

    /** \@internal
      * brief Contains parameters of the next request. */
    protected $parameters;

    /**
     * \@internal
     * brief Initialize a new bot.
     * \details Initialize a new bot passing its token.
     * @param $token Bot's token given by @botfather.
     */
    public function __construct(string $token)
    {
        $this->logger = new Logger('phpbotframework');

        $this->_api_url = "https://api.telegram.org/bot$token/";

        // Init connection and config it
        $this->_http = new \GuzzleHttp\Client([
            'base_uri' => $this->_api_url,
            'connect_timeout' => 5,
            'verify' => false,
            'timeout' => 60,
            'http_errors' => false
        ]);

        $this->_file = new TelegramFile();
    }

    /**
     * \addtogroup Bot Bot
     * @{
     */

    /**
     * @deprecated
     * \brief Get chat ID of the current user.
     * @return int Chat ID of the user.
     */
    public function getChatID()
    {
        return $this->chat_id;
    }

    /**
     * @deprecated
     * \brief Set current chat ID.
     * \details Change the chat ID on which the bot acts.
     * @param $chat_id The new chat ID to set.
     */
    public function setChatID($chat_id)
    {
        $this->chat_id = $chat_id;
    }

    /**
     * \brief Get bot ID using `getMe` method.
     * @return int Bot id, 0 on errors.
     */
    public function getBotID() : int
    {
        // If it is not valid
        if (!isset($this->_bot_id) || $this->_bot_id == 0) {
            // get it again
            $this->_bot_id = ($this->getMe())['id'];
        }

        return $this->_bot_id ?? 0;
    }

    /** @} */

    /**
     * \addtogroup Api Api Methods
     * \brief Implementations for Telegram Bot API's methods.
     * @{
     */

    /**
     * \brief Execute any API request using this method.
     * \details Use this method for custom api calls using this syntax:
     *
     *     $param = [
     *             'chat_id' => $chat_id,
     *             'text' => 'Hello!'
     *     ];
     *     apiRequest("sendMessage", $param);
     *
     * @param string $method The method to call.
     * @param array $parameters Parameters to add.
     * @return mixed Depends on api method.
     */
    public function apiRequest(string $method, array $parameters)
    {
        return $this->execRequest($method . '?' . http_build_query($parameters));
    }

    /** @} */

    /**
     * \@internal
     * brief Process an API method by taking method and parameter.
     * \details optionally create a object of $class class name with the response as constructor param.
     * @param string $method Method to call.
     * @param array $param Parameter for the method.
     * @param string $class Class name of the object to create using response.
     * @return mixed Response or object of $class class name.
     */
    protected function processRequest(string $method, string $class = '', $file = false)
    {
        $url = "$method?" . http_build_query($this->parameters);

        // If there is a file to upload
        if ($file === false) {
            $response = $this->execRequest($url);
        } else {
            $response = $this->execMultipartRequest($url);
        }

        if ($response === false) {
            return false;
        }

        if ($class !== '') {
            $object_class = "PhpBotFramework\Entities\\$class";

            return new $object_class($response);
        }

        return $response;
    }

    /**
     * @internal
     * \brief Check if the current file is local or not.
     * \details If the file file is local, then it has to be uploaded using multipart. If not, then it is a url/file_id so it has to be added in request parameters as a string.
     * @return PhpBotFramework\Entities\File|false The file that will be sent using multipart, false otherwise.
     */
    protected function checkCurrentFile()
    {
        if (!$this->_file->isLocal()) {
            $this->parameters[$this->_file->getFormatName()] = $this->_file->getString();
            return false;
        }

        return $this->_file;
    }


    /**
     * @internal
     * \brief Core function to execute HTTP request.
     * @param string $url The request's URL.
     * @return Array|false Url response decoded from JSON, false on error.
     */
    protected function execRequest(string $url)
    {
        $request = $this->_http->request('POST', $url);
        $response = $this->checkRequestError($request, $url);

        return $response;
    }

    /**
     * @internal
     * \brief Core function to execute HTTP request uploading a file.
     * \details Using an object of type PhpBotFramework\Entities\File contained in $_file and Guzzle multipart request option, it uploads the file along with api method requested.
     * @param $url The request's URL.
     * @return Array|false Url response decoded from JSON, false on error.
     */
    protected function execMultipartRequest(string $url)
    {
        $response = $this->_http->request('POST', $url, [
            'multipart' => [
                [
                    'name' => $this->_file->getFormatName(),
                    'contents' => $this->_file->getResource()
                ]
            ]]);

        return $this->checkRequestError($response, $url);
    }

    public function checkRequestError($response, string $url)
    {
        $http_code = $response->getStatusCode();

        if ($http_code === 200) {
            $response = json_decode($response->getBody(), true);

            return $response['result'];
        } elseif ($http_code >= 500) {
            // Avoids to send too many requests to the server if something goes wrong.
            $this->logger->warning("Got '500 Internal Server Error', sleeping 10s.");
            $this->logger->warning("Response object:\n" . var_dump($response));

            sleep(10);
            return false;
        } elseif ($http_code === 404) {
            $this->logger->warning('Request returned 404 Page Not Found');
            return false;
        } else {
            $response = json_decode($response->getBody(), true);
            $this->logger->error("Request '$url' has failed with error {$response['error_code']}: {$response['description']}\n");
            return false;
        }
    }

    /**
     * \brief Call a single api method using another chat_id without changing the current one.
     * @param string|int $chat_id API method target chat_id.
     * @param string $method Bot API method name.
     * @param mixed ...$param Parameters for the API method.
     * @return mixed The return value of the API method.
     */
    public function withChatId($chat_id, $method, ...$param)
    {
        $last_chat = $this->chat_id;
        $this->chat_id = $chat_id;
        $value = $this->$method(...$param);
        $this->chat_id = $last_chat;

        return $value;
    }

    /**
     * \brief Call the closure with the selected `chat_id`.
     * \detail At the end of the method, $chat_id will still contain the original value.
     * @param string|int $chat_id Target chat while executing the closure.
     * @param closure $closure Closure to execute with the selected `chat_id`.
     * @return mixed The return value of the closure.
     */
    public function useChatId($chat_id, \closure $closure)
    {
        $last_chat = $this->chat_id;
        $this->chat_id = $chat_id;
        $value = $closure();
        $this->chat_id = $last_chat;

        return $value;
    }

    /** @} */

    /** @} */
}
