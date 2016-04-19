<?php

require_once "exceptions/RowNotFoundException.php";

class AudiosModel extends Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getAllFilenamesWithCommon($userId) {
        $names = $this->getAllNamesWithCommon($userId);
        $filenames = array();
        foreach ($names as $name) {
            $filenames[] = array(
                "id" => $name["id"],
                "filename" => self::nameToFilename($name["name"])
            );
        }
        return $filenames;
    }

    public function getAllNamesWithCommon($userId) {
        return $this->select("audios", array(
            "id",
            "name"
        ), "user_id IS NULL OR user_id=" . $userId);
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

    public function insertAudio($userId, $name) {
        $this->insert("audios", [
            "user_id" => $userId,
            "name" => self::filenameToName($name)
        ]);
    }

    public function deleteAudio($id, $userId) {
        $this->delete('audios', 'id=' . $id . ' AND user_id=' . $userId);
    }

    public static function filenameToName($name) {
        return implode(" ", explode("_", ucfirst($name)));
    }

    public static function nameToFilename($name) {
        return implode("_", explode(" ", strtolower($name)));
    }

}