var isInit = true;

/**
 * Inicjalizacja aplikacji
 */
$(document).ready(function() {

    Mixer.init(80);
    Storage.init();
    if (Storage.isLocalStorageFilled()) {
        Mixer.parse(Storage.getFromLocalStorage());
    }
    else {
        Mixer.parse(content);
    }
    isInit = false;

});