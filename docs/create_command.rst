==============
Create Command
==============

Command rules are simply classes that inherit ``BasicCommand``.

Let's create a simple command that get triggered when messages start with the wanted string:

.. code:: php

   class MessageCommand extends PhpBotFramework\Commands\BasicCommand {

       // Type of the update which can trigger this command
       public static $type = 'message';

       // Type of the object to return
       // It must be an Entity class
       // The namespace is required
       public static $object_class = 'PhpBotFramework\Entities\Message';

       // Priority of this rule over other rules
       // for the same update
       // Rules with low value have high priority
       public static $priority = 1;

       // The constructor of the object
       // In this example we pass a string, $command
       // And a callable function, $script
       public function __construct(string $command, callable $script) {
           $this->command = $command;
           $this->script = $script;
       }

       // This function is called on each update of the corrisponding type (defined by $type)
       // $message (it can be called how you want) contains the update in the form of an array
       // Return true if the update triggers this command
       public function checkCommand(array $message) : bool {
           // Check if the text contains the wanted string
           if (strpos($this->command, $message["text"]) !== 0) {
               return true;
           }
           // We didn't find it
           return false;
       }
   }

   Now just tell the bot what to do when a message contains a bot command (like /start) using our newly created MessageCommand:

.. code:: php
   // Command triggered when the message start with /ping
   $command = new MessageCommand("/ping",
            function ($bot, $message) {
               $bot->sendMessage("/pong");
            }
    );
