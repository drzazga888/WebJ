function Audio(name) {
    this.name = name;
    this.buffer = null;
}

Audio.ctx = new AudioContext();