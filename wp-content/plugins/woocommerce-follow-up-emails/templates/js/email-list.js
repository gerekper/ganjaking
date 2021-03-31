jQuery().ready(function() {
    // Active/Archived Tabs
    jQuery(".email-tab.inactive").hide();

    var url_hash = window.location.hash;
    if (url_hash != "") {
        jQuery( 'a[href="' + url_hash + '"]' ).trigger( 'click' );
    }

    // Sorting
    jQuery('table.fue tbody').sortable({
        items:'tr',
        cursor:'move',
        axis:'y',
        handle: 'td',
        scrollSensitivity:40,
        helper:function(e,ui){
            ui.children().each(function(){
                jQuery(this).width(jQuery(this).width());
            });
            ui.css('left', '0');
            return ui;
        },
        start:function(event,ui){
            ui.item.css('background-color','#f6f6f6');
        },
        stop:function(event,ui){
            ui.item.prop( 'style', false );
            update_priorities();
        }
    });

    // Cloning
    jQuery( 'a.clone-email' ).on( 'click', function( e ) {
        e.preventDefault();

        var name        = prompt(FUE.email_name);
        var email_id    = jQuery(this).data("id");
        var parent      = jQuery(this).parents("table");

        if (name) {
            jQuery(parent).block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });

            var data = {
                action: 'fue_clone_email',
                id:     email_id,
                name:   name,
                nonce: jQuery( this ).data( 'nonce' )
            };

            jQuery.post(ajaxurl, data, function(resp) {
                if (resp.status == "OK") {
                    window.location.href = resp.url;
                } else {
                    alert(resp.message);
                    jQuery(parent).unblock();
                }

            });

        }
    } );

    jQuery( document ).on( 'click', '.archive-email', function( e ) {
        e.preventDefault();

        var table   = jQuery(this).parents("table");
        var parent  = jQuery(this).parents("tr");
        var id      = jQuery(this).data("id");
        var key     = jQuery(this).data("key");
        var that    = this;
        var nonce   = jQuery( this ).data( 'nonce' );

        jQuery(table).block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });

        var data = {
            action: 'fue_archive_email',
            id:     id,
            nonce: nonce
        };

        jQuery.post(ajaxurl, data, function(resp) {
            if (resp.ack != "OK") {
                alert(resp.error);
            } else {
                var $tr = jQuery(parent).clone();;
                jQuery(parent).fadeOut(function() {
                    jQuery(parent).remove();
                });

                jQuery($tr).find("td.status").html(resp.status_html);

                jQuery( "#"+ key +"_archived_tab table."+ key +"-table tbody").append($tr);
                jQuery( "#"+ key +"_archived_tab table."+ key +"-table tr.no-archived-emails").hide();

            }
            jQuery(table).unblock();
        });

    } );

    jQuery( '.unarchive' ).on( 'click', function( e ) {
        e.preventDefault();

        var table   = jQuery(this).parents("table");
        var parent  = jQuery(this).parents("tr");
        var id      = jQuery(this).data("id");
        var key     = jQuery(this).data("key");
        var that    = this;
        var nonce   = jQuery( this ).data( 'nonce' );

        jQuery(table).block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });

        var data = {
            action: 'fue_unarchive_email',
            id:     id,
            nonce: nonce
        };

        jQuery.post(ajaxurl, data, function(resp) {
            if (resp.ack != "OK") {
                alert(resp.error);
            } else {
                var $tr = jQuery(parent).clone();;
                jQuery(parent).fadeOut(function() {
                    jQuery(parent).remove();

                    if ( jQuery( "#"+ key +"_archived_tab table."+ key +"-table tbody tr").length == 1 ) {
                        jQuery( "#"+ key +"_archived_tab table."+ key +"-table tr.no-archived-emails").show();
                    }

                });

                jQuery($tr).find("td.status").html(resp.status_html);

                jQuery( "#"+ key +"_active_tab table."+ key +"-table tbody").append($tr);

            }
            jQuery(table).unblock();
        });

    } );

    jQuery( document ).on( 'click', '.toggle-activation', function( e ) {
        e.preventDefault();

        var parent  = jQuery(this).parents("table");
        var id      = jQuery(this).data("id");
        var that    = this;

        jQuery(parent).block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });

        var data = {
            action: 'fue_toggle_email_status',
            id:     id,
            nonce: jQuery( this ).data( 'nonce' )
        };

        jQuery.post(ajaxurl, data, function(resp) {
            if (resp.ack != "OK") {
                alert(resp.error);
            } else {
                var el = jQuery(that).parents("td.status").eq(0).find("span.status-toggle");
                jQuery(el).html(resp.new_status + '<br/><small><a href="#" class="toggle-activation" data-id="'+ id +'" data-nonce="' + resp.new_nonce + '">'+ resp.new_action +'</a></small>');
            }
            jQuery(parent).unblock();
        });

    } );

});
function update_priorities() {
    jQuery('table tbody').each(function(i) {

        jQuery(this).find("tr").each(function(x) {
            jQuery(this).find("td .priority").html(x+1);
        });

    });
}
