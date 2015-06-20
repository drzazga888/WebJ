function Audio(name) {
    this.name = name;
    this.loadBuffer();
}

Audio.prototype.showButton = function(buffer) {
    this.buffer = buffer;
    this.handler = $('<div class="button" draggable="true">' + this.name + '</div>');
    this.handler[0].addEventListener("click", Audio.events.click, true);
    this.handler[0].addEventListener("dragstart", Audio.events.dragstart, true);
    this.handler[0].addEventListener("dragend", Audio.events.dragend, true);
    this.handler.appendTo(Audio.wrapper);
};

Audio.prototype.loadBuffer = function() {
    var request = new XMLHttpRequest();
    var audio = this;
    request.open('GET', this.convertToUrl(), true);
    request.responseType = 'arraybuffer';
    request.onload = function() {
        Audio.ctx.decodeAudioData(request.response, function(buffer) {
            audio.showButton(buffer);
        });
    };
    request.send();
};

Audio.prototype.convertToUrl = function() {
    return "/userdata/share/" + this.name.replace(/ /g, '_').toLowerCase() + ".wav";
};

Audio.events = {

    dragstart: function(event) {
        var droppedAudio = Mixer.audios[event.target.innerHTML];
        Mixer.draggedSample = new Sample(0, Number(droppedAudio.buffer.duration) * 30, droppedAudio);
        event.dataTransfer.setData("text/plain", "");
        event.dataTransfer.effectAllowed = "move";
        event.dataTransfer.dropEffect = "move";
        Track.timelinesWrapper.addClass("dragging");
    },

    dragend: function(event) {
        Track.timelinesWrapper.removeClass("dragging");
    },

    click: function(event) {
        var source = Audio.ctx.createBufferSource();
        source.buffer = Mixer.audios[event.target.innerHTML].buffer;
        source.connect(Audio.ctx.destination);
        source.start();
    }

};

Audio.ctx = new AudioContext();
Audio.wrapper = null;