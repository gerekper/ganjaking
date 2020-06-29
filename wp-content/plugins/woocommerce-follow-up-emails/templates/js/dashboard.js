jQuery(window).load(function() {
    var $ = jQuery.noConflict();

    $("#fue_dash_period").change(function() {
        Cookies.set( 'fue_report_period', $(this).val() );
        window.location.reload();
    });

    $("div.gauge").each(function() {
        var that = this;
        new JustGage({
            id: $(this).attr("id"),
            min: 0,
            max: 100,
            hideMinMax: true,
            gaugeWidthScale:.5,
            textRenderer: function(t) {
                t = t.toLocaleString("en");

                if ( $(that).data("symbol") ) {
                    t += $(that).data("symbol");
                }

                return t;
            }
        });
    });
});