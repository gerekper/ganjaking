!function (e) {
    if (!window.pintrk) {
        window.pintrk = function () {
            window.pintrk.queue.push(
                Array.prototype.slice.call(arguments))
        };
        var
            n = window.pintrk;
        n.queue = [], n.version = "3.0";
        var
            t = document.createElement("script");
        t.async = !0, t.src = e;
        var
            r = document.getElementsByTagName("script")[0];
        r.parentNode.insertBefore(t, r)
    }
}("https://s.pinimg.com/ct/core.js");

if ( typeof( enhancedSettings ) != "undefined" && enhancedSettings.email !== null ) {
    pintrk('load', pinterestSettings.tagId , {np: 'premmerce', em: enhancedSettings.email});
} else {
    pintrk('load', pinterestSettings.tagId , {np: 'premmerce'});
}
pintrk('page');