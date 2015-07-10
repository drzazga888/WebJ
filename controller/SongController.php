<?php

/**
 * Class SongController - klasa typu Controller, obsługuje żądania związane z utworami
 */
class SongController extends Controller {

    /**
     * Funkcja wywoływana po ty by wyświetlić stronę z mikserem utworu
     * @param $params - parametry wywołania (używanie "id" - nr utworu)
     */
    public function mix($params) {

        if (!isset($_SESSION["user_id"]))
            self::redirect("Musisz się zalogować by skorzystać z mixera!");

        $model = new SongsModel();
        $content = $model->getContent($params["id"]);

        //creating and seting
        $baseTop = new Template("base_top");
        $baseTop->setVar("description", "Mixer");
        $baseTop->setVar("title", $baseTop->getVar("description") . " - WebJ");
        $mixer = new Template("mixer");
        $mixer->setVar("id", $params["id"]);
        $mixer->setVar("content", $content);
        $baseBottom = new Template("base_bottom");
        $baseBottom->loadScript("scripts");
        $baseBottom->loadScript("Audio");
        $baseBottom->loadScript("Sample");
        $baseBottom->loadScript("Track");
        $baseBottom->loadScript("Mixer");
        $baseBottom->loadScript("Storage");
        $baseBottom->loadScript("mixerInit");

        // rendering
        $baseTop->render();
        $mixer->render();
        $baseBottom->render();

    }

    /**
     * Funkcja wywoływana po ty by wyświetlić wszystkie utwory zalogowanego użytkownika
     * @param $params - parametry wywołania (używanie "id" - nr utworu)
     */
    public function showAll($params) {

        if (!isset($_SESSION["user_id"]))
            self::redirect("Musisz się zalogować by skorzystać z mixera!");

        $model = new SongsModel();
        $songs = $model->getAll($params["id"]);

        //creating and seting
        $baseTop = new Template("base_top");
        $baseTop->setVar("description", "Lista piosenek");
        $baseTop->setVar("title", $baseTop->getVar("description") . " - WebJ");
        $songList = new Template("song_list");
        $songList->setVar("songs", $songs);
        $baseBottom = new Template("base_bottom");
        $baseBottom->loadScript("scripts");

        // rendering
        $baseTop->render();
        $songList->render();
        $baseBottom->render();

    }

    /**
     * Funkcja wywoływana w celu uworzenia noweg utworu
     * @param $params - parametry wywołania (używanie "id" - nr utworu)
     */
    public function create($params) {

        if (!isset($_SESSION["user_id"]))
            self::redirect("Musisz się zalogować by skorzystać z mixera!");

        $model = new SongsModel();
        $insertedId = $model->create();

        self::redirect(null, null, "song", "mix", array(
            "id" => $insertedId
        ));

    }

    /**
     * Funkcja wywoływana w celu aktualizacji bieżącego utworu
     * @param $params - parametry wywołania (używanie "id" - nr utworu)
     */
    public function update($params) {

        if (!isset($_SESSION["user_id"]))
            self::redirect("Musisz się zalogować by skorzystać z mixera!");

        $model = new SongsModel();
        $model->update($params["id"], $_POST["content"]);

    }

    /**
     * Funkcja wywoływana w celu usunięcia utworu
     * @param $params - parametry wywołania (używanie "id" - nr utworu)
     */
    public function delete($params) {

        if (!isset($_SESSION["user_id"]))
            self::redirect("Musisz się zalogować by skorzystać z mixera!");

        $model = new SongsModel();
        $model->delete($params["id"]);

        self::redirect("Piosenka została usunięta!", null, "song", "show-all");

    }

    public function make($params) {

        if (!isset($_SESSION["user_id"]))
            self::redirect("Musisz się zalogować by skorzystać z mixera!");

        $folder = "userdata/user_" . $_SESSION["user_id"];
        if (!file_exists($folder))
            mkdir($folder);

        $mixedAudio = $folder . "/mixed_audio.wav";

        $model = new SongsModel();
        $content = json_decode($model->getContent($params["id"]), true);
        $samples = array();
        foreach ($content["tracks"] as $track) {
            foreach ($track["samples"] as $sample) {
                $samples[] = $sample;
            }
        }
        $this->soxMerge($samples, $content["timelineDuration"], $mixedAudio);

        header('Content-Description: File Transfer');
        header('Content-Type: audio/wav');
        header('Content-Disposition: attachment; filename=' . str_replace(" ", "_", $model->getName($params["id"])));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($mixedAudio));

        readfile($mixedAudio);

    }

    private function soxMerge($samples, $duration, $output) {
        //var_dump(func_get_args());

        $wavs = array(
            "userdata/share/choir_vibes.wav",
            "userdata/share/hip_hop_drum_loop.wav",
            "userdata/share/jammu_guitar_remake.wav"
        );

        //----------------------------------------
        $path = "sox/src/";
        $cmd = $path . "sox -m";
        foreach ($wavs as $wav)
            $cmd .= " " . $wav;
        $cmd .= " " . $output;
        system($cmd);
    }

}