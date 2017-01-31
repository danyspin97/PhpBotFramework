<?php

namespace PhpBotFramework\Test;

use PhpBotFramework\Entities\Message;

/* \class FakeUpdate
 * \brief Contains abstrataction methods for processing fake updates.
 */
trait FakeUpdate {

    /**
     * \brief Process fake update for tests.
     * @param array $update Fake update given by tests.
     * @return int Id of the update processed.
     */
    public function processFakeUpdate($update) {

        $this->processUpdate($update);

    }

}
