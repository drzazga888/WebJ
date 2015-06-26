<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 09.06.15
 * Time: 13:35
 */

class MainController {

    public function perform($params) {

        //creating and seting
        $baseTop = new Template("base_top");
        $baseTop->setVar("title", "WebJ");
        $baseTop->setVar("description", "Twórz muzykę gdzie tylko chcesz!");
        $loremIpsum = new Template("lorem_ipsum");
        $baseBottom = new Template("base_bottom");
        $baseBottom->loadScript("scripts");

        // rendering
        $baseTop->render();
        $loremIpsum->render();
        $baseBottom->render();

    }

}