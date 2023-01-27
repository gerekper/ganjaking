//assign variables
//const pmDelayTimer = setTimeout(pmTriggerDOMListener, 10000); //set inline before main minified script for filterable timeout variable
const pmUserInteractions =["keydown","mousedown","mousemove","wheel","touchmove","touchstart","touchend"];
const pmDelayedScripts = {normal: [], defer: [], async: []};
const jQueriesArray = [];
const pmInterceptedClicks = [];
var pmDOMLoaded = false;
var pmClickTarget = '';

//add pageshow listener
window.addEventListener("pageshow", (e) => {
    window.pmPersisted = e.persisted;
});

//add user interaction event listeners
pmUserInteractions.forEach(function(event) {
    window.addEventListener(event, pmTriggerDOMListener, {passive:true});
});

//add click handling listeners
if(pmDelayClick) {
    window.addEventListener("touchstart", pmTouchStartHandler, {passive: true});
    window.addEventListener("mousedown", pmTouchStartHandler);
}

//add visibility change listener
document.addEventListener("visibilitychange", pmTriggerDOMListener);

//add dom listener and trigger scripts
function pmTriggerDOMListener() {

    //clear existing timeout
    if(typeof pmDelayTimer !== 'undefined') {
        clearTimeout(pmDelayTimer);
    }

    //remove existing user interaction event listeners
    pmUserInteractions.forEach(function(event) {
        window.removeEventListener(event, pmTriggerDOMListener, {passive:true});
    });

    //remove visibility change listener
    document.removeEventListener("visibilitychange", pmTriggerDOMListener);

    //add dom listner if page is still loading
    if(document.readyState === 'loading') {
        document.addEventListener("DOMContentLoaded", pmTriggerDelayedScripts);
    }
    else {

        //trigger delayed script process
        pmTriggerDelayedScripts();
    }
}

//main delayed script process
async function pmTriggerDelayedScripts() {

    //prep
    pmDelayEventListeners();
    pmDelayJQueryReady();
    pmProcessDocumentWrite();
    pmSortDelayedScripts();
    pmPreloadDelayedScripts();

    //load scripts
    await pmLoadDelayedScripts(pmDelayedScripts.normal);
    await pmLoadDelayedScripts(pmDelayedScripts.defer);
    await pmLoadDelayedScripts(pmDelayedScripts.async);

    //trigger delayed DOM events
    await pmTriggerEventListeners();

    //load delayed styles
    document.querySelectorAll("link[data-pmdelayedstyle]").forEach(function (e) {
        e.setAttribute("href", e.getAttribute("data-pmdelayedstyle"));
    });

    //start click replay event
    window.dispatchEvent(new Event("perfmatters-allScriptsLoaded")), pmReplayClicks();    
}

//delay original page event listeners
function pmDelayEventListeners() {

    //create event listeners array
    let eventListeners = {};

    //delay dom event
    function delayDOMEvent(object, event) {

        //rewrites event name to trigger later
        function rewriteEventName(eventName) {
            return eventListeners[object].delayedEvents.indexOf(eventName) >= 0 ? "perfmatters-" + eventName : eventName;
        }

        //make sure we haven't added this object yet
        if(!eventListeners[object]) {

            //setup object in eventlisteners array
            eventListeners[object] = { 
                originalFunctions: { 
                    add:    object.addEventListener, 
                    remove: object.removeEventListener 
                }, 
                delayedEvents: [] 
            }
            
            //swap delayed events with originals
            object.addEventListener = function () {
                arguments[0] = rewriteEventName(arguments[0]);
                eventListeners[object].originalFunctions.add.apply(object, arguments);
            }
            object.removeEventListener = function () {
                arguments[0] = rewriteEventName(arguments[0]);
                eventListeners[object].originalFunctions.remove.apply(object, arguments);
            }
        }

        //add event to delayed events array for object
        eventListeners[object].delayedEvents.push(event);
    }

    //delay dom event trigger
    function delayDOMEventTrigger(object, event) {
        const originalEvent = object[event];
        Object.defineProperty(object, event, {
            get: !originalEvent ? function () {} : originalEvent,
            set: function(n) {
                object["perfmatters" + event] = n;
            },
        });
    }

    //delay dom events
    delayDOMEvent(document, "DOMContentLoaded");
    delayDOMEvent(window, "DOMContentLoaded");
    delayDOMEvent(window, "load");
    delayDOMEvent(window, "pageshow");
    delayDOMEvent(document, "readystatechange");

    //delay dom event triggers
    delayDOMEventTrigger(document, "onreadystatechange");
    delayDOMEventTrigger(window, "onload");
    delayDOMEventTrigger(window, "onpageshow");
}

