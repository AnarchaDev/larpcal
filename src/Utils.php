<?php

namespace Nahkampf\Larpcal;

class Utils
{
    public static function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && strtolower($d->format($format)) === strtolower($date);
    }

    public static function getCountries(): ?array
    {
        $db = new DB();
        $query = "SELECT * FROM countries";
        return $db->getAll($query);
    }
}
