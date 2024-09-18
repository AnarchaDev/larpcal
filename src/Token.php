<?php

namespace Nahkampf\Larpcal;

class Token
{
    public static function generateToken()
    {
        $validchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $tokenLength = 16;
        $token = null;
        for ($x = 0; $x < $tokenLength - 1; $x++) {
            $token .= substr($validchars, mt_rand(0, strlen($validchars) - 1), 1);
        }
        return ["token" => $token, "hash" => password_hash($token, PASSWORD_DEFAULT)];
    }

    public static function checkToken(Larp $larp, string $token)
    {
        $db = new DB();
        $hash = $db->query("SELECT token_hash FROM tokens WHERE larp_id=" . (int)$larp->id)->fetchObject();
        print_r($hash);
    }
}
