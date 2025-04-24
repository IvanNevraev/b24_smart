<?php
namespace ChelBit\Smart\CRM;

use CCrmOwnerType;
use ChelBit\Smart\Core\Base\Smart;

class Deal extends Smart
{
    static function getEntityTypeName(): string
    {
        return CCrmOwnerType::DealName;
    }
    public static function getEntityTypeId() : int
    {
        return CCrmOwnerType::Deal;
    }

    static function getEntityTypeTitle(): string
    {
        return "Сделка";
    }
}