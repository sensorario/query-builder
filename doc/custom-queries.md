# Custom queries

Because of this is substantially a doctrine's wrapper, always remember to
create a service that collaborate with doctrine's EntityManager.

```
class GetSomeValues
{
    private $manager;

    public function __construct(\Doctrine\ORM\EntityManager $manager)
    {
        $this->manager = $manager;
    }
}
```

Second, add an __invoke method like the following. Remember to properly
configure Criteria class with entity class name and fields.

```
public function __invoke()
{
    $criteria = \Sensorario\QueryBuilder\Objects\Criteria::fromArray([
        'class' => 'Foo\\Bar',
        'what' => [
          'field',
          'relation.field',
        ],
    ]);

    $metadata = \Sensorario\QueryBuilder\Objects\MetaData::fromEntityManager(
        $this->manager,
        $criteria
    );

    return (new \Sensorario\QueryBuilder\CustomQueryBuilder())($metadata);
}
```
Here a more detailed example:

```
public function __invoke()
{
    $criteria = \Sensorario\QueryBuilder\Objects\Criteria::fromArray([
        'class' => \App\Entity\Task::class,
        'what' => [
            'title',
            'subcategory.name',
            'subcategory.id',
            'subcategory.category.id',
            'subcategory.category.name',
            'subcategory.category.macrocategory.name',
            'subcategory.category.macrocategory.id',
        ],
    ]);

    $metadata = \Sensorario\QueryBuilder\Objects\MetaData::fromEntityManager(
        $this->manager,
        $criteria
    );

    return (new \Sensorario\QueryBuilder\CustomQueryBuilder())($metadata);
}
```

Finally, to get all tasks, subcategory name, category name and so on to
macrocategory name, just call:

```
$query = new GetSomeValues();
```

This way you'll obtain a builder. Feel free to get just the Dql or full
list of results.

```
$query = new GetSomeValues();
$dql = $query->getDql();
$res = $query->getResult();
```
