CREATE TYPE language AS ENUM('en', 'it', 'fr', 'de', 'ru', 'fa', 'hi', 'es');

CREATE TABLE TelegramUser (
  chat_id int,
  language language DEFAULT 'en',

  PRIMARY KEY (chat_id)
);
