<?php
namespace ChelBit\Smart\CRM;

use ChelBit\Smart\Core\Base\Smart;

class Invoice extends Smart
{

    static function getEntityTypeName(): string
    {
        return "SmartInvoice";
    }

    static function getEntityTypeTitle(): string
    {
        return "Счет";
    }
}