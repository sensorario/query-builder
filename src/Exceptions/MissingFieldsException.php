<?php

namespace Sensorario\QueryBuilder\Exceptions;

use Exception;

/**
 * @since Class available since Release 1.0.13
 */
final class MissingFieldsException extends Exception
{
    protected $message = 'Extractor needs fields to build select statement';
}
