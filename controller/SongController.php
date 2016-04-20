<?php

require_once "Producer.php";

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
        $audiosModel = new AudiosModel();
        $content = $model->getContent($params["id"]);
        $audios = $audiosModel->getAllNamesWithCommon($_SESSION["user_id"]);

        //creating and seting
        $baseTop = new Template("base_top");
        $baseTop->setVar("description", "Mixer");
        $baseTop->setVar("title", $baseTop->getVar("description") . " - WebJ");
        $mixer = new Template("mixer");
        $mixer->setVar("id", $params["id"]);
        $mixer->setVar("content", json_encode($content));
        $mixer->setVar("audios", json_encode($audios));
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
        $songs = $model->getAll();

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

        $audiosModel = new AudiosModel();
        $songsModel = new SongsModel();
        try {
            $producer = new Producer(
                $songsModel->getContent($params["id"]),
                $audiosModel->getAllFilenamesWithCommon($_SESSION["user_id"])
            );
            $producer->make();
            $producer->download();
        } catch (EmptySongException $e) {
            self::redirect("Utwór nie zawiera żadnych kawałków muzycznych", "danger", "song", "mix", [
                'id' => $params['id']
            ]);
        }
    }

}