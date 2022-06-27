(function(){
    function loadDeferredScripts(){
        var deferredScriptDivs = document.querySelectorAll('.ct-ultimate-gdpr-deferred-js[has-loaded="0"]');
        for(var x = 0; x < deferredScriptDivs.length; x++){
            const src = deferredScriptDivs[x].getAttribute('src');
            var scriptElement = document.createElement( 'script' );
            scriptElement.setAttribute( 'src', src );
            document.body.appendChild( scriptElement );
            deferredScriptDivs[x].setAttribute('has-loaded', 1);
        }
    }
    function loadDeferredStyles(){
        var deferredStyleDivs = document.querySelectorAll('.ct-ultimate-gdpr-deferred-css[has-loaded="0"]');
        for(var x = 0; x < deferredStyleDivs.length; x++){
            var src = deferredStyleDivs[x].getAttribute('href');
            var cssId = deferredStyleDivs[x].getAttribute('id');

            var head  = document.getElementsByTagName('head')[0];
            var link  = document.createElement('link');
            link.id   = cssId;
            link.rel  = 'stylesheet';
            link.type = 'text/css';
            link.href = src;
            link.media = 'all';
            head.appendChild(link);
        }
    }
    let deferredDoneLoading = false;
    function userInteracted(){
        if(deferredDoneLoading){
            return false;
        }
        deferredDoneLoading = true;
        stopListeningToUserInteraction();
        loadDeferredScripts();
        loadDeferredStyles();
    }
    function stopListeningToUserInteraction(){
        document.getElementsByTagName("BODY")[0].removeEventListener("mousemove", userInteracted);
        document.getElementsByTagName("BODY")[0].removeEventListener("onscroll", userInteracted);
        document.getElementsByTagName("BODY")[0].removeEventListener("onkeydown", userInteracted);
        document.getElementsByTagName("BODY")[0].removeEventListener("ontouchstart", userInteracted);
        document.getElementsByTagName("BODY")[0].removeEventListener("click", userInteracted);
    }
    function startListeningToUserInteraction(){
        document.getElementsByTagName("BODY")[0].addEventListener("mousemove", userInteracted);
        document.getElementsByTagName("BODY")[0].addEventListener("onscroll", userInteracted);
        document.getElementsByTagName("BODY")[0].addEventListener("onkeydown", userInteracted);
        document.getElementsByTagName("BODY")[0].addEventListener("ontouchstart", userInteracted);
        document.getElementsByTagName("BODY")[0].addEventListener("click", userInteracted);
    }
    document.addEventListener('DOMContentLoaded', function(){
        //   The purpose of the events below is to determine if the user has interacted with the page
        startListeningToUserInteraction();
    }, false);
})();