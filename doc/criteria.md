# Criteria

This object aims to wrap array configuration for query builder.

## Usage

It wrap entire configuration in a single object and expose two methods to
provide class name and fields. Class name must contains the entity class that
should be queried. Fields are all fields that will be placed in select
statement.

    $criteria = Criteria::fromArray([
        'class' => 'Foo\\Bar',
        'what' => [
            'fooo',
        ],
    ]);

    $criteria->getClassName()); // 'Foo\\Bar'
    $criteria->getFields());    // ['fooo']
