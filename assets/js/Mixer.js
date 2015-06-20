$(document).ready(function() {

    var tracksDom = $("#tracks");
    Mixer.trackHeadsDom = tracksDom.children("aside");
    Mixer.trackTimelinesDom = tracksDom.find(".timelines");
    Mixer.audiosDom = $("#audios");
    Mixer.newTrackButton = $("#newTrack");
    Mixer.setPixelsPerSecond(60);
    Mixer.setTimelineLength(15);
    var audio1 = new Audio("Beautiful Touch Pad Trap");
    var audio2 = new Audio("Bottem Shelf Drums");
    var audio3 = new Audio("Somedaydreams Chillout Guitars V2");
    Mixer.addAudio(audio1);
    Mixer.addAudio(audio2);
    Mixer.addAudio(audio3);
    var sample1 = new Sample(3, 1, 3, audio1, Mixer.pixelsPerSecond);
    var sample2 = new Sample(0, 0, 2, audio2, Mixer.pixelsPerSecond);
    var sample3 = new Sample(7, 2, 4, audio3, Mixer.pixelsPerSecond);
    var track1 = new Track();
    var track2 = new Track();
    track1.addSample(sample1);
    track1.addSample(sample3);
    track2.addSample(sample2);
    Mixer.addTrack(track1);
    Mixer.addTrack(track2);

});

var Mixer = {};

Mixer.addAudio = function(audio) {
    this.audios[audio.name] = audio;
    audio.dom.appendTo(Mixer.audiosDom);
};

Mixer.addTrack = function(track) {
    this.tracks.push(track);
    track.headDom.appendTo(Mixer.trackHeadsDom);
    track.timelineDom.appendTo(Mixer.trackTimelinesDom);
};

Mixer.setTimelineLength = function(duration) {
    Mixer.trackTimelinesDom.css( {
        width: duration * Mixer.pixelsPerSecond + "px"
    });
};

Mixer.setPixelsPerSecond = function(pixelsPerSecond) {
    Mixer.pixelsPerSecond = Number(pixelsPerSecond);
    Mixer.trackTimelinesDom.css( {
        backgroundImage: "repeating-linear-gradient(\
            to right,\
            transparent,\
            transparent " + (pixelsPerSecond - 1) + "px,\
            rgba(20%, 50%, 80%, 0.7) " + pixelsPerSecond + "px\
        )"
    });
};

Mixer.tracks = [];
Mixer.audios = [];
Mixer.pixelsPerSecond = Number(0);
Mixer.trackHeadsDom = null;
Mixer.trackTimelinesDom = null;
Mixer.audiosDom = null;
Mixer.newTrackButton = null;