jQuery(document).ready(function () {
    /*
     * Buddypress Nonce solution
     */
    jQuery(document).ajaxSuccess(function (event, xhr, settings) {
        if (hmwp_tr_arr.enable_nonce == 'on') {
            var content = xhr.responseText;
            var new_js = jQuery.parseJSON(content);
            var get_content = new_js.data['contents'];
            var get_content_n = get_content.replace(/_wpnonce=/g, "_nonce=");
            jQuery('#activity-stream').replaceWith(get_content_n);
        }
    });
});