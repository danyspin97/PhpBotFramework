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

namespace PhpBotFramework\Test;

use PhpBotFramework\Entities\Message;

/* \class FakeUpdate
 * \brief Contains abstrataction methods for processing fake updates.
 */
trait FakeUpdate
{

    /**
     * \brief Process fake update for tests.
     * @param array $update Fake update given by tests.
     * @return int Id of the update processed.
     */
    public function processFakeUpdate($update)
    {

        $this->processUpdate($update);
    }
}
