<?php
namespace ChelBit\Smart\CRM;

use CCrmOwnerType;
use ChelBit\Smart\Core\Base\Smart;

class Lead extends Smart
{

    static function getName(): string
    {
        return CCrmOwnerType::LeadName;
    }
    public static function getEntityTypeId() : int
    {
        return CCrmOwnerType::Lead;
    }
}