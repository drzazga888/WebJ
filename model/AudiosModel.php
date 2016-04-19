<?php

require_once "exceptions/RowNotFoundException.php";

class AudiosModel extends Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getAllFilenames($userId) {
        $names = $this->getAllNames($userId);
        $filenames = array();
        foreach ($names as $name) {
            $filenames[] = array(
                "id" => $name["id"],
                "filename" => self::nameToFilename($name["name"])
            );
        }
        return $filenames;
    }

    public function getAllNames($userId) {
        return $this->select("audios", array(
            "id",
            "name"
        ), "user_id=" . $userId);
    }

    public function getFilename($id) {
        $result = $this->select("audios", array("name"), "id=" . $id);
        if (count($result) == 0)
            throw new RowNotFoundException("Audio o id=" . $id . " nie istnieje");
        return self::nameToFilename($result[0]["name"]);
    }

    private static function nameToFilename($name) {
        $exploded = explode(" ", $name);
        for ($i = 0; $i < count($exploded); ++$i) {
            $exploded[$i] = strtolower($exploded[$i]);
        }
        return implode("_", $exploded);
    }

}