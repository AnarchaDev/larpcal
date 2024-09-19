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
        $sql = "SELECT token_hash FROM tokens WHERE larp_id=" . $larp->id;
        $res = $db->getOne($sql);
        if (empty($res)) {
            \Nahkampf\Larpcal\Output::write("An error occured (larp has no token)", 500);
            exit;
        }
        if (password_verify($token, $res["token_hash"])) {
            return true;
        }
        return false;
    }
}
