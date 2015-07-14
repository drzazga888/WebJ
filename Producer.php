<?php

class Producer
{

    private $producerPath = "sox";
    private $mergingOption = "-m";
    private $extension = "wav";
    private $output = "userdata/user_{id}/mixed_audio.wav";
    private $audiosFolder = "userdata/user_{id}/audios";
    private $trimmedFolder = "userdata/user_{id}/samples_trimmed";
    private $paddedFolder = "userdata/user_{id}/samples_padded";
    private $samples;
    private $audios;
    private $pattern;

    public function __construct(array $pattern, array $audios) {
        $this->audios = $audios;
        $this->pattern = $pattern;
        $this->samples = $this->deflateTracks();
        $this->output = str_replace('{id}', $_SESSION["user_id"], $this->output);
        $this->audiosFolder = str_replace('{id}', $_SESSION["user_id"], $this->audiosFolder);
        $this->trimmedFolder = str_replace('{id}', $_SESSION["user_id"], $this->trimmedFolder);
        $this->paddedFolder = str_replace('{id}', $_SESSION["user_id"], $this->paddedFolder);
    }

    public function make() {
        var_dump($this->samples);
        echo "<br>";
        if (!file_exists($this->trimmedFolder))
            mkdir($this->trimmedFolder);
        if (!file_exists($this->paddedFolder))
            mkdir($this->paddedFolder);
        foreach ($this->samples as $sample) {
            $this->trim($sample);
            $this->pad($sample);
        }
        $this->merge();
    }

    public function download() {
        header('Content-Description: File Transfer');
        header('Content-Type: audio/wav');
        header('Content-Disposition: attachment; filename=' . str_replace(" ", "_", $this->pattern["name"]) . ".wav");
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($this->output));
        readfile($this->output);
    }

    private function trim($sample) {
        $cmd = $this->producerPath .
            ' ' . $this->audiosFolder . '/' . $this->audioOfSample($sample)["filename"] . '.' . $this->extension .
            ' ' . $this->trimmedFolder . '/' . $sample["id"] . '.' . $this->extension .
            ' trim ' . $sample['offset'] . ' ' . $sample['duration'];
        echo $cmd . "<br>";
        return system($cmd);
    }

    private function pad($sample) {
        $cmd = $this->producerPath .
            ' ' . $this->trimmedFolder . '/' . $sample["id"] . '.' . $this->extension .
            ' ' . $this->paddedFolder . '/' . $sample["id"] . '.' . $this->extension .
            ' pad ' . $sample['when'];
        echo $cmd . "<br>";
        return system($cmd);
    }

    private function merge() {
        $cmd = $this->producerPath . ' ' . $this->mergingOption;
        foreach ($this->samples as $sample)
            $cmd .= ' ' . $this->paddedFolder . '/' . $sample["id"] . '.' . $this->extension;
        $cmd .= ' ' . $this->output;
        echo $cmd . "<br>";
        return system($cmd);
    }

    private function audioOfSample($sample) {
        foreach ($this->audios as $audio) {
            if ($audio["id"] == $sample["audioId"])
                return $audio;
        }
        throw new Exception("no audio found related to sample");
    }

    private function deflateTracks() {
        $samples = array();
        foreach ($this->pattern["tracks"] as $track) {
            foreach ($track["samples"] as $sample)
                $samples[] = $sample;
        }
        return $samples;
    }

}