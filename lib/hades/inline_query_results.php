<?php

class Inline_query_results {
    private $results;
    private $id_article;

    public function __construct() {
        $this->results = [];
        $this->id_article = 0;
    }

    public function newArticle($title, $message_text, $description,  $inline_keyboard = null, $parse_mode = 'HTML', $disable_web_preview = false) {
        array_push($this->results, [
            'type' => 'article',
            'id' => (string)$this->id_article,
            'title' => $title,
            'message_text' => $message_text,
            'description' => $description,
            'parse_mode' => $parse_mode,
            'reply_markup' => $inline_keyboard,
            'disable_web_page_preview' => $disable_web_preview
        ]);
        return $this->id_article++;
    }

    public function newArticleKeyboard(&$title, &$message_text, &$description, &$inline_keyboard, $parse_mode = 'HTML', $disable_web_preview = false) {
        array_push($this->results, [
            'type' => 'article',
            'id' => (string)$this->id_article,
            'title' => &$title,
            'message_text' => &$message_text,
            'description' => &$description,
            'reply_markup' => &$inline_keyboard,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_preview
        ]);
        return $this->id_article++;
    }

    public function &getResults() {
        $this->results = json_encode($this->results);
        return $this->results;
    }
}
