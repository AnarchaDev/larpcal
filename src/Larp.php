<?php

declare(strict_types=1);

namespace Nahkampf\Larpcal;

class Larp
{
    public int $id;
    public string $name;
    public ?array $dates;
    public ?string $organizers;
    public ?string $pitch;
    public ?string $url;
    public ?string $email;
    public string $published;
    public string $cancelled;
    public string $changedAt;
    public string $createdAt;
    public ?int $countryId;
    public ?array $where;

    public function __construct(array ...$args)
    {
        if (!isset($args[0])) {
            return $this;
        }
        $args = $args[0];
        foreach (array_keys($args) as $idx => $key) {
            $this->{$key} = $args[$key];
        }
    }

    public static function getAll(?array ...$filters): array
    {
        $db = new DB();
        // set defaults
        $sortby = "changedAt";
        $order = "DESC";
        $offset = 0;
        $limit = 10;
        $larps = [];
        $andwhere["published"] = "published=\"Y\"";
        // handle filters and build SQL
        // todo
        if (isset($filters)) {
            foreach ($filters[0] as $key => $value) {
                switch ($key) {
                    case "published":
                        $andwhere["published"] = "published = " . $db->e($value);
                        break;
                    case "from":
                        $andwhere["from"] = "id IN (SELECT larp_id FROM dates WHERE date_start >=" . $db->e($value) . ")";
                        break;
                    case "to":
                        $andwhere["to"] = "id IN (SELECT larp_id FROM dates WHERE date_start <=" . $db->e($value) . ")";
                        break;
                    case "org":
                        $andwhere["org"] = "organizers LIKE (" . $db->e("%{$value}%") . ")";
                        break;
                    case "countries":
                        $countries = explode(",", $value);
                        foreach ($countries as $country) {
                            $c[] = $db->e($country);
                        }
                        $countries = implode(",", $c);
                        $andwhere["countries"] = "countryId IN (SELECT id FROM countries WHERE isoAlpha3 IN({$countries}))";
                        break;
                    case "continent":
                        $andwhere["continent"] = "countryId IN (SELECT id FROM countries WHERE continentName=" . $db->e($value) . ")";
                        break;
                    case "tags":
                        break;
                    case "sortby":
                        break;
                    case "order":
                        break;
                    case "limit":
                        break;
                    case "offset":
                        break;
                }
            }
        }
        $andwhere = implode(" AND ", $andwhere);

        // select the ID of all larps
        $sql = "SELECT id FROM calendar WHERE {$andwhere} ORDER BY {$sortby} {$order} LIMIT {$offset},{$limit}";
        $result = $db->getAll($sql);
        foreach ($result as $idx => $val) {
            $larps[] = self::getById($val["id"]);
        }
        return $larps;
    }

    public static function getById(int $id): bool|\Nahkampf\Larpcal\Larp
    {
        $db = new DB();
        $result = $db->getOne("SELECT * FROM calendar WHERE id = " . (int)$id);
        if (!$result) {
            return false;
        }
        $larp = new Larp($result);
        $larp->dates = $larp->getDates();
        $larp->where = $larp->getCountryData();
        return $larp;
    }

    public function getDates()
    {
        $db = new DB();
        $sql = "SELECT dates.date_start, dates.date_end FROM dates WHERE larp_id = " . (int)$this->id . " ORDER BY date_start ASC";
        return $db->getAll($sql);
    }

    public function getCountryData()
    {
        $db = new DB();
        if (!$this->countryId) {
            return [
                "name" => "Online",
                "iso" => "--",
                "continent" => "--"
            ];
        }
        $sql = "SELECT countryName as name, isoAlpha3 as iso, continentName as continent FROM countries WHERE id = " . (int)$this->countryId;
        $res = $db->getOne($sql);
        return $db->getOne($sql);
    }
}
