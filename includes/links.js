$(document).on(
    "click",
    "a",
    function(event) {
        if (!$(this).hasClass("external")) {
            event.preventDefault();
            if (!$(event.target).attr("href")) {
                location.href = $(event.target).parent().attr("href");
            } else {
                location.href = $(event.target).attr("href");
            }
        } else {}
    }
);