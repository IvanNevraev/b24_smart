<?php
namespace ChelBit\Smart\Core\Base;

use Bitrix\Crm\Item;
use Bitrix\Crm\Model\Dynamic\TypeTable;
use Bitrix\Crm\Service\Container;
use Bitrix\Crm\Service\Factory;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

abstract class Smart
{
    //ToDo метод для добавления комментария в timeline
    //ToDo метод для получения связанных элементов и обратных связей
    //ToDo Метод для быстрого сохранения и сохранения через операции
    //ToDo удобный метод для смены стадий, как то генерировать перечисление стадий
    //ToDo автоматической создания, обновление и удаление полей если их нет
    //ToDo Удобный фильтр который сразу возвращает объекты класса обертки
    //ToDo Автоматическое создание смартов путем сканирования папки или еще как то
    private Item $item;

    /**
     * Метод должен вернуть имя смарт процесса как указано в таблице b_crm_dynamic_type
     * @return string
     */
    abstract static function getName(): string;
    public function __construct(Item $item){
        $this->item = $item;
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @return int
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getEntityTypeId() : int
    {
        $res = TypeTable::getRow([
            "filter" => ["NAME" => static::getName()],
            "select" => ["ENTITY_TYPE_ID"],
        ]);
        if (!$res) {
            throw new SystemException("Ошибка получения идентификатора смарт процесса по имени: " . static::getName());
        }
        return (int)$res["ENTITY_TYPE_ID"];
    }

    /**
     * @return Factory
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getFactory() : Factory
    {
        $factory = Container::getInstance()->getFactory(static::getEntityTypeId());
        if(!$factory){
            throw new SystemException("Ошибка получения фабрики для ".static::getEntityTypeId());
        }
        return $factory;
    }

    /**
     * Получить объект класса обертки по ID элементу
     * @param int $id
     * @return self
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getInstanceById(int $id) : self
    {
        $factory = static::getFactory();
        $item = $factory->getItem($id);
        if(!$item){
            throw new SystemException("Ошибка получения элемента CRM. Для ENTITY_TYPE_ID: ".static::getEntityTypeId()." элемент с ID:".$id." отсуствует");
        }
        return new static($item);
    }

    /**
     * Метод дополняет переданный текст ошибки информацией о типе сущности и идентификаторе элемента
     * @param string $message
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getErrorMessage(string $message) : string
    {
        return $message." NAME:".$this->getName()." ENTITY_TYPE_ID:".$this->getEntityTypeId()." ID:".$this->getItem()->getId();
    }

}