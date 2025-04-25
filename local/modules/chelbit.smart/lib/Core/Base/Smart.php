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
     * @var Factory[]
     */
    private static array $factories;
    /**
     * @var Smart[] $parents
     */
    private array $parents;
    private array $children;

    /**
     * Метод должен вернуть имя смарт процесса как указано в таблице b_crm_dynamic_type если смарт процесс создавали
     * через WEB интерфейс. Если смарт процесс будет создан при установке модуля, нужно указать символьный код
     * Не допускаются любые символы кроме [A-Za-z], рекомендуется использовать UPPERCASE.
     * Задается один раз при создании смарт процесса
     * Например: MYNAME
     * @return string
     */
    abstract static function getEntityTypeName(): string;
    abstract static function getEntityTypeTitle() : string;
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
        if(isset(static::$factories[static::class])) {
            return static::$factories[static::class];
        }
        $factory = Container::getInstance()->getFactory(static::getEntityTypeId());
        if(!$factory){
            throw new SystemException("Ошибка получения фабрики для ".static::getEntityTypeId());
        }
        static::$factories[static::class] = $factory;
        return  static::$factories[static::class];
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
     * Надстройка над штатным методом для получения массива элементов
     * уже обернутых во вспомогательные классы
     * @param array $parameters
     * @return self[]
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getItems(array $parameters) : array
    {
        $factory = static::getFactory();
        $items = $factory->getItems($parameters);
        $return = [];
        foreach($items as $item){
            $return[$item->getId()] = new static($item);
        }
        return $return;
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
    public function getParent(string $class) : Smart|null
    {
        if(isset($this->parents[$class])){
            return $this->parents[$class];
        }
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
                $this->parents[$class] = $class::getInstanceById($parentItemIdentifier->getEntityId());
                return $this->parents[$class];
            }
        }
        $this->parents[$class] = null;
        return null;
    }

    /**
     * Устанавливает переданный идентификатор в качестве родительской сущности. Перезаписывает текущую связь.
     * Права не учитывает. Выбросит исключение если переданный объект не может быть установлен родителем.
     * @param Smart $parent
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function setParent(Smart $parent): void
    {
        if(!static::areItemsBound($parent->getItemIdentifier(), $this->getItemIdentifier())){
            static::bindItems($parent->getItemIdentifier(), $this->getItemIdentifier());
        }
        unset($this->parents[$parent::class]);
    }

    /**
     * Очищает связь с родительским элементом переданного класса.
     * Выбросит исключение если данный тип связи не настроен.
     * @param string $class
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function unsetParent(string $class): void
    {
        $parent = $this->getParent($class);
        if($parent){
            if(static::areItemsBound($parent->getItemIdentifier(), $this->getItemIdentifier())){
                static::unbindItems($parent->getItemIdentifier(), $this->getItemIdentifier());
            }
        }
        unset($this->parents[$parent::class]);
    }

    /**
     *  Метод возвращать дочерние элементы или пустой массив
     *  Если тип связи не настроен выбрасывается исключение
     * @param string $class Имя класса родителя например \ChelBit\Smart\CRM\Invoice::class
     * @return Smart[]
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getChildren(string $class) : array
    {
        if(isset($this->children[$class])){
            return $this->children[$class];
        }
        /**
         * @var Smart $class
         */
        $relationManager = Container::getInstance()->getRelationManager();
        $relationIdentifier = new RelationIdentifier($this::getEntityTypeId(), $class::getEntityTypeId());
        if(!$relationManager->areTypesBound($relationIdentifier)){
            throw new SystemException("Смарт-процесс ".$class::getEntityTypeTitle()." не может быть дочерним для ".$this::getEntityTypeTitle());
        }
        $childrenItemIdentifiers = $relationManager->getChildElements($this->getItemIdentifier());
        $this->children[$class] = [];
        foreach ($childrenItemIdentifiers as $childrenItemIdentifier){
            if($childrenItemIdentifier->getEntityTypeId() === $class::getEntityTypeId()){
                $this->children[$class][] = $class::getInstanceById($childrenItemIdentifier->getEntityId());
            }
        }
        return $this->children[$class];
    }

    /**
     * Добавляет дочерний элемент к текущим
     * @param Smart $child
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function addChild(Smart $child) : void
    {
        if(!static::areItemsBound($this->getItemIdentifier(), $child->getItemIdentifier())){
            static::bindItems($this->getItemIdentifier(), $child->getItemIdentifier());
        }
        unset($this->children[$child::class]);
    }

    /**
     * Удаляет связь с дочерним элементом.
     * Выбросит исключение если данный тип связи не поддерживается
     * @param Smart $child
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function deleteChild(Smart $child) : void
    {
        if(static::areItemsBound($this->getItemIdentifier(), $child->getItemIdentifier())){
            static::unbindItems($this->getItemIdentifier(), $child->getItemIdentifier());
        }
        unset($this->children[$child::class]);
    }

    /**
     * Очистит связи с дочерними элементами переданного класса.
     * @param string $class
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function unsetChildren(string $class): void
    {
        $children = $this->getChildren($class);
        foreach($children as $child){
            $this->deleteChild($child);
        }
        unset($this->children[$class]);
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

    /**
     * Связывает два элемента CRM. Права на доступ не учитывает
     * @param ItemIdentifier $parent
     * @param ItemIdentifier $child
     * @return void
     * @throws SystemException
     */
    private static function bindItems(ItemIdentifier $parent, ItemIdentifier $child) : void
    {
        $res = Container::getInstance()->getRelationManager()->bindItems($parent, $child);
        if(!$res->isSuccess()){
            $message = "Ошибка связывания объектов.";
            $message .= " PARENT_ENTITY_TYPE_ID: ".$parent->getEntityTypeId();
            $message .= " PARENT_ID: ".$parent->getEntityId();
            $message .= " CHILD_ENTITY_TYPE_ID: ".$child->getEntityTypeId();
            $message .= " CHILD_ID: ".$child->getEntityId();
            $message .= " -> ".implode(";",$res->getErrorMessages());
            throw new SystemException($message);
        }
    }
    private static function areItemsBound(ItemIdentifier $parent, ItemIdentifier $child) : bool
    {
        return Container::getInstance()->getRelationManager()->areItemsBound($parent, $child);
    }

    /**
     * @param ItemIdentifier $parent
     * @param ItemIdentifier $child
     * @return void
     * @throws SystemException
     */
    private static function unbindItems(ItemIdentifier $parent, ItemIdentifier $child) : void
    {
        $res = Container::getInstance()->getRelationManager()->unbindItems($parent, $child);
        if(!$res->isSuccess()){
            $message = "Ошибка отвязывания объектов.";
            $message .= " PARENT_ENTITY_TYPE_ID: ".$parent->getEntityTypeId();
            $message .= " PARENT_ID: ".$parent->getEntityId();
            $message .= " CHILD_ENTITY_TYPE_ID: ".$child->getEntityTypeId();
            $message .= " CHILD_ID: ".$child->getEntityId();
            $message .= " -> ".implode(";",$res->getErrorMessages());
            throw new SystemException($message);
        }
    }

}