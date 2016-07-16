<?php

class Inline_query_results {
    private $results;
    private $id_article;

    public function __construct() {
        $results = [];
        $id_article = 0;
    }

    public function newArticle($title, $message_text, $description, $parse_mode = 'HTML', $disable_web_page_preview = false) {
        array_push($results, [
            'type' => 'article',
            'id' => (string)$id_article,
            'title' => $title,
            'message_text' => $message_text,
            'description' => $description,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => $disable_web_page_preview
        ]);
        $id_article++;
    }

    public function newArticleKeyboard($title, $message_text, $description, $inline_keyboard, $parse_mode = 'HTML', $disable_web_page_preview = false) {
        array_push($results, [
            'type' => 'article',
            'id' => (string)$id_article,
            'title' => $title,
            'message_text' => $message_text,
            'description' => $description,
            'reply_markup' => $inline_keyboard,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => $disable_web_page_preview
        ]);
        $id_article++;
    }

    public function newArticleKeyboardRef(&$title, &$message_text, &$description, &$inline_keyboard, $parse_mode = 'HTML', $disable_web_page_preview = false) {
        array_push($results, [
            'type' => 'article',
            'id' => (string)$id_article,
            'title' => &$title,
            'message_text' => &$message_text,
            'description' => &$description,
            'reply_markup' => &$inline_keyboard,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => $disable_web_page_preview
        ]);
        $id_article++;
    }

    public function &getResults() {
        $results = json_encode($results);
        return $results;
    }
}
