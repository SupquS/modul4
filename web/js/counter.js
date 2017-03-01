$(document).ready(function (){

    function fixDate(date) {
        var base = new Date(0);
        var skew = base.getTime();
        if (skew > 0)
            date.setTime(date.getTime() - skew)
    }

    function setCookie(cname,cvalue,exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires=" + d.toGMTString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
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

    var now = new Date();
    fixDate(now);
    now.setTime(now.getTime() + 365 * 24 * 60 * 60 * 1000);
    var visits = getCookie("counter");

    function counting() {
    var readers = Math.floor((Math.random() * 5) + 1);
    if (!visits)
        visits = 1;
    else
        visits = parseInt(visits) + parseInt(readers);
        setCookie("counter", visits, now);
        $("#readers").text(readers);
        $("#visits").text(visits);
    }
    setInterval(counting, 3000);

});