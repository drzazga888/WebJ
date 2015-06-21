function Sample(when, offset, duration, audio, pixelsPerSecond, id) {
    this.audio = audio;
    this.dom = $('<div draggable="true" class="sample">' + this.audio.name + '</div>');
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
}

Sample.prototype.assignId = function() {
    if (this.id !== undefined)
        return;
    this.id = Sample.counter++;
    this.dom.data("id", this.id);
};

Sample.prototype.play = function(cursorPos) {
    this.audio.play(this.when - cursorPos, this.offset, this.duration);
};

Sample.prototype.setWhen = function(when) {
    this.when = when > 0 ? when : 0;
    this.dom.css("left", (this.when * this.pixelsPerSecond) + "px");
};

Sample.prototype.setOffset = function(offset) {
    this.offset = offset;
};

Sample.prototype.setDuration = function(duration) {
    this.duration = duration;
    this.dom.css("width", (this.duration * this.pixelsPerSecond) + "px");
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
    }

};

Sample.counter = 0;
Sample.collection = [];