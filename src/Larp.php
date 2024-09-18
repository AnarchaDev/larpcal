<?php

declare(strict_types=1);

namespace Nahkampf\Larpcal;

class Larp
{
    public string $name;
    public array $dates;
    public string $organizers;
    public ?string $pitch;
    public ?string $url;
    public ?string $email;
    public string $published;
    public string $cancelled;
    public ?\DateTime $changedAt;
    public int $id;

    public function __construct(
        $name = "?",
        $dates = [],
        $organizers = "",
        $pitch = "",
        $url = "",
        $email = "",
        string $published = "N",
        $cancelled = "N",
        $id = false,
        $changedAt = new \DateTime()
    ) {
        $this->name = $name;
        $this->dates = $dates;
        $this->organizers = $organizers;
        $this->pitch = $pitch;
        $this->url = $url;
        $this->email = $email;
        $this->published = $published;
        $this->cancelled = $cancelled;
        $this->changedAt = ($changedAt instanceof \DateTime) ? $changedAt : new \DateTime($changedAt);
        $this->id = $id;
    }

    /**
     * Get all larps in calendar
     * @param \DateTime $fromDate Get all larps that have one (or more) dates equal to or greater than this date
     * @param array $args Optional arguments for selecting stuff from db. Key-valued, like ["organizer" => "Joe Smith", "genre" => "Fantasy"]
     *
     * @return array
     */
    public static function getAll(\DateTime $fromDate = new \DateTime(), ?array ...$args): array
    {
        $db = new DB();
        $add = null;
        if ($args) {
            foreach ($args as $arg) {
                foreach ($arg as $key => $value) {
                    switch ($key) {
                        case "org":
                            $add = " AND organizers LIKE " . $db->e("%" . $value . "%") . " ";
                            break;
                        case "country":
                            $add = " AND organizers LIKE " . $db->e("%" . $value . "%") . " ";
                            break;
                    }
                }
            }
        }
        $sql = "SELECT MIN(dates.date_start) AS earliest_date, calendar.* FROM dates, calendar WHERE (dates.larp_id = calendar.id) AND (published = \"Y\") AND (date_start >= " . $db->e($fromDate->format("Y-m-d")) . ") $add GROUP BY calendar.id ORDER BY earliest_date ASC";
        $res = $db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $larps = [];
        if (count($res) > 0) {
            foreach ($res as $idx => $larpdata) {
                // remove "earliest_date" from result set
                unset($larpdata["earliest_date"]);
                $larp = new Larp(...$larpdata);
                // add dates
                $larpdata["dates"] = $larp->getDates();
                $larps[] = [
                    ...$larpdata
                ];
            }
        }
        return $larps;
    }

    public static function getLarpById(int $id)
    {
        $db = new DB();
        $larpArray = (array)$db->query("SELECT calendar.* FROM calendar WHERE id = " . (int)$id . " AND published = \"Y\"")->fetchObject();
        $larp = new Larp(...$larpArray);
        // add dates
        $larp->dates = $larp->getDates();
        return $larp;
    }

    public function getDates()
    {
        $db = new DB();
        $sql = "SELECT dates.date_start, dates.date_end FROM dates WHERE larp_id = " . (int)$this->id . " ORDER BY date_start ASC";
        return $db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
