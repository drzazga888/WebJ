<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 09.06.15
 * Time: 13:35
 */

class MainController {

    function perform() {
        Loader::importTemplate("base", array(
            "title" => "działam"
        ));
    }

}