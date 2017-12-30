# Query Builder

This is just an adapter of doctrine's query builder. Suppose to need all
entities inside a database that stores tasks organized by categories
structured. Here there are three joins to get all results.

```json
{
  "result": [
    {
      "task_title": "one task",
      "subcategory_name": "first sub cateogory",
      "category_name": "second category",
      "macrocategory_name": "second macro category"
    },
    {
      "task_title": "two task",
      "subcategory_name": "first sub cateogory",
      "category_name": "second category",
      "macrocategory_name": "second macro category"
    },
    {
      "task_title": "third task",
      "subcategory_name": "second dub cateogory",
      "category_name": "first category",
      "macrocategory_name": "first macro category"
    }
  ]
}
```

Following relations, we can synthetize fields and relations in one class:

> \App\Entity\Task::class

Select statement should be:

> select
>   task.title,
>   subcategory.name,
>   category.name,
>   macrocategory.name

We need to create criteria value.

```php
use Sensorario\QueryBuilder\Objects\Criteria;

$criteria = Criteria::fromArray([
    'class' => \App\Entity\Task::class,
    'what' => [
        'title',
        'subcategory.name',
        'subcategory.category.name',
        'subcategory.category.macrocategory.name',
    ],
]);
```

Then, build metadata with criteria.

```php
use Sensorario\QueryBuilder\Objects\MetaData;

$metadata = MetaData::fromEntityManager(
    $this->manager,
    $criteria
);
```

Finally, passing metadata to the builder, we'll obtain the expeted result.

```php
use Sensorario\QueryBuilder\CustomQueryBuilder;

$builder = new \Sensorario\QueryBuilder\CustomQueryBuilder();
$result = $builder($metadata);
```

… expected result like:

```json
{
  "result": [
    {
      "task_title": "one task",
      "subcategory_name": "first sub cateogory",
      "category_name": "second category",
      "macrocategory_name": "second macro category"
    }
  ]
}
```
