<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 17.06.15
 * Time: 00:56
 */

class UsageController {

    public function perform() {

        //creating and seting
        $baseTop = new Template("base_top");
        $baseTop->setVar("description", "Jak używać");
        $baseTop->setVar("title", $baseTop->getVar("description") . " - WebJ");
        $usage = new Template("usage");
        $baseBottom = new Template("base_bottom");
        $baseBottom->loadScript("scripts");

        // rendering
        $baseTop->render();
        $usage->render();
        $baseBottom->render();

    }

}