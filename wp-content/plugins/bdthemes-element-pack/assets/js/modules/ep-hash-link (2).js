jQuery(document).ready(function () {
    var $el       = jQuery('#ep-hash-link');

    if ($el.length <= 0) {
        return;
    }

    var $settings = $el.data('settings'),
        selector  = jQuery($settings.container).find($settings.selector);

    if (selector.length <= 0) {
        return;
    }

    jQuery(selector).addClass('ep-hash-link-inner-el');

    function ep_linker_builder(e) {
        var specialChars = "!@#$^&%*()+=-[]/{}|:<>?,.",
            rawText      = e,
            text         = rawText.replace(/\s+/g, "-").toLowerCase();
            text         = text.replace(new RegExp("\\" + specialChars, "g"), "");
        return text;
    }

    jQuery(selector).each(function (e) {
        var rawText = jQuery(this).text(),
            url     = ep_linker_builder(rawText);
        jQuery(this).wrapAll(
            '<a id="ep-hash-link-' +
            e +
            '" data-id="' +
            e +
            '" class="ep-hash-link" href="#' +
            e +
            "_" +
            url +
            '"/>'
        );
    });

    if (window.location.hash) {
        var hash = window.location.hash;
            hash = hash.slice(0, 2);
            hash = "ep-hash-link-" + hash.substring(1);
        jQuery("html, body").animate({
                scrollTop: jQuery("#" + hash).offset().top - 150
            },
            1000
        );
    }

});