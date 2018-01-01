# Example

The following is an example based on four entities. A Task, its sub category,
the related category and finally the macro category.

```
Task
└── Subcategory
    └── Category
        └── Macrocategory
```

Between entities there are a relation.

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

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category")
     */
    private $category;
}
```

## Category entity

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Macrocategory")
     */
    private $macrocategory;
}
```

## Macrocategory entity

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MacroategoryRepository")
 */
class Macrocategory
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
subcategory name, category name until macrocategory name.

```php
$criteria = \Sensorario\QueryBuilder\Objects\Criteria::fromArray([
    'class' => \App\Entity\Task::class,
    'what' => [
        'name',
        'subcategory.name',
        'subcategory.category.name',
        'subcategory.category.macrocategory.name',
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
  s1_.name AS name_1,
  c2_.name AS name_2,
  m3_.name AS name_3
FROM
  task t0_
INNER JOIN
  subcategory s1_
ON
  (s1_.id = t0_.subcategory_id)
INNER JOIN
  category c2_
ON
  (c2_.id = s1_.category_id)
INNER JOIN
  macrocategory m3_
ON
  (m3_.id = c2_.macrocategory_id)
```
