<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 26.06.15
 * Time: 18:48
 */

class SongsModel extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function create() {
        $newName = "bez nazwy";
        $newContext = $this->getNewContent($newName);
        $insertedId = $this->insert("songs", array(
            "name" => $newName,
            "content" => $newContext
        ));
        $userId = $this->select("users", array("id"), "email = '" . $_SESSION["logged"] . "'");
        $this->insert("users_songs", array(
            "user_id" => $userId[0]["id"],
            "song_id" => $insertedId
        ));
        return $insertedId;
    }

    public function getContent($id) {
        $result = $this->select("songs", array("content"), "id = '" . $id . "'");
        return $result[0]["content"];
    }

    private function getNewContent($name) {
        return '{"timelineDuration":"16","name":"' . $name . '","audios":[{"id":0,"name":"Beautiful Touch Pad Trap"},{"id":1,"name":"Bottem Shelf Drums"},{"id":2,"name":"Somedaydreams Chillout Guitars V2"},{"id":3,"name":"105 Upbeat Kinda"},{"id":4,"name":"Avicci Type Chords"},{"id":5,"name":"Choir Vibes"},{"id":6,"name":"Danke Piano Groovy"},{"id":7,"name":"Exfain Arptime Aminor Garvois"},{"id":8,"name":"Herotime Rotten Dam"},{"id":9,"name":"Hip Hop Drum Loop"},{"id":10,"name":"Jammu Guitar Remake"},{"id":11,"name":"My Fat Cat George Drums"}],"tracks":[{"samples":[]}]}';
    }

}