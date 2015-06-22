function Track() {
    this.samples = [];
    this.id = Track.counter++;
    this.name = "Track #<em>" + this.id + "</em>";
    this.headDom = $('<div data-id="' + this.id + '" class="track track-head">' +
        '<h4>' + this.name + '</h4>' +
        '<p class="icon-cancel track-deleter">Usuń</p>' +
        '</div>');
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

Track.events = {

    dragenter: function(event) {
        $(event.target).addClass("emphase");
        var track = Track.getTrack(event.target);
        Mixer.draggedSample.dom.appendTo(track.timelineDom);
    },

    dragover: function(event) {
        event.preventDefault();
        Mixer.draggedSample.setWhen((event.layerX / Mixer.draggedSample.pixelsPerSecond) - (Mixer.draggedSample.duration * 0.5));
    },

    dragleave: function(event) {
        $(event.target).removeClass("emphase");
        Mixer.draggedSample.dom.remove();
        Mixer.draggedSample.dom.data("id", Mixer.draggedSample.id);
    },

    drop: function(event) {
        event.preventDefault();
        $(event.target).removeClass("emphase");
        var track = Track.getTrack(event.target);
        if (Mixer.draggedSample.id === undefined)
            Mixer.draggedSample.assignId();
        track.addSample(Mixer.draggedSample);
        Sample.collection[Mixer.draggedSample.id] = Mixer.draggedSample;
        Mixer.draggedSample = null;
    }

};

Track.getTrack = function(dom) {
    return Track.collection[$(dom).closest(".track").data("id")];
};

Track.counter = 0;
Track.collection = [];