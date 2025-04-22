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

    static function isCategoriesEnabled(): bool
    {
        return true;
    }

    static function isStagesEnabled(): bool
    {
        return true;
    }

    static function isBeginCloseDatesEnabled(): bool
    {
        return true;
    }

    static function isClientEnabled(): bool
    {
        return true;
    }

    static function isLinkWithProductsEnabled(): bool
    {
        return true;
    }

    static function isMyCompanyEnabled(): bool
    {
        return true;
    }

    static function isDocumentsEnabled(): bool
    {
        return true;
    }

    static function isSourceEnabled(): bool
    {
        return true;
    }

    static function isObserversEnabled(): bool
    {
        return true;
    }

    static function isRecyclebinEnabled(): bool
    {
        return true;
    }

    static function isAutomationEnabled(): bool
    {
        return true;
    }

    static function isBizProcEnabled(): bool
    {
        return true;
    }

    static function isSetOpenPermissions(): bool
    {
        return true;
    }
}