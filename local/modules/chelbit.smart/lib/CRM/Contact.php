<?php
namespace ChelBit\Smart\CRM;

use CCrmOwnerType;
use ChelBit\Smart\Core\Base\Smart;

class Contact extends Smart
{

    static function getEntityTypeName(): string
    {
        return CCrmOwnerType::ContactName;
    }
    public static function getEntityTypeId() : int
    {
        return CCrmOwnerType::Contact;
    }

    static function getEntityTypeTitle(): string
    {
        return "Контакт";
    }
}