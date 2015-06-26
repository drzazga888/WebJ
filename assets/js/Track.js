function Track(id, name) {
    this.samples = [];
    if (id !== undefined)
        this.id = id;
    else
        this.assignId();
    if (name !== undefined)
        this.name = name;
    else
        this.name = "Track #" + this.id;
    this.headDom = $('<div data-id="' + this.id + '" class="track track-head">' +
        '<input type="text" class="name" value="' + this.name + '" required />' +
        '<p class="icon-cancel track-deleter">Usuń</p>' +
        '</div>');
    this.timelineDom = $('<div data-id="' + this.id + '" class="track timeline"></div>');
    this.timelineDom[0].addEventListener("dragenter", Track.events.dragenter, true);
    this.timelineDom[0].addEventListener("dragleave", Track.events.dragleave, true);
    this.timelineDom[0].addEventListener("dragover", Track.events.dragover, true);
    this.timelineDom[0].addEventListener("drop", Track.events.drop, true);
    this.headDom.find(".name")[0].addEventListener("change", Track.events.changeName, true);
}

Track.prototype.shorten = function() {
    var obj = {
        id: this.id,
        name: this.name,
        samples: []
    };
    for (var i = 0; i < this.samples.length; ++i) {
        if (this.samples[i] !== undefined)
            obj.samples.push(this.samples[i].shorten());
        else
            obj.samples.push(undefined);
    }
    return obj;
};

Track.enlarge = function(obj) {
    var track;
    if (!obj.id && !obj.name)
        track = new Track(obj.id, obj.name);
    else
        track = new Track();
    for (var i = 0; i < obj.samples.length; ++i) {
        var sample = Sample.enlarge(obj.samples[i], Mixer.pixelsPerSecond);
        track.samples.push(sample);
        track.timelineDom.append(sample.dom);
    }
    return track;
};

Track.prototype.assignId = function() {
    if (this.id !== undefined)
        return;
    var i = 0;
    while (Mixer.tracks[i] !== undefined)
        ++i;
    this.id = i;
};

Track.prototype.addSample = function(sample) {
    this.samples.push(sample);
    this.timelineDom.append(sample.dom);
};

Track.prototype.removeSample = function(sample) {
    for (var i = 0; i < this.samples.length; ++i) {
        if (this.samples[i] === sample) {
            this.samples.splice(i, 1);
            return;
        }
    }
    throw Error("Nie można usunąć sampla ID = " +
        sample.id +
        " z tracku ID = " +
        this.id +
        " i nazwie " +
        this.name +
        ", gdyż go tam nie ma");
};

Track.events = {

    dragenter: function(event) {
        $(event.target).addClass("emphase");
        var track = Track.getTrack(event.target);
        Mixer.draggedSample.dom.appendTo(track.timelineDom);
    },

    dragover: function(event) {
        event.preventDefault();
        var elemLayerX = event.dataTransfer.getData("text/plain");
        Mixer.draggedSample.setWhen((event.layerX - elemLayerX) / Mixer.draggedSample.pixelsPerSecond);
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
    },

    changeName: function(event) {
        var track = Track.getTrack(event.target);
        track.name = event.target.value;
    }

};

Track.getTrack = function(dom) {
    return Mixer.tracks[$(dom).closest(".track").data("id")];
};