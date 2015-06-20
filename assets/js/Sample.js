function Sample(start, length, audio) {
    this.start = start;
    this.length = length;
    this.audio = audio;
    this.id = Sample.sampleNumber++;
    this.handler = $('<div draggable="true" class="sample"></div>').css( {
        left: this.start + "px",
        width: this.length + "px"
    }).text(this.audio.name);
    this.handler[0].addEventListener("dragstart", Sample.events.dragstart, true);
    this.handler[0].addEventListener("dragend", Sample.events.dragend, true);
}

Sample.prototype.play = function() {
    this.audio.play();
};

Sample.prototype.move = function(newStart) {
    this.start = newStart > 0 ? newStart : 0;
    this.handler.css("left", this.start + "px");
};

Sample.events = {

    dragstart: function(event) {
        var droppedSample = Mixer.tracks[$(event.target).index()].get;
        droppedSample.handler.remove();
        Mixer.draggedSample = new Sample(0, Number(droppedAudio.buffer.duration) * 30, droppedAudio);
        event.dataTransfer.setData("text/plain", "");
        event.dataTransfer.effectAllowed = "move";
        event.dataTransfer.dropEffect = "move";
        Track.timelinesWrapper.addClass("dragging");
    },

    dragend: function(event) {
        Track.timelinesWrapper.removeClass("dragging");
    }

};

Sample.sampleNumber = 0;