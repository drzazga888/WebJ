<?php

class Producer
{

    const PRODUCER_PATH = "sox";
    const MERGING_OPTION = "-m";
    const EXTENSION = "wav";
    const OUTPUT = "mixed_audio.wav";
    const SILENCE_SAMPLE = "silence.wav";
    const JOINED_SAMPLES = "joined.wav";
    const AUDIOS_FOLDER = "audios";
    const PROCESSED_FOLDER = "processed";
    const COMMON_FOLDER = "userdata/common/";
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
        if (!file_exists($this->basePath))
            mkdir($this->basePath);
        if (!file_exists($this->basePath . self::PROCESSED_FOLDER))
            mkdir($this->basePath . self::PROCESSED_FOLDER);
        foreach ($this->samples as $sample)
            $this->processSingle($sample);
        $this->processAll();
        $this->clean();
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

    private function getAudioPath($filename) {
        $path = $this->basePath . self::AUDIOS_FOLDER . '/' . $filename . '.' . self::EXTENSION;
        if (!file_exists($path))
            $path = self::COMMON_FOLDER . self::AUDIOS_FOLDER . '/' . $filename . '.' . self::EXTENSION;
        else if (!file_exists($path))
            throw new FileNotFoundException();
        return $path;
    }

    private function processSingle($sample) {
        $cmd = self::PRODUCER_PATH .
            ' \'' . $this->getAudioPath($this->audioOfSample($sample)["filename"]) .
            '\' \'' . $this->basePath . self::PROCESSED_FOLDER . '/' . $sample["id"] . '.' . self::EXTENSION .
            '\' repeat trim ' . $sample['offset'] . ' ' . $sample['duration'] . ' pad ' . $sample['when'];
        //echo $cmd . "<br><hr />";
        system($cmd);
    }

    private function processAll() {
        $silenceCmd = self::SILENCE . $this->basePath . self::PROCESSED_FOLDER . '/' . self::SILENCE_SAMPLE . self::SILENCE_PARAMS . $this->songLength;
        //echo $silenceCmd . "<br><hr />";
        system($silenceCmd);
        $joinCmd = self::PRODUCER_PATH . ' ' . self::MERGING_OPTION;
        foreach ($this->samples as $sample)
            $joinCmd .= ' \'' . $this->basePath . self::PROCESSED_FOLDER . '/' . $sample["id"] . '.' . self::EXTENSION . '\'';
        $joinCmd .= ' \'' . $this->basePath . self::PROCESSED_FOLDER . '/' . self::SILENCE_SAMPLE . '\'' .
            ' \'' . $this->basePath . self::PROCESSED_FOLDER . '/' . self::JOINED_SAMPLES. '\'';
        //echo $joinCmd . "<br><hr />";
        system($joinCmd);
        $cmd = self::PRODUCER_PATH . ' ' .
            $this->basePath . self::PROCESSED_FOLDER . '/' . self::JOINED_SAMPLES .
            ' ' . $this->basePath . self::OUTPUT . ' trim 0 ' . $this->songLength;
        system($cmd);
    }

    private function audioOfSample($sample) {
        foreach ($this->audios as $audio) {
            if ($audio["id"] == $sample["audioId"])
                return $audio;
        }
        throw new Exception("No audio found related to sample");
    }

    private function clean() {
        if (is_dir($this->basePath . self::PROCESSED_FOLDER)) {
            $objects = scandir($this->basePath . self::PROCESSED_FOLDER);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($this->basePath . self::PROCESSED_FOLDER . "/" . $object))
                        rmdir($this->basePath . self::PROCESSED_FOLDER . "/" . $object);
                    else
                        unlink($this->basePath . self::PROCESSED_FOLDER . "/" . $object);
                }
            }
            rmdir($this->basePath . self::PROCESSED_FOLDER);
        }
    }

}