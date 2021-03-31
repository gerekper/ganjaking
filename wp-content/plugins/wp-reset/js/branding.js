/**
 * WP Reset PRO
 * https://wpreset.com/
 * (c) WebFactory Ltd, 2017-2021
 */

jQuery(document).ready(function($){
    if (typeof wpr_rebranding  == 'undefined') {
      return;
    }
  
    if($('[data-slug="wp-reset"]').length > 0){
        $('[data-slug="wp-reset"]').children('.plugin-title').children('strong').html('<strong>' + wpr_rebranding.name + '</strong>');
    }
  
  });
  