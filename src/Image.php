<?php

namespace Nahkampf\Larpcal;

use \Nahkampf\Larpcal\Output;
use \Nahkampf\Larpcal\Larp;
use \Nahkampf\Larpcal\Token;

class Image
{
    public static function handleUpload(Larp $larp)
    {
        if (empty($_POST)) {
            Output::write(["No POST data!", 400]);
        }
        if (!$larp instanceof Larp) {
            Output::write(["That larp doesn't exist!"], 404);
            exit;
        }
        if (!Token::checkToken($larp, $_POST["token"])) {
            Output::write(["Token invalid!"], 401);
            exit;
        }
        if (empty($_FILES["file"])) {
            Output::write(["No file provided?"], 400);
            exit;
        }
        // handle file
        // we can't trust mime detection because it's just working off extension
        // so instead we need to check the file type
        $imageinfo = getimagesize($_FILES["file"]["tmp_name"]);
        if (!$imageinfo) {
            Output::write(["Not a valid image file"], 400);
            exit;
        }
        list($width, $height, $type, $attributes) = $imageinfo;
        // Image needs to be at least 1024x768
        if ($width < 1024 || $height < 768) {
            Output::write(["Image resolution too low, minimum 1024x768 px"], 422);
            exit;
        }
        // move the file
        try {
            $move = move_uploaded_file($_FILES["file"]["tmp_name"], "/var/www/html/images/" . $larp->id . "_orig.jpg");
        } catch (\Throwable $t) {
            print_r($t->getMessage());
        }
        if (!$move) {
            Output::write(["Failed saving or copying uploaded file!"], 500);
            exit;
        }
        // convert the file
        $cmd = "convert /var/www/html/images/{$larp->id}_orig.jpg -resize 1024x768^ -gravity center -extent 1024x768 -quality 85 ../html/images/" . $larp->id . ".jpg";
        $res = shell_exec($cmd);
        Output::write(
            ["larpId" => $larp->id, "imageUrl" => "https://{$_SERVER["HTTP_HOST"]}/images/{$larp->id}.jpg"],
            201
        );
        // delete originals
        exec("rm -rf /var/www/html/images/*_orig.jpg");
        exit;
    }
}
