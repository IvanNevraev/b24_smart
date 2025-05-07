<?php
namespace ChelBit\Smart\Core\Option;

use Bitrix\Crm\Model\Dynamic\TypeTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

class EntityOption
{
    private bool $isCategoriesEnabled = false;
    private bool $isStagesEnabled = false;
    private bool $isBeginCloseDatesEnabled = false;
    private bool $isClientEnabled = false;
    private bool $isLinkWithProductsEnabled = false;
    private bool $isMyCompanyEnabled = false;
    private bool $isDocumentsEnabled = false;
    private bool $isSourceEnabled = false;
    private bool $isObserversEnabled = false;
    private bool $isRecyclebinEnabled = false;
    private bool $isAutomationEnabled = false;
    private bool $isBizProcEnabled = false;
    private bool $isSetOpenPermissions = false;
    /**
     * Включены ли направления у смарт-процесса.
     * @return bool
     */
    public function isCategoriesEnabled(): bool
    {
        return $this->isCategoriesEnabled;
    }

    /**
     * Включены ли направления у смарт-процесса
     * @param bool $isCategoriesEnabled
     */
    public function setIsCategoriesEnabled(bool $isCategoriesEnabled): void
    {
        $this->isCategoriesEnabled = $isCategoriesEnabled;
    }

    /**
     * Включены ли стадии у смарт-процесса.
     * @return bool
     */
    public function isStagesEnabled(): bool
    {
        return $this->isStagesEnabled;
    }

    /**
     * Включены ли стадии у смарт-процесса.
     * @param bool $isStagesEnabled
     */
    public function setIsStagesEnabled(bool $isStagesEnabled): void
    {
        $this->isStagesEnabled = $isStagesEnabled;
    }

    /**
     * Включены ли поля "Дата начала" и "Дата окончания".
     * @return bool
     */
    public function isBeginCloseDatesEnabled(): bool
    {
        return $this->isBeginCloseDatesEnabled;
    }

    /**
     * Включены ли поля "Дата начала" и "Дата окончания".
     * @param bool $isBeginCloseDatesEnabled
     */
    public function setIsBeginCloseDatesEnabled(bool $isBeginCloseDatesEnabled): void
    {
        $this->isBeginCloseDatesEnabled = $isBeginCloseDatesEnabled;
    }

    /**
     * Включено ли поле "Клиент" (привязка к компании и контактам).
     * @return bool
     */
    public function isClientEnabled(): bool
    {
        return $this->isClientEnabled;
    }

    /**
     * Включено ли поле "Клиент" (привязка к компании и контактам).
     * @param bool $isClientEnabled
     */
    public function setIsClientEnabled(bool $isClientEnabled): void
    {
        $this->isClientEnabled = $isClientEnabled;
    }

    /**
     * Включен ли функционал товаров.
     * @return bool
     */
    public function isLinkWithProductsEnabled(): bool
    {
        return $this->isLinkWithProductsEnabled;
    }

    /**
     * Включен ли функционал товаров.
     * @param bool $isLinkWithProductsEnabled
     */
    public function setIsLinkWithProductsEnabled(bool $isLinkWithProductsEnabled): void
    {
        $this->isLinkWithProductsEnabled = $isLinkWithProductsEnabled;
    }

    /**
     * Включено ли поле "Реквизиты моей компании".
     * @return bool
     */
    public function isMyCompanyEnabled(): bool
    {
        return $this->isMyCompanyEnabled;
    }

    /**
     * Включено ли поле "Реквизиты моей компании".
     * @param bool $isMyCompanyEnabled
     */
    public function setIsMyCompanyEnabled(bool $isMyCompanyEnabled): void
    {
        $this->isMyCompanyEnabled = $isMyCompanyEnabled;
    }

    /**
     * Включена ли печать документов.
     * @return bool
     */
    public function isDocumentsEnabled(): bool
    {
        return $this->isDocumentsEnabled;
    }

    /**
     * Включена ли печать документов.
     * @param bool $isDocumentsEnabled
     */
    public function setIsDocumentsEnabled(bool $isDocumentsEnabled): void
    {
        $this->isDocumentsEnabled = $isDocumentsEnabled;
    }

    /**
     * Включено ли поле "Источник" и "Подробнее об источнике".
     * @return bool
     */
    public function isSourceEnabled(): bool
    {
        return $this->isSourceEnabled;
    }

    /**
     * Включено ли поле "Источник" и "Подробнее об источнике".
     * @param bool $isSourceEnabled
     */
    public function setIsSourceEnabled(bool $isSourceEnabled): void
    {
        $this->isSourceEnabled = $isSourceEnabled;
    }

    /**
     * Включено ли поле "Наблюдатели".
     * @return bool
     */
    public function isObserversEnabled(): bool
    {
        return $this->isObserversEnabled;
    }

    /**
     * Включено ли поле "Наблюдатели".
     * @param bool $isObserversEnabled
     */
    public function setIsObserversEnabled(bool $isObserversEnabled): void
    {
        $this->isObserversEnabled = $isObserversEnabled;
    }

    /**
     * Включен ли функционал корзины.
     * @return bool
     */
    public function isRecyclebinEnabled(): bool
    {
        return $this->isRecyclebinEnabled;
    }

