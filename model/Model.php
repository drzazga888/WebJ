<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 17.06.15
 * Time: 02:59
 */

class Model {

    private $pdo;
    private $db = "WebJ.db";

    protected function __construct() {
        $this->pdo = new PDO('sqlite:' . $this->db);
        $this->pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    protected function insert($table, $params) {
        $query = 'insert into ' . $table . '(';
        foreach ($params as $name => $value)
            $query .= $name . ', ';
        $query = substr($query, 0, -2);
        $query .= ') values (';
        foreach ($params as $value)
            $query .= var_export($value, true) . ', ';
        $query = substr($query, 0, -2);
        $query .= ")";
        $this->pdo->prepare($query)->execute();
    }

    protected function select($table, $names = array("*"), $where = null) {
        $query = 'select ';
        foreach ($names as $name)
            $query .= $name . ', ';
        $query = substr($query, 0, -2);
        $query .= ' from ' . $table;
        if (!$where)
            $query .= ' where ' . $where;
        $this->pdo->prepare($query)->execute();
    }

}