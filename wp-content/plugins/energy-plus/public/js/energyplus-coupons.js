jQuery(document).ready(function() {
  "use strict";

  detectHash(EnergyPlusGlobal._admin_url + "post.php?post=HASH&action=edit");

  jQuery( "body" ).on( "click", ".__A__ActivePassive", function() {

    EnergyPlusAjax();

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery(this).attr('data-nonce') || jQuery('input[name=_wpnonce]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      action: "energyplus_ajax",
      segment: 'coupons',
      do: 'active',
      id: jQuery(this).attr('data-id'),
      state: jQuery(this).prop('checked')
    }, function(r) {
      if (1 === r.status) {
        jQuery('#item_' + r.id).removeClass('__A__Statusprivate').removeClass('__A__Statuspublish').addClass('__A__Status' + r.new);
        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      } else {
        EnergyPlusAjax('error', r.error);
      }
    }, 'json');

  });

  jQuery(".__A__Bulk_Do").on( "click",function() {
    var sList = "";

    jQuery('.__A__Checkbox').each(function () {

      sThisVal = jQuery(this).attr('data-id');

      if (this.checked) {
        sList += (sList === "" ? sThisVal : "," + sThisVal);
      }
    });

    EnergyPlusAjax();

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      action: "energyplus_ajax",
      segment: 'coupons',
      do: 'bulk',
      id: sList,
      state: jQuery(this).attr('data-do')
    }, function(r) {
      if (1 === r.status) {
        jQuery.each(r.id, function(i, item) {
          jQuery('#item_' + item).removeClass('__A__Statusprivate __A__Statuspublish __A__ItemChecked').addClass('__A__Status' + r.new);
        });
        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      } else {
        EnergyPlusAjax('error', r.error);
      }
    }, 'json');

  });

  jQuery(".__A__Checkbox").on( "click",function() {
    if ( 0 === jQuery(".__A__Checkbox:checked").length )  {
      jQuery(".__A__Bulk").hide();
      jQuery(".__A__Standart").show();

    } else {
      jQuery(".__A__Standart").hide();
      jQuery(".__A__Bulk").show();
    }
    if (this.checked) {
      jQuery(this).parent().parent().addClass('__A__ItemChecked');
    } else {
      jQuery(this).parent().parent().removeClass('__A__ItemChecked');
    }

    if ( 0 < jQuery(".__A__Checkbox[data-state=publish]:checked").length ) {
      jQuery(".__A__Bulk_private").show();
    } else {
      jQuery(".__A__Bulk_private").hide();
    }

    if ( 0 < jQuery(".__A__Checkbox[data-state=private]:checked").length ) {
      jQuery(".__A__Bulk_publish").show();
    } else {
      jQuery(".__A__Bulk_publish").hide();
    }

    jQuery(".__A__Checkbox").addClass('__A__NoHide');
  });

  jQuery(".__A__CheckAll").on( "click",function() {
    if (this.checked) {
      jQuery(".__A__Standart").hide();
      jQuery(".__A__Bulk").show();
    } else {
      jQuery(".__A__Bulk").hide();
      jQuery(".__A__Standart").show();
    }

    jQuery(".__A__Checkbox").addClass('__A__NoHide').prop('checked', this.checked);
    jQuery(".__A__CheckAll").prop('checked', this.checked);


    if ( 0 < jQuery(".__A__Checkbox[data-state=publish]:checked").length ) {
      jQuery(".__A__Bulk_private").show();
    } else {
      jQuery(".__A__Bulk_private").hide();
    }

    if ( 0 < jQuery(".__A__Checkbox[data-state=private]:checked").length ) {
      jQuery(".__A__Bulk_publish").show();
    } else {
      jQuery(".__A__Bulk_publish").hide();
    }

  });
});
