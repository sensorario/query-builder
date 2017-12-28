# Query Builder

This is just an adapter of doctrine's query builder.

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LuckyController extends Controller
{
    /**
     * @Route("/hello")
     */
    public function hello()
    {
        $manager = $this->container->get('doctrine.orm.entity_manager');

        $builder = (new \Sensorario\QueryBuilder\CustomQueryBuilder([
            'class' => \App\Entity\Task::class,
            'what' => [
                'title',
                'subcategory.name',
                'subcategory.id',
                'subcategory.category.name',
                'subcategory.category.macrocategory.name',
            ],
        ]))($manager);

        return new JsonResponse([
            'dql' => $builder->getDql(),
            'result' => $builder->getResult(),
        ]);
    }
}
```

This configuration will generate this DQL:

> SELECT task.title task_title, subcategory.name subcategory_name, subcategory.id subcategory_id, category.name category_name, macrocategory.name macrocategory_name FROM App\\Entity\\Task task INNER JOIN App\\Entity\\SubCategory subcategory WITH subcategory.id = task.subcategory INNER JOIN App\\Entity\\Category category WITH category.id = subcategory.category INNER JOIN App\\Entity\\MacroCategory macrocategory WITH macrocategory.id = category.macrocategory"

and with some data you can obtain ... :

```json
{
  "dql": "SELECT task.title task_title, subcategory.name subcategory_name, subcategory.id subcategory_id, category.name category_name, macrocategory.name macrocategory_name FROM App\\Entity\\Task task INNER JOIN App\\Entity\\SubCategory subcategory WITH subcategory.id = task.subcategory INNER JOIN App\\Entity\\Category category WITH category.id = subcategory.category INNER JOIN App\\Entity\\MacroCategory macrocategory WITH macrocategory.id = category.macrocategory",
  "result": [
    {
      "task_title": "primo task",
      "subcategory_name": "scat",
      "subcategory_id": 1,
      "category_name": "cat",
      "macrocategory_name": "second macro category"
    },
    {
      "task_title": "secondo task",
      "subcategory_name": "scat",
      "subcategory_id": 1,
      "category_name": "cat",
      "macrocategory_name": "second macro category"
    },
    {
      "task_title": "quarto task",
      "subcategory_name": "scat",
      "subcategory_id": 1,
      "category_name": "cat",
      "macrocategory_name": "second macro category"
    },
    {
      "task_title": "terzo task",
      "subcategory_name": "second dub cateogory",
      "subcategory_id": 2,
      "category_name": "seconda categoria",
      "macrocategory_name": "second macro category"
    }
  ]
}
```
