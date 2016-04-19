<?php

class AudioController extends Controller {

    public function getAudio($params) {

        if (!isset($_SESSION["user_id"]))
            self::redirect("Musisz się zalogować by skorzystać z mixera!");

        $model = new AudiosModel();
        try {
            $fileName = $model->getFilename($params["id"]);
        } catch (RowNotFoundException $e) {
            http_response_code(404);
            die();
        }

        $folder = "userdata/user_" . $_SESSION["user_id"] . "/audios";
        if (!file_exists($folder))
            mkdir($folder);
        $path = $folder . "/" . $fileName . ".wav";

        header('Content-Type: audio/wav');
        readfile($path);

    }

}