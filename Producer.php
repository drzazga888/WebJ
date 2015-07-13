<?php

class Producer
{

    private $producerPath = "sox";
    private $mergingOption = "-m";
    private $extension = "wav";
    private $output = "userdata/user_{id}/mixed_audio.wav";
    private $audiosFolder = "userdata/user_{id}/audios";
    private $audios;
    private $pattern;

    public function __construct(array $pattern, array $audios) {
        $this->audios = $audios;
        $this->pattern = $pattern;
        $this->output = str_replace('{id}', $_SESSION["user_id"], $this->output);
        $this->audiosFolder = str_replace('{id}', $_SESSION["user_id"], $this->audiosFolder);
    }

    public function make() {
        system($this->prepareMergingCommand());
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

    private function prepareMergingCommand() {
        $cmd = $this->producerPath . ' ' . $this->mergingOption;
        $samples = $this->deflateTracks();
        foreach ($samples as $sample) {
            $targetAudio = null;
            foreach ($this->audios as $audio) {
                if ($audio["id"] == $sample["audioId"]) {
                    $targetAudio = $audio;
                    break;
                }
            }
            if ($targetAudio === null)
                throw new Exception("no audio found related to sample");
            $cmd .= ' ' . $this->audiosFolder . '/' . $targetAudio["filename"] . "." . $this->extension;
        }
        $cmd .= ' ' . $this->output;
        return $cmd;
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