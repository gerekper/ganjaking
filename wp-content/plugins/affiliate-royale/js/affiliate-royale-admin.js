function wafp_toggle_new_form() {
    jQuery('.wafp-new-link').toggle();
    jQuery('.wafp-display-new-form').toggle();
}

function wafp_delete_link(link_id, msg) {
    if (confirm(msg)) {
        jQuery.ajax({
            type: "POST",
            url: "index.php",
            data: "plugin=wafp&controller=links&action=delete&lid=" + link_id,
            success: function (html) {
                jQuery('#wafp-link-' + link_id).fadeOut();
            }
        });
    }
}

function wafp_view_admin_affiliate_page(action, period, wafpage, search, show_loader) {
    if( search==undefined || search==null )
      search='';

    if( show_loader==undefined || show_loader==null )
      show_loader=false;

    if(show_loader)
      jQuery('.wafp-stats-loader').show();

    jQuery('.wafp-loading-image').css('display','inline-block');
    jQuery.ajax({
        type: "POST",
        url: "index.php",
        data: "plugin=wafp&controller=reports&action=" + action + "&period=" + period + "&wafpage=" + wafpage + "&search=" + search,
        success: function (html) {
            jQuery("#tooltip").remove(); // clear out the tooltip
            jQuery('#wafp-admin-affiliate-panel').replaceWith(html);
            if(show_loader)
              jQuery('.wafp-stats-loader').hide();
            jQuery('.wafp-loading-image').css('display','none');
            jQuery('.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
            jQuery('.nav-tab-wrapper .' + action).addClass('nav-tab-active');
        }
    });
}

function wafp_set_multi_commission_type_new(obj) {
  var ancestor = jQuery(obj).parent().parent().parent().parent(); // go to the table element
  if(jQuery(obj).val()=='percentage') {
    ancestor.find('.commissions-currency-symbol').hide();
    ancestor.find('.commissions-percent-symbol').show();
  }
  else if(jQuery(obj).val()=='fixed') {
    ancestor.find('.commissions-currency-symbol').show();
    ancestor.find('.commissions-percent-symbol').hide();
  }
}

function wafp_set_multi_commission_type(obj) {
  var data_id = jQuery(obj).attr('data-id');
  if(jQuery(obj).val()=='percentage') {
    jQuery('#'+data_id+'-currency-symbol').hide();
    jQuery('#'+data_id+'-percent-symbol').show();
  }
  else if(jQuery(obj).val()=='fixed') {
    jQuery('#'+data_id+'-currency-symbol').show();
    jQuery('#'+data_id+'-percent-symbol').hide();
  }
}

function wafp_set_commission_type() {
  if(jQuery('#wafp_commission_type').val()=='percentage') {
    jQuery('.wafp_commission_currency_symbol').hide();
    jQuery('.wafp_commission_percentage_symbol').show();
  }
  else if(jQuery('#wafp_commission_type').val()=='fixed') {
    jQuery('.wafp_commission_currency_symbol').show();
    jQuery('.wafp_commission_percentage_symbol').hide();
  }
}

function get_pagesdropdown( index, pages, selected ) {
  index = (intval(index)+1);
  jQuery("ol#wafp-dash-pages").attr('data-index',index);
  var dropdown = '<li id="wafp-nav-page-' + index + '"><select name="wafp-dash-nav[]">';

  for( var i=0; i < pages.length; i++ ) {
    if( pages[i]['ID'] == selected )
      dropdown += '<option value="' + pages[i]['ID'] + '" selected="selected">' + pages[i]['title'] + '</option>';
    else
      dropdown += '<option value="' + pages[i]['ID'] + '">' + pages[i]['title'] + '</option>';
  }

  dropdown += '</select></li>';
  return dropdown;
}

function intval(mixed_var, base) {
  // Get the integer value of a variable using the optional base for the conversion
  //
  // version: 1109.2015
  // discuss at: http://phpjs.org/functions/intval    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: stensi
  // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   input by: Matteo
  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)    // +   bugfixed by: Rafał Kukawski (http://kukawski.pl)
  // *     example 1: intval('Kevin van Zonneveld');
  // *     returns 1: 0
  // *     example 2: intval(4.2);
  // *     returns 2: 4    // *     example 3: intval(42, 8);
  // *     returns 3: 42
  // *     example 4: intval('09');
  // *     returns 4: 9
  // *     example 5: intval('1e', 16);    // *     returns 5: 30
  var tmp;

  var type = typeof(mixed_var);
   if (type === 'boolean') {
      return +mixed_var;
  } else if (type === 'string') {
      tmp = parseInt(mixed_var, base || 10);
      return (isNaN(tmp) || !isFinite(tmp)) ? 0 : tmp;    } else if (type === 'number' && isFinite(mixed_var)) {
      return mixed_var | 0;
  } else {
      return 0;
  }
}

