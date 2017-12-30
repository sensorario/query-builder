# Query Builder

This is just an adapter of doctrine's query builder. Suppose to need all
entities inside a database that stores tasks organized by categories
structured. Here there are three entities related together.

```xml
<entity repository-class="App\Repository\TaskRepository" name="App\Entity\Task" table="task">
  <id name="id" type="integer" column="id">
    <generator strategy="IDENTITY"/>
  </id>
  <field name="title" type="string" column="title" length="255" precision="0" scale="0" nullable="false"/>
  <many-to-one field="subcategory" target-entity="App\Entity\SubCategory" inversed-by="tasks">
      <join-column name="sub_category_id" referenced-column-name="id" />
  </many-to-one>
</entity>
```

```xml
<entity name="App\Entity\SubCategory" table="subcategory">
  <id name="id" type="integer" column="id">
    <generator strategy="IDENTITY"/>
  </id>
  <field name="name" type="string" column="name" length="255" precision="0" scale="0" nullable="false"/>
  <one-to-many field="tasks" target-entity="App\Entity\Task" mapped-by="subcategories" />
  <many-to-one field="category" target-entity="App\Entity\Category" inversed-by="categories">
      <join-column name="category_id" referenced-column-name="id" />
  </many-to-one>
</entity>
```

```xml
<entity name="App\Entity\MacroCategory" table="macrocategory">
  <id name="id" type="integer" column="id">
    <generator strategy="IDENTITY"/>
  </id>
  <field name="name" type="string" column="name" length="255" precision="0" scale="0" nullable="false"/>
  <one-to-many field="category" target-entity="App\Entity\Category" mapped-by="macrocategory" />
</entity>
```

```xml
<entity name="App\Entity\Category" table="category">
  <id name="id" type="integer" column="id">
    <generator strategy="IDENTITY"/>
  </id>
  <field name="name" type="string" column="name" length="255" precision="0" scale="0" nullable="false"/>
  <one-to-many field="subcategory" target-entity="App\Entity\SubCategory" mapped-by="category" />
  <many-to-one field="macrocategory" target-entity="App\Entity\MacroCategory" inversed-by="macrocategories">
      <join-column name="macro_category_id" referenced-column-name="id" />
  </many-to-one>
</entity>
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

A result like this:

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

Auto generated SQL statement should be the following:

> 
> SELECT
>   task.title task_title,
>   subcategory.name subcategory_name,
>   subcategory.id subcategory_id,
>   category.id category_id,
>   category.name category_name,
>   macrocategory.name macrocategory_name,
>   macrocategory.id macrocategory_id
>
> FROM App\Entity\Task task
>
> INNER JOIN App\Entity\SubCategory subcategory
>   WITH subcategory.id = task.subcategory
>
> INNER JOIN App\Entity\Category category
>   WITH category.id = subcategory.category
>
> INNER JOIN App\Entity\MacroCategory macrocategory
>   WITH macrocategory.id = category.macrocategory
>
