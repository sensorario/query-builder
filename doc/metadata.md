# MetaData

This object aims to provide all necessary metadata component of a database.
It uses Doctrine's EntityManager and Objects\Criteria component.

## Usage

First of all criteria must be created.

    $criteria = \Sensorario\QueryBuilder\Objects\Criteria::fromArray([
        'class' => 'Foo\\Bar',
        'what' => [
        ],
    ]);

After, with entity manager and criteria it is possibile to build new MetaData
component.

    \Sensorario\QueryBuilder\Objects\MetaData::fromEntityManager(
        $this->manager,
        $criteria
    );

Now we have an object that can provide all information like:

 - all doctrine's metadata (`MetaData::getAllEntities() : array`)
 - main table (`MetaData::getTable() : string`)
 - criteria (`MetaData::getCriteria() : Criteria`)
 - doctrine's query builder (`MetaData::getQueryBuilder() : QueryBuilder`)
