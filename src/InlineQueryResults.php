<?php

namespace DanySpin97\PhpBotFramework;

/**
 * \class InlineQueryResults InlineQueryResults
 * \brief Handle and store results before sending them to an answerInlineQuery api call.
 */
class InlineQueryResults {

    /**
     * \addtogroup InlineQueryResults InlineQueryResults
     * @{
     */

    /** \brief Array of the results stored */
    private $results;

    /** \brief Count the result so we can assign them an unique id */
    private $id_article;

    /**
     * \constructor Create an InlineQueryResult object. */
    public function __construct() {

        // Initialize the array to empty
        $this->results = [];

        $this->id_article = 0;

    }

    /**
     * \brief Add a result of type article article.
     * \details Add a result that will be show to the user.
     * @param $title Title of the result.
     * @param $message_text Text of the message to be sent.
     * @param $description <i>Optional</i>. Short description of the result
     * @param $reply_markup Inline keyboard object (Not JSON serialized, use getArray from InlineKeyboard class).
     * @param $parse_mode <i>Optional</i>. Formattation of the message.
     * @param $disable_web_preview <i>Optional</i>. Disables link previews for links in the sent message
     * @return Id the the article added
     */
    public function newArticle($title, $message_text, $description = '', array $reply_markup = null, $parse_mode = 'HTML', $disable_web_preview = false) {

        array_push($this->results, [
            'type' => 'article',
            'id' => (string)$this->id_article,
            'title' => $title,
            'message_text' => $message_text,
            'description' => $description,
            'parse_mode' => $parse_mode,
            'reply_markup' => $reply_markup,
            'disable_web_page_preview' => $disable_web_preview
        ]);

        return $this->id_article++;

    }

    public function newArticleKeyboard(&$title, &$message_text, &$description, array &$reply_markup, $parse_mode = 'HTML', $disable_web_preview = false) {

        array_push($this->results, [
            'type' => 'article',
            'id' => (string)$this->id_article,
            'title' => &$title,
            'message_text' => &$message_text,
            'description' => &$description,
            'reply_markup' => &$reply_markup,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_preview
        ]);

        return $this->id_article++;
    }

    /**
     * \brief Get all results created.
     * @return JSON-serialized string containing the results.
     */
    public function &getResults() {

        $this->results = json_encode($this->results);

        return $this->results;

    }
}

/** @} */
