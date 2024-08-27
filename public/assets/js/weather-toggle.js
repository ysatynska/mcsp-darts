function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
$(document).ready(function() {
    $('#header').append($('#weather-toggle'));
    $('#weather-toggle').show();
    var tempScale = getCookie("temp-scale");
    if (tempScale == 'C') {
        $('#c-toggle').addClass('active');
        $('#degree-c').show();
    }
    else {
        $('#f-toggle').addClass('active');
        $('#degree-f').show();
    }
});
$(document).on("click", "#f-toggle", function () {
    $(this).addClass('active');
    $('#c-toggle').removeClass('active');
    $('#degree-c').hide();
    $('#degree-f').show();
    document.cookie = "temp-scale=F";
});
$(document).on("click", "#c-toggle", function () {
    $(this).addClass('active');
    $('#f-toggle').removeClass('active');
    $('#degree-f').hide();
    $('#degree-c').show();
    document.cookie = "temp-scale=C";
});
