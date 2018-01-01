# Example

The following is an example based on two entities. A Task and a sub category.
Between two entities there are a relation. Precisely Task have a ManyToOne
relation to Subcategory.

## Task entity

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Subcategory")
     */
    private $subcategory;
}
```

## Subcategory entity

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubCategoryRepository")
 */
class Subcategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;
}
```

## Library usage

And the following is an example to select all tasks with the name of their
subcategory. As you can see, subcategory is the name of relation. The key point
is that with `relation.fieldName` it is possibile to build full SQL. It is also
possibile to build complex relations map like `relation.realtion.field` and so
on.

```php
$criteria = \Sensorario\QueryBuilder\Objects\Criteria::fromArray([
    'class' => \App\Entity\Task::class,
    'what' => [
        'name',
        'subcategory.name',
    ],
]);

$metadata = \Sensorario\QueryBuilder\Objects\MetaData::fromEntityManager(
    $this->get('doctrine.orm.entity_manager'),
    $criteria
);

$builder = new \Sensorario\QueryBuilder\CustomQueryBuilder();

$builder = $builder($metadata);

$result = $builder->getResult();
```

## Generated SQL

The following is a formatted sql query.

```sql
SELECT
  t0_.name AS name_0,
  s1_.name AS name_1
FROM
  task t0_
INNER JOIN
  subcategory s1_
ON
  (s1_.id = t0_.subcategory_id)
```
