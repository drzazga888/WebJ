<?php

class Producer
{

    const PRODUCER_PATH = "sox";
    const MERGING_OPTION = "-m";
    const EXTENSION = "wav";
    const OUTPUT = "mixed_audio.wav";
    const SILENCE_SAMPLE = "silence.wav";
    const AUDIOS_FOLDER = "audios";
    const TRIMMED_FOLDER = "samples_trimmed";
    const PADDED_FOLDER = "samples_padded";
    const SILENCE = 'sox -n -r 44100 -c 2 ';
    const SILENCE_PARAMS = ' trim 0.0 ';

    private $basePath = "userdata/user_{id}/";
    private $samples = array();
    private $audios;
    private $songLength;
    private $songName;

    public function __construct(array $pattern, array $audios) {
        if (empty($audios))
            throw new Exception("Song is empty");
        $this->audios = $audios;
        $this->basePath = str_replace('{id}', $_SESSION["user_id"], $this->basePath);
        $this->songLength = $pattern["duration"];
        $this->songName = $pattern["name"];
        foreach ($pattern["tracks"] as $track) {
            foreach ($track["samples"] as $sample)
                $this->samples[] = $sample;
        }
    }

    public function make() {
        //echo '<pre>';
        //var_dump($this->samples);
        //echo '</pre><br>';
        if (!file_exists($this->basePath . self::TRIMMED_FOLDER))
            mkdir($this->basePath . self::TRIMMED_FOLDER);
        if (!file_exists($this->basePath . self::PADDED_FOLDER))
            mkdir($this->basePath . self::PADDED_FOLDER);
        foreach ($this->samples as $sample) {
            $this->trim($sample);
            $this->pad($sample);
        }
        $this->merge();
    }

    public function download() {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . str_replace(" ", "_", $this->songName . '.' . self::EXTENSION));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($this->basePath . self::OUTPUT));
        ob_clean();
        flush();
        readfile($this->basePath . self::OUTPUT);
    }

    private function trim($sample) {
        $cmd = self::PRODUCER_PATH .
            ' ' . $this->basePath . self::AUDIOS_FOLDER . '/' . $this->audioOfSample($sample)["filename"] . '.' . self::EXTENSION .
            ' ' . $this->basePath . self::TRIMMED_FOLDER . '/' . $sample["id"] . '.' . self::EXTENSION .
            ' trim ' . $sample['offset'] . ' ' . $sample['duration'];
        //echo '<h1>trim</h1>';
        //echo $cmd . "<br><hr />";
        return system($cmd);
    }

    private function pad($sample) {
        $cmd = self::PRODUCER_PATH .
            ' ' . $this->basePath . self::TRIMMED_FOLDER . '/' . $sample["id"] . '.' . self::EXTENSION .
            ' ' . $this->basePath . self::PADDED_FOLDER . '/' . $sample["id"] . '.' . self::EXTENSION .
            ' pad ' . $sample['when'];
        //echo '<h1>pad</h1>';
        //echo $cmd . "<br><hr />";
        return system($cmd);
    }

    private function merge() {
        $silenceCmd = self::SILENCE . $this->basePath . self::SILENCE_SAMPLE . self::SILENCE_PARAMS . $this->songLength;
        //echo '<h1>silence</h1>';
        //echo $silenceCmd . "<br><hr />";
        system($silenceCmd);
        $cmd = self::PRODUCER_PATH . ' ' . self::MERGING_OPTION;
        foreach ($this->samples as $sample)
            $cmd .= ' ' . $this->basePath . self::PADDED_FOLDER . '/' . $sample["id"] . '.' . self::EXTENSION;
        $cmd .= ' ' . $this->basePath . self::SILENCE_SAMPLE . ' ' . $this->basePath . self::OUTPUT;
        //echo '<h1>merge</h1>';
        //echo $cmd . "<br><hr />";
        return system($cmd);
    }

    private function audioOfSample($sample) {
        foreach ($this->audios as $audio) {
            if ($audio["id"] == $sample["audioId"])
                return $audio;
        }
        throw new Exception("No audio found related to sample");
    }

}