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

namespace PhpBotFramework\Commands;

/**
 * \addtogroup Commands
 * @{
 */

/** \class PingCommand
 */
class PingCommand extends MessageCommand
{
    /** @} */

    public static $type = 'message';

    public static $object_class = 'PhpBotFramework\Entities\Message';

    public static $priority = 1;

    private $command;

    private $length;

    /**
     * \brief Registers a ping command which returns response time..
     * \details It works like <code>MessageCommand</code> but it not requires arguments to be passed.
     *
     *     $ping_command = new PhpBotFramework\Commands\PingCommand();
     */
    public function __construct()
    {
        $this->command = "/ping";
        $this->length = strlen($command) + 1;
        $this->script = $this->returnResponseTime();
    }

    /**
     * @internal
     * \brief Process a message checking if it trigger any MessageCommand.
     * @param string $message Message to process.
     * @return bool True if the message trigger any command.
     */
    public function checkCommand(array $message) : bool
    {
        // If the message contains a bot command at the start
        $message_is_command = (isset($message['entities']) && $message['entities'][0]['type'] === 'bot_command') ? true : false;

        // If we found a valid command (check first length, then use strpos)
        if ($message_is_command && $this->length == $message['entities'][0]['length'] &&
            strpos($this->command, $message['text'], $message['entities'][0]['offset']) !== false) {
            return true;
        }

        return false;
    }

    private function returnResponseTime() {
      return function($bot, $message) {
        $startTime = time();
        $messageId = $bot->sendMessage('PONG!').getMessageId();

        $responseTime = time() - $startTime;
        $bot->editMessage($messageId, "PONG!\n\n$responseTime s");
      };
    }
}
