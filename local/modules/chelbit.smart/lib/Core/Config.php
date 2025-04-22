<?php
namespace ChelBit\Smart\Core;

class Config
{
    /**
     * Должен вернуть путь к директории в которой находятся CRM классы
     * @return string
     */
    public static function getSrmClassesDir() : string
    {
        return $_SERVER['DOCUMENT_ROOT']."/local/modules/chelbit.smart/lib/CRM/";
    }
    public static function getRootPath() : string
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }

    /**
     * Должен вернуть массив классов для которых не нужно делать автоматическую установку
     * @return array
     */
    public static function getDefaultCrmClasses() : array
    {
        return [
            "ChelBit\\Smart\\CRM\\Lead",
            "ChelBit\\Smart\\CRM\\Deal",
            "ChelBit\\Smart\\CRM\\Contact",
            "ChelBit\\Smart\\CRM\\Company",
            "ChelBit\\Smart\\CRM\\Invoice",
        ];
    }
}