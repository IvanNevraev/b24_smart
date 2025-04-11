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
    //ToDo Метод для получения instance по ИД или по item
    private Item $item;

    /**
     * Метод должен вернуть имя смарт процесса как указано в таблице b_crm_dynamic_type
     * @return string
     */
    abstract static function getName(): string;
    public function __construct(Item $item){
        $this->item = $item;
    }
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
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

}