function wafp_new_commissions_form(WafpCommissions) {
  var new_commish  = '<div class="wafp-commissions-table postbox wafp-hidden">';
  new_commish +=     '  <div class="wafp-commissions-delete-new"><a href="#" class="wafp-icon remove-16"></a></div>';
  new_commish +=     '  <table>';
  new_commish +=     '    <tbody>';
  new_commish +=     '      <tr>';
  new_commish +=     '        <td width="150px"><label for="new_commissions[commission_level][]">' + WafpCommissions.commission_level + '</label></td>';
  new_commish +=     '        <td><input type="text" name="new_commissions[commission_level][]" class="regular-text" value="0" style="width: 30px;"></td>';
  new_commish +=     '      </tr>';
  new_commish +=     '      <tr>';
  new_commish +=     '        <td><label for="new_commissions[referrer][]">' + WafpCommissions.referrer + '</label></td>';
  new_commish +=     '        <td><input type="text" name="new_commissions[referrer][]" class="regular-text wafp-affiliate-referrer" value="" autocomplete="off"></td>';
  new_commish +=     '      </tr>';
  new_commish +=     '      <tr>';
  new_commish +=     '        <td><label for="new_commissions[commission_type][]">' + WafpCommissions.commission_type + '</label></td>';
  new_commish +=     '        <td>';
  new_commish +=     '          <select name="new_commissions[commission_type][]" class="wafp_multi_commission_type_new">';
  new_commish +=     '            <option value="percentage" selected="selected">' + WafpCommissions.commission_type_percentage + '</option>';
  new_commish +=     '            <option value="fixed">' + WafpCommissions.commission_type_fixed + '</option>';
  new_commish +=     '          </select>';
  new_commish +=     '        </td>';
  new_commish +=     '      </tr>';
  new_commish +=     '      <tr>';
  new_commish +=     '        <td><label for="new_commissions[commission_percentage][]">' + WafpCommissions.commission_percentage + '</label></td>';
  new_commish +=     '        <td><span class="commissions-currency-symbol" style="display: none;">' + WafpCommissions.currency_symbol + '</span><input type="text" name="new_commissions[commission_percentage][]" value="" style="width: 60px;"><span class="commissions-percent-symbol" style="">%</span></td>';
  new_commish +=     '      </tr>';
  new_commish +=     '    </tbody>';
  new_commish +=     '  </table>';
  new_commish +=     '</div>';
  return new_commish;
}

function wafp_toggle_default_redirect_url() {
  if(jQuery('#wafp_custom_default_redirect').is(':checked')) {
    jQuery('.wafp_custom_default_redirect_url_wrap').show();
  }
  else {
    jQuery('.wafp_custom_default_redirect_url_wrap').hide();
  }
}

