jQuery(function () {
  "use strict";

  function serialize() {

    var widgets =[];

    grid.getItems().forEach(function (item, i) {
      var widget = {};
      widget.id =  jQuery(item.getElement()).attr('data-id');
      widget.w =  jQuery(item.getElement()).attr('data-w');
      widget.h =  jQuery(item.getElement()).attr('data-h');
      widgets.push(widget);
    });

    return widgets;

  }

  /* Remap widgets */

  function remap2() {

    EnergyPlusAjax();

    jQuery.ajax({
      type: 'POST',
      url: EnergyPlusGlobal.ajax_url,
      data: {
        _wpnonce: jQuery('input[name=_wpnonce]').val(),
        _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
        _asnonce: EnergyPlusGlobal._asnonce,
        action: 'energyplus_widgets',
        a: 'remap',
        p: 'dashboard',
        widgets: serialize()

      },
      cache: false,
      headers: {
        'cache-control': 'no-cache'
      },
      success: function(response) {
        EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      }
    }, 'json');
  }

if (jQuery(window).width()>800) {
  var grid = new Muuri('.grid', {
    items: '.__A__Widget',
    layoutDuration: 400,
    layoutEasing: 'ease',
    dragEnabled: true,
    dragSortInterval: 20,
    layout: {
      fillGaps: true,
      horizontal: false,
      alignRight: false,
      alignBottom: false,
      rounding: true
    },

    dragStartPredicate: {
      distance: 0,
      delay: 10,
      handle: '.XX'
    }

  });

  grid.on('dragEnd', function (item, event) {
    remap2();
  });

  grid.layout(function (items) {
    if (jQuery(window).width()<800) {
      grid.destroy();
    }
    grid.refreshItems();
    grid.layout(true);
    jQuery('.grid, .__A__Widget_Add').animate({opacity:1},100);
  });

  window.onresize = refreshGrid;

  jQuery(".__A__Widget_Resize").on('change', function() {
    var h = jQuery("#__A__Widget_H_"+ jQuery(this).attr("data-id")).find('option:selected').val();
    var w = jQuery("#__A__Widget_W_"+ jQuery(this).attr("data-id")).find('option:selected').val();

    jQuery(this).parent().parent().parent().parent().parent().parent().parent()
    .removeClass("__A__Widget_w1 __A__Widget_w2 __A__Widget_w3 __A__Widget_w4 __A__Widget_w5 __A__Widget_w6 __A__Widget_w7 __A__Widget_w8 __A__Widget_w9 __A__Widget_w10 __A__Widget_w1_5 __A__Widget_w2_5 __A__Widget_w3_5 __A__Widget_w4_5 __A__Widget_w5_5 __A__Widget_w6_5 __A__Widget_w7_5 __A__Widget_w8_5 __A__Widget_w9_5")
    .removeClass("__A__Widget_h1 __A__Widget_h2 __A__Widget_h3 __A__Widget_h4 __A__Widget_h5 __A__Widget_h6 __A__Widget_h7 __A__Widget_h8 __A__Widget_h9 __A__Widget_h10")
    .addClass("__A__Widget_w"+w)
    .addClass("__A__Widget_h"+h)
    .attr("data-h", h)
    .attr("data-w", w);
    grid.refreshItems();
    grid.layout(true);

    remap2();
  });

  jQuery(".__A__Widget_Settings_Button").on( "click", function() {
    jQuery(".__A__ControlSettings", jQuery(this).parent().parent()).toggle();

  });
} else {
  jQuery('.grid').animate({opacity:1},100);
}

function refreshGrid() {
  grid.refreshItems();
  grid.layout(true);
}

  /* Widget Live */
  var lasttime = Date.now || function() {
    return +new Date();
  };

  var counter = 0;

  var audio = new Audio();
  audio.preload = 'auto';
  audio.volume = 0.1;

  var delay = 250;

  // For Safari
  if(/^((?!chrome|android).)*safari/i.test(navigator.userAgent)) {
    delay = 0;
  }

  window.audio = audio;

  window.reload_widgets = function(new_counter) {

    if (new_counter) {
      counter = new_counter;
    }

    jQuery.ajax({
      type: 'POST',
      url: EnergyPlusGlobal.ajax_url,
      data: {
        _wpnonce: jQuery('input[name=_wpnonce]').val(),
        _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
        _asnonce: EnergyPlusGlobal._asnonce,
        action: 'energyplus_widgets',
        t: lasttime,
        c: counter
      },
      cache: false,
      headers: {
        'cache-control': 'no-cache'
      },
      success: function(response) {

        jQuery.each(jQuery.parseJSON(response), function(i, item) {

          switch (item.type) {

            case 'system':
            lasttime = item.lasttime;
            break;

            case 'lastactivity':
            var offline_sess = item.result.off_time;

            jQuery.each(item.result.updated, function(i, item) {
              jQuery('#__A__Widget_Lastactivity_Sess_'+item).remove();
              if(-2 === item) {
                jQuery('.__A__Widget_Lastactivity_row').remove();
              }
            });

            jQuery('#__A__Widget_' + i+ " .__A__Widget_Content .__A__Widget_Lastactivity_container").prepend(item.result.list);
            jQuery('.bs-tooltip-bottom').remove();
            jQuery('[data-toggle="tooltip"]').tooltip({ boundary: 'window' });

            if (jQuery(".__A__Widget_Lastactivity_container").hasClass("__A__Range_online")) {
              jQuery(".__A__Time_"+offline_sess).remove();
            } else {

              jQuery.each(jQuery(".__A__Time_"+offline_sess), function(i, item) {
                jQuery(".badge-success", item).html(jQuery(".badge", item).attr("data-time")).removeClass('badge').removeClass('badge-success');
              });
            }

            if (jQuery(".__A__Widget_Lastactivity_row").length === 0) {
              jQuery(".__A__EmptyTable").addClass("animated").addClass("slideInUp").addClass("d-flex");
            } else {
              jQuery(".__A__EmptyTable").removeClass('d-flex').hide();

            }

            break;

            case 'onlineusers':
            setGaugeMax(i,parseInt(item.result),0,"set");
            break;

            case 'links':
            break;

            default:
            jQuery('#__A__Widget_' + i+ " .__A__Widget_Content").html(item.result);
            break;
          }
        });
      }
    }, 'json');
  };

  /* Refresh widgets every X seconds */
  if (EnergyPlusGlobal.refresh>9000) {
    setInterval(function() {
      ++counter;
      reload_widgets();
    }, EnergyPlusGlobal.refresh);
  }

  /* Widget List */

  jQuery(".__A__Widget_Add_Now").on( "click", function() {

    EnergyPlusAjax();

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      action: "energyplus_ajax",
      segment: 'dashboard',
      do: 'add-widget',
      id: jQuery(this).attr('data-id'),

    }, function(r) {
      if (1 === r.status) {
        EnergyPlusAjax('success', 'Done');
        window.parent.location = window.parent.location;
        window.parent.trigGlobal.slideReveal('hide');
      } else {
        alert(r.error);
      }
    }, 'json');

  });


  jQuery(".__A__Widget_Delete").on( "click", function() {

    EnergyPlusAjax();

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      action: "energyplus_ajax",
      segment: 'dashboard',
      do: 'delete-widget',
      id: jQuery(this).attr('data-id'),

    }, function(r) {
      if (1 === r.status) {
        EnergyPlusAjax('success', 'Done');
        window.parent.location = window.parent.location;
        window.parent.trigGlobal.slideReveal('hide');
      }else {
        alert(r.error);
      }
    }, 'json');

  });


});
