function Track() {
    this.samples = [];
    this.nr = ++Track.trackNumber;
    this.headHandler = $('<div class="track-head">Track <em>' + this.nr + '</em></div>');
    this.timelineHandler = $('<div class="timeline"></div>');
    this.timelineHandler[0].addEventListener("dragenter", Track.events.dragenter, true);
    this.timelineHandler[0].addEventListener("dragleave", Track.events.dragleave, true);
    this.timelineHandler[0].addEventListener("dragover", Track.events.dragover, true);
    this.timelineHandler[0].addEventListener("drop", Track.events.drop, true);
    this.headHandler.appendTo(Track.headsWrapper);
    this.timelineHandler.appendTo(Track.timelinesWrapper);
}

Track.prototype.addSample = function(sample) {
    this.samples.push(sample);
    this.timelineHandler.append(sample.handler);
};

Track.prototype.removeSample = function(sample) {
    sample.handler.remove();
    this.samples.splice(this.samples.indexOf(sample), 1);
};

Track.prototype.getSample = function(id) {
    for (var i = 0; i < this.samples.length; ++i) {
        if (this.samples[i].id === id)
            return this.samples[i];
    }
    return null;
};

Track.events = {

    dragenter: function(event) {
        $(event.target).addClass("emphase");
        Mixer.tracks[$(event.target).index()].addSample(Mixer.draggedSample);
    },

    dragover: function(event) {
        event.preventDefault();
        Mixer.draggedSample.move(event.layerX - (Mixer.draggedSample.length * 0.5));
    },

    dragleave: function(event) {
        $(event.target).removeClass("emphase");
        Mixer.tracks[$(event.target).index()].removeSample(Mixer.draggedSample);
    },

    drop: function(event) {
        $(event.target).removeClass("emphase");
        Mixer.draggedSample = null;
    }

};

Track.trackNumber = 0;
Track.wrapper = null;
Track.headsWrapper = null;
Track.timelinesWrapper = null;