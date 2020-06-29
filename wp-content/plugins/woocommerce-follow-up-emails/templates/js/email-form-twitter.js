jQuery( function( $ ) {

    $("body").bind("updated_variables_list", function() {
        twitter_toggle_variables( $("#email_type").val() );
    });

    $("body").bind("updated_email_type", function() {
        if ( $("#email_type").val() == 'twitter' ) {
            // hide the WP editor and show the simple twitter textarea
            $("#postdivrich").hide();
            $("#subjectdiv").hide();
            $("#fue-twitter-content").show();

            $("#fue-email-template").hide();
            $("#fue-email-test").hide();
        } else {
            $("#postdivrich").show();
            $("#subjectdiv").show();
            $("#fue-twitter-content").hide();

            $("#fue-email-template").show();
            $("#fue-email-test").show();
        }
    });

    $("body").bind("updated_email_details", function() {
        twitter_toggle_fields( $("#email_type").val() );
    });

    $("#fue-twitter-content").hide();

    $("#post").on("keyup", "#twitter_content", function() {
        fue_twitter_count_characters();
    });

    fue_twitter_count_characters();

    function fue_twitter_count_characters() {
        var limit = 280;
        var count = $("#twitter_content").val().length;

        if ( count > limit ) {
            var trimmed = $("#twitter_content").val().substr( 0, limit );
            
            $("#twitter_content").val( trimmed );
            count = $("#twitter_content").val().length;
        }

        $("#fue-twitter-count").html( count );
    }

    function twitter_toggle_variables( type ) {

        if ( type == "twitter" ) {
            var hide = ['.var'];
            var show = ['.twitter', '.var_twitter', '.var_coupon', '.var_store_url'];

            for (x = 0; x < hide.length; x++) {
                $(hide[x]).hide();
            }

            for (x = 0; x < show.length; x++) {
                $(show[x]).show();
            }
        } else {
            var hide = ['.twitter', '.var_twitter'];

            for (x = 0; x < hide.length; x++) {
                $(hide[x]).hide();
            }
        }
    }

    function twitter_toggle_fields( type ) {
        if ( type == 'twitter' ) {
            // hide the settings and GA tabs
            $(".email_settings_options, .tracking_options").hide();
        } else {
            // hide the settings and GA tabs
            $(".email_settings_options, .tracking_options").show();
        }
    }
} );
