<?php

namespace Sensorario\QueryBuilder\Exceptions;

use Exception;

/**
 * @since Class available since Release 1.0.1
 */
final class MissingClassNameException extends Exception
{
    protected $message = 'Criteria object should contains class field';
}
