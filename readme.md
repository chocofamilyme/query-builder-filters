# Phalcon - Query Builder Filters

Библиотека для динамической фильтрации данных через Query Builder фреймворка Phalcon

## Установка

С помощью Composer

``` bash
$ composer require chocofamilyme/phalcon-query-builder-filters
```

## Использование

1. Нужно создать класс фильтра, который будет наследоваться у абстракного класса `Chocofamily\QueryBuilderFilters\BaseFilter`
<br>
Затем вам нужно определить логику фильтра, следуя этим правилам: <br>

-  Строка запроса без соответствующего метода фильтра игнорируется
-  Пустые строки игнорируются
-  Все ключи по умолчанию переводятся на camelCase
-  Значение каждого ключа запроса инджектится в соответствующий метод фильтра.
-  Вы можете получить доступ к экземпляру  Builder-a (`Phalcon\Mvc\Model\Query\Builder`) , используя ```$this-> builder```

**Пример**: <br>

Для фильтрации данных по следующему URL-запросу:

``` http://yourdomain.com/api/users?gender=male&working=1  ```

<br>

Нам нужно написать следующие методы:

```php

namespace RestAPI\Models\Filters;

use Chocofamily\QueryBuilderFilters\BaseFilter;

class UserFilter extends BaseFilter
{
    public function gender($value)
    {
        return $this->builder->andWhere('RestAPI\Models\User.gender = :gender:', [
                    'gender' =>  $value
                ]);
    }

    public function working($value)
    {
        return $this->builder->andWhere('RestAPI\Models\Profile.is_working = :isWorking:', [
                            'isWorking' =>  $value
                        ]);
    }
}
```

2 . В модели Phalcon, нужно добавить имплементацию интерфейса `Chocofamily\QueryBuilderFilters\Contracts\HasFilters` и реализовать метод `getFilterClass`

```php

namespace RestAPI\Models;

use Chocofamily\QueryBuilderFilters\Contracts\HasFilters;
use RestAPI\Models\Filters\UserFilter; 
use Phalcon\Mvc\Model;

class User extends Model implements HasFilters
{

    /**
     * @return string
     */
    public function getFilterClass(): string
    {
        return UserFilter::class;
    }

}
```

3 . Теперь мы можем применить использовать фильтрацию:

```php

public function getFilteredUsers()
{
    $filterHandler = new FilterHandler();
    
    $filters = [
        'is_working'        =>  true,
        'gender'            =>  1,
        'not_exist_column'  =>  'test' // так как такого метода у нас нету, этот фильтр проигнорируется
    ];
	
    $builder = $this->modelsManager->createBuilder()
                            ->columns(
                                [
                                    'RestAPI\Models\User.id',
                                    'RestAPI\Models\User.name',
                                    'RestAPI\Models\User.gender',
                                    'RestAPI\Models\Profile.is_working',
                                    'RestAPI\Models\Profile.is_active'
                                ]
                            )
                            ->from('RestAPI\Models\User')
                            ->innerJoin('RestAPI\Models\Profile', 'RestAPI\Models\User.id = RestAPI\Models\Profile.user_id')
                            ->orderBy('is_active DESC');
							
    $filteredBuilder = $filterHandler->handle($builder, $filters);
    return $filteredBuilder->getQuery()->execute();
}
```

4 . Также для удобства можно добавить обработчик фильтра в DI

```php
use Chocofamily\QueryBuilderFilters\FilterHandler;

$di = \Phalcon\Di::getDefault();
    $di->set('query-builder-filter', function () use ($di) {
      return new FilterHandler();
    });  
```

## TODO

* Добавить alias для запросов
* Покрыть код юнит тестами
* Дописать документацию



