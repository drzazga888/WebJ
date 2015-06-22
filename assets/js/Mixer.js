$(document).ready(function() {

    // sample data
    Mixer(15, 80);
    var audio1 = new Audio("Beautiful Touch Pad Trap");
    var audio2 = new Audio("Bottem Shelf Drums");
    var audio3 = new Audio("Somedaydreams Chillout Guitars V2");
    Mixer.addAudio(audio1);
    Mixer.addAudio(audio2);
    Mixer.addAudio(audio3);
    var sample1 = new Sample(3, 1, 3, audio1, Mixer.pixelsPerSecond, 0);
    var sample2 = new Sample(0, 0, 2, audio2, Mixer.pixelsPerSecond, 1);
    var sample3 = new Sample(7, 2, 4, audio3, Mixer.pixelsPerSecond, 2);
    var track1 = new Track();
    var track2 = new Track();
    track1.addSample(sample1);
    track1.addSample(sample3);
    track2.addSample(sample2);
    Mixer.addTrack(track1);
    Mixer.addTrack(track2);

});

function Mixer(timelineDuration, pixelsPerSecond) {

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
        if (!Mixer.isPlaying) {
            Mixer.play();
            Mixer.trackTimelinesDom.addClass("dragging");
        }
        else {
            Mixer.pause();
            Mixer.trackTimelinesDom.removeClass("dragging");
        }
    });
    Mixer.timelineDurationChanger.on("change", function(event) {
        Mixer.setTimelineDuration(event.target.value);
    });

    // setting params
    Mixer.setTimelineDuration(timelineDuration);
    Mixer.setPixelsPerSecond(pixelsPerSecond);
    Mixer.timelineDurationChanger.val(timelineDuration);

}

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
    this.tracks[id] = null;
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
    Mixer.trackTimelinesDom.css( {
        backgroundImage: "repeating-linear-gradient(\
            to right,\
            transparent,\
            transparent " + (pixelsPerSecond - 1) + "px,\
            rgba(20%, 50%, 80%, 0.7) " + pixelsPerSecond + "px\
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
    Mixer.isPlaying = false;
};

Mixer.actualizeTimes = function() {
    Mixer.timeLabels.children(".time-item").remove();
    var t = 0;
    while (t < Mixer.timelineDuration) {
        var timeItem = $('<div class="time-item"></div>');
        timeItem.css("left", (t * Mixer.pixelsPerSecond) + "px");
        timeItem.text(t + "s");
        timeItem.appendTo(Mixer.timeLabels);
        ++t;
    }
};

Mixer.tracks = [];
Mixer.audios = [];
Mixer.pixelsPerSecond = 0;
Mixer.timelineDuration = 0;
Mixer.draggedSample = null;
Mixer.isPlaying = false;