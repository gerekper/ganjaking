(function (window, document, $) {
    "use strict";

    $(document).ready(function () {

        $(document).on("show.bs.aromodal", ".product-aromodal", function (event, table) {
        	var epo = $(event.currentTarget).find($.tcAPIGet("epoSelector"));
            $(window).trigger("tc_manual_init", {"container":epo, "reactivate":true});            
        });

    });

}(window, document, window.jQuery));