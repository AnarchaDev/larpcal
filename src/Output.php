<?php

namespace Nahkampf\Larpcal;

class Output
{
    public static function json(array|string $data)
    {
        return json_encode($data);
    }

    public static function write(stdClass|array|string $data, int $code = 200)
    {
        http_response_code($code);
        header("Content-type: application/json");
        // sanitize output so we don't get html/xss stuff
        if (is_array($data)) {
            array_walk_recursive(
                $data,
                function (&$string) {
                    if (is_string($string)) {
                        $string = trim(strip_tags($string));
                    }
                }
            );
        } else {
            $data = strip_tags($data);
        }
        echo self::json($data);
    }
}
