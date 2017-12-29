<?php

namespace Sensorario\QueryBuilder\Exceptions;

use Exception;

final class MissingWhatParamException extends Exception
{
    protected $message = 'Criteria object should contains what field';
}
