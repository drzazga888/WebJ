<?php

/**
 * Class MainController - obsługuje żądania związane ze stroną główną
 */
class MainController extends Controller {

    /**
     * Domyślna funckja kontrolera, która wyświetla stronę główną
     * @param $params - parametry wywołania (nieużywane!)
     */
    public function perform($params) {

        //creating and seting
        $baseTop = new Template("base_top");
        $baseTop->setVar("title", "WebJ");
        $baseTop->setVar("description", "Twórz muzykę gdzie tylko chcesz!");
        $main = new Template("main");
        $baseBottom = new Template("base_bottom");
        $baseBottom->loadScript("scripts");

        // rendering
        $baseTop->render();
        $main->render();
        $baseBottom->render();

    }

}