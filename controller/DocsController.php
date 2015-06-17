<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 17.06.15
 * Time: 00:57
 */

class DocsController {

    public function perform() {

        //creating and seting
        $baseTop = new Template("base_top");
        $baseTop->setVar("description", "Dokumentacja");
        $baseTop->setVar("title", $baseTop->getVar("description") . " - WebJ");
        $docs = new Template("docs");
        $baseBottom = new Template("base_bottom");

        // rendering
        $baseTop->render();
        $docs->render();
        $baseBottom->render();

    }

}