$(document).ready(function() {

    $(".dropdown-body").hide();
    $(".dropdown").on("click", function() {
        $(this).find(".dropdown-body").stop().slideToggle(200);
    });

});