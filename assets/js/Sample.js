function Sample(when, offset, duration, audio, pixelsPerSecond, id) {
    this.isPlaying = false;
    this.audio = audio;
    this.dom = $('<div draggable="true" class="sample">' +
        '<p>' + this.audio.name + '</p>' +
        '<p>start [s]: <em><input type="number" step="0.01" min="0" max="999.99" class="offset" /></em></p>' +
        '<p>dlugość [s]: <em><input type="number" step="0.01" min="0" max="999.99" class="duration" /></em></p>' +
        '</div>');
    if (id !== undefined) {
        this.id = id;
        this.dom.data("id", id);
        Sample.collection[this.id] = this;
        if (id + 1 > Sample.counter)
            Sample.counter = id + 1;
    }
    this.pixelsPerSecond = pixelsPerSecond;
    this.setWhen(when);
    this.setOffset(offset);
    this.setDuration(duration);
    this.dom[0].addEventListener("dragstart", Sample.events.dragstart, true);
    this.dom[0].addEventListener("dragend", Sample.events.dragend, true);
    this.dom.find(".duration")[0].addEventListener("change", Sample.events.changeDuration, true);
    this.dom.find(".offset")[0].addEventListener("change", Sample.events.changeOffset, true);
}

Sample.prototype.assignId = function() {
    if (this.id !== undefined)
        return;
    this.id = Sample.counter++;
    this.dom.data("id", this.id);
};

Sample.prototype.play = function() {
    if (!this.audio.buffer || this.isPlaying)
        return;
    this.source = Audio.ctx.createBufferSource();
    this.source.buffer = this.audio.buffer;
    this.source.loop = true;
    this.source.connect(Audio.ctx.destination);
    var sample = this;
    this.source.onended = function() {
        sample.isPlaying = false;
    };
    this.startTimeout = window.setTimeout(function() {
        sample.isPlaying = true;
        sample.source.start(0, sample.offset);
        sample.stopTimeout = window.setTimeout(function() {
            sample.pause();
        }, sample.duration * 1000);
    }, sample.when * 1000);
};

Sample.prototype.pause = function() {
    window.clearTimeout(this.stopTimeout);
    window.clearTimeout(this.startTimeout);
    if (!this.audio.buffer || !this.isPlaying)
        return;
    if (this.source !== undefined) {
        this.source.stop();
        this.source = undefined;
    }
    this.isPlaying = false;
};

Sample.prototype.setWhen = function(when) {
    this.when = when > 0 ? when : 0;
    this.dom.css("left", (this.when * this.pixelsPerSecond) + "px");
};

Sample.prototype.setOffset = function(offset) {
    offset = Number(offset);
    if (isNaN(offset))
        return;
    this.offset = offset;
    this.dom.find(".offset").val(offset.toFixed(2));
};

Sample.prototype.setDuration = function(duration) {
    if (isNaN(duration))
        return;
    this.duration = duration;
    this.dom.css("width", (this.duration * this.pixelsPerSecond) + "px");
    this.dom.find(".duration").val(Number(duration).toFixed(2));
};

Sample.prototype.changeScale = function(pixelsPerSecond) {
    this.pixelsPerSecond = pixelsPerSecond;
    this.dom.css("width", (this.duration * this.pixelsPerSecond) + "px");
    this.dom.css("left", (this.when * this.pixelsPerSecond) + "px");
};

Sample.getSample = function(dom) {
    return Sample.collection[$(dom).closest(".sample").data("id")];
};

Sample.events = {

    dragstart: function(event) {
        Mixer.draggedSample = Sample.getSample(event.target);
        Sample.collection[Mixer.draggedSample.id] = undefined;
        var track = Track.getTrack($(event.target).closest(".track"));
        track.samples[Mixer.draggedSample.id] = undefined;
        event.dataTransfer.setData("text/plain", "");
        event.dataTransfer.effectAllowed = "move";
        event.dataTransfer.dropEffect = "move";
        Mixer.trackTimelinesDom.addClass("dragging");
    },

    dragend: function(event) {
        Mixer.trackTimelinesDom.removeClass("dragging");
        Mixer.draggedSample = null;
    },

    changeDuration: function(event) {
        var sample = Sample.getSample(event.target);
        sample.setDuration(event.target.value);
    },

    changeOffset: function(event) {
        var sample = Sample.getSample(event.target);
        sample.setOffset(event.target.value);
    }

};

Sample.counter = 0;
Sample.collection = [];