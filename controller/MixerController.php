<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 18.06.15
 * Time: 02:56
 */

class MixerController extends Controller {

    public function perform() {

        if (!isset($_SESSION["logged"]))
            self::redirect("Musisz się zalogować by skorzystać z mixera!");

        //creating and seting
        $baseTop = new Template("base_top");
        $baseTop->setVar("description", "Mixer");
        $baseTop->setVar("title", $baseTop->getVar("description") . " - WebJ");
        $mixer = new Template("mixer");
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

}