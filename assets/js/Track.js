function Track() {
    this.samples = [];
    this.id = Track.counter++;
    this.name = "Track #<em>" + this.id + "</em>";
    this.headDom = $('<div data-id="' + this.id + '" class="track track-head">' + this.name + '</div>');
    this.timelineDom = $('<div data-id="' + this.id + '" class="track timeline"></div>');
    this.timelineDom[0].addEventListener("dragenter", Track.events.dragenter, true);
    this.timelineDom[0].addEventListener("dragleave", Track.events.dragleave, true);
    this.timelineDom[0].addEventListener("dragover", Track.events.dragover, true);
    this.timelineDom[0].addEventListener("drop", Track.events.drop, true);
    Track.collection[this.id] = this;
}

Track.prototype.addSample = function(sample) {
    this.samples[sample.id] = sample;
    this.timelineDom.append(sample.dom);
};

Track.prototype.removeSample = function(id) {
    this.samples[id].dom.remove();
    this.samples[id] = null;
};

Track.events = {

    dragenter: function(event) {
        $(event.target).addClass("emphase");
    },

    dragover: function(event) {
        event.preventDefault();
    },

    dragleave: function(event) {
        $(event.target).removeClass("emphase");
    },

    drop: function(event) {
        $(event.target).removeClass("emphase");
    }

};

Track.getTrack = function(dom) {
    return Track.collection[$(dom).closest(".track").data("id")];
};

Track.counter = 0;
Track.collection = [];