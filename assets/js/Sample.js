function Sample(start, stop, audio) {
    this.start = start;
    this.stop = stop;
    this.audio = audio;
}

Sample.prototype.play = function() {
    console.log("gram", audio);
};