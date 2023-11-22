(function ($) {
    var WidgetElements_NextPrevHandler = function ($scope, $) {
        if (typeof $('#main').ajaxify === "function") {
            $('#main').ajaxify({
                /* basic config parameters */
                selector: ".elementor-widget-dyncontel-post-nextprev a:not(.no-ajaxy)", //Selector for elements to ajaxify - without being swapped - e.g. a selection of links
                maincontent: false, //Default main content is last element of selection, specify a value like "#content" to override
                forms: "form:not(.no-ajaxy)", // jQuery selection for ajaxifying forms - set to "false" to disable
                canonical: true, // Fetch current URL from "canonical" link if given, updating the History API.  In case of a re-direct...
                refresh: false, // Refresh the page if clicked link target current page

                /* visual effects settings */
                requestDelay: 0, //in msec - Delay of Pronto request
                aniTime: 0, //in msec - must be set for animations to work
                aniParams: false, //Animation parameters - see below.  Default = off
                previewoff: true, // Plugin previews prefetched pages - set to "false" to enable or provide a jQuery selection to selectively disable
                scrolltop: "s", // Smart scroll, true = always scroll to top of page, false = no scroll
                bodyClasses: false, // Copy body classes from target page, set to "true" to enable
                idleTime: 0, //in msec - master switch for slideshow / carousel - default "off"
                slideTime: 0, //in msec - time between slides
                menu: false, //Selector for links in the menu
                addclass: "jqhover", //Class that gets added dynamically to the highlighted element in the slideshow
                toggleSlide: false, //Toggle slide parameters - see below.  Default = off

                /* script and style handling settings, prefetch */
                deltas: true, // true = deltas loaded, false = all scripts loaded
                asyncdef: false, // default async value for dynamically inserted external scripts, false = synchronous / true = asynchronous
                alwayshints: "leaflet-embed", // strings, - separated by ", " - if matched in any external script URL - these are always loaded on every page load
                inline: true, // true = all inline scripts loaded, false = only specific inline scripts are loaded
                inlinehints: false, // strings - separated by ", " - if matched in any inline scripts - only these are executed - set "inline" to false beforehand
                inlineskip: "adsbygoogle", // strings - separated by ", " - if matched in any inline scripts - these are NOT are executed - set "inline" to true beforehand
                inlineappend: true, // append scripts to the main content div, instead of "eval"-ing them
                style: true, // true = all style tags in the head loaded, false = style tags on target page ignored
                prefetch: true, // Plugin pre-fetches pages on hoverIntent

                /* debugging & advanced settings*/
                verbosity: 0, //Debugging level to console: default off.  Can be set to 10 and higher (in case of logging enabled)
                memoryoff: false, // strings - separated by ", " - if matched in any URLs - only these are NOT executed - set to "true" to disable memory completely
                cb: null, // callback handler on completion of each Ajax request - default null
                pluginon: true // Plugin set "on" or "off" (==false) manually
            });
        }

    };
    // Make sure you run this code under Elementor..
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/dyncontel-post-nextprev.default', WidgetElements_NextPrevHandler);
    });
})(jQuery);
