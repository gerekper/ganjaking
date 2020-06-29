jQuery(document).ready(function($) {

    // SPF
    $("#spf_enabled").change(function() {
        $(".spf, .spf-result").hide();

        if ( $(this).is(":checked") ) {
            $(".spf").show();
        }
    }).change();

    $("#spf_domain").change(function() {
        $(".validate-spf").attr( "disabled", true );

        if ( $(this).val().length > 3 ) {
            $(".validate-spf").attr( "disabled", false );
        }
    }).change();

    $(".validate-spf").click(function() {
        var domain  = $("#spf_domain").val();
        var ip      = $("#ip_check").val();
        var $btn    = $(this);
        var nonce   = $( this ).data( 'nonce' );

        if ( domain == "" ) {
            return false;
        }

        $(".spf-spinner").css({
            display: "inline-block",
            float: "none"
        });

        $btn.attr("disabled", true);
        $(".spf-dns-result").hide();

        $.get(ajaxurl, {
            action: "fue_verify_spf_dns",
            domain: domain,
            ip: ip,
            nonce: nonce
        }, function( resp ) {
            $(".spf-spinner").hide();

            if ( resp.status == true ) {
                $(".spf-dns-result").show();
                $("#spf_result_domain").html( domain );
                $("#spf_result_data").html( resp.data.data );
            } else {
                alert('No SPF record found for the domain '+ domain);
            }

            $btn.attr("disabled", false);
        }, 'json' );
    });

    $(".generate-spf-record").click(function() {
        var domain  = $("#spf_domain").val();
        var ip      = $("#ip_check").val();
        var $btn    = $(this);
        var nonce   = $( this ).data( 'nonce' );

        if ( domain == "" ) {
            return false;
        }

        $(".spf-gen-spinner").css({
            display: "inline-block",
            float: "none"
        });

        $btn.attr("disabled", true);
        $(".spf-result").hide();

        $.get(ajaxurl, {
            action: "fue_generate_spf",
            domain: domain,
            ip: ip,
            nonce: nonce
        }, function( resp ) {
            $(".spf-gen-spinner").hide();

            if ( resp.status == true ) {
                $(".spf-result").show();
                $("#spf_dns").html( resp.spf );
            } else {
                alert('Error generating an SPF record. '+ resp.error );
            }

            $btn.attr("disabled", false);
        }, 'json' );
    });

    //DKIM
    $("#dkim_enabled").change(function() {
        $(".dkim").hide();

        if ( $(this).is(":checked") ) {
            $(".dkim").show();
        }
    }).change();

    $(".generate-dkim-keys").click( function() {
        var $btn = $(this);
        var data = {
            action: "fue_generate_dkim_keys",
            size: $("#dkim_key_size").val(),
            nonce: $( this ).data( 'nonce' )
        };

        $(".spf-dkim-spinner").css({
            display: "inline-block",
            float: "none"
        });
        $btn.attr("disabled", true);

        $.post( ajaxurl, data, function(resp) {

            $btn.attr("disabled", false);
            $(".spf-dkim-spinner").hide();

            if ( resp.status ) {
                $("#dkim_public_key").val( resp.public_key );
                $("#dkim_private_key").val( resp.private_key );
            } else {
                alert( resp.error );
            }
        }, 'json' );
    });

    // Emails Settings
    var form_modified = false;

    $("#bounce_handling").change(function() {
        $(".bounce_enabled").hide();

        if ( $(this).is(":checked") ) {
            $(".bounce_enabled").show();
        }
    }).change();

    $("#bounce_ssl").change(function() {
        if ( $(this).is(":checked") ) {
            $("#bounce_port").val("995");
        } else {
            $("#bounce_port").val("110");
        }
    });

    $("#emails_form :input").change(function() {
        form_modified = true;
    })

    $(".test-bounce").click(function() {
        if ( form_modified ) {
            alert('Your settings have been changed. Please save the form first before running this test.');
            return false;
        }

        $(".test-bounce-spinner").css("display", "inline-block");
        $(".test-bounce-status").css("display", "inline-block");
        $(this).prop("disabled", true);
        var that = this;
        $.getJSON( ajaxurl, {action: "fue_bounce_emails_test", nonce: $( this ).data( 'nonce' ) }, function(resp) {
            fue_bounce_test_check(resp.identifier, 1, function() {
                $(that).prop('disabled', false);
                $( that ).data( 'nonce', resp.new_nonce );
            });
        } );
    });

    function fue_bounce_test_check( identifier, count, cb ) {
        var $this   = $(this),
            loader  = $(".test-bounce-spinner"),
            status  = $(".test-bounce-status");

        $.post(ajaxurl, {action: "fue_bounce_emails_test_check", identifier: identifier, passes: count}, function(resp) {
            status.html(resp.msg);

            if ( resp.complete ) {
                loader.hide();
                cb && cb();
            } else {
                setTimeout( function() {
                    fue_bounce_test_check( identifier, ++count, cb );
                }, 1000 );
            }
        }, 'json');
    }

});