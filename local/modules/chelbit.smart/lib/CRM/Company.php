<?php
namespace ChelBit\Smart\CRM;

use CCrmOwnerType;
use ChelBit\Smart\Core\Base\Smart;

class Company extends Smart
{

    static function getEntityTypeName(): string
    {
        return CCrmOwnerType::CompanyName;
    }
    public static function getEntityTypeId() : int
    {
        return CCrmOwnerType::Company;
    }

    static function getEntityTypeTitle(): string
    {
        return "Компания";
    }
}
