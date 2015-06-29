<?php

/**
 * Class SongsModel - klasa typu Model, zarządza utworami
 */
class SongsModel extends Model {

    /**
     * Konstruktor - wywołuje konstruktor klasy nadrzędnej
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Metoda tworzy nowy utwór, zapisuje go w bazie i go zwraca
     * @return string - nr ID wstawionego utworu
     */
    public function create() {
        $newName = "bez nazwy";
        $newContext = $this->getNewContent($newName);
        $insertedId = $this->insert("songs", array(
            "name" => $newName,
            "content" => $newContext
        ));
        $userId = $this->getUserId();
        $this->insert("users_songs", array(
            "user_id" => $userId,
            "song_id" => $insertedId
        ));
        return $insertedId;
    }

    /**
     * Metoda pobiera utwór w formacie JSON z bazy danych
     * @param $id - nr ID utworu
     * @return mixed - zakodowany utwór (obiekt typu Mixer, okrojony, w formacie JSON)
     */
    public function getContent($id) {
        $result = $this->select("songs", array("content"), "id = '" . $id . "'");
        return $result[0]["content"];
    }

    /**
     * Metoda aktualizuje utwór
     * @param $id - nr ID utwotu
     * @param $content - zakodowany utwór (obiekt typu Mixer, okrojony, w formacie JSON)
     */
    public function update($id, $content) {
        parent::update("songs", array(
            "name" => $this->getNameFromContent($content),
            "content" => $content
        ), "id = " . $id);
    }

    /**
     * Metoda pobiera wszystkie utwory, które należą do użytkownika
     * @return array - pobrane rekordy (id i nazwa utworu, bez zawartości)
     */
    public function getAll() {
        $userId = $this->getUserId();
        return $this->select(
            "songs, users_songs",
            array(
                "songs.id as id",
                "songs.name as name"
            ),
            "users_songs.user_id=" . $userId . " and songs.id=users_songs.song_id"
        );
    }

    /**
     * Metoda, która usuwa utwór z bazy danych
     * @param $id - nr ID utworu
     */
    public function delete($id) {
        parent::delete("users_songs", "song_id = " . $id);
        parent::delete("songs", "id = " . $id);
    }

    /**
     * Funkcja pomocnicza, zwraca nowy utwór w postaci JSON
     * @param $name - nazwa utworu
     * @return string - zakodowany utwór (obiekt typu Mixer, okrojony, w formacie JSON)
     */
    private function getNewContent($name) {
        return '{"timelineDuration":"16","name":"' . $name . '","audios":[{"id":0,"name":"Beautiful Touch Pad Trap"},{"id":1,"name":"Bottem Shelf Drums"},{"id":2,"name":"Somedaydreams Chillout Guitars V2"},{"id":3,"name":"105 Upbeat Kinda"},{"id":4,"name":"Avicci Type Chords"},{"id":5,"name":"Choir Vibes"},{"id":6,"name":"Danke Piano Groovy"},{"id":7,"name":"Exfain Arptime Aminor Garvois"},{"id":8,"name":"Herotime Rotten Dam"},{"id":9,"name":"Hip Hop Drum Loop"},{"id":10,"name":"Jammu Guitar Remake"},{"id":11,"name":"My Fat Cat George Drums"}],"tracks":[{"samples":[]}]}';
    }

    /**
     * Funckja pomocnicza, która na podstawie JSON-a zwraca nazwę utworu
     * @param $content - zakodowany utwór (obiekt typu Mixer, okrojony, w formacie JSON)
     * @return mixed - nazwa utworu
     */
    private function getNameFromContent($content) {
        $obj = json_decode($content, true);
        return $obj["name"];
    }

    /**
     * Funkcja pomocnicza, zwraca nr ID zalogowanego użytkownika
     * @return mixed - nr ID zalogowanego użytkownika
     */
    private function getUserId() {
        $result = $this->select("users", array("id"), "email = '" . $_SESSION["logged"] . "'");
        return $result[0]["id"];
    }

}