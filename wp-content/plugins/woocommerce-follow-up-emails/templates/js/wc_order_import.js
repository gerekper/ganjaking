jQuery(document).ready(function($) {
    var progressbar     = $("#progressbar"),
        progressLabel   = $(".progress-label"),
        total_processed = 0,
        total_orders    = 0,
        session         = "",
        _test           = false;

    progressbar.progressbar({
        value: false,
        change: function() {
            progressLabel.text( progressbar.progressbar( "value" ) + "%" );
        },
        complete: function() {
            progressLabel.text( "Importing complete!" );
            $("body").trigger("progress-complete");
        }
    });

    // initialize the import
    (function() {
        // attach the event listeners
        $("body").bind("import_init_completed", import_filter_orders);
        $("body").bind("import_filter_completed", import_orders);
        $("body").bind("import_completed", import_completed);

        var params = {
            "action": "fue_wc_order_import",
            "cmd": "start",
            "woo_nonce": "",
            "email_id": email_id,
            "test": _test
        };

        $("#total-orders-label").html("<h2><img alt=\"processing\" src=\"images/wpspin_light.gif\" class=\"waiting\" style=\"margin-left: 6px;\" /> Scanning orders to import</h2><p>Please do not close this window. This may take a few minutes.</p>");

        $.post(
            ajaxurl,
            params,
            function( resp ) {
                if (! resp ) {
                    alert("There was an error executing the request. Please try again later.");
                } else {
                    session = resp.session;

                    if ( resp.warning ) {
                        if ( !confirm( resp.warning ) ) {
                            $("#total-orders-label").hide();
                            $("#progressbar").hide();
                            $("#log").append('<p class="failure">Importing cancelled</p>');
                            return false;
                        }
                    }

                    $("body").trigger("import_init_completed");

                }

            }
        );
    })();

    function import_filter_orders() {
        var params = {
            "action"            : "fue_wc_order_import",
            "woo_nonce"         : "",
            "email_id"          : email_id,
            "cmd"               : "filter",
            "import_session"    : session
        };

        $.post( ajaxurl, params, function( resp ) {
            if (resp.status == "partial") {
                // not done filtering
                import_filter_orders();
            } else if (resp.status == "completed") {
                $("#total-orders-label").html("Total orders found: "+ resp.total_orders);
                total_orders = resp.total_orders;
                update_progressbar(0);
                $("body").trigger("import_filter_completed");
            } else if (resp.error) {
                $("#total-orders-label").html("");
                $("#log").append('<p class="failure"><span class="dashicons dashicons-no"></span> Error: '+ resp.error +'</p>');
                $("#log").scrollTop($("#log")[0].scrollHeight);

                progressbar.progressbar("destroy");
                $("#progressbar").hide();
            }
        });

    }

    function import_orders() {
        var params = {
            "action"            : "fue_wc_order_import",
            "woo_nonce"         : "",
            "email_id"          : email_id,
            "cmd"               : "import",
            "test"              : _test,
            "import_session"    : session
        };

        xhr = $.post( ajaxurl, params, function( resp ) {
            if ( resp.error ) {
                $("#log").append('<p class="failure"><span class="dashicons dashicons-no"></span> Error: '+ resp.error +'</p>');
            } else {
                if ( resp.status == 'partial' ) {
                    log_import_data( resp.import_data );

                    // update the progress bar and execute again
                    var num_processed = resp.import_data.length;

                    total_processed = total_processed + num_processed;
                    var progress_value = ( total_processed / total_orders ) * 100;
                    update_progressbar( progress_value );

                    import_orders();
                } else if ( resp.status == 'completed' ) {
                    if (resp.import_data)
                        log_import_data( resp.import_data );

                    $("body").trigger("import_completed");
                }
            }

        });

    }

    function import_completed() {
        importing_complete();
    }

    function update_progressbar( value ) {
        progressbar.progressbar( "value", Math.ceil(value) );
    }

    function log_import_data( data ) {
        if ( ! data.length ) {
           console.error( 'Invalid data supplied to log_import_data', data );
           return;
        }

        for ( var x = 0; x < data.length; x++ ) {
            var row;
            var id = data[x].id;

            if ( data[x].status == 'success' ) {
                row = '<p class="success"><span class="dashicons dashicons-yes"></span> Order #'+ id +' imported</p>';
            } else {
                row = '<p class="failure"><span class="dashicons dashicons-no"></span> Order #'+ id +' - ' + data[x].reason +'</p>';
            }

            $("#log").append(row);

            var height = $("#log")[0].scrollHeight;
            $("#log").scrollTop(height);

        }
    }

    function importing_complete() {
        update_progressbar( 100 );
        if ( $("#log").find("a.return_link").length == 0 ) {
            $("#log").append('<div class="updated"><p>Import completed successfully. <a href="#" class="return_link">Go back</a></p></div>');
            var height = $("#log")[0].scrollHeight;
            $("#log").scrollTop(height);
            $(".return_link").attr("href", return_url);
        }
    }
});
