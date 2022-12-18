/* global vcv */
(function ($) {
  var initMap = function($obj) {
    if (typeof google === 'undefined') {
      return null;
    }
    var attrs = $obj.data('attrs'),
      coordinateId = new google.maps.LatLng(attrs.lat, attrs.lng),
      mapOptions = {
        scaleControl: true,
        streetViewControl: ('true' === attrs.streetviewcontrol),
        mapTypeControl: ('true' === attrs.maptypecontrol),
        panControl: ('true' === attrs.pancontrol),
        zoomControl: ('true' === attrs.zoomcontrol),
        scrollwheel: !attrs.scrollwheel,
        draggable: ('true' === attrs.dragging),
        zoomControlOptions: {
          position: google.maps.ControlPosition[attrs.zoomcontrolposition]
        }
      },
      styledMap,
      mapObj,
      markerObj,
      infowindow;
    if (!attrs.map_style) {
      mapOptions.mapTypeId = google.maps.MapTypeId[attrs.map_type];
    } else {
      mapOptions.mapTypeControlOptions = {
        mapTypeIds: [google.maps.MapTypeId[attrs.map_type], 'map_style']
      };
      var styles = decodeURIComponent( (attrs.map_style + '').replace(/%(?![\da-f]{2})/gi, function () {
        return '%25'
      }) );
      styles = JSON.parse(styles);
      styledMap = new google.maps.StyledMapType(styles, {name: "Styled Map"});
    }

    mapObj = new google.maps.Map($obj.get(0), mapOptions);
    mapObj.setCenter(coordinateId);
    mapObj.setZoom(Number(attrs.zoom));
    if (attrs.map_style) {
      mapObj.mapTypes.set('map_style', styledMap);
      mapObj.setMapTypeId('map_style');
    }

    var toggleBounce = function() {
      if (markerObj.getAnimation() != null) {
        markerObj.setAnimation(null);
      } else {
        markerObj.setAnimation(google.maps.Animation.BOUNCE);
      }
    };

    if (attrs.lat && attrs.lng) {
      if (!markerObj) {
        markerObj = new google.maps.Marker({
          position: new google.maps.LatLng(attrs.lat, attrs.lng),
          animation: google.maps.Animation.DROP,
          map: mapObj,
          icon: attrs.icon_img_url
        });
      }
      if (typeof attrs.icon_img_url != 'undefined') {
        markerObj.setIcon(attrs.icon_img_url);
      }
      google.maps.event.addListener(markerObj, 'click', toggleBounce);

      if ( jQuery.trim(attrs.content) !== "" ) {
        if (!infowindow) {
          infowindow = new google.maps.InfoWindow();
        }
        infowindow.setContent('<div class="map_info_text" style="color:#000;">' + jQuery.trim(attrs.content.replace('/\s+/', ' ')) + '</div>');

        if(attrs.infowindow_open == 'off') {
          infowindow.open(mapObj, markerObj);
        }

        google.maps.event.addListener(markerObj, 'click', function() {
          infowindow.open(mapObj, markerObj);
        });

      }
    }
    google.maps.event.trigger(mapObj, 'resize');
  }

  vcv.on('ready', function (action, id, options, tag) {
    var skipAttrs = ['height', 'el_class', 'designOptions'],
      skipCounter = (tag && tag !== 'portoGoogleMap') || (action === 'merge') || (options && options.changedAttribute && skipAttrs.indexOf(options.changedAttribute) !== -1);
    if (!skipCounter) {
      setTimeout(function() {
        var $obj = id ? $('#el-' + id) : $(document.body);
        $obj = $obj.find('.porto-google-map');
        if ($obj.length) {
          if (typeof google === 'undefined' && !$('#porto-google-map-js').length) {
            var js = document.createElement('script');
            js.id = 'porto-google-map-js';
            $(js).appendTo('body').on('load', function() {
              $obj.each(function() {
                initMap($obj);
              });
            }).attr('src', 'https://maps.googleapis.com/maps/api/js?' + js_porto_vars.gmap_uri);
          } else if (typeof google !== 'undefined') {
            $obj.each(function() {
              initMap($obj);
            });
          }
        }
      }, action ? 100 : 10);
    }
  })
})(window.jQuery)
