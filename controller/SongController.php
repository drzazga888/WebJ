<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 18.06.15
 * Time: 02:56
 */

class SongController extends Controller {

    public function mix($params) {

        if (!isset($_SESSION["logged"]))
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

        // rendering
        $baseTop->render();
        $mixer->render();
        $baseBottom->render();

    }

    public function showAll($params) {

        if (!isset($_SESSION["logged"]))
            self::redirect("Musisz się zalogować by skorzystać z mixera!");

        //creating and seting
        $baseTop = new Template("base_top");
        $baseTop->setVar("description", "Lista piosenek");
        $baseTop->setVar("title", $baseTop->getVar("description") . " - WebJ");
        $songList = new Template("song_list");
        $baseBottom = new Template("base_bottom");
        $baseBottom->loadScript("scripts");

        // rendering
        $baseTop->render();
        $songList->render();
        $baseBottom->render();

    }

    public function create($params) {

        if (!isset($_SESSION["logged"]))
            self::redirect("Musisz się zalogować by skorzystać z mixera!");

        $model = new SongsModel();
        $insertedId = $model->create();

        self::redirect(null, null, "song", "mix", array(
            "id" => $insertedId
        ));

    }

}