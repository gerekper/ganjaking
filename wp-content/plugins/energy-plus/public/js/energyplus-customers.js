jQuery(document).ready(function() {
  "use strict";

  detectHash(EnergyPlusGlobal._admin_url + "admin.php?page=energyplus&segment=customers&action=view&id=HASH");

  jQuery('body').on('click', '.__A__Customer_Details .trig', function(e) {
    e.stopPropagation();
  });


  jQuery(".__A__Edit_Text").on( "click", function() {
    jQuery(this).addClass('d-none');
    jQuery('.__A__Edit_Save').removeClass('d-none');
    jQuery(".__A__Editable").each(function() {
      var text = jQuery(this).text();
      jQuery(this).html("<input type='textbox' class='input-group' name='" + jQuery(this).attr('data-name')+"' value='"+text+"'/>");
    });

    jQuery(".__A__Editable_C").each(function() {
      var text = jQuery(this).text();
      jQuery(".__A__H", jQuery(this)).hide();
      jQuery(".__A__S", jQuery(this)).show();
    });
  });

  jQuery(".country_select").on('change', function() {

    EnergyPlusAjax();

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      action: "energyplus_ajax",
      segment: 'customers',
      do: 'states',
      country: jQuery(this).find('option:selected').attr("value")

    }, function(r) {
      if (1 === r.status) {
        jQuery("span[data-name='billing_state']").html(r.message);
        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      }
    }, 'json');
  });


  jQuery(".__A__Edit_Save").on( "click", function() {

    EnergyPlusAjax();

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      action: "energyplus_ajax",
      segment: 'customers',
      do: 'update',
      id: jQuery(this).attr('data-id'),
      billing_first_name: jQuery("input[name=billing_first_name]").val(),
      billing_last_name: jQuery("input[name=billing_last_name]").val(),
      billing_company: jQuery("input[name=billing_company]").val(),
      billing_address_1: jQuery("input[name=billing_address_1]").val(),
      billing_address_2: jQuery("input[name=billing_address_2]").val(),
      billing_city: jQuery("input[name=billing_city]").val(),
      billing_state: jQuery("select[name=billing_state]").find('option:selected').attr("value") ? jQuery("select[name=billing_state]").find('option:selected').attr("value") : jQuery("input[name=billing_state]").val() ,
      billing_postcode: jQuery("input[name=billing_postcode]").val(),
      billing_country: jQuery("select[name=billing_country]").find('option:selected').attr("value") ? jQuery("select[name=billing_country]").find('option:selected').attr("value") : jQuery("input[name=billing_country]").val() ,
      billing_email: jQuery("input[name=billing_email]").val(),
      billing_phone: jQuery("input[name=billing_phone]").val()

    }, function(r) {
      if (1 === r.status) {
        jQuery('.__A__Edit_Save').text('Saved!').css({color: 'green'});
        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      } else {
        alert( r.error);
      }
    }, 'json');
  });



  jQuery('body').on('click', '.btnA', function() {

    var customer_id = jQuery(this).attr('id').replace(/item_/, '');

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      action: "energyplus_ajax",
      segment: 'customers',
      do: 'details',
      id: customer_id
    }, function(r) {
      jQuery('#item_' + customer_id ).find('.__A__Customer_Details').html(r);
    });

  });

  jQuery(".__A__Bulk_Do").on( "click",function() {
    /* Nothing to do */
  });


  jQuery(".__A__Checkbox").on( "click",function() {
    if ( 0 === jQuery(".__A__Checkbox:checked").length )  {
      jQuery(".__A__Bulk").hide();

    } else {
      jQuery(".__A__Bulk").show();
    }
    if (this.checked) {
      jQuery(this).parent().parent().addClass('__A__ItemChecked');
    } else {
      jQuery(this).parent().parent().removeClass('__A__ItemChecked');
    }

    jQuery(".__A__Checkbox").addClass('__A__NoHide');
  });

  jQuery(".__A__CheckAll").on( "click",function() {
    if (this.checked) {
      jQuery(".__A__Bulk").show();
    } else {
      jQuery(".__A__Bulk").hide();
    }

    jQuery(".__A__Checkbox").addClass('__A__NoHide').prop('checked', this.checked);
    jQuery(".__A__CheckAll").prop('checked', this.checked);
  });

});
