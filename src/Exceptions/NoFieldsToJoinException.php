<?php

namespace Sensorario\QueryBuilder\Exceptions;

use Exception;

/**
 * @since Class available since Release 1.0.13
 */
final class NoFieldsToJoinException extends Exception
{
    protected $message = 'Join require that at least one fields cames from another entity';
}