//delay jquery ready
function pmDelayJQueryReady() {

    //store original jquery
    let originalJQuery = window.jQuery;

    //modify original jquery object
    Object.defineProperty(window, "jQuery", {

        //return original when accessed directly
        get() {
            return originalJQuery;
        },

        //modify value when jquery is set
        set(newJQuery) {

            //make sure it's valid and we haven't modified it already
            if(newJQuery && newJQuery.fn && !jQueriesArray.includes(newJQuery)) {

                //modify new jquery ready event
                newJQuery.fn.ready = newJQuery.fn.init.prototype.ready = function(originalJQuery) {

                    //dom loaded, go ahead
                    if(pmDOMLoaded) {
                        originalJQuery.bind(document)(newJQuery);
                    }

                    //dom not loaded, so wait for listener
                    else {
                        document.addEventListener("perfmatters-DOMContentLoaded", function() {
                            originalJQuery.bind(document)(newJQuery);
                        });
                    }
                };

                //store on event
                const newJQueryOn = newJQuery.fn.on;

                //modify new jquery on event
                newJQuery.fn.on = newJQuery.fn.init.prototype.on = function() {

                    if(this[0] === window) {

                        //rewrite event name
                        function rewriteEventName(eventName) {
                            eventName = eventName.split(" ");
                            eventName = eventName.map(function(name) {
                                if(name === "load" || name.indexOf("load.") === 0) {
                                    return "perfmatters-jquery-load";
                                }
                                else {
                                    return name;
                                }
                            });
                            eventName = eventName.join(" ");

                            return eventName;
                        }

                        //rewrite event name/s
                        if(typeof arguments[0] == "string" || arguments[0] instanceof String) {
                            arguments[0] = rewriteEventName(arguments[0]);
                        }
                        else if(typeof arguments[0] == "object") {
                            Object.keys(arguments[0]).forEach(function(argument) {
                                  delete Object.assign(arguments[0], {[rewriteEventName(argument)]: arguments[0][argument]})[argument];
                            });
                        }
                    }
                    return newJQueryOn.apply(this, arguments), this;
                };

                //add modified jquery to storage array
                jQueriesArray.push(newJQuery);
            }

            //replace original jquery with modified version
            originalJQuery = newJQuery;
        }
    });
}

//print document write values directly after their parent script
function pmProcessDocumentWrite() {

    //create map to store scripts
    const map = new Map();

    //modify document.write functions
    document.write = document.writeln = function(value) {

        //prep
        var script = document.currentScript;
        var range = document.createRange();

        //make sure script isn't in map yet
        let mapScript = map.get(script);
        if(mapScript === void 0) {

            //add script's next sibling to map
            mapScript = script.nextSibling;
            map.set(script, mapScript);
        }
        
        //insert value before script's next sibling
        var fragment = document.createDocumentFragment();
        range.setStart(fragment, 0);
        fragment.appendChild(range.createContextualFragment(value));
        script.parentElement.insertBefore(fragment, mapScript);
    };
}

//find all delayed scripts and sort them by load order
function pmSortDelayedScripts() {
    document.querySelectorAll("script[type=pmdelayedscript]").forEach(function(event) {
        if(event.hasAttribute("src")) {
            if(event.hasAttribute("defer") && event.defer !== false) {
                pmDelayedScripts.defer.push(event);
            }
            else if(event.hasAttribute("async") && event.async !== false) {
                pmDelayedScripts.async.push(event);
            }
            else {
                pmDelayedScripts.normal.push(event);
            }
        }
        else {
            pmDelayedScripts.normal.push(event);
        }
    });
}

//add block of preloads for delayed scripts that have src URLs
function pmPreloadDelayedScripts() {
    var preloadFragment = document.createDocumentFragment();
    [...pmDelayedScripts.normal, ...pmDelayedScripts.defer, ...pmDelayedScripts.async].forEach(function(script) {
        var src = script.getAttribute("src");
        if(src) {
            var link = document.createElement("link");
            link.href = src;
            link.rel = "preload";
            link.as = "script";
            preloadFragment.appendChild(link);
        }
    });
    document.head.appendChild(preloadFragment);
}

//load array of delayed scripts one at a time
async function pmLoadDelayedScripts(scripts) {

    //grab first script in array
    var script = scripts.shift();

    //replace script and move to the next one
    if(script) {
        await pmReplaceScript(script);
        return pmLoadDelayedScripts(scripts);
    }

    //resolve when all scripts have been swapped
    return Promise.resolve();
}

