<?php

namespace Sensorario\QueryBuilder\Exceptions;

use Exception;

final class MissingClassNameException extends Exception
{
    protected $message = 'Criteria object should contains class field';
}
