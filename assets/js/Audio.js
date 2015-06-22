function Audio(name) {
    this.name = name;
    this.id = Audio.counter++;
    this.source = null;
    this.buffer = null;
    this.dom = $('<div data-id="' + this.id + '" class="audio button icon-play" draggable="true">' + this.name + '</div>');
    this.dom[0].addEventListener("click", Audio.events.click, true);
    this.dom[0].addEventListener("dragstart", Audio.events.dragstart, true);
    this.dom[0].addEventListener("dragend", Audio.events.dragend, true);
    Audio.collection[this.id] = this;
    this.loadBuffer();
}

Audio.prototype.loadBuffer = function() {
    var request = new XMLHttpRequest();
    var audio = this;
    request.open('GET', this.convertToUrl(), true);
    request.responseType = 'arraybuffer';
    request.onload = function() {
        Audio.ctx.decodeAudioData(request.response, function(buffer) {
            audio.buffer = buffer;
        });
    };
    request.send();
};

Audio.prototype.convertToUrl = function() {
    return "/userdata/share/" + this.name.replace(/ /g, '_').toLowerCase() + ".wav";
};

Audio.prototype.play = function() {
    if (!this.buffer || this.source)
        return;
    this.source = Audio.ctx.createBufferSource();
    this.source.buffer = this.buffer;
    this.source.connect(Audio.ctx.destination);
    var dom = this.dom;
    dom.removeClass("icon-play").addClass("icon-pause");
    this.timeoutId = window.setTimeout(function() {
        dom.removeClass("icon-pause").addClass("icon-play");
    }, this.buffer.duration * 1000);
    this.source.start(0);
};

Audio.prototype.pause = function() {
    if (!this.buffer || !this.source)
        return;
    this.dom.removeClass("icon-pause").addClass("icon-play");
    window.clearInterval(this.timeoutId);
    this.source.stop();
    this.source = null;
};

Audio.getAudio = function(dom) {
    return Audio.collection[$(dom).closest(".audio").data("id")];
};

Audio.events = {

    dragstart: function(event) {
        var audio = Audio.getAudio(event.target);
        if (!audio.buffer)
            return false;
        Mixer.draggedSample = new Sample(0, 0, audio.buffer.duration, audio, Mixer.pixelsPerSecond);
        event.dataTransfer.setData("text/plain", "");
        event.dataTransfer.effectAllowed = "move";
        event.dataTransfer.dropEffect = "move";
        Mixer.trackTimelinesDom.addClass("dragging");
    },

    dragend: function(event) {
        Mixer.trackTimelinesDom.removeClass("dragging");
        Mixer.draggedSample = null;
    },

    click: function(event) {
        var audio = Audio.getAudio(event.target);
        if (!audio.source)
            audio.play();
        else
            audio.pause();
    }

};

Audio.collection = [];
Audio.ctx = new AudioContext();
Audio.counter = 0;