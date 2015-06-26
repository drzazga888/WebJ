$(document).ready(function() {

    Mixer.init(80);
    Mixer.parse(content);

});

var Mixer = {};

Mixer.init = function(pixelsPerSecond) {

    // dom init
    var tracksDom = $("#tracks");
    Mixer.trackHeadsDom = tracksDom.children("aside");
    Mixer.trackTimelinesDom = tracksDom.find(".timelines");
    Mixer.audiosDom = $("#audios");
    Mixer.newTrackButton = $("#new-track");
    Mixer.zoomIn = $("#zoom-in");
    Mixer.zoomOut = $("#zoom-out");
    Mixer.playPauseButton = $("#play");
    Mixer.timeLabels = tracksDom.find(".time");
    Mixer.pipe = tracksDom.find(".pipe");
    Mixer.timelineDurationChanger = $("#timeline-duration");
    Mixer.mixerName = $("#mixer-name");

    // events
    Mixer.newTrackButton.on("click", function() {
        Mixer.addTrack(new Track());
    });
    Mixer.trackHeadsDom.on("click", ".track-deleter", function(event) {
        Mixer.removeTrack($(this).closest(".track").data("id"));
    });
    Mixer.zoomIn.on("click", function(event) {
        Mixer.setPixelsPerSecond(Mixer.pixelsPerSecond * 1.6);
    });
    Mixer.zoomOut.on("click", function(event) {
        Mixer.setPixelsPerSecond(Mixer.pixelsPerSecond * 0.625);
    });
    Mixer.playPauseButton.on("click", function(event) {
        if (!Mixer.isPlaying)
            Mixer.play();
        else
            Mixer.pause();
    });
    Mixer.timelineDurationChanger.on("change", function(event) {
        Mixer.setTimelineDuration(event.target.value);
    });
    Mixer.mixerName.on("change", function(event) {
        Mixer.setName(event.target.value);
    });

    // setting params
    Mixer.setPixelsPerSecond(pixelsPerSecond);
    Mixer.setName("bez nazwy");

};

Mixer.stringify = function() {
    var obj = {
        timelineDuration: this.timelineDuration,
        name: this.name,
        audios: [],
        tracks: []
    };
    var i;
    for (i = 0; i < this.audios.length; ++i) {
        if (this.audios[i] !== undefined)
            obj.audios.push(this.audios[i].shorten());
        else
            obj.audios.push(undefined);
    }
    for (i = 0; i < this.tracks.length; ++i) {
        if (this.tracks[i] !== undefined)
            obj.tracks.push(this.tracks[i].shorten());
        else
            obj.tracks.push(undefined);
    }
    return JSON.stringify(obj);
};

Mixer.parse = function(stringified) {
    var obj = JSON.parse(stringified);
    var i;
    for (i = 0; i < obj.audios.length; ++i)
        Mixer.addAudio(Audio.enlarge(obj.audios[i]));
    for (i = 0; i < obj.tracks.length; ++i)
        Mixer.addTrack(Track.enlarge(obj.tracks[i]));
    Mixer.setTimelineDuration(obj.timelineDuration);
    Mixer.timelineDurationChanger.val(obj.timelineDuration);
    Mixer.setName(obj.name);
};

Mixer.addAudio = function(audio) {
    this.audios[audio.id] = audio;
    audio.dom.appendTo(Mixer.audiosDom);
};

Mixer.addTrack = function(track) {
    this.tracks[track.id] = track;
    track.headDom.appendTo(Mixer.trackHeadsDom);
    track.timelineDom.appendTo(Mixer.trackTimelinesDom);
};

Mixer.removeTrack = function(id) {
    this.tracks[id].headDom.remove();
    this.tracks[id].timelineDom.remove();
    for (var i = 0; i < this.tracks[id].samples.length; ++i) {
        for (var j = 0; j < Sample.collection.length; ++j) {
            if (this.tracks[id].samples[i] === Sample.collection[j]) {
                Sample.collection[j] = undefined;
                break;
            }
        }
    }
    this.tracks[id] = undefined;
};

Mixer.setTimelineDuration = function(duration) {
    Mixer.timelineDuration = duration;
    Mixer.trackTimelinesDom.css( {
        width: duration * Mixer.pixelsPerSecond + "px"
    });
    Mixer.actualizeTimes();
};

Mixer.setPixelsPerSecond = function(pixelsPerSecond) {
    Mixer.pixelsPerSecond = pixelsPerSecond;
    var incrementValue = Math.ceil(30 / Mixer.pixelsPerSecond);
    Mixer.trackTimelinesDom.css( {
        backgroundImage: "repeating-linear-gradient(\
            to right,\
            transparent,\
            transparent " + ((incrementValue * pixelsPerSecond) - 1) + "px,\
            rgba(20%, 50%, 80%, 0.7) " + (incrementValue * pixelsPerSecond) + "px\
        )"
    });
    Mixer.trackTimelinesDom.css( {
        width: (Mixer.timelineDuration * Mixer.pixelsPerSecond) + "px"
    });
    for (var i = 0; i < Sample.collection.length; ++i) {
        if (Sample.collection[i] !== undefined)
            Sample.collection[i].changeScale(pixelsPerSecond);
    }
    Mixer.actualizeTimes();
};

Mixer.play = function() {
    Mixer.isPlaying = true;
    for (var i = 0; i < Sample.collection.length; ++i) {
        if (Sample.collection[i] !== undefined)
            Sample.collection[i].play(0);
    }
    Mixer.pipe.css("left", 0);
    Mixer.pipe.show();
    Mixer.playPauseButton.removeClass("icon-play").addClass("icon-pause");
    Mixer.trackTimelinesDom.addClass("dragging");
    var t = 0;
    Mixer.pipeInterval = window.setInterval(function() {
        Mixer.pipe.css("left", (t * Mixer.pixelsPerSecond) + "px");
        t += 0.05;
    }, 50);
    Mixer.stopTimeout = window.setTimeout(function() {
        Mixer.pause();
    }, Mixer.timelineDuration * 1000);
};

Mixer.pause = function() {
    window.clearTimeout(Mixer.stopTimeout);
    window.clearInterval(Mixer.pipeInterval);
    for (var i = 0; i < Sample.collection.length; ++i) {
        if (Sample.collection[i] !== undefined)
            Sample.collection[i].pause();
    }
    Mixer.pipe.hide();
    Mixer.playPauseButton.removeClass("icon-pause").addClass("icon-play");
    Mixer.trackTimelinesDom.removeClass("dragging");
    Mixer.isPlaying = false;
};

Mixer.actualizeTimes = function() {
    Mixer.timeLabels.children(".time-item").remove();
    var t = 0;
    var incrementValue = Math.ceil(30 / Mixer.pixelsPerSecond);
    while (t < Mixer.timelineDuration) {
        var timeItem = $('<div class="time-item"></div>');
        timeItem.css("left", (t * Mixer.pixelsPerSecond) + "px");
        timeItem.text(t + "s");
        timeItem.appendTo(Mixer.timeLabels);
        t += incrementValue;
    }
};

Mixer.setName = function(name) {
    Mixer.mixerName.val(name);
    Mixer.name = name;
};

Mixer.tracks = [];
Mixer.audios = [];
Mixer.pixelsPerSecond = 0;
Mixer.timelineDuration = 0;
Mixer.draggedSample = null;
Mixer.name = null;
Mixer.isPlaying = false;