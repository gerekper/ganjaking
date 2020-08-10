jQuery( document ).ready(function() {
  "use strict";

  detectHash(EnergyPlusGlobal._admin_url + "post.php?post=HASH&action=edit");

  jQuery(".__A__OnOff").on( "click", function() {
    var prnt = jQuery(this);

    EnergyPlusAjax();

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      dataType: 'json',
      action: "energyplus_ajax",
      segment: 'products',
      do: 'visible',
      id: jQuery(this).attr('data-id'),
      state: jQuery(this).prop('checked')
    }, function(r) {
      if (1 === r.status) {
        jQuery(".__A__OnOff[data-parent='" +prnt.attr('data-id') + "']").prop('checked', prnt.prop('checked'));
        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      }
    }, 'json');
  });

  jQuery(".__A__StockAjax").on('change', function() {
    var prnt = jQuery(this);

    EnergyPlusAjax();

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      dataType: 'json',
      action: "energyplus_ajax",
      segment: 'products',
      do: 'quantity',
      id: jQuery(this).attr('data-id'),
      state: jQuery(this).prop('checked'),
      name: jQuery(this).attr('name'),
      val: jQuery(this).val()
    }, function(r) {
      if (1 === r.status) {
        jQuery("#__A__Stock_" +prnt.attr('data-id')).html(r.message);
        if ('outofstock' === prnt.attr('name') || 'unlimited' === prnt.attr('name')) {
          jQuery('.__A__Item[data-id=' +prnt.attr('data-id') + '] .__A__StockAjax2').val('');
        }
        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      } else {
        EnergyPlusAjax('error', r.error);
      }
    }, 'json');
  });


  jQuery('body').on('click', ".__A__StockAjax1", function() {
    var prnt = jQuery(this);
    var data_id = prnt.attr('data-id');
    var obj = jQuery('.__A__StockAjax2[data-id='+data_id+']');

    EnergyPlusAjax();

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      dataType: 'json',
      action: "energyplus_ajax",
      segment: 'products',
      do: 'quantity',
      id: obj.attr('data-id'),
      state: obj.prop('checked'),
      name: obj.attr('name'),
      val: obj.val()
    }, function(r) {
      if (1 === r.status) {
        jQuery("#__A__Stock_" +prnt.attr('data-id')).html(r.message);
        jQuery('.__A__Item[data-id=' +prnt.attr('data-id') + '] .__A__Item_Details input[type=checkbox]').prop('checked', false);
        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      } else {
        EnergyPlusAjax('error', r.error);
      }
    }, 'json');
  });

  jQuery("#energyplus-products-2 .__A__PriceAjax").on('change', function() {
    var prnt = jQuery(this);

    EnergyPlusAjax();

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      dataType: 'json',
      action: "energyplus_ajax",
      segment: 'products',
      do: 'quantity',
      id: jQuery(this).attr('data-id'),
      state: false,
      name: jQuery(this).attr('name'),
      val: jQuery(this).val()
    }, function(r) {
      if (1 === r.status) {

        jQuery("#__A__Price_" +prnt.attr('data-id')).html(r.message);
        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      } else {
        EnergyPlusAjax('error', r.error);
      }
    }, 'json');
  });

  jQuery("body").on('click', "#energyplus-products-1 .__A__PriceAjax1", function() {

    var prnt = jQuery(this);
    var data_id = prnt.attr('data-id');

    EnergyPlusAjax();

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      dataType: 'json',
      action: "energyplus_ajax",
      segment: 'products',
      do: 'quantity',
      id: data_id,
      state: false,
      name: 'set_price',
      val: jQuery('.__A__PriceAjax_Regular[data-id='+data_id+']').val(),
      val1: jQuery('.__A__PriceAjax_Sale[data-id='+data_id+']').val(),
    }, function(r) {
      if (1 === r.status) {
        jQuery("#__A__Price_" +prnt.attr('data-id')).html(r.message);
        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      } else {
        EnergyPlusAjax('error', r.error);
      }
    }, 'json');
  });

  jQuery(".__A__AjaxButton").on('click', function(e) {
    e.preventDefault();

    if (jQuery(this).data('confirm')) {
      if (!confirm(jQuery(this).data('confirm'))) {
        return false;
      }
    }

    EnergyPlusAjax();

    var id = jQuery(this).data('id');
    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery(this).data('nonce'),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      dataType: 'json',
      action: "energyplus_ajax",
      segment: 'products',
      do: jQuery(this).data('do'),
      id: id,
    }, function(r) {
      if (1 === r.status) {
        jQuery("#item_" +id).slideUp();
        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      } else {
        EnergyPlusAjax('error', r.error);
      }
    }, 'json');
  });


  jQuery(".__A__ShowVariantProducts").on( "click", function() {
    //jQuery('span', jQuery(this)).toggleClass('__A__Rotate');
    jQuery("tr[data-parent='" +jQuery(this).attr('data-id') + "']").toggle();
  });

  jQuery('.__A__Search_Button').on( "click", function() {
    jQuery('.__A__Searching').toggleClass('__A__Overflow_Inherit');
    jQuery('.__A__Channels').toggle();
    jQuery('.__A__Cat_Title').toggle();
    jQuery('.__A__Right').toggleClass('col-lg-12');

    if (  jQuery('.__A__Searching').hasClass('closed') === true) {

      jQuery('.__A__Right').addClass('col-lg-9');

    }
  });

  jQuery('.__A__Products_Cat_Dropdown').on( "click", function() {
    jQuery('.__A__Searching button').text(jQuery(this).text());
    jQuery('.__A__Input_Status').val(jQuery(this).attr('data-slug'));
    window.searchMe();
  });


  jQuery(".__A__Bulk_Do").on( "click",function() {

    EnergyPlusAjax();
    var sList = "";
    var do_state = jQuery(this).attr('data-do');

    jQuery('.__A__Checkbox').each(function () {
      var sThisVal = jQuery(this).attr('data-id');
      if (this.checked) {
        sList += (sList === "" ? sThisVal : "," + sThisVal);
      }
    });

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      action: "energyplus_ajax",
      segment: 'products',
      do: 'bulk',
      id: sList,
      state: jQuery(this).attr('data-do')
    }, function(r) {
      if (1 === r.status) {
        jQuery.each(r.id, function(i, item) {
          jQuery('#__A__Stock_' + item.id).html(item.status);
          jQuery('#item_' + item.id).removeClass('__A__ItemChecked');

          if ('trash' === do_state || 'deleteforever' === do_state) {
            jQuery('#item_' + item.id).hide('slow').remove();
          }
        });
        sList = '';
        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      } else {
        EnergyPlusAjax('error', r.error);
      }
    }, 'json');
  });


  jQuery(".__A__Bulk_Change_Price").on( "click",function() {

    var sList = "";

    jQuery('.__A__Checkbox').each(function () {
      var sThisVal = jQuery(this).attr('data-id');
      if (this.checked) {
        sList += (sList === "" ? sThisVal : "-" + sThisVal);
      }
    });

    jQuery(this).attr('href', jQuery('.__A__Bulk_Change_Price').attr('href')+'&ids='+sList);
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

    jQuery(".__A__Checkbox").not("[disabled]").addClass('__A__NoHide').prop('checked', this.checked);
    jQuery(".__A__CheckAll").prop('checked', this.checked);
  });

  /* Bulk Prices */

  jQuery('.change_type').on( "click", function() {
    if (jQuery(this).val() === '1') {
      jQuery('#collapseOne').addClass('showing show');
      jQuery('#collapseTwo').removeClass('show');
      jQuery('.percent_1').focus();
    } else {
      jQuery('#collapseTwo').addClass('showing show');
      jQuery('#collapseOne').removeClass('show');
      jQuery('.percent_2').focus();

    }
  });

  var number_format = function (number, decimals, dec_point, thousands_sep) {
    number = number.toFixed(decimals);

    var nstr = number.toString();
    nstr += '';
    x = nstr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? dec_point + x[1] : '';
    var rgx = /(\d+)(\d{3})/;

    while (rgx.test(x1))
    x1 = x1.replace(rgx, '$1' + thousands_sep + '$2');

    return x1 + x2;
  };

  function addCommas(nStr)
  {
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
      x1 = x1.replace(rgx, '$1' + '.' + '$2');
    }
    x2 = x2.replace('.', ',');
    return x1 + x2;
  }

  function calculate(type, percent, fixed) {
    var new_price;

    if (percent === '') {
      percent = 0;
    }
    if (fixed === '') {
      fixed = 0;
    }

    percent = parseFloat(percent);
    fixed = parseFloat(fixed);
    jQuery('.change_price').each( function() {
      var item = jQuery(this);
      var price = parseFloat(item.data('old'));
      if (0 < price) {
        if ('2' === type) {
          new_price = (price*(1+(percent*-1)/100)+(fixed*-1)).toFixed(2);
        } else {
          new_price = (price*(1+percent/100)+fixed).toFixed(2);
        }

        item.html(addCommas(new_price));
      }
    });
  }

  jQuery('.fixed_1, .percent_1, .fixed_2, .percent_2').on('keyup', function() {
    var increase_or_decrease = jQuery(".change_type:checked").val();
    calculate(increase_or_decrease, jQuery('.percent_'+increase_or_decrease).val(), jQuery('.fixed_'+increase_or_decrease).val());
  });

  jQuery('.change_type').on( "click", function() {
    var increase_or_decrease = jQuery(".change_type:checked").val();
    console.log(increase_or_decrease);
    calculate(increase_or_decrease, jQuery('.percent_'+increase_or_decrease).val(), jQuery('.fixed_'+increase_or_decrease).val());
  });

  /* Reorder */

  var handle = '.__A__Products_Hand';

  if (jQuery('.__A__Products_Sortable').hasClass('__A__Products_Sortable')) {
  } else {
    handle = false;
  }

  jQuery( ".__A__Product_Sortable tbody, .__A__Products_Sortable" ).sortable( {
    axis: "y",
    revert: true,
    scroll: false,
    placeholder: "sortable-placeholder",
    cursor: "move",
    opacity: 1,
    handle: handle,
    start: function(event, ui) {
      jQuery('.__A__Products_Sortable').addClass('__A__Sorting');
    },
    stop: function(event, ui) {
      jQuery('.__A__Products_Sortable').removeClass('__A__Sorting');
    },
    update: function(event, ui) {

      EnergyPlusAjax();

      var current_id = ui.item.data('id');
      var next_id = ui.item.next().data('id');
      var prev_id = ui.item.prev().data('id');
      var arr = jQuery(this).sortable('toArray');

      jQuery.post( EnergyPlusGlobal.ajax_url, {
        _wpnonce: jQuery('input[name=_wpnonce]').val(),
        _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
        _asnonce: EnergyPlusGlobal._asnonce,
        action: "woocommerce_product_ordering",
        id: current_id,
        previd: prev_id,
        nextid: next_id
      }, function(r) {
        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      }, 'json');
    }

    /*  handle: ".__A__Handle"*/

  });

  var ns_p = jQuery('.__A__Product_Sortablex').nestedSortable({
    forcePlaceholderSize: true,
    handle: 'tr',
    helper:	'clone',
    items: 'tr',
    listType: "table",
    opacity: 0.6,
    placeholder: 'placeholder',
    revert: 250,
    tabSize: 25,
    tolerance: 'pointer',
    xtoleranceElement: '> div',
    maxLevels: 1,
    isTree: false,
    expandOnHover: 700,
    startCollapsed: false,
    relocate: function() {
      arr = $('.__A__Product_Sortable').nestedSortable('toArray', {startDepthCount: 0});

      jQuery.post( "", {
        _wpnonce: jQuery('input[name=_wpnonce]').val(),
        _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
        _asnonce: EnergyPlusGlobal._asnonce,
        action: "energyplus_ajax",
        segment: 'products',
        do: 'products_reorder',
        ids: arr
      }, function(r) {
        if (1 === r.status) {
        }
      }, 'json');
    }
  });

  // Reorder categories

  if (jQuery('.__A__Depth_0__A__Sortable').length > 0) {
    var ns = jQuery('.__A__Depth_0__A__Sortable').nestedSortable({
      forcePlaceholderSize: true,
      handle: 'div',
      helper:	'clone',
      items: 'li',
      opacity: 0.6,
      placeholder: 'placeholder',
      revert: 250,
      tabSize: 25,
      tolerance: 'pointer',
      toleranceElement: '> div',
      maxLevels: 4,
      isTree: true,
      expandOnHover: 700,
      startCollapsed: false,
      relocate: function() {
        var arr = jQuery('.__A__Depth_0__A__Sortable').nestedSortable('toArray', {startDepthCount: 0});

        EnergyPlusAjax();

        jQuery.post( EnergyPlusGlobal.ajax_url, {
          _wpnonce: jQuery('input[name=_wpnonce]').val(),
          _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
          _asnonce: EnergyPlusGlobal._asnonce,
          action: "energyplus_ajax",
          segment: 'products',
          do: 'categories_reorder',
          ids: arr
        }, function(r) {
          if (1 === r.status) {
            EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
          } else {
            EnergyPlusAjax('error', r.error);
          }
        }, 'json');
      }
    });
  }


});