    /**
     * Включен ли функционал корзины.
     * @param bool $isRecyclebinEnabled
     */
    public function setIsRecyclebinEnabled(bool $isRecyclebinEnabled): void
    {
        $this->isRecyclebinEnabled = $isRecyclebinEnabled;
    }

    /**
     * Включены ли роботы и триггеры.
     * @return bool
     */
    public function isAutomationEnabled(): bool
    {
        return $this->isAutomationEnabled;
    }

    /**
     * Включены ли роботы и триггеры.
     * @param bool $isAutomationEnabled
     */
    public function setIsAutomationEnabled(bool $isAutomationEnabled): void
    {
        $this->isAutomationEnabled = $isAutomationEnabled;
    }

    /**
     * Включен ли дизайнер бизнес-процессов.
     * @return bool
     */
    public function isBizProcEnabled(): bool
    {
        return $this->isBizProcEnabled;
    }

    /**
     * Включен ли дизайнер бизнес-процессов.
     * @param bool $isBizProcEnabled
     */
    public function setIsBizProcEnabled(bool $isBizProcEnabled): void
    {
        $this->isBizProcEnabled = $isBizProcEnabled;
    }

    /**
     * Открывать ли доступ к новому направлению всем ролям.
     * @return bool
     */
    public function isSetOpenPermissions(): bool
    {
        return $this->isSetOpenPermissions;
    }

    /**
     * Открывать ли доступ к новому направлению всем ролям.
     * @param bool $isSetOpenPermissions
     */
    public function setIsSetOpenPermissions(bool $isSetOpenPermissions): void
    {
        $this->isSetOpenPermissions = $isSetOpenPermissions;
    }

    /**
     * Устанавливает фактической значений опций смарт-процесса
     * @param string $entityTypeName Имя указанное в столбце NAME таблицы b_crm_dynamic_type
     * @return void
     * @throws SystemException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     */
    public function setOptionsFromTypeTable(string $entityTypeName) : void
    {
        $typeData = TypeTable::getRow(
            [
                "filter" => [
                    "NAME" => $entityTypeName
                ]
            ]
        );
        if(!empty($typeData)){
            $this->setIsCategoriesEnabled($typeData["IS_CATEGORIES_ENABLED"] === "Y");
            $this->setIsStagesEnabled($typeData["IS_STAGES_ENABLED"] === "Y");
            $this->setIsBeginCloseDatesEnabled($typeData["IS_BEGIN_CLOSE_DATE_ENABLED"] === "Y");
            $this->setIsClientEnabled($typeData["IS_CLIENT_ENABLED"] === "Y");
            $this->setIsLinkWithProductsEnabled($typeData["IS_LINK_WITH_PRODUCTS_ENABLED"] === "Y");
            $this->setIsMyCompanyEnabled($typeData["IS_MYCOMPANY_ENABLED"] === "Y");
            $this->setIsDocumentsEnabled($typeData["IS_DOCUMENTS_ENABLED"] === "Y");
            $this->setIsSourceEnabled($typeData["IS_SOURCE_ENABLED"] === "Y");
            $this->setIsObserversEnabled($typeData["IS_OBSERVERS_ENABLED"] === "Y");
            $this->setIsRecyclebinEnabled($this["IS_RECYCLEBIN_ENABLED"] === "Y");
            $this->setIsAutomationEnabled($typeData["IS_AUTOMATION_ENABLED"] === "Y");
            $this->setIsBizProcEnabled($typeData["IS_BIZ_PROC_ENABLED"] === "Y");
            $this->setIsSetOpenPermissions($typeData["IS_SET_OPEN_PERMISSIONS"] === "Y");
        }else{
            $message = "Ошибка получения данных из таблицы b_crm_dynamic_type. ";
            $message .= "В таблице отсутствует запись для NAME = $entityTypeName";
            throw new SystemException($message);
        }
    }
    public function getArrayData() : array
    {
        return [
            "IS_CATEGORIES_ENABLED" => $this->isCategoriesEnabled(),
            "IS_STAGES_ENABLED" => $this->isStagesEnabled(),
            "IS_BEGIN_CLOSE_DATES_ENABLED" => $this->isBeginCloseDatesEnabled(),
            "IS_CLIENT_ENABLED" => $this->isClientEnabled(),
            "IS_LINK_WITH_PRODUCTS" => $this->isLinkWithProductsEnabled(),
            "IS_MYCOMPANY_ENABLED"=> $this->isMyCompanyEnabled(),
            "IS_DOCUMENTS_ENABLED"=> $this->isDocumentsEnabled(),
            "IS_SOURCE_ENABLED"=> $this->isSourceEnabled(),
            "IS_OBSERVERS_ENABLED" => $this->isObserversEnabled(),
            "IS_RECYCLEBIN_ENABLED" => $this->isRecyclebinEnabled(),
            "IS_AUTOMATION_ENABLED" => $this->isAutomationEnabled(),
            "IS_BIZ_PROC_ENABLED" => $this->isBizProcEnabled(),
            "IS_SET_OPEN_PERMISSIONS" => $this->issetOpenPermissions(),
        ];
    }


}