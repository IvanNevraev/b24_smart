<?php
use \Bitrix\Main\ModuleManager;

class chelbit_smart extends CModule
{
    public function __construct()
    {
        $arModuleVersion = [
            'VERSION' => '1.0.0',
            'VERSION_DATE' => '2024-12-20 14:36:00'
        ];
        $this->MODULE_ID = 'chelbit.smart';
        $this->MODULE_VERSION = "0.0.2";
        $this->MODULE_VERSION_DATE = '2025-04-11 14:23:00';
        $this->MODULE_NAME = 'Первый бит - Умный модуль';
        $this->MODULE_DESCRIPTION  = 'Модуль для работы со смарт-процессами и не только...';
        $this->PARTNER_NAME = 'Первый Бит (Челябинск)';
        $this->PARTNER_URI = 'https://chelyabinsk.1cbit.ru/';
    }

    public function DoInstall()
    {

        ModuleManager::registerModule($this->MODULE_ID);
        return true;
    }

    public function DoUninstall()
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);
        return true;
    }
}
