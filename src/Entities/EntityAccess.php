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

trait EntityAccess
{
    /** \brief Contains the array passed __construct */
    private $container;

    public function __construct($data)
    {
        $this->container = $data;
    }

    public function offsetSet($offset, $value)
    {
    }

    /** \brief Check that the given offset exists or not */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) { /* Log a warning */ }

    /**
     * \brief Get the given offset.
     * @param $offset The given offset.
     * @return Data relative to the offset.
     */
    public function offsetGet($offset)
    {
        // Get name of the method, the class should have. Like "getText"
        $method = Text::camelCase("get $offset");

        // If it exists, call it and return its return value
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        // If not return the data from the array after checking it is set
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}
