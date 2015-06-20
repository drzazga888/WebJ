<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 16.06.15
 * Time: 21:39
 */

class Template {

    private $name;
    private $vars = array();

    public function __construct($name) {
        $this->name = $name;
    }

    public function render() {
        extract($this->vars);
        include_once "templates/".$this->name.".phtml";
    }

    public function setVar($name, $value) {
        $this->vars[$name] = $value;
    }

    public function getVar($name) {
        return $this->vars[$name];
    }

    public function loadScript($name) {
        $this->vars["scripts"][] = $name;
    }

}