<?php

require_once "exceptions/RowNotFoundException.php";

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
        $insertedId = $this->insert("songs", array(
            "name" => "bez nazwy",
            "tracks" => json_encode(array(
                array(
                    "samples" => array()
                )
            )),
            "duration" => 16.0
        ));
        $this->insert("users_songs", array(
            "user_id" => $_SESSION["user_id"],
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
        $result = $this->select("songs", array(
            "name",
            "tracks",
            "duration"
        ), "id = '" . $id . "'");
        if (count($result) == 0)
            throw new RowNotFoundException("Nie ma utworu o ID = " . $id);
        $result[0]["tracks"] = json_decode($result[0]["tracks"] ,true);
        return $result[0];
    }

    /**
     * Metoda aktualizuje utwór
     * @param $id - nr ID utwotu
     * @param $song - zakodowany utwór (obiekt typu Mixer, okrojony, w formacie JSON)
     */
    public function update($id, $song) {
        $song = json_decode($song, true);
        parent::update("songs", array(
            "name" => $song["name"],
            "tracks" => json_encode($song["tracks"]),
            "duration" => $song["duration"]
        ), "id = " . $id);
    }

    /**
     * Metoda pobiera wszystkie utwory, które należą do użytkownika
     * @return array - pobrane rekordy (id i nazwa utworu, bez zawartości)
     */
    public function getAll() {
        return $this->select(
            "songs, users_songs",
            array(
                "songs.id as id",
                "songs.name as name"
            ),
            "users_songs.user_id=" . $_SESSION["user_id"] . " and songs.id=users_songs.song_id"
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

}