//replace delayed script in document
async function pmReplaceScript(script) {

    //wait
    await pmNextFrame();

    //create new script and replace
    return new Promise(function(replaceScript) {

        //prep
        const newscript = document.createElement("script");

        //loop through script attributes
        [...script.attributes].forEach(function(attribute) {
            let attributeName = attribute.nodeName;

            if(attributeName !== "type") {

                //swap data-type if needed
                if(attributeName === "data-type") {
                    attributeName = "type";
                }

                //add attribute to newscript
                newscript.setAttribute(attributeName, attribute.nodeValue);
            }
        });

        //src script
        if(script.hasAttribute("src")) {
            newscript.addEventListener("load", replaceScript);
            newscript.addEventListener("error", replaceScript);
        }

        //inline script
        else {
            newscript.text = script.text;
            replaceScript();
        }

        //replace original script with final
        script.parentNode.replaceChild(newscript, script);
    });
}

//trigger delayed event listeners after scripts have loaded
async function pmTriggerEventListeners() {

    //set flag
    pmDOMLoaded = true;
    await pmNextFrame();

    //trigger events
    document.dispatchEvent(new Event("perfmatters-DOMContentLoaded"));
    await pmNextFrame();
    window.dispatchEvent(new Event("perfmatters-DOMContentLoaded"));
    await pmNextFrame();
    document.dispatchEvent(new Event("perfmatters-readystatechange"));
    await pmNextFrame();
    if(document.perfmattersonreadystatechange) {
        document.perfmattersonreadystatechange();
    }
    await pmNextFrame();
    window.dispatchEvent(new Event("perfmatters-load"));
    await pmNextFrame();
    if(window.perfmattersonload) {
        window.perfmattersonload();
    }
    await pmNextFrame();
    jQueriesArray.forEach(function(singleJQuery) {
        singleJQuery(window).trigger("perfmatters-jquery-load")
    });
    const pmPageShowEvent = new Event("perfmatters-pageshow");
    pmPageShowEvent.persisted = window.pmPersisted;
    window.dispatchEvent(pmPageShowEvent);
    await pmNextFrame();
    if(window.perfmattersonpageshow) {
        window.perfmattersonpageshow({ persisted: window.pmPersisted });
    }
}

//wait for next frame before proceeding
async function pmNextFrame() {
    return new Promise(function(e) {
        requestAnimationFrame(e);
    });   
}

function pmClickHandler(e) {
    e.target.removeEventListener("click", pmClickHandler);
    pmRenameDOMAttribute(e.target, "pm-onclick", "onclick");
    pmInterceptedClicks.push(e), e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
}

function pmReplayClicks() {
    window.removeEventListener("touchstart", pmTouchStartHandler, {passive: true});
    window.removeEventListener("mousedown", pmTouchStartHandler);
    pmInterceptedClicks.forEach((e) => {
        if(e.target.outerHTML === pmClickTarget) {
            e.target.dispatchEvent(new MouseEvent("click", {view: e.view, bubbles: true, cancelable: true}));
        }
    });
}

function pmTouchStartHandler(e) {
    
    if(e.target.tagName !== "HTML") {
        if(!pmClickTarget) {
            pmClickTarget = e.target.outerHTML;
        }
        window.addEventListener("touchend", pmTouchEndHandler);
        window.addEventListener("mouseup", pmTouchEndHandler);
        window.addEventListener("touchmove", pmTouchMoveHandler, {passive: true});
        window.addEventListener("mousemove", pmTouchMoveHandler);
        e.target.addEventListener("click", pmClickHandler);
        pmRenameDOMAttribute(e.target, "onclick", "pm-onclick");
    }     
}

function pmTouchMoveHandler(e) {
    window.removeEventListener("touchend", pmTouchEndHandler);
    window.removeEventListener("mouseup", pmTouchEndHandler);
    window.removeEventListener("touchmove", pmTouchMoveHandler, {passive: true});
    window.removeEventListener("mousemove", pmTouchMoveHandler);
    e.target.removeEventListener("click", pmClickHandler);
    pmRenameDOMAttribute(e.target, "pm-onclick", "onclick");
}

function pmTouchEndHandler(e) {
    window.removeEventListener("touchend", pmTouchEndHandler);
    window.removeEventListener("mouseup", pmTouchEndHandler);
    window.removeEventListener("touchmove", pmTouchMoveHandler, {passive: true});
    window.removeEventListener("mousemove", pmTouchMoveHandler);
}

function pmRenameDOMAttribute(e, t, n) {
    if(e.hasAttribute && e.hasAttribute(t)) {
        event.target.setAttribute(n, event.target.getAttribute(t));
        event.target.removeAttribute(t);
    }
}