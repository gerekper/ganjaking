jQuery(document).ready(function($) {
    var progressbar     = $( "#progressbar" ),
        progressLabel   = $( ".progress-label"),
        total_emails    = 0,
        total_processed = 0,
        xhr             = null,
        _test           = false;


    progressbar.progressbar({
        value: false,
        change: function() {
            progressLabel.text( progressbar.progressbar( "value" ) + "%" );
        },
        complete: function() {
            progressLabel.text( "Sending complete!" );
            sending_complete();
        }
    });

    function update_progressbar( value ) {
        progressbar.progressbar( "value", Math.ceil(value) );
    }

    var params = {
        "action": "fue_send_manual_emails",
        "cmd": "start",
        "key": key,
        "test": _test
    };

    $.post(
        ajaxurl,
        params,
        function( resp ) {

            if (! resp ) {
                alert("There was an error executing the request. Please try again later.");
            } else {
                total_emails = resp.total_emails;

                if ( total_emails == 0 ) {
                    alert("There are no emails to send");
                    update_progressbar(100);
                    sending_complete();
                } else {
                    // initiate the import and the order_import() will call itself until the import is done
                    update_progressbar(0);
                    send_emails( key );
                }

            }

        }
    );

    function send_emails( key ) {

        var params = {
            "action": "fue_send_manual_emails",
            "woo_nonce": "",
            "cmd": "continue",
            "key": key,
            "test": _test
        };

        xhr = $.post( ajaxurl, params, function( resp ) {

            if ( resp.status == 'partial' ) {
                _log( resp.data );

                // update the progress bar and execute again
                var num_processed = resp.data.length;

                total_processed = total_processed + num_processed;
                var progress_value = ( total_processed / total_emails ) * 100;
                update_progressbar( progress_value );

                send_emails( key );
            } else if ( resp.status == 'completed' ) {
                // display the success message
                update_progressbar( 100 );
            }
        });

    }

    function _log( data ) {
        for ( var x = 0; x < data.length; x++ ) {
            var row;
            var id = data[x].id;

            if ( 'queued' == data[x].status ) {
                row = '<p class="success"><span class="dashicons dashicons-yes"></span> Queued for '+ data[x].email +'</p>';
            } else if ( 'success' == data[x].status ) {
                row = '<p class="success"><span class="dashicons dashicons-yes"></span> Sent to '+ data[x].email +'</p>';
            } else {
                row = '<p class="failure"><span class="dashicons dashicons-no"></span> ' + data[x].email +' failed ('+ data[x].error +')</p>';
            }

            $("#log").append(row);

            var height = $("#log")[0].scrollHeight;
            $("#log").scrollTop(height);

        }
    }

    function sending_complete() {
        if ( $("#log").find("a.return_link").length == 0 ) {
            $("#log").append('<div class="updated"><p>All done! <a href="admin.php?page=followup-emails#manual_mails" class="return_link">Go back</a></p></div>');
            var height = $("#log")[0].scrollHeight;
            $("#log").scrollTop(height);
        }
    }
});
