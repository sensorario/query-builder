<?php

namespace Sensorario\QueryBuilder\Exceptions;

use Exception;

/**
 * @since Class available since Release 1.0.3
 */
final class MissingTableException extends Exception
{
    protected $message = 'Table is missed in configuration';
}
