# Модуль для работы с смарт-процессами Битрикс24

Данный модуль позволяет "удобнее" работать с элементами CRM и смарт-процессами, инкапсулируя в себя такие действия как получение фабрики, получение связанных элементов, взаимодействия с прочими объектами Битрикс24 и многое другое. Модуль уменьшает количество кода необходимое для работы с элементами и позволяет лаконичнее организовать бизнес-логику вашего приложения.
## 🗂 Меню
- [Создание объекта](#user-content-создание-объекта)
- [Получение родительских и дочерних элементов](#user-content-получение-родительских-и-дочерних-элементов)
- [Установка родительских и дочерних элементов](#user-content-установка-родительских-и-дочерних-элементов)
- [Получение списка элементов](#user-content-получение-списка-элементов)
- [Получение и изменение пользовательских полей](#user-content-получение-и-изменение-пользовательских-полей)
- [API](#user-content--api)
## ⚙️ Установка
Для установки загрузите модуль в директорию /local/modules/ и установите используя штатный административный интерфейс Битрикс.
## 🔧 Настройка
После установки доступны классы для работы с штатными сущностями CRM:
- Лид - \ChelBit\Smart\CRM\Lead
- Сделка - \ChelBit\Smart\CRM\Deal
- Контакт - \ChelBit\Smart\CRM\Contact
- Компания - \ChelBit\Smart\CRM\Company
- Счет - \ChelBit\Smart\CRM\Invoice

Так же есть пример класса для март процесса `\ChelBit\Smart\CRM\Example`
Вам необходимо создать класс(-ы) для своего(-их) смарт-процессов. Необходимо наследоваться от класса ChelBit\Smart\Core\Base\Smart и определить реализацию абстрактных методов.
### Абстрактные методы класса `Smart`

| Метод                   | Описание                          | Параметры                              | Возвращаемое значение |
|-------------------------|-----------------------------------|----------------------------------------|-----------------------|
| `getEntityTypeName()`          | Метод должен вернуть имя смарт-процессов указанное в столбце NAME таблицы b_crm_dynamic_type       |     | `string`                |
| `getEntityTypeTitle()`          | Метод должен вернуть название отображаемое в публичной части. Используется для формирования описания ошибок.     |                | `string`                |


### Пример использования
```php
namespace ChelBit\Smart\CRM;

use ChelBit\Smart\Core\Base\Smart;

/**
 * Класс - пример описывающий смарт процесс
 */
class Example extends Smart
{

    static function getEntityTypeName(): string
    {
        return "EXAMPLE";
    }

    static function getEntityTypeTitle(): string
    {
        return "Название";
    }
}
```
## 📂 Структура проекта
```text
lib/
├── Core/                  # Ядро модуля
│   └── Base/              # Базовые классы для сущностей
|       └── Smart.php      # Базовый класс для сущностей CRM
└── CRM/                   # Классы описывающие CRM сущности 
    ├── Company.php        # Класс описывающий бизнес логику компаний
    ├── Contact.php        # Класс описывающий бизнес логику контактов
    ├── Deal.php           # Класс описывающий бизнес логику сделок
    ├── Example.php        # Пример добавленного класса смарт-процесса
    ├── Invoice.php        # Класс описывающий бизнес логику счетов
    └── Lead.php           # Класс описывающий бизнес логику лидов
```
## 🚀 Использование
### Создание объекта
Методы для получения фабрик уже инкапсулированы в базовый класс. Для создания объекта класса обертки можно использовать статический метод.
> ⚠️ **Внимание!**  
> Не забудьте предварительно подключить модуль используя `\Bitrix\Main\Loader::includeModule("chelbit.smart");`
```php
/**
 * Получаем объект элемента смарт-процесса "Пример" с ID=1
 */
$example = \ChelBit\Smart\CRM\Example::getInstanceById(1);
```
Если у вас уже есть объект класса `\Bitrix\Crm\Item` вы можете создать объект-обертку через конструктор.
```php
$item = \ChelBit\Smart\CRM\Example::getFactory()->getItem(1);
$example = new \ChelBit\Smart\CRM\Example($item);
```
Это может быть полезно например в обработчиках событий, когда уже есть объект штатного класса CRM.
### Получение родительских и дочерних элементов
Например у нас есть смарт-процесс "Отпуск" `\Chelbit\Smart\CRM\Vacation` и этого смарт-процесса в настройках связи включили привязку к сделке. Таким образом мы имеем в карточке смарт-процесса "Отпуск" поле сделка. Это означает, что сделка является родителем отпуска.
```php
$vacation = \ChelBit\Smart\CRM\Vacation::getInstanceById(1);
$parentDeal = $vacation->getParent(\ChelBit\Smart\CRM\Vacation::class);
```
Для примеры выше можно сказать, что отпуск является дочерним элементом для сделки. И предположим, что к одной сделке привязано много элементов отпусков. Мы можем их все получить следующим образом.
```php
$children = \ChelBit\Smart\CRM\Deal::getInstanceById(1)->getChildren(\ChelBit\Smart\CRM\Vacation::class);
foreach ($children as $child){
    \Bitrix\Main\Diag\Debug::dump($child->getItem()->getTitle()); //Выведет название элемента смарт-процесса
}
```
Предположим, что есть смарт-процесс "Оборудование" `\ChelBit\Smart\CRM\Equipment` В оборудование указывается "Сервер" `\ChelBit\Smart\CRM\Server` В сервере указывается где он находится "Локация" `\ChelBit\Smart\CRM\Location` Вот так можно получить локацию конкретного оборудования.
```php
$location = \ChelBit\Smart\CRM\Equipment::getInstanceById(1)->getParent(\ChelBit\Smart\CRM\Server::class)->getParent(\ChelBit\Smart\CRM\Location::class);
//При использование USE это будет намного короче и красивее)) Здесь для понимания полный namespace
```
> ⚠️ **Внимание!**  
> Методы получения родителей и детей проверяют настройки связей и выбросят исключение если запрашиваемый тип связи не возможен!

> ⚠️ **Внимание!**  
> Методы получения родителей и детей кэшируют полученные данные в свойстве объекта. Можно не записыват результат в переменную, а вызывать метод по месту.

> 💡 **Подсказка!**  
> Родитель всегда один, детей может быть много )))

### Установка родительских и дочерних элементов
Установить элемент в качестве родительского.
```php
$example = Example::getInstanceById(1);
$example->setParent(Vacation::getInstanceById(15));
```
Удалить связь с родительским элементом.
```php
$example = Example::getInstanceById(1);
$example->unsetParent(Vacation::class);
```
Добавить элемент в качестве дочернего.
```php
$example = Example::getInstanceById(1);
$example->addChild(Vacation::getInstanceById(1));
$example->addChild(Vacation::getInstanceById(2));
$example->addChild(Vacation::getInstanceById(3));
```
Удалить элемент из дочерних.
```php
$example = Example::getInstanceById(1);
$example->deleteChild(Vacation::getInstanceById(2));
```
Очистить все дочерние элементы.
```php
$example = Example::getInstanceById(1);
$example->usetChildren(Vacation::class);
```

### Получение списка элементов
Можно использовать метод для получения элементов аналогичный штатному. Под капотом уже реализовано получение фабрики. 
Передаваемый параметр такой же как у штатного метода, он как есть пробрасывается в `$factory->getItems()`
```php
$companies = \ChelBit\Smart\CRM\Company::getItems(
    [
        "filter" => [
            "ASSIGNED_BY_ID" => 1
        ],
        "order" => ["ID" => "ASC"]
    ]
);
foreach ($companies as $company){
    echo $company::class; //Выведет \ChelBit\Smart\CRM\Company
}
```
### Получение и изменение пользовательских полей
Надстройка над штатным методом `get()` позволяет передавать в параметр только код поля.
```php
$example = Example::getInstanceById(1);
$example->getUF("SOME_CODE_STRING");
```
Аналогичная история для `set()`. Кроме этого добавлен метод save который сохраняет изменения используя операции.
```php
$example = Example::getInstanceById(1);
$example->setUF("SOME_CODE_STRING", "Значение строкового поля");
$example->save();
```
Если хотите сохранить без операций, делайте так.
```php
$example = Example::getInstanceById(1);
$example->setUF("SOME_CODE_STRING", "Значение строкового поля");
$example->getItem()->save();
```
Можно изменить конфигурацию update операции.
```php
$example = Example::getInstanceById(1);
$example->setUF("SOME_CODE_STRING", "Значение строкового поля");
$example->getUpdateOperation()->disableAllChecks();
$example->save();
```
### Работа напрямую с объектом Item
Если вдруг очень хочется, можно получить нативный `\Bitrix\Crm\Item` Хранится он в приватном свойстве объекта и доступен для получения через геттер.
```php
echo \ChelBit\Smart\CRM\Company::getInstanceById(1)->getItem()->getTitle(); // Выведет название компании с ID=1
```
### Получение фабрики и entityTypeId
```php
$factory = \ChelBit\Smart\CRM\Example::getFactory();
$entityTypeId = Example::getEntityTypeId();
```
### Получение сообщения об ошибки
При формировании бизнес логики вашего модуля часто приходиться формировать сообщения для пользователя. Для удобства есть метод который добавляет к сообщению ENTITY_TYPE_NAME и ID объекта.
```php
echo $vacation->getErrorMessage("Ваше сообщение"); //Выведет 'Ваше сообщение " NAME:VACATION ENTITY_TYPE_ID:157 ID:14'
```
## 📝 API
### Smart.php

| Метод                   | Описание                          | Параметры                              | Возвращаемое значение |
|-------------------------|-----------------------------------|----------------------------------------|-----------------------|
| `getItem()`          |        |     | `\Bitrix\Crm\Item`                |
| `getEntityTypeId()`          |      |                | `int`                |
|`getFactory()`|||`\Bitrix\Crm\Service\Factory`|
|`getInstanceById()`||`int $id` - ID элемента CRM для которого нужно создать объект|`self`|
|`getItems()`|Получение списка элементов|`array $params` - аналогично штатному методу `$factory->getItems()`|`self[]`|
|`getItemIdentifier()`|||\Bitrix\Crm\ItemIdentifier|
|`getParent()`|Получение родительского элемента|`string $class` - имя класса объекта родителя|`\ChelBit\Smart\Core\Base\Smart or null` - объект класса переданного в параметр или null  если родитель не указан|
|`getChildren()`|Получение дочерних элементов|`string $class` - имя класса дочерних объектов|`\ChelBit\Smart\Core\Base\Smart[]` - массив с дочерними объектами класса переданного в параметр|
|`getErrorMessage()`|Метод добавляет к переданному сообщения информацию об бъекте|`string $message` - текст сообщения|`string`|
|`setParent()`|Устанавливает переданный идентификатор в качестве родительской сущности. Перезаписывает текущую связь. Права не учитывает. Выбросит исключение если переданный объект не может быть установлен родителем.|`Smart $parent`|`void`|
|`unsetParent()`|Очищает связь с родительским элементом переданного класса. Выбросит исключение если данный тип связи не настроен.|`string $class`|`void`|
|`addChild()`|Добавляет дочерний элемент к текущим.|`Smart $child`|`void`|
|`deleteChild()`|Удаляет связь с дочерним элементом. Выбросит исключение если данный тип связи не поддерживается.|`Smart $child`|`void`|
|`unsetChildren()`|Очистит связи с дочерними элементами переданного класса.|`string $class`|`void`|
|`getUF()`|Получает значение по коду пользовательского поля|`string $code` - код поля без UF_CRM_5_|`mixed`|
|`setUF()`|Устанавливает значение по коду пользовательского поля|`string $code` - код поля без UF_CRM_5_ <br>`mixed $value` - значение|`void`|
|`save()`|Сохраняет изменения с использованием операции. Выбросит исключение если операция не успешна.||`void`|
|`getUpdateOperation()`|Получение объекта операции обновления||`\Bitrix\Crm\Service\Operation\Update`|
