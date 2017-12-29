<?php

namespace Sensorario\QueryBuilder\Objects;

use Sensorario\QueryBuilder\Exceptions;

final class Criteria
{
    const CLASS_NAME = 'class';

    const FIELDS = 'what';

    private $params;

    private function __construct(array $params)
    {
        if (!isset($params[self::CLASS_NAME])) {
            throw new Exceptions\MissingClassNameException();
        }

        if (!isset($params[self::FIELDS])) {
            throw new Exceptions\MissingWhatParamException();
        }

        $this->params = $params;
    }

    public static function fromArray(array $params) : Criteria
    {
        return new self($params);
    }

    public function getClassName() : string
    {
        return $this->params[self::CLASS_NAME];
    }

    public function getFields() : array
    {
        return $this->params[self::FIELDS];
    }
}
