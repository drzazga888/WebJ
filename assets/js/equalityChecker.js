$(document).ready(function() {

    $('* [name="email2"]').on("blur", function() {
        checkEquality.call(this, $('* [name="email"]'), 'Adresy e-mail muszą być takie same');
    }).blur();

    $('* [name="password2"]').on("blur", function() {
        checkEquality.call(this, $('* [name="password"]'), 'Hasła muszą być takie same');
    }).blur();

});

function checkEquality(source, message) {
    if ($(this).val() !== $(source).val())
        $(this)[0].setCustomValidity(message);
    else
        $(this)[0].setCustomValidity('');
}