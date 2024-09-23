<?php

namespace Nahkampf\Larpcal;

class Image
{
    use \Nahkampf\Larpcal\Output;
    use \Nahkampf\Larpcal\Larp;
    use \Nahkampf\Larpcal\Token;

    public static function handleUpload(Larp $larp)
    {
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
        if (!$imageinfo = getimagesize($_FILES["file"]["tmp_name"])) {
            Output::write(["Not a valid image file"], 400);
            exit;
        }

        // convert the file
        // @todo use ImageMagick `convert` through exec() here

        //move_uploaded_file($_FILES["file"]["tmp_name"], __DIR__ "../public/images/" . )
    }
}
