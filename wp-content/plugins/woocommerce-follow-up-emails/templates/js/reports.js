google.load("visualization", "1.1", {packages:["corechart", "bar"]});
//google.setOnLoadCallback(drawChart);
function draw_chart( target ) {

    switch ( target ) {
        case '#opens':
            draw_opens_chart();
            break;

        case '#clicks':
            draw_clicks_chart();
            break;

        case '#ctor':
            draw_ctor_chart();
            break;
    }

}

function draw_clicks_chart() {
    if ( clicks_rendered || clicks_json.length == 1 ) {
        return;
    }

    var clicks_data = google.visualization.arrayToDataTable( clicks_json );

    var options = {
        height: 300,
        seriesType: "bars"
    };

    var clicks_chart = new google.visualization.ComboChart(document.getElementById('clicks_chart'));
    clicks_chart.draw(clicks_data, options);

    clicks_rendered = true;
}

function draw_opens_chart() {
    if ( opens_rendered || opens_json.length == 1 ) {
        return;
    }

    var opens_data = google.visualization.arrayToDataTable( opens_json );

    var options = {
        height: 300,
        seriesType: "bars"
    };

    var opens_chart = new google.visualization.ComboChart(document.getElementById('opens_chart'));
    opens_chart.draw(opens_data, options);

    opens_rendered = true;
}

function draw_ctor_chart() {
    if ( ctor_rendered || ctor_json.length == 1 ) {
        return;
    }
    var ctor_data = google.visualization.arrayToDataTable( ctor_json );

    var options = {
        height: 300,
        seriesType: "bars",
        series: {2: {type: "line"}}
    };

    var ctor_chart = new google.visualization.ComboChart(document.getElementById('ctor_chart'));
    ctor_chart.draw(ctor_data, options);


    //var ctor_chart = new google.charts.Bar(document.getElementById('ctor_chart'));
    //ctor_chart.draw(ctor_data);

    ctor_rendered = true;
}

jQuery(document).ready(function($) {
    $("div.section:gt(0)").hide();

    $("h2.reports-overview-tabs a.nav-tab").click(function(e) {
        e.preventDefault();

        var $clicked    = $(this);
        var $target     = $clicked.attr('href');

        $clicked.parents('h2').find('a').removeClass('nav-tab-active');
        $clicked.addClass('nav-tab-active');

        if ( $(".chart_sections .chart_section:visible").size() > 0 ) {
            $(".chart_sections .chart_section:visible").fadeOut( 100, function() {
                $(".chart_sections").find( $target ).fadeIn('fast', function() {
                    draw_chart( $target );
                });
            });
        } else {
            $(".chart_sections").find( $target ).fadeIn('fast', function() {
                draw_chart( $target );
            });
        }

        return false;
    });

    jQuery(".help_tip").tipTip();
    // Subsubsub tabs
    jQuery('div.subsubsub_section ul.subsubsub li a:eq(0)').addClass('current');
    jQuery('div.subsubsub_section .section:gt(0)').hide();

    jQuery('div.subsubsub_section ul.subsubsub li a').click(function(){
        var $clicked = jQuery(this);
        var $section = $clicked.closest('.subsubsub_section');
        var $target  = $clicked.attr('href');

        $section.find('a').removeClass('current');

        if ( $section.find('.section:visible').size() > 0 ) {
            $section.find('.section:visible').fadeOut( 100, function() {
                $section.find( $target ).fadeIn('fast');
            });
        } else {
            $section.find( $target ).fadeIn('fast');
        }

        $clicked.addClass('current');
        jQuery('#last_tab').val( $target );

        return false;
    });

    $("a.table-toggle").click(function(e) {
        e.preventDefault();

        var tbody = $(this).parents("table").find("tbody");
        var icon_span = $(this).find("span.dashicons");

        if (tbody.is(":visible")) {
            tbody.hide();
            icon_span
                .removeClass("dashicons-arrow-up")
                .addClass("dashicons-arrow-down");
        } else {
            tbody.show();
            icon_span
                .removeClass("dashicons-arrow-down")
                .addClass("dashicons-arrow-up");
        }
    });
});

jQuery(window).load(function() {
    var $ = jQuery.noConflict();
    jQuery("h2.reports-overview-tabs a:eq(0)").click();

    $("div.circle").each(function() {
        $(this).circliful();
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
