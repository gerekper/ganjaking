jQuery(document).ready(function() {
  "use strict";

  detectHash(EnergyPlusGlobal._admin_url + "post.php?post=HASH&action=edit");

  jQuery('.__A__Ajax_Btn_SP').on( "click",function(e) {
    e.stopPropagation();
    e.preventDefault();

    if (window.isMobile) {
      if (jQuery('body').hasClass('energyplus-half')) {
        window.location = EnergyPlusGlobal._admin_url + 'admin.php?page=energyplus&segment=frame&in=' + encodeURIComponent(jQuery(this).attr('href')) + "&_asnonce="+ EnergyPlusGlobal._asnonce_notifications;
        return false;
      } else {
        window.location = jQuery(this).attr('href');
        return false;
      }
    }

    if (jQuery(this).attr('data-hash') && jQuery(this).attr('data-hash').length>0) {
      window.location.hash = jQuery(this).attr('data-hash');
    }

    window.trigGlobal.slideReveal("show");
    jQuery("#inbrowser").attr("src", jQuery(this).attr('href'));
    jQuery('#inbrowser').on("load", function() {
      jQuery("#inbrowser--loading").removeClass('d-flex').addClass('d-none');
      jQuery(".__A__Trig_Close").removeClass('d-none');
      jQuery("#inbrowser").show();
    });
  });



  jQuery("body").on( "click", ".__A__Ajax_Button", function(e) {
    e.preventDefault();

    EnergyPlusAjax();

    var t = jQuery('#item_'+jQuery(this).attr('data-id'));
    var status = jQuery(this).data('status').replace(/wc-/, '');
    var text = jQuery(this).data('text');

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      action: "energyplus_ajax",
      segment: 'orders',
      do: jQuery(this).data('do'),
      id: jQuery(this).data('id'),
      status: status
    }, function(r) {
      if (1 === r.status) {
        jQuery.each(r.success, function(i, item) {
          jQuery('.energyplus-orders--item-badge > span', '#item_' + item).removeClass().addClass('siparisdurumu text-'+status);
          jQuery('.energyplus-orders--item-badge > span > ', '#item_' + item).removeClass().addClass('bg-custom bg-'+status);
          jQuery('.energyplus-orders--item-badge', '#item_' + item).html('<span class="siparisdurumu text-'+status+'"><span class="bg-custom bg-'+status+'" aria-hidden="true"></span><br>'+text+'</span>');
          if ('trash' === status || 'restore' === status || 'deleteforever' === status) {
            jQuery('#item_' + item).hide('slow');
          }  else {
            jQuery('#item_' + item).removeClass('__A__ItemChecked');
          }
        });
        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);

      } else {
        EnergyPlusAjax('error', r.error);
      }
    }, 'json');

  });

  jQuery(".__A__Bulk_Do").on( "click",function() {

    EnergyPlusAjax();

    var sThisVal = '',
    sList = "",
    status = jQuery(this).data('status');

    jQuery('.__A__Checkbox').each(function () {
      sThisVal = jQuery(this).attr('data-id');
      if (this.checked) {
        sList += (sList === "" ? sThisVal : "," + sThisVal);
      }
    });

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      action: "energyplus_ajax",
      segment: 'orders',
      do: jQuery(this).data('do'),
      id: sList,
      status: status
    }, function(r) {
      if (1 === r.status) {
        jQuery.each(r.success, function(i, item) {
          jQuery('.energyplus-orders--item-badge > span', '#item_' + item).html(status).removeClass().addClass('badge badge-pill badge-'+status);
          if ('trash' === status || 'restore' === status || 'deleteforever' === status) {
            jQuery('#item_' + item).hide('slow').remove();
          }  else {
            jQuery('#item_' + item).removeClass('__A__ItemChecked');
          }
        });

        sList = '';

        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      } else {
        EnergyPlusAjax('error', r.error);
      }
    }, 'json');

  });

  jQuery(".__A__Checkbox").on( "click",function() {
    if ( 0 === jQuery(".__A__Checkbox:checked").length )  {
      jQuery(".__A__Bulk").hide();
    } else {
      jQuery(".__A__Bulk").show();
      jQuery(".__A__Item.btnA").addClass('collapsed').attr('aria-expanded', false);
      jQuery(".__A__Item.btnA .collapse").removeClass('show');
      jQuery('.__A__Checkbox_Hidden').show();
    }
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
