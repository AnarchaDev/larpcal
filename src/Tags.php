<?php

namespace Nahkampf\Larpcal;

class Tags
{
    public static function getAll(): array
    {
        $db = new DB();
        $tags = $db->getAll("SELECT * FROM tags ORDER BY tag ASC");
        return $tags;
    }

    public static function getTagsForLarp(Larp $larp): array
    {
        $db = new DB();
        $tags = $db->getAll(
            "SELECT tags.* FROM tags, larp_has_tags
            WHERE larp_has_tags.tag_id = tags.id AND larp_has_tags.larp_id = "
            . (int)$larp->id . " ORDER BY tag ASC"
        );
        return $tags;
    }
}
