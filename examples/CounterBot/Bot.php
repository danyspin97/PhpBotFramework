<?php

require_once './../../vendor/autoload.php';

class CounterBot extends PhpBotFramework\Bot {
  /**
   * Find the user by her user ID.
   *
   * @param $userId The user ID.
   * @return object The user found.
   */
  public function findUser ($userId) {
    $query = $this->database->pdo->prepare('SELECT username, points FROM users WHERE userId = :userId');
    $query->bindParam(':userId', $userId);

    $query->execute();
    return (object) $query->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Checks if the user is already registered.
   * If yes, checks if her username is changed and update it.
   * Otherwise, register the new user.
   *
   * @param $db The reference to the PDO object.
   * @param $message The incoming message.
   *
   * @return void
   */
  public function storeUserIfMissing ($message) {
    $userId = $message['from']['id'];
    $username = strtolower($message['from']['username']);

    $query = $this->database->pdo->prepare('SELECT username FROM users WHERE userId = :userId');
    $query->bindParam(':userId', $userId);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_ASSOC) ?? false;

    // If the user is not registered.
    if (!$user) {
      $query = $this->database->pdo->prepare('INSERT INTO users (userId, username, points) VALUES(:userId, :username, 0)');
      $query->bindParam(':userId', $userId);
      $query->bindParam(':username', $username);

      $query->execute();
      return;
    }

    // If the username changed.
    if ($username !== $user['username']) {
      $query = $this->database->pdo->prepare('UPDATE users SET username = :username WHERE userId = :userId');
      $query->bindParam(':userId', $userId);
      $query->bindParam(':username', $username);

      $query->execute();
    }
  }
}

$bot = new CounterBot(getenv('BOT_TOKEN'));

$bot->database->connect([
  'dbname' => getenv('DBNAME'),
  'username' => getenv('DBUSER'),
  'password' => getenv('DBPASSWORD'),
  'host' => getenv('DBHOST'),
]);


$bot->addMessageCommand('start', function ($bot, $message) {
  $bot->sendMessage('Type `/points` to see your points.');
});

$bot->addMessageCommand('points', function ($bot, $message) {
  $user = $bot->findUser($message['from']['id']);
  $bot->sendMessage("@$user->username has $user->points points");
});

$bot->answerUpdate['message'] = function ($bot, $message) {
  $bot->storeUserIfMissing($message);
  $REGEXP = '/^(?<username>\@\w+)(?<operation>(\+\+|--))$/';

  if (preg_match_all($REGEXP, $message['text'], $matches)) {
    $username = $matches['username'][0];
    $verb = $matches['operation'][0] === '++' ? 'got' : 'lost';
    $bot->sendMessage("$username $verb 1 point");

    $operation = $matches['operation'][0][0];
    $query = $bot->database->pdo->prepare("UPDATE users SET points = points $operation 1 WHERE username = :username");
    $query->bindParam(':username', strtolower(substr($username, 1))); // avoid the '@'

    $query->execute();
  }
};

$bot->run(GETUPDATES);
