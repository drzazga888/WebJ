function Audio(name) {
    this.name = name;
    this.id = Audio.counter++;
    this.source = null;
    this.buffer = null;
    this.dom = $('<div data-id=' + this.id + ' class="audio button" draggable="true"><span class="icon-play">' + this.name + '</span></div>');
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

Audio.prototype.play = function(when, offset, duration) {
    if (!this.buffer || this.source)
        return;
    if (duration === undefined)
        duration = this.buffer.duration;
    this.source = Audio.ctx.createBufferSource();
    this.source.buffer = this.buffer;
    this.source.loop = true;
    this.source.connect(Audio.ctx.destination);
    var audio = this;
    this.waitingTimeoutID = window.setTimeout(function() {
        audio.source.start(0, offset);
        audio.playingTimeoutID = window.setTimeout(function() {
            audio.pause();
        }, duration * 1000);
    }, when * 1000);

};

Audio.prototype.pause = function() {
    if (!this.buffer || !this.source)
        return;
    this.source.stop();
    window.clearTimeout(this.playingTimeoutID);
    window.clearTimeout(this.waitingTimeoutID);
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
        if (!audio.buffer)
            return;
        if (!audio.source) {
            audio.dom.find(".icon-play").removeClass("icon-play").addClass("icon-pause");
            audio.play();
            audio.showingButtonTimeoutID = window.setTimeout(function() {
                audio.dom.find(".icon-pause").removeClass("icon-pause").addClass("icon-play");
            }, audio.buffer.duration * 1000);
        } else {
            audio.dom.find(".icon-pause").removeClass("icon-pause").addClass("icon-play");
            audio.pause();
            window.clearTimeout(audio.showingButtonTimeoutID);
        }
    }

};

Audio.collection = [];
Audio.ctx = new AudioContext();
Audio.counter = 0;