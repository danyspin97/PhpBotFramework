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

namespace PhpBotFramework\Entities;

use PhpBotFramework\Exceptions\BotException;

/**
 * \addtogroup Entities Entities
 * @{
 */

/**
 * \class InlineQueryResults InlineQueryResults
 * \brief Handle and store results before sending them to an <code>answerInlineQuery</code> API call.
 */
class InlineQueryResults
{
    /**
     * \addtogroup InlineQueryResults InlineQueryResults
     * \brief Handles and stores results before sending them to an answerInlineQuery API call.
     * \details In order to answer <i>inline queries</i>: create an object of type
     * PhpBotFramework\Entities\InlineQueryResults and add the wanted results
     * using InlineQueryResults::addResult() method or
     * type-based result method (InlineQueryResults::newArticle()).
     *
     *     use PhpBotFramework\Entities\InlineQueryResults;
     *     use PhpBotFramework\Entities\InlineQuery;
     *     ~
     *     ~
     *     ~
     *         processInlineQuery(InlineQuery $inline_query) {
     *             $handler = new InlineQueryResults();
     *             $handler->newArticle('First article', 'This is the first result');
     *
     *             $this->answerInlineQuery($handler->get());
     *         }
     *
     * @{
     */

    /** \brief Array of the results stored. */
    private $results;

    /** \brief Counts the result so we can assign them an unique id. */
    private $id_result;

    /** \brief Accepted types for results. */
    private $accepted_type = [
        'audio',
        'article',
        'photo',
        'gif',
        'mpeg4_gif',
        'video',
        'voice',
        'document',
        'location',
        'venue',
        'contact',
        'game'
    ];

    /**
     * \constructor Create an InlineQueryResult object. */
    public function __construct()
    {
        // Initialize the array to empty
        $this->results = [];
        $this->id_result = 0;
    }

    /**
     * \brief Add a result passing an array containing data.
     * \details Create a result of one of these types:
     * - InlineQueryResultCachedAudio
     * - InlineQueryResultCachedDocument
     * - InlineQueryResultCachedGif
     * - InlineQueryResultCachedMpeg4Gif
     * - InlineQueryResultCachedPhoto
     * - InlineQueryResultCachedSticker
     * - InlineQueryResultCachedVideo
     * - InlineQueryResultCachedVoice
     * - InlineQueryResultArticle
     * - InlineQueryResultAudio
     * - InlineQueryResultContact
     * - InlineQueryResultGame
     * - InlineQueryResultDocument
     * - InlineQueryResultGif
     * - InlineQueryResultLocation
     * - InlineQueryResultMpeg4Gif
     * - InlineQueryResultPhoto
     * - InlineQueryResultVenue
     * - InlineQueryResultVideo
     * - InlineQueryResultVoice
     *
     * To add a result, create an array containing data as showed on API Reference,
     *'id' parameter will be automatically genereted so there is no need to add it.
     *
     * Example of adding an article result:
     *
     *     $data = [
     *         'type' => 'result',
     *         'title' => 'Example title',
     *         'message_text' => 'Text of the message'
     *     ];
     *
     *     $handler->addResult($data);
     *
     * @param array $result Array containing data result to add.
     * @return int Id of the result added.
     */
    public function addResult(array $result) : int
    {
        if (array_key_exists('type', $result) && ! in_array($result['type'], $this->accepted_type)) {
            throw new BotException("Result has wrong or no type at all. Check that the result has a value of key 'type' that correspond to a type in the API Reference");
        }

        // Set the id of the result to add
        $result['id'] = (string)$this->id_result;

        $this->results[] = $result;
        return $this->id_result++;
    }

    public function addResults(array $results) : int
    {
        foreach ($results as $result) {
            $this->addResult($result);
        }
        return $this->id_result;
    }

    /**
     * \brief Add a result of type Article.
     * \details Add a result that will be show to the user.
     * @param string $title Title of the result.
     * @param string $message_text Text of the message to be sent.
     * @param string $description <i>Optional</i>. Short description of the result
     * @param array $reply_markup Inline keyboard object.
     * Not JSON serialized, use getArray from InlineKeyboard class.
     * @param string $parse_mode <i>Optional</i>. Formattation style for the message.
     * @param string $disable_web_preview <i>Optional</i>. Disables link previews for
     * links in the sent message.
     * @return int Id the the article added.
     */
    public function newArticle(
        string $title,
        string $message_text,
        string $description = '',
        array $reply_markup = null,
        $parse_mode = 'HTML',
        $disable_web_preview = false
    ) : int {

        array_push($this->results, [
            'type' => 'article',
            'id' => (string)$this->id_result,
            'title' => $title,
            'message_text' => $message_text,
            'description' => $description,
            'parse_mode' => $parse_mode,
            'reply_markup' => $reply_markup,
            'disable_web_page_preview' => $disable_web_preview
        ]);

        if ( is_null($reply_markup) ) {
            unset( $this->results[ $this->id_result ]['reply_markup'] );
        }

        return $this->id_result++;
    }

    /**
     * \brief Get all results created.
     * @return string JSON string containing the results.
     */
    public function get()
    {
        $encoded_results = json_encode($this->results);

        // Clear results by resetting ID counter and results' container
        $this->results = [];
        $this->id_result = 0;

        return $encoded_results;
    }

    /** @} */

    /** @} */
}
