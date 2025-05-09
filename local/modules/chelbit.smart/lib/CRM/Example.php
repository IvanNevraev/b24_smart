<?php
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