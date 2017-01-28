<?php

namespace PhpBotFramework\Entities;

/**
 * \addtogroup Core
 * @{
 */
trait EntityAccess {

    /** @} */

    /** \brief Contains the array passed __construct */
    private $container;

    public function __construct($data) {

        $this->container = $data;

    }

    public function offsetSet($offset, $value) {

    }

    public function offsetExists($offset) {

        // Is it set?
        return isset($this->container[$offset]);

    }

    public function offsetUnset($offset) {

        // Log a warning

    }

    public function offsetGet($offset) {

        // Get name of the method, the class should have. Like "getText"
        $method = Text::camelCase("get $offset");

        // If it exists, call it and return its return value
        if (method_exists($this, $method)) return $this->{$method}();

        // If not return the data from the array after checking it is set
        return isset($this->container[$offset]) ? $this->container[$offset] : null;

    }

}
