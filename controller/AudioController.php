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
        $path = $folder . "/" . $fileName . ".wav";
        if (!file_exists($path))
            $path = "userdata/common/audios/" . $fileName . ".wav";

        header('Content-Type: audio/wav');
        readfile($path);

    }

    public function manage($params) {

        if (!isset($_SESSION["user_id"]))
            self::redirect("Musisz się zalogować by skorzystać z mixera!");

        $model = new AudiosModel();

        //creating and seting
        $baseTop = new Template("base_top");
        $baseTop->setVar("description", "Przesyłanie audio");
        $baseTop->setVar("title", $baseTop->getVar("description") . " - WebJ");
        $manage = new Template("manage_audio");
        $manage->setVar("audios", $model->getAllNames($_SESSION["user_id"]));
        $baseBottom = new Template("base_bottom");
        $baseBottom->loadScript("scripts");

        // rendering
        $baseTop->render();
        $manage->render();
        $baseBottom->render();

    }

    public function upload($params) {

        var_dump($_FILES);

        if (!isset($_SESSION["user_id"]))
            self::redirect("Musisz się zalogować by skorzystać z mixera!");

        $pathParts = pathinfo($_FILES['audio']['name']);
        if ($pathParts['extension'] != 'wav') {
            self::redirect("Akceptowane są jedynie pliki z rozszerzeniem wav", "danger", "audio", "manage");
        }

        $uploadFilePrefix = "userdata/user_" . $_SESSION["user_id"];
        if (!file_exists($uploadFilePrefix))
            mkdir($uploadFilePrefix);
        $uploadFile = "/audios/" ;
        if (!file_exists($uploadFilePrefix . $uploadFile))
            mkdir($uploadFilePrefix . $uploadFile);
        $uploadFileBase = AudiosModel::nameToFilename($pathParts['filename']);
        $uploadFile .= $uploadFileBase . '.wav';

        if (file_exists($uploadFilePrefix . $uploadFile) || file_exists('userdata/common' . $uploadFile))
            self::redirect("Plik o podanej nazwie już istnieje", "danger", "audio", "manage");
        if (move_uploaded_file($_FILES['audio']['tmp_name'], $uploadFilePrefix . $uploadFile)) {
            $model = new AudiosModel();
            $model->insertAudio($_SESSION["user_id"], AudiosModel::filenameToName($uploadFileBase));
            self::redirect("Plik został dodany ", "success", "audio", "manage");
        }
        else
            self::redirect("Wystąpił błąd podczas dodawania pliku " . $uploadFilePrefix . $uploadFile, "danger", "audio", "manage");
    }

    public function delete($params) {

        if (!isset($_SESSION["user_id"]))
            self::redirect("Musisz się zalogować by skorzystać z mixera!");

        $audiosModel = new AudiosModel();
        $songsModel = new SongsModel();
        if (unlink("userdata/user_" . $_SESSION["user_id"] . '/audios/' . $audiosModel->getFilename($params['id']) . '.wav')) {
            $audiosModel->deleteAudio($params['id'], $_SESSION['user_id']);
            $songsBasic = $songsModel->getAll();
            foreach ($songsBasic as &$songBasic) {
                $song = $songsModel->getContent($songBasic['id']);
                //echo "Przed<br>";
                //var_dump($song);
                //echo "<hr>";
                foreach ($song['tracks'] as $trackKey => &$track) {
                    foreach ($track['samples'] as $sampleKey => &$sample) {
                        if ($sample['audioId'] == $params['id']) {
                            unset($song['tracks'][$trackKey]['samples'][$sampleKey]);
                        }
                    }
                }
                //echo "Po<br>";
                //var_dump($song);
                //echo "<hr>";
                //echo "<hr>";
                $songsModel->update($songBasic['id'], $song, false);
                //die();
            }
            self::redirect("Plik audio został usunięty", "success", "audio", "manage");
        } else {
            self::redirect("Plik audio nie mógł zostać usunięty", "danger", "audio", "manage");
        }
    }

}