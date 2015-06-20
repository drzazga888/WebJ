$(document).ready(function() {

    Mixer.init();

});

var Mixer = {

    tracks: [],
    audios: {},
    draggedSample: null,

    init: function() {
        // podstawowe handlery
        Track.wrapper = $("#tracks");
        Track.headsWrapper = Track.wrapper.children("aside");
        Track.timelinesWrapper = Track.wrapper.find(".timelines");
        Track.addButton = $("#new-track");
        Audio.wrapper = $("#audios");
        // podpięcię zdarzeń
        Track.addButton.on("click", function() {
            Mixer.addTrack(new Track());
        });
        // dodawanie tracków, audio i sampli
        var track = new Track();
        this.addTrack(track);
        var audio = new Audio("Bottem Shelf Drums");
        this.addAudio(audio);
        track.addSample(new Sample(30, 120, audio));
        track.addSample(new Sample(200, 80, audio));
    },

    addAudio: function(audio) {
        this.audios[audio.name] = audio;
    },

    addTrack: function(track) {
        this.tracks.push(track);
    }

};