<?php

namespace PhpBotFramework\Logging;

use PhpBotFramework\Core\BasicBot;
use \Monolog\Handler\AbstractProcessingHandler;

/**
 * Sends notifications through Slack's Slackbot
 *
 * @author Haralan Dobrev <hkdobrev@gmail.com>
 * @see    https://slack.com/apps/A0F81R8ET-slackbot
 */
class TelegramHandler extends AbstractProcessingHandler
{
    /**
     * \brief The bot owning the logger
     */
    private $bot;

    private $channel;
    /**
     * @param string $slackTeam Slack team slug
     * @param string $token     Slackbot token
     * @param string $channel   Slack channel (encoded ID or name)
     * @param int    $level     The minimum logging level at which this handler will be triggered
     * @param bool   $bubble    Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(BasicBot $bot, $level = Logger::CRITICAL, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->bot = $bot;
    }
    /**
     * \brief Send the message to telegram chat
     * @param array $record
     */
    protected function write(array $record)
    {
        $chat_id = $this->bot->getChatLog();
        // Check chat_id is valid
        if ($chat_id !== "") {
            $this->bot->withChatId($chat_id, 'sendMessage', $record['message']);
        }
    }
}
