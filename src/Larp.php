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
    public ?array $tags;
    public string $image;

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
        // a hack for nullable variadic arrays
        if ($filters[0] == null) {
            $filters = null;
        }
        if ($filters != null) {
            foreach ($filters[0] as $key => $value) {
                switch ($key) {
                    case "published":
                        $andwhere["published"] = "published = " . $db->e($value);
                        break;
                    case "from":
                        $andwhere["from"] = "id IN (SELECT larp_id FROM dates WHERE date_start >="
                            . $db->e($value) . ")";
                        break;
                    case "to":
                        $andwhere["to"] = "id IN (SELECT larp_id FROM dates WHERE date_start <="
                            . $db->e($value) . ")";
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
                        $andwhere["countries"] = "countryId IN (SELECT id FROM countries WHERE isoAlpha3 IN("
                            . $countries . "))";
                        break;
                    case "continent":
                        $andwhere["continent"] = "countryId IN (SELECT id FROM countries WHERE continentName="
                            . $db->e($value) . ")";
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
        $larp->tags = Tags::getTagsForLarp($larp);
        if (file_exists("../app/images/{$larp->id}.jpg") || file_exists("../html/images/{$larp->id}.jpg")) {
            $larp->image = "https://{$_SERVER["HTTP_HOST"]}/images/{$larp->id}.jpg";
        }
        return $larp;
    }

    public function getDates()
    {
        $db = new DB();
        $sql = "SELECT dates.date_start, dates.date_end FROM dates WHERE larp_id = "
        . (int)$this->id . " ORDER BY date_start ASC";
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
        $sql = "SELECT countryName as name, isoAlpha3 as iso, continentName as continent FROM countries WHERE id = "
        . (int)$this->countryId;
        $res = $db->getOne($sql);
        return $db->getOne($sql);
    }

    private function validate(): bool|\Throwable
    {
        // validates a larp object
        if (!strlen($this->name) > 2) {
            throw new \Exception("Name missing or too short");
        }
        if (!strlen($this->organizers) > 2) {
            throw new \Exception("Organizers missing or too short");
        }
        if (!strlen($this->pitch) > 10) {
            throw new \Exception("Pitch missing or too short");
        }
        if (strlen($this->url) > 0) {
            if (!filter_var($this->url, FILTER_VALIDATE_URL)) {
                throw new \Exception("URL malformed?");
            }
        }
        if (strlen($this->email) > 0) {
            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Email address invalid?");
            }
        }
        if (isset($this->dates) && is_array($this->dates) && count($this->dates) > 0) {
            foreach ($this->dates as $idx => $date) {
                // do we have both a start and end date?
                if (!isset($date["date_start"]) || !$date["date_end"]) {
                    throw new \Exception("Dates require both a start and end");
                }
                // are these valid dates
                if (!Utils::validateDate($date["date_start"], "Y-m-d")) {
                    throw new \Exception("Start date has invalid date or format.");
                }
                if (!Utils::validateDate($date["date_end"], "Y-m-d")) {
                    throw new \Exception("End date has invalid date or format.");
                }
                // is this date in the future?
                $now = new \DateTime("now");
                $start = new \DateTime($date["date_start"]);
                $end = new \DateTime($date["date_end"]);
                if ($now->format("Ymd") >= $start->format("Ymd") || $now->format("Ymd") >= $end->format("Ymd")) {
                    throw new \Exception("Dates have to be in the future");
                }
            }
        } else {
            throw new \Exception("No dates set?");
        }
        return true;
    }

    /**
     * saves this larp in the db
     */
    public function save(): array|\Throwable
    {
        $this->validate();
        $db = new DB();
        $sql = sprintf(
            "INSERT INTO calendar SET 
            name = %s,
            organizers = %s,
            pitch = %s,
            url = %s,
            email = %s,
            published = 'N',
            cancelled = 'N',
            countryId = %d",
            $db->e($this->name),
            $db->e($this->organizers),
            $db->e($this->pitch),
            $db->e($this->url),
            $db->e($this->email),
            (int)$this->countryId
        );
        $res = $db->query($sql);
        $larp_id = (int)$db->lastInsertId();
        if ($larp_id > 0) {
            // insert dates
            foreach ($this->dates as $idx => $dates) {
                $sql = sprintf(
                    "INSERT INTO dates SET 
                    larp_id = %d,
                    date_start = %s,
                    date_end = %s",
                    $larp_id,
                    $db->e($dates["date_start"]),
                    $db->e($dates["date_end"])
                );
                $db->query($sql);
            }
            // insert tags (if any)
            foreach ($this->tags as $idx => $tagId) {
                $sql = sprintf("INSERT INTO larp_has_tags SET larp_id = %d, tag_id = %d", $larp_id, $tagId);
                $db->query($sql);
            }
        } else {
            throw new \Exception("Insert failed or could not get last_insert_id");
        }
        // generate a token for this larp
        $token = Token::generateToken();
        $sql = sprintf("INSERT INTO tokens SET larp_id={$larp_id}, token_hash=%s", $db->e($token["hash"]));
        $db->query($sql);
        return ["larpId" => $larp_id, "token" => $token];
    }
}
