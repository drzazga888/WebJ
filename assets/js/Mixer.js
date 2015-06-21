$(document).ready(function() {

    // dom init
    var tracksDom = $("#tracks");
    Mixer.trackHeadsDom = tracksDom.children("aside");
    Mixer.trackTimelinesDom = tracksDom.find(".timelines");
    Mixer.audiosDom = $("#audios");
    Mixer.newTrackButton = $("#new-track");

    // events
    Mixer.newTrackButton.on("click", function() {
        Mixer.addTrack(new Track());
    });
    Mixer.trackHeadsDom.on("click", ".track-deleter", function(event) {
        Mixer.removeTrack($(this).closest(".track").data("id"));
    });

    // sample data
    Mixer.setPixelsPerSecond(30);
    Mixer.setTimelineDuration(15);
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

function jebnij(komunikat) {
    console.log("-<>--<>--<>--<>--<>-");
    console.warn(komunikat);
    console.log("Mixer.draggedSample", Mixer.draggedSample);
    console.log("Sample.collection", Sample.collection);
    console.log("track.samples");
    for (var i = 0; i < Track.collection.length; ++i) {
        console.log("i", i, "track.samples", Track.collection[i].samples);
    }
}

var Mixer = {};

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
    this.tracks[id] = null;
};

Mixer.setTimelineDuration = function(duration) {
    Mixer.timelineDuration = duration;
    Mixer.trackTimelinesDom.css( {
        width: duration * Mixer.pixelsPerSecond + "px"
    });
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
    for (var i = 0; i < Sample.collection.length; ++i)
        Sample.collection[i].changeScale(pixelsPerSecond);
};

Mixer.tracks = [];
Mixer.audios = [];
Mixer.pixelsPerSecond = 0;
Mixer.timelineDuration = 0;
Mixer.trackHeadsDom = null;
Mixer.trackTimelinesDom = null;
Mixer.audiosDom = null;
Mixer.newTrackButton = null;
Mixer.draggedSample = null;