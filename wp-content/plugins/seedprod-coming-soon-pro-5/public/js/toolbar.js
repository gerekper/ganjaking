( function( window, wp ){

    var link_id = 'edit_seedprod_custom_link';

    var url_string = window.location;
    var url = new URL(url_string);
    var post_id = url.searchParams.get("post");

    var active_seedprod_btn = jQuery(".active-seed-prod-buttons").html();
    jQuery(".active-seed-prod-buttons").remove();

    var link_html = active_seedprod_btn;

    var post_ID = jQuery("#post_ID").val();
    var seedprod_template_type = jQuery("._seedprod_template_type").val();
    var seedprod_label = jQuery("._seedprod_label").val();
    var seedprod_template_edit_url = jQuery("._seedprod_template_edit_url").val();
    var seedprod_true = jQuery("._seedprod_true").val();

    var seedprod_template_edit_url_ = '';
    var admin_url = localizedVars.admin_url; 
    var seedprod_plugin_url = localizedVars.plugin_url; 

    

    

    if(seedprod_template_type=="template"){
        seedprod_template_edit_url_ = `${admin_url}?page=${seedprod_label}_${seedprod_template_type}&id=${post_ID}#/template/${post_ID}`;
    }else{
        seedprod_template_edit_url_ = `${admin_url}?page=${seedprod_label}_${seedprod_template_type}&id=${post_ID}#/setup/${post_ID}`;
    }

    //${seedprod_template_edit_url_}

    var seedprod_html = `
    <div class="${seedprod_true}">
        <span class="seedprod-off">
            <a href="#edit" id="edit_seedprod_custom_link" class="edit_seedprod_custom_link button button-primary button-large">
            <img src="${seedprod_plugin_url}public/svg/admin-bar-icon.svg" style="margin-right:7px; margin-top:5px"> Edit with SeedProd
            </a>
        </span>
        <span class="seedprod-on">
            <a href="#back" class="back_to_wp_editor button">Switch Back to WordPress Editor</a>
        </span>
    </div>`;

    // check if gutenberg's editor root element is present.
    var editorEl = document.getElementById( 'editor' );
    if( !editorEl ){ // do nothing if there's no gutenberg root element on page.
        return;
    }

    var unsubscribe = wp.data.subscribe( function () {
        setTimeout( function () {
            if ( !document.getElementById( link_id ) ) {
                var toolbalEl = editorEl.querySelector( '.edit-post-header-toolbar__left' );
                if( toolbalEl instanceof HTMLElement ){
                    toolbalEl.insertAdjacentHTML( 'beforeend', seedprod_html );
                }
            }
        }, 1 )
    } );
    // unsubscribe is a function - it's not used right now 
    // but in case you'll need to stop this link from being reappeared at any point you can just call unsubscribe();

        
    /*
    jQuery(document).ready(function(){  
        jQuery(document).on("click", '.back_to_wp_editor', function(event) { 
            
            wp.data.dispatch( 'core/block-editor' ).resetBlocks([]);
            jQuery('.block-editor-block-list__layout').show();
            jQuery(".managed_by_seedprod").hide();
            
            var ajax_url = localizedVars.ajax_url;
            var post_id =  jQuery("#post_ID").val();

            var formData = new FormData();
            formData.append('action', 'seedprod_pro_remove_post');
            formData.append('post_id', post_id);

            jQuery.ajax({ // JQuery Ajax
                type: 'POST',
                url: ajax_url, 
                data: formData,
                cache: false,
                processData : false,
                contentType: false,
                success: function(data) {
                    console.log("removed seedprod settings");
                },
            });
            

            
        }); 
    });
    */

    

} )( window, wp )
