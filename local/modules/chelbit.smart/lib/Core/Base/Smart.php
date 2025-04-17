<?php
namespace ChelBit\Smart\Core\Base;

use Bitrix\Crm\Item;
use Bitrix\Crm\ItemIdentifier;
use Bitrix\Crm\Model\Dynamic\TypeTable;
use Bitrix\Crm\RelationIdentifier;
use Bitrix\Crm\Service\Container;
use Bitrix\Crm\Service\Factory;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\SystemException;
use Exception;

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
     * Метод должен вернуть имя смарт процесса как указано в таблице b_crm_dynamic_type если смарт процесс создавали
     * через WEB интерфейс. Если смарт процесс будет создан при установке модуля, нужно указать символьный код
     * Не допускаются любые символы кроме [A-Za-z], рекомендуется использовать UPPERCASE.
     * Задается один раз при создании смарт процесса
     * Например: MYNAME
     * @return string
     */
    abstract static function getEntityTypeName(): string;

    /**
     * То что отображается в публичной части, можно менять неограниченное количество раз
     * @return string
     */
    abstract static function getEntityTypeTitle(): string;

    /**
     * Включены ли направления у смарт-процесса.
     * @return bool
     */
    abstract static function isCategoriesEnabled() : bool;

    /**
     * Включены ли стадии у смарт-процесса.
     * @return bool
     */
    abstract static function isStagesEnabled() : bool;

    /**
     * Включены ли поля "Дата начала" и "Дата окончания".
     * @return bool
     */
    abstract static function isBeginCloseDatesEnabled() : bool;

    /**
     * Включено ли поле "Клиент" (привязка к компании и контактам).
     * @return bool
     */
    abstract static function isClientEnabled() : bool;

    /**
     * Включен ли функционал товаров.
     * @return bool
     */
    abstract static function isLinkWithProductsEnabled() : bool;

    /**
     * Включено ли поле "Реквизиты моей компании".
     * @return bool
     */
    abstract static function isMyCompanyEnabled() : bool;

    /**
     * Включена ли печать документов.
     * @return bool
     */
    abstract static function isDocumentsEnabled() : bool;

    /**
     * Включено ли поле "Источник" и "Подробнее об источнике".
     * @return bool
     */
    abstract static function isSourceEnabled() : bool;

    /**
     * Включено ли поле "Наблюдатели".
     * @return bool
     */
    abstract static function isObserversEnabled() : bool;

    /**
     * Включен ли функционал корзины.
     * @return bool
     */
    abstract static function isRecyclebinEnabled() : bool;

    /**
     * Включены ли роботы и триггеры
     * @return bool
     */
    abstract static function isAutomationEnabled() : bool;

    /**
     * Включен ли дизайнер бизнес процессов
     * @return bool
     */
    abstract static function isBizProcEnabled() : bool;

    /**
     * Открывать ли доступ к новому направлению всем ролям.
     * @return bool
     */
    abstract static function isSetOpenPermissions() : bool;
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
            "filter" => ["NAME" => static::getEntityTypeName()],
            "select" => ["ENTITY_TYPE_ID"],
        ]);
        if (!$res) {
            throw new SystemException("Ошибка получения идентификатора смарт процесса по имени: " . static::getEntityTypeName());
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
            throw new SystemException("Ошибка получения элемента CRM. Для ENTITY_TYPE_ID: ".static::getEntityTypeId()." элемент с ID:".$id." отсутствует");
        }
        return new static($item);
    }

    /**
     * @return ItemIdentifier
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getItemIdentifier() : ItemIdentifier
    {
        return new ItemIdentifier($this::getEntityTypeId(), $this->getItem()->getId());
    }

    /**
     * Метод возвращать родительский элемент или null
     * Если тип связи не настроен выбрасывается исключение
     * @param string $class Имя класса родителя например \ChelBit\Smart\CRM\Invoice::class
     * @return Smart|null Вернет объект класса переданного в параметре или null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getParentItem(string $class) : Smart|null
    {
        /**
         * @var Smart $class
         */
        $relationManager = Container::getInstance()->getRelationManager();
        $relationIdentifier = new RelationIdentifier($class::getEntityTypeId(), $this::getEntityTypeId());
        if(!$relationManager->areTypesBound($relationIdentifier)){
            throw new SystemException("Смарт-процесс ".$class::getEntityTypeTitle()." не может быть родителем для ".$this::getEntityTypeTitle());
        }
        $parentItemIdentifiers = $relationManager->getParentElements($this->getItemIdentifier());
        foreach ($parentItemIdentifiers as $parentItemIdentifier){
            if($parentItemIdentifier->getEntityTypeId() === $class::getEntityTypeId()){
                return $class::getInstanceById($parentItemIdentifier->getEntityId());
            }
        }
        return null;
    }

    /**
     *  Метод возвращать дочерние элементы или пустой массив
     *  Если тип связи не настроен выбрасывается исключение
     * @param string $class Имя класса родителя например \ChelBit\Smart\CRM\Invoice::class
     * @return static[]
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getChildren(string $class) : array
    {
        /**
         * @var Smart $class
         */
        $relationManager = Container::getInstance()->getRelationManager();
        $relationIdentifier = new RelationIdentifier($this::getEntityTypeId(), $class::getEntityTypeId());
        if(!$relationManager->areTypesBound($relationIdentifier)){
            throw new SystemException("Смарт-процесс ".$class::getEntityTypeTitle()." не может быть дочерним для ".$this::getEntityTypeTitle());
        }
        $childrenItemIdentifiers = $relationManager->getChildElements($this->getItemIdentifier());
        $return = [];
        foreach ($childrenItemIdentifiers as $childrenItemIdentifier){
            if($childrenItemIdentifier->getEntityTypeId() === $class::getEntityTypeId()){
                $return[] = $class::getInstanceById($childrenItemIdentifier->getEntityId());
            }
        }
        return $return;
    }

    /**
     * @return AddResult
     * @throws Exception
     */
    public static function add() : AddResult
    {
        return TypeTable::add(
            [
                "NAME" => static::getEntityTypeName(),
                "TITLE" => static::getEntityTypeTitle(),
                "ENTITY_TYPE_ID" => TypeTable::getNextAvailableEntityTypeId(),
                "IS_CATEGORIES_ENABLED" => static::isCategoriesEnabled(),
                "IS_STAGES_ENABLED" => static::isStagesEnabled(),
                "IS_BEGIN_CLOSE_DATES_ENABLED" => static::isBeginCloseDatesEnabled(),
                "IS_CLIENT_ENABLED" => static::isClientEnabled(),
                "IS_LINK_WITH_PRODUCTS" => static::isLinkWithProductsEnabled(),
                "IS_MYCOMPANY_ENABLED"=> static::isMyCompanyEnabled(),
                "IS_DOCUMENTS_ENABLED"=> static::isDocumentsEnabled(),
                "IS_SOURCE_ENABLED"=> static::isSourceEnabled(),
                "IS_OBSERVERS_ENABLED" => static::isObserversEnabled(),
                "IS_RECYCLEBIN_ENABLED" => static::isRecyclebinEnabled(),
                "IS_AUTOMATION_ENABLED" => static::isAutomationEnabled(),
                "IS_BIZ_PROC_ENABLED" => static::isBizProcEnabled(),
                "IS_SET_OPEN_PERMISSIONS" => static::isSetOpenPermissions(),
            ]
        );
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
        return $message." NAME:".$this->getEntityTypeName()." ENTITY_TYPE_ID:".$this->getEntityTypeId()." ID:".$this->getItem()->getId();
    }

}