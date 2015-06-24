$(document).ready(function() {

    Mixer.init(80);
    Mixer.parse("{\"timelineDuration\":\"16\",\"name\":\"MakersON, tu jestem!\",\"audios\":[{\"id\":0,\"name\":\"Beautiful Touch Pad Trap\"},{\"id\":1,\"name\":\"Bottem Shelf Drums\"},{\"id\":2,\"name\":\"Somedaydreams Chillout Guitars V2\"},{\"id\":3,\"name\":\"105 Upbeat Kinda\"},{\"id\":4,\"name\":\"Avicci Type Chords\"},{\"id\":5,\"name\":\"Choir Vibes\"},{\"id\":6,\"name\":\"Danke Piano Groovy\"},{\"id\":7,\"name\":\"Exfain Arptime Aminor Garvois\"},{\"id\":8,\"name\":\"Herotime Rotten Dam\"},{\"id\":9,\"name\":\"Hip Hop Drum Loop\"},{\"id\":10,\"name\":\"Jammu Guitar Remake\"},{\"id\":11,\"name\":\"My Fat Cat George Drums\"}],\"tracks\":[{\"id\":0,\"name\":\"Podstawowy loop\",\"samples\":[{\"id\":1,\"audioId\":9,\"when\":0,\"offset\":0,\"duration\":15.999433106575964}]},{\"id\":1,\"name\":\"Przebitki\",\"samples\":[{\"id\":0,\"audioId\":3,\"when\":0,\"offset\":0,\"duration\":\"1.5\"},{\"id\":2,\"audioId\":3,\"when\":3.9875,\"offset\":4,\"duration\":\"1.5\"},{\"id\":3,\"audioId\":4,\"when\":8.025,\"offset\":0,\"duration\":\"8\"}]},{\"id\":2,\"name\":\"Uwaga czyt.\",\"samples\":[{\"id\":6,\"audioId\":7,\"when\":12.99,\"offset\":0,\"duration\":\"0.5\"},{\"id\":5,\"audioId\":7,\"when\":14.01,\"offset\":0,\"duration\":\"0.5\"},{\"id\":8,\"audioId\":7,\"when\":14.995,\"offset\":0,\"duration\":\"0.25\"},{\"id\":7,\"audioId\":7,\"when\":15.495,\"offset\":0,\"duration\":\"0.25\"},{\"id\":4,\"audioId\":5,\"when\":1.94,\"offset\":2,\"duration\":\"2\"},{\"id\":9,\"audioId\":5,\"when\":5.980374149659864,\"offset\":6,\"duration\":\"2\"}]}]}");

    // sample data
    /*Mixer.setName("Przykładowy utwór");
    var audio1 = new Audio("Beautiful Touch Pad Trap", 0);
    var audio2 = new Audio("Bottem Shelf Drums", 1);
    var audio3 = new Audio("Somedaydreams Chillout Guitars V2", 2);
    var audio4 = new Audio("105 Upbeat Kinda", 3);
    var audio5 = new Audio("Avicci Type Chords", 4);
    var audio6 = new Audio("Choir Vibes", 5);
    var audio7 = new Audio("Danke Piano Groovy", 6);
    var audio8 = new Audio("Exfain Arptime Aminor Garvois", 7);
    var audio9 = new Audio("Herotime Rotten Dam", 8);
    var audio10 = new Audio("Hip Hop Drum Loop", 9);
    var audio11 = new Audio("Jammu Guitar Remake", 10);
    var audio12 = new Audio("My Fat Cat George Drums", 11);
    Mixer.addAudio(audio1);
    Mixer.addAudio(audio2);
    Mixer.addAudio(audio3);
    Mixer.addAudio(audio4);
    Mixer.addAudio(audio5);
    Mixer.addAudio(audio6);
    Mixer.addAudio(audio7);
    Mixer.addAudio(audio8);
    Mixer.addAudio(audio9);
    Mixer.addAudio(audio10);
    Mixer.addAudio(audio11);
    Mixer.addAudio(audio12);
    var track1 = new Track(0);
    Mixer.addTrack(track1);
    Mixer.setTimelineDuration(16);
    Mixer.timelineDurationChanger.val(16);*/

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