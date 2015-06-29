<?php

/**
 * Class Model - abstrakcyjna klasa, która tworzy abstrakcję dostępu do bazy danych
 */
abstract class Model {

    private $pdo;
    private $db = "WebJ.db";

    /**
     * Konstruktor, który tworzy pole $pdo
     */
    protected function __construct() {
        $this->pdo = new PDO('sqlite:' . $this->db);
        $this->pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    /**
     * Metoda wstawia rekord do bazy danych
     * @param $table - nazwa tabeli
     * @param $params - parametry - tablica asocjacyjna klucz - wartość
     * @return string - nr ID wstawionego rekordu
     */
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
        return $this->pdo->lastInsertId();
    }

    /**
     * Metoda pobiera dane z bazy danych
     * @param $table - nazwa tabeli
     * @param array $names - nazwa pól, któr muszą być pobrane
     * @param null $where - dodatkowy parametr "where" który precyzuje zapytanie
     * @return array - pobrane rekordy
     */
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

    /**
     * Metoda uaktualnia stan pola w tabeli
     * @param $table - nazwa tabeli
     * @param $values - wartości do zmenienia - tablica asocjacyjna
     * @param null $where - dodatkowy parametr "where" który precyzuje zapytanie
     * @return string - nr ID zmienionego rekordu
     */
    protected function update($table, $values, $where = null) {
        $query = 'update ' . $table . ' set ';
        foreach ($values as $name => $value)
            $query .= $name . '=:' . $name . ', ';
        $query = substr($query, 0, -2);
        if ($where)
            $query .= ' where ' . $where;
        $stmt = $this->pdo->prepare($query);
        foreach ($values as $name => $value)
            $stmt->bindParam(':' . $name, $values[$name], PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
        return $this->pdo->lastInsertId();
    }

    /**
     * Metoda usuwa rekordy z tabeli
     * @param $table - nazwa tabeli
     * @param null $where - dodatkowy parametr "where" który precyzuje zapytanie
     */
    protected function delete($table, $where = null) {
        $query = 'delete from ' . $table;
        if ($where)
            $query .= ' where ' . $where;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $stmt->closeCursor();
    }

}