<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 17.06.15
 * Time: 02:59
 */

abstract class Model {

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
        $queryNamePart = "";
        $queryValuePart = "";
        foreach ($params as $name => $value) {
            $queryNamePart .= $name . ', ';
            $queryValuePart .= ':' . $name . ', ';
        }
        $queryNamePart = substr($queryNamePart, 0, -2);
        $queryValuePart = substr($queryValuePart, 0, -2);
        $query = 'insert into ' . $table . ' (' . $queryNamePart . ') values (' . $queryValuePart . ')';
        $stmt = $this->pdo->prepare($query);
        foreach ($params as $name => $value)
            $stmt->bindParam(':' . $name, $params[$name], PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }

    protected function select($table, $names = array("*"), $where = null) {
        $query = 'select ';
        foreach ($names as $name)
            $query .= $name . ', ';
        $query = substr($query, 0, -2);
        $query .= ' from ' . $table;
        if ($where)
            $query .= ' where ' . $where;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }

}