jQuery(document).ready(function() {
  jQuery('.wafp-show-integration-option').click( function() {
    var integration = jQuery(this).attr('integration-option');
    jQuery('.wafp-' + integration + '-option').slideToggle();
  });

  jQuery('.wafp-show-config-option').click( function() {
     var config = jQuery(this).attr('integration-option');
    jQuery('.wafp-' + config + '-config').slideToggle();
  });

  jQuery('#test-silent-post').click(function()
  {
     subid = jQuery('#test-silent-post-subid option:selected').val();
     if (subid == '')
     {
        alert('Please select a subscription');
        return false;
     }

     href = jQuery(this).attr('href') + subid;
     jQuery(this).attr('href', href);
     return true;
  });

  var formfield;
  // Image uploader
  jQuery('.wafp-upload-button').click(function() {
    formfield = jQuery(this).prev();
    tb_show('', 'media-upload.php?type=image&amp;TB_iframe=1');
  });

  window.send_to_editor = function(html) {
    imgurl = jQuery('img',html).attr('src');
    formfield.val(imgurl);
    tb_remove();
  }

  // AJAX Affiliate search
  function wafp_setup_autocomplete() {
    jQuery('#wafp-affiliate-referrer,.wafp-affiliate-referrer').suggest(
      ajaxurl+'?action=wafp_affiliate_search',
      {
        delay: 500,
        minchars: 2,
        /*onSelect: function() {
          var selected = jQuery('ul.ac_results li.ac_over span.suggest-result-name').text();
          this.value = selected;
        }*/
      }
    );
  }
  wafp_setup_autocomplete();

  if(jQuery('#cspf-table-search').val() == '') {
    jQuery('#cspf-table-search').val(jQuery('#cspf-table-search').attr('data-value'));
    jQuery('#cspf-table-search').css('color','#767676');
  }

  jQuery('#cspf-table-search').focus( function() {
    if(jQuery('#cspf-table-search').val() == jQuery('#cspf-table-search').attr('data-value')) {
      jQuery('#cspf-table-search').val('');
      jQuery('#cspf-table-search').css('color','#000000');
    }
  });

  jQuery('#cspf-table-search').blur( function() {
    if(jQuery('#cspf-table-search').val() == '') {
      jQuery('#cspf-table-search').val(jQuery('#cspf-table-search').attr('data-value'));
      jQuery('#cspf-table-search').css('color','#767676');
    }
  });

  jQuery("#cspf-table-search").keyup(function(e) {
    // Apparently 13 is the enter key
    if(e.which == 13) {
      e.preventDefault();
      var loc = window.location.href;
      loc = loc.replace(/&search=[^&]*/gi,'');

      if(jQuery(this).val() != '')
        window.location = loc + '&search=' + jQuery(this).val();
      else
        window.location = loc;
    }
  });

  jQuery("#cspf-table-perpage").change(function(e) {
    var loc = window.location.href;
    loc = loc.replace(/&perpage=[^&]*/gi,'');

    if(jQuery(this).val() != '')
      window.location = loc + '&perpage=' + jQuery(this).val();
    else
      window.location = loc;
  });

  var pages = jQuery.parseJSON( jQuery("#wafp-data-pages").text() );
  var pagesselected = jQuery.parseJSON( jQuery("#wafp-data-selected").text() );

  if( pagesselected != null && pagesselected != undefined && pagesselected.length > 0 ) {
    for(var i=0; i < pagesselected.length; i++) {
      jQuery("ol#wafp-dash-pages").append( get_pagesdropdown( i, pages, pagesselected[i] ) );
      jQuery('#wafp_remove_nav_pages').show();
    }
  }

  jQuery('#wafp_add_nav_pages').click( function() {
    jQuery("ol#wafp-dash-pages").append( get_pagesdropdown( jQuery("ol#wafp-dash-pages").attr('data-index'), pages, '' ) );
    jQuery('#wafp_remove_nav_pages').show();
  });

  jQuery('#wafp_remove_nav_pages').click( function() {
    var index = jQuery("ol#wafp-dash-pages").attr('data-index');
    jQuery("#wafp-nav-page-" + index).remove();
    index = (intval(index)-1);
    jQuery("ol#wafp-dash-pages").attr('data-index',index);
    if(index <= 0)
      jQuery('#wafp_remove_nav_pages').hide();
  });

  //Checkbox js for signup agreement option
  if(jQuery('#wafp-affiliate-agreement-enabled').is(":checked")) {
    jQuery('#wafp-affiliate-agreement-text').show();
  } else {
    jQuery('#wafp-affiliate-agreement-text').hide();
  }
  jQuery('#wafp-affiliate-agreement-enabled').click(function() {
    jQuery('#wafp-affiliate-agreement-text').slideToggle('fast');
  });

  wafp_set_commission_type();
  jQuery('#wafp_commission_type').change( function() {
    wafp_set_commission_type();
  });

  jQuery('.wafp-toggle-link').click( function(e) {
    e.preventDefault();
    jQuery('#' + jQuery(this).attr('data-id')).slideToggle('fast');
  });

  //Delete Sale (txn) AJAX
  jQuery('a.wafp_del_txn').click(function() {
    if(confirm('Are you sure you want to delete this Sale?')) {
      var i = jQuery(this).attr('data-id');
      var data = {
        action: 'wafp_delete_transaction',
        id: i
      };

      jQuery.post(ajaxurl, data, function(response) {
        if(response == 'true') {
          jQuery('tr#record_' + i).fadeOut('slow');
        } else {
          alert(response);
        }
      });
    }

    return false;
  });

  //Delete Commission AJAX
  jQuery('.wafp-commissions-delete a').click(function() {
    if(confirm('Are you sure you want to delete this Commission?')) {
      var i = jQuery(this).attr('data-id');
      var data = {
        action: 'wafp_delete_commission',
        id: i
      };

      jQuery.post(ajaxurl, data, function(response) {
        if(response == 'true') {
          jQuery('#wafp-commissions-' + i).fadeOut('slow');
        } else {
          alert(response);
        }
      });
    }

    return false;
  });

  //Delete Subscription AJAX
  jQuery('a.wafp_del_sub').click(function() {
    if(confirm('Are you sure you want to delete this Subscription?')) {
      var i = jQuery(this).attr('data-value');
      var data = {
        action: 'wafp_delete_subscription',
        subscr_id: i
      };

      jQuery.post(ajaxurl, data, function(response) {
        if(response == 'true') {
          jQuery('tr#record_' + i).fadeOut('slow');
        } else {
          alert(response);
        }
      });
    }

    return false;
  });

  jQuery(".wafp_multi_commission_type").each( function() {
    wafp_set_multi_commission_type(this);
  });

  jQuery(".wafp_multi_commission_type").change( function() {
    wafp_set_multi_commission_type(this);
  });

  jQuery(".wafp-commissions").on( 'click', '.wafp-commissions-delete-new', function() {
    jQuery(this).parent().slideUp('fast', function() {
      jQuery(this).remove();
    });
    return false;
  });

  jQuery(".wafp-commissions").on('change', '.wafp_multi_commission_type_new', function() {
    wafp_set_multi_commission_type_new(this);
  });

  jQuery('.wafp-commissions-add a').click( function() {
    jQuery('.wafp-commissions').append( wafp_new_commissions_form(WafpCommissions) );
    wafp_setup_autocomplete();
    jQuery('.wafp-commissions .wafp-commissions-table:last-child').slideDown('fast');
    return false;
  });

  wafp_toggle_default_redirect_url();
  jQuery('#wafp_custom_default_redirect').click( function() {
    wafp_toggle_default_redirect_url();
  });

  // Validate Form
  jQuery('.wafp-link-update-button').click( function(e) {
    if(jQuery("#wafp_custom_default_redirect").is(':checked')) {
      if(!jQuery("#wafp_custom_default_redirect_url").val().match(/https?:\/\/.*/)) {
        jQuery("#wafp_custom_default_redirect_error").show();
        e.preventDefault();
      }
    }
  });
});
