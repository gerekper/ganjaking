jQuery( document ).ready(function() {
  "use strict";

  if (!window.isMobile ) {
    var searchbox = jQuery('.search-box');
    var tmp = searchbox.html();

    searchbox.parent().prepend('<div class="__A__WP_searchbox"><a class="__A__WP_searchbox_Filter" href="javascript:;">Filter</a> &nbsp; &nbsp; <a class="__A__WP_searchbox_Search" href="javascript:;">Search</a></div>');

    jQuery('.__A__WP_searchbox .__A__WP_searchbox_Search').on('click', function() {
      jQuery(this).parent().remove();
      searchbox.show();
      jQuery('#post-search-input').focus();
    });

    jQuery('.__A__WP_searchbox .__A__WP_searchbox_Filter').on('click', function() {
      jQuery('.tablenav.top').slideToggle('fast');
    });

    jQuery('.check-column input[type="checkbox"]').on('click', function() {
      jQuery('.tablenav.top').slideDown('fast');
    });

    if ("1" === EnergyPlus_Energizer.click) {

      jQuery('.wp-list-table tbody tr').on('click', function(e) {
        var th = jQuery(this);
        var excludeInputs = [
          "text", "password", "number", "email", "url", "range", "date", "month", "week", "time", "datetime",
          "datetime-local", "search", "color", "tel", "textarea", "checkbox", "button", "a"];
console.log(e.target.tagName.toLowerCase());
          if (!th.hasClass('type-shop_order') && !th.hasClass('plugin-update-tr') && e.target.tagName.toLowerCase() !== 'a' && jQuery.inArray(e.target.type, excludeInputs) == -1) {
            jQuery('.wp-list-table tbody tr').not(this).removeClass('__A__Energizer_Click');
            th.toggleClass('__A__Energizer_Click');
          }

        });
      }

    }

  });
