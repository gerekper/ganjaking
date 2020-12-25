/*
 * WP Reset PRO
 * Backend GUI pointers
 * (c) Web factory Ltd, 2017 - 2019
 */

jQuery(document).ready(function($){
  if (typeof wp_reset_pointers  == 'undefined') {
    return;
  }

  $.each(wp_reset_pointers, function(index, pointer) {
    if (index.charAt(0) == '_') {
      return true;
    }
    $(pointer.target).pointer({
        content: '<h3>WP Reset PRO</h3><p>' + pointer.content + '</p>',
        pointerWidth: 380,
        position: {
            edge: pointer.edge,
            align: pointer.align
        },
        close: function() {
                $.get(ajaxurl, {
                    notice_name: index,
                    _ajax_nonce: wp_reset_pointers._nonce_dismiss_pointer,
                    action: 'wp_reset_dismiss_notice'
                });
        }
      }).pointer('open');
  });
});
