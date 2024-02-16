var AdvGoogleMap = function ($scope, $) {
  $scope = jQuery($scope);
  $ = typeof jQuery != 'undefined' ? jQuery : $;
  window.eaelHasMapAPI = window.google ? window.google : undefined;
  
  if( !$scope.find(".eael-google-map").length ){
    return;
  }
  
  if (!window.eaelHasMapAPI) {
    var $map_class = $scope.find(".eael-google-map").eq(0),
      $map_notice = $scope.find(".google-map-notice").eq(0);

    $map_class.css("display", "none");

    $map_notice.html(
      "Whoops! It seems like you didn't set Google Map API key. You can set from <b>WordPress Dashboard > Essential" +
        " Addons > Elements > Dynamic Content Elements > Advanced Google Map (Settings)</b>"
    );
    $map_notice.addClass("alert alert-warning");
    $map_notice.css({
      "background-color": "#f2dede",
      color: "#a94442",
      "font-size": "85%",
      padding: "15px",
      "border-radius": "3px",
    });
  } else {
    var $map = $scope.find(".eael-google-map"),
      $thisMap = $("#" + $map.attr("id")),
      $mapID = $thisMap.data("id"),
      $mapType = $thisMap.data("map_type"),
      $mapAddressType = $thisMap.data("map_address_type"),
      $mapLat = $thisMap.data("map_lat"),
      $mapLng = $thisMap.data("map_lng"),
      $mapAddr = $thisMap.data("map_addr"),
      $mapBasicMarkerTitle = $thisMap.data("map_basic_marker_title"),
      $mapBasicMarkerContent = $thisMap.data("map_basic_marker_content"),
      $mapBasicMarkerIconEnable = $thisMap.data("map_basic_marker_icon_enable"),
      $mapBasicMarkerIcon = $thisMap.data("map_basic_marker_icon"),
      $mapBasicMarkerIconWidth = $thisMap.data("map_basic_marker_icon_width"),
      $mapBasicMarkerIconHeight = $thisMap.data("map_basic_marker_icon_height"),
      $mapZoom = $thisMap.data("map_zoom"),
      $mapMarkerContent = $thisMap.data("map_marker_content"),
      $mapMarkers = $thisMap.data("map_markers"),
      $mapStaticWidth = $thisMap.data("map_static_width"),
      $mapStaticHeight = $thisMap.data("map_static_height"),
      $mapStaticLat = $thisMap.data("map_static_lat"),
      $mapStaticLng = $thisMap.data("map_static_lng"),
      $mapPolylines = $thisMap.data("map_polylines"),
      $mapStrokeColor = $thisMap.data("map_stroke_color"),
      $mapStrokeOpacity = $thisMap.data("map_stroke_opacity"),
      $mapStrokeWeight = $thisMap.data("map_stroke_weight"),
      $mapStrokeFillColor = $thisMap.data("map_stroke_fill_color"),
      $mapStrokeFillOpacity = $thisMap.data("map_stroke_fill_opacity"),
      $mapOverlayContent = $thisMap.data("map_overlay_content"),
      $mapRoutesOriginLat = $thisMap.data("map_routes_origin_lat"),
      $mapRoutesOriginLng = $thisMap.data("map_routes_origin_lng"),
      $mapRoutesDestLat = $thisMap.data("map_routes_dest_lat"),
      $mapRoutesDestLng = $thisMap.data("map_routes_dest_lng"),
      $mapRoutesTravelMode = $thisMap.data("map_routes_travel_mode"),
      $mapPanoramaLat = $thisMap.data("map_panorama_lat"),
      $mapPanoramaLng = $thisMap.data("map_panorama_lng"),
      $mapTheme = JSON.parse(
        decodeURIComponent(
          ($thisMap.data("map_theme") + "").replace(/\+/g, "%20")
        )
      ),
      $map_streeview_control = $thisMap.data("map_streeview_control"),
      $map_type_control = $thisMap.data("map_type_control"),
      $map_zoom_control = $thisMap.data("map_zoom_control"),
      $map_fullscreen_control = $thisMap.data("map_fullscreen_control"),
      $map_scroll_zoom = $thisMap.data("map_scroll_zoom"),
      $map_center_lat = $thisMap.data("map-center-lat"),
      $map_center_lng = $thisMap.data("map-center-lng");
var eaelMapHeader = new GMaps({
        el: "#eael-google-map-" + $mapID,
        lat: parseFloat($mapLat),
        lng: parseFloat($mapLng),
        zoom: $mapZoom,
        streetViewControl: $map_streeview_control,
        mapTypeControl: $map_type_control,
        zoomControl: $map_zoom_control,
        fullscreenControl: $map_fullscreen_control,
        scrollwheel: $map_scroll_zoom,
      });

    if ($mapTheme != "") {
      eaelMapHeader.addStyle({
        styledMapName: "Styled Map",
        styles: JSON.parse($mapTheme),
        mapTypeId: "map_style",
      });

      eaelMapHeader.setStyle("map_style");
    }

    if ("basic" == $mapType) {
      var infoWindowHolder =
        $mapBasicMarkerContent != "" ? { content: $mapBasicMarkerContent } : "";

      if ($mapBasicMarkerIconEnable == "yes") {
        var iconHolder = {
          url: $mapBasicMarkerIcon,
          scaledSize: new google.maps.Size(
            parseFloat($mapBasicMarkerIconWidth),
            parseFloat($mapBasicMarkerIconHeight)
          ),
        };
      } else {
        var iconHolder = null;
      }

      if ($mapAddressType == "address") {
        GMaps.geocode({
          address: $mapAddr,
          callback: function (results, status) {
            if (status == "OK") {
              var latlng = results[0].geometry.location;
              eaelMapHeader.setCenter(
                parseFloat(latlng.lat()),
                parseFloat(latlng.lng())
              );
              eaelMapHeader.addMarker({
                lat: parseFloat(latlng.lat()),
                lng: parseFloat(latlng.lng()),
                title: $mapBasicMarkerTitle,
                infoWindow: infoWindowHolder,
                icon: iconHolder,
              });
            }
          },
        });
      } else if ($mapAddressType == "coordinates") {
        eaelMapHeader.addMarker({
          lat: $mapLat,
          lng: $mapLng,
          title: $mapBasicMarkerTitle,
          infoWindow: infoWindowHolder,
          icon: iconHolder,
        });
      }
    } // end of basic map script

    if ("marker" == $mapType) {
      var $data = JSON.parse(
        decodeURIComponent(($mapMarkers + "").replace(/\+/g, "%20"))
      );

      if ($data.length > 0) {
        var MarkersMap = new GMaps({
          el: "#eael-google-map-" + $mapID,
          lat: $data[0].eael_google_map_marker_lat,
          lng: $data[0].eael_google_map_marker_lng,
          zoom: $mapZoom,
          streetViewControl: $map_streeview_control,
          mapTypeControl: $map_type_control,
          zoomControl: $map_zoom_control,
          fullscreenControl: $map_fullscreen_control,
          scrollwheel: $map_scroll_zoom,
        });

        MarkersMap.setCenter(
            $map_center_lat === undefined || $map_center_lat === '' ? $data[0].eael_google_map_marker_lat : $map_center_lat,
            $map_center_lng === undefined || $map_center_lng === '' ? $data[0].eael_google_map_marker_lng : $map_center_lng
        );

        if ($mapTheme != "") {
          MarkersMap.addStyle({
            styledMapName: "Styled Map",
            styles: JSON.parse($mapTheme),
            mapTypeId: "map_style",
          });

          MarkersMap.setStyle("map_style");
        }

        $data.forEach(function ($marker) {
          if ($marker.eael_google_map_marker_content != "") {
            var infoWindowHolder = {
              content: $marker.eael_google_map_marker_content,
            };
          } else {
            var infoWindowHolder = "";
          }

          if ($marker.eael_google_map_marker_icon_enable == "yes") {
            var iconHolder = {
              url: $marker.eael_google_map_marker_icon.url,
              scaledSize: new google.maps.Size(
                parseFloat($marker.eael_google_map_marker_icon_width),
                parseFloat($marker.eael_google_map_marker_icon_height)
              ), // scaled size
            };
          }else {
            var iconHolder = {
              path: `M6.1,0C3.7,0,1.7,1.9,1.7,4.3c0,3,3.9,7.3,4.1,7.5c0.2,0.2,0.4,0.2,0.6,0c0.2-0.2,4.1-4.6,4.1-7.5C10.4,1.9,8.5,0,6.1,0zM6.1,6.5c-1.2,0-2.2-1-2.2-2.2s1-2.2,2.2-2.2s2.2,1,2.2,2.2S7.3,6.5,6.1,6.5z`,
              fillColor: $marker.eael_google_map_marker_icon_color,
              fillOpacity: 1,
              strokeWeight: 0,
              rotation: 0,
              scale: 2,
              anchor: new google.maps.Point(7, 15),
            };
          }
          
          MarkersMap.addMarker({
            lat: parseFloat($marker.eael_google_map_marker_lat),
            lng: parseFloat($marker.eael_google_map_marker_lng),
            title: $marker.eael_google_map_marker_title,
            infoWindow: infoWindowHolder,
            icon: iconHolder,
          });
        });
      }
    } // end of multiple markers map

    if ("static" == $mapType) {
      var $data = JSON.parse(
          decodeURIComponent(($mapMarkers + "").replace(/\+/g, "%20"))
        ),
        markersHolder = [];

      if ($data.length > 0) {
        $data.forEach(function ($marker) {
          markersHolder.push({
            lat: parseFloat($marker.eael_google_map_marker_lat),
            lng: parseFloat($marker.eael_google_map_marker_lng),
            color: $marker.eael_google_map_marker_icon_color,
          });
        });
      }

      var eaelStaticMapUrl = GMaps.staticMapURL({
        size: [$mapStaticWidth, $mapStaticHeight],
        lat: $mapStaticLat,
        lng: $mapStaticLng,
        markers: markersHolder,
      });

      $("<img />")
        .attr("src", eaelStaticMapUrl)
        .appendTo("#eael-google-map-" + $mapID);
    } // End of static map

    if ("polyline" == $mapType) {
      var $polylines_data = JSON.parse(
          decodeURIComponent(($mapPolylines + "").replace(/\+/g, "%20"))
        ),
        $data = JSON.parse(
          decodeURIComponent(($mapMarkers + "").replace(/\+/g, "%20"))
        ),
        $eael_polylines = [];

      $polylines_data.forEach(function ($polyline) {
        $eael_polylines.push([
          parseFloat($polyline.eael_google_map_polyline_lat),
          parseFloat($polyline.eael_google_map_polyline_lng),
        ]);
      });

      var path = JSON.parse(JSON.stringify($eael_polylines));

      var eaelPolylineMap = new GMaps({
        el: "#eael-google-map-" + $mapID,
        lat: $map_center_lat === undefined || $map_center_lat === '' ? path[0][0] : $map_center_lat,
        lng: $map_center_lng === undefined || $map_center_lng === '' ? path[0][1] : $map_center_lng,
        zoom: $mapZoom,
      });

      eaelPolylineMap.drawPolyline({
        path: path,
        strokeColor: $mapStrokeColor.toString(),
        strokeOpacity: $mapStrokeOpacity,
        strokeWeight: $mapStrokeWeight,
      });

      $data.forEach(function ($marker) {
        if ($marker.eael_google_map_marker_content != "") {
          var infoWindowHolder = {
            content: $marker.eael_google_map_marker_content,
          };
        } else {
          var infoWindowHolder = "";
        }

        if ($marker.eael_google_map_marker_icon_enable == "yes") {
          var iconHolder = {
            url: $marker.eael_google_map_marker_icon.url,
            scaledSize: new google.maps.Size(
                parseFloat($marker.eael_google_map_marker_icon_width),
                parseFloat($marker.eael_google_map_marker_icon_height)
            ), // scaled size
          };
        } else {
          var iconHolder = "";
        }

        eaelPolylineMap.addMarker({
          lat: $marker.eael_google_map_marker_lat,
          lng: $marker.eael_google_map_marker_lng,
          title: $marker.eael_google_map_marker_title,
          infoWindow: infoWindowHolder,
          icon: iconHolder,
        });
      });

      if ($mapTheme != "") {
        eaelPolylineMap.addStyle({
          styledMapName: "Styled Map",
          styles: JSON.parse($mapTheme),
          mapTypeId: "polyline_map_style",
        });

        eaelPolylineMap.setStyle("polyline_map_style");
      }
    } // End of polyline map

    if ("polygon" == $mapType) {
      var $polylines_data = JSON.parse(
          decodeURIComponent(($mapPolylines + "").replace(/\+/g, "%20"))
        ),
        $eael_polylines = [];

      $polylines_data.forEach(function ($polyline) {
        let lat = parseFloat($polyline.eael_google_map_polyline_lat),
            long = parseFloat($polyline.eael_google_map_polyline_lng);

        if (isNaN(lat) || isNaN(long) || lat < -90 || lat > 90 || long < -180 || long > 180) {
          return;
        }

        $eael_polylines.push([
          lat,
          long,
        ]);
      });

      var path = JSON.parse(JSON.stringify($eael_polylines));

      if (path) {
        var map = new GMaps({
          div: "#eael-google-map-" + $mapID,
          lat: $map_center_lat === undefined || $map_center_lat === '' ? path[0][0] : $map_center_lat,
          lng: $map_center_lng === undefined || $map_center_lng === '' ? path[0][1] : $map_center_lng,
          zoom: $mapZoom,
        });

        polygon = map.drawPolygon({
          paths: path,
          strokeColor: $mapStrokeColor.toString(),
          strokeOpacity: $mapStrokeOpacity,
          strokeWeight: $mapStrokeWeight,
          fillColor: $mapStrokeFillColor.toString(),
          fillOpacity: $mapStrokeFillOpacity,
        });
      }
    } // End of polygon map

    if ("overlay" == $mapType) {
      if ($mapOverlayContent != "") {
        var contentHolder =
          '<div class="eael-gmap-overlay">' + $mapOverlayContent + "</div>";
      } else {
        var contentHolder = "";
      }

      eaelMapHeader.drawOverlay({
        lat: $mapLat,
        lng: $mapLng,
        content: contentHolder,
      });
    } // End of overlay map

    if ("routes" == $mapType) {
      var routeMap = new GMaps({
        el: "#eael-google-map-" + $mapID,
        lat: $mapRoutesOriginLat,
        lng: $mapRoutesOriginLng,
        zoom: $mapZoom,
      });

      routeMap.drawRoute({
        origin: [$mapRoutesOriginLat, $mapRoutesOriginLng],
        destination: [$mapRoutesDestLat, $mapRoutesDestLng],
        travelMode: $mapRoutesTravelMode.toString(),
        strokeColor: $mapStrokeColor.toString(),
        strokeOpacity: $mapStrokeOpacity,
        strokeWeight: $mapStrokeWeight,
      });

      var $data = JSON.parse(
        decodeURIComponent(($mapMarkers + "").replace(/\+/g, "%20"))
      );

      if ($data.length > 0) {
        $data.forEach(function ($marker) {
          if ($marker.eael_google_map_marker_content != "") {
            var infoWindowHolder = {
              content: $marker.eael_google_map_marker_content,
            };
          } else {
            var infoWindowHolder = "";
          }

          if ($marker.eael_google_map_marker_icon_enable == "yes") {
            var iconHolder = {
              url: $marker.eael_google_map_marker_icon.url,
              scaledSize: new google.maps.Size(
                parseFloat($marker.eael_google_map_marker_icon_width),
                parseFloat($marker.eael_google_map_marker_icon_height)
              ), // scaled size
            };
          } else {
            var iconHolder = "";
          }

          eaelMapHeader.addMarker({
            lat: $marker.eael_google_map_marker_lat,
            lng: $marker.eael_google_map_marker_lng,
            title: $marker.eael_google_map_marker_title,
            infoWindow: infoWindowHolder,
            icon: iconHolder,
          });
        });
      }
    } // End of map routers

    if ("panorama" == $mapType) {
      var eaelPanorama = GMaps.createPanorama({
        el: "#eael-google-map-" + $mapID,
        lat: $mapPanoramaLat,
        lng: $mapPanoramaLng,
      });
    } // end of map panorama
  }
};

jQuery(window).on("elementor/frontend/init", function () {
  if (ea.elementStatusCheck('eaelGoogleMap')) {
    return false;
}
  elementorFrontend.hooks.addAction(
    "frontend/element_ready/eael-google-map.default",
    AdvGoogleMap
  );
  ea.hooks.addAction("ea-toggle-triggered", "ea", AdvGoogleMap);
  ea.hooks.addAction("ea-lightbox-triggered", "ea", AdvGoogleMap);
  ea.hooks.addAction("ea-advanced-tabs-triggered", "ea", AdvGoogleMap);
  ea.hooks.addAction("ea-advanced-accordion-triggered", "ea", AdvGoogleMap );
});
