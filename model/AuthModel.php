<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 17.06.15
 * Time: 03:00
 */

class AuthModel extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function register($model) {
        $this->insert("users", $model);
    }

}