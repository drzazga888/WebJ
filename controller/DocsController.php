<?php

/**
 * Class DocsController - obsługuje żądania związane z dokumentacją
 */
class DocsController extends Controller {

    /**
     * Domyślna funckja kontrolera, która wyświetla dokumentację
     * @param $params - parametry wywołania (nieużywane!)
     */
    public function perform($params) {

        //creating and seting
        $baseTop = new Template("base_top");
        $baseTop->setVar("description", "Dokumentacja");
        $baseTop->setVar("title", $baseTop->getVar("description") . " - WebJ");
        $docs = new Template("docs");
        $baseBottom = new Template("base_bottom");
        $baseBottom->loadScript("scripts");

        // rendering
        $baseTop->render();
        $docs->render();
        $baseBottom->render();

    }

}