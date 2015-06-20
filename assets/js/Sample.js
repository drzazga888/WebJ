function Sample(when, offset, duration, audio, pixelsPerSecond) {
    this.audio = audio;
    this.id = Sample.counter++;
    this.pixelsPerSecond = pixelsPerSecond;
    this.dom = $('<div data-id="' + this.id + '" draggable="true" class="sample">' + this.audio.name + '</div>');
    this.setWhen(when);
    this.setOffset(offset);
    this.setDuration(duration);
    this.dom[0].addEventListener("dragstart", Sample.events.dragstart, true);
    this.dom[0].addEventListener("dragend", Sample.events.dragend, true);
    Sample.collection[this.id] = this;
}

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

Sample.getSample = function(dom) {
    return Sample.collection[$(dom).closest(".sample").data("id")];
};

Sample.events = {

    dragstart: function(event) {
        event.dataTransfer.setData("text/plain", "");
        event.dataTransfer.effectAllowed = "move";
        event.dataTransfer.dropEffect = "move";
    },

    dragend: function(event) {
    }

};

Sample.counter = 0;
Sample.collection = [];