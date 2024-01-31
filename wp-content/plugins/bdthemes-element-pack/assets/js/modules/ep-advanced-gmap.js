;(function ($, elementor) {
    'use strict';

    var widgetAvdGoogleMap = function ($scope, $) {
        var $advancedGoogleMap = $scope.find('.bdt-advanced-gmap'),
            $GmapWrapper = $scope.find('.bdt-advanced-map'),
            map_settings = $advancedGoogleMap.data('map_settings'),
            markers = $advancedGoogleMap.data('map_markers'),
            map_lists = $scope.find('ul.bdt-gmap-lists div.bdt-gmap-list-item'),
            map_search_form = $scope.find('.bdt-search'),
            map_search_text_box = $scope.find('.bdt-search-input'),
            map_form = $scope.find('.bdt-gmap-search-wrapper > form'),
            listMarker, markupPhone, markupWebsite, markupPlace, markerImage, markupTitle, markupContent;


        if (!$advancedGoogleMap.length) {
            return;
        }

        $GmapWrapper.removeAttr("style");
        var avdGoogleMap = new GMaps(map_settings);

        function createMarkerContent(marker, markerImage) {

            listMarker = markerImage !== '' ? `<div class="bdt-map-tooltip-top-image"><img class="bdt-map-image" src="${markerImage}" alt="" /></div>` : "";
            markupWebsite = marker.website !== undefined ? `<a href="${marker.website}">${marker.website}</a>` : '';
            markupPhone = marker.phone !== undefined ? `<a href="tel:${marker.phone}">${marker.phone}</a>` : '';
            markupContent = marker.content !== undefined ? `<span class="bdt-tooltip-content">${marker.content}</span><br>` : '';
            markupPlace = marker.place !== undefined ? `<h5 class="bdt-tooltip-place">${marker.place}</h5>` : '';
            markupTitle = marker.title !== undefined ? `<h4 class="bdt-tooltip-title">${marker.title}</h4>` : '';
            return `<div class="bdt-map-tooltip-view">
                        <div class="bdt-map-tooltip-view-inner">
                            ${listMarker}
                            <div class="bdt-map-tooltip-bottom-footer">
                                ${markupTitle}
                                ${markupPlace}
                                ${markupContent}
                                ${markupWebsite}
                                ${markupPhone}
                            </div>
                        </div>
                    </div>`;
        }

        for (var i in markers) {
		  markerImage = markers[i].image !== undefined ? markers[i].image: "";
          avdGoogleMap.addMarker({
            lat: markers[i].lat,
            lng: markers[i].lng,
            title: markers[i].title,
            icon: markers[i].icon,
            infoWindow: {
              content: createMarkerContent(markers[i], markerImage)
            },
          });
        }

        if ($advancedGoogleMap.data('map_geocode')) {
            $(map_form).submit(function (e) {
                e.preventDefault();
                GMaps.geocode({
                    address: $(this).find('.bdt-search-input').val().trim(),
                    callback: function (results, status) {
                        if (status === 'OK') {
                            var latlng = results[0].geometry.location;
                            avdGoogleMap.setCenter(latlng.lat(), latlng.lng());
                            avdGoogleMap.addMarker({
                                lat: latlng.lat(),
                                lng: latlng.lng()
                            });
                        }
                    }
                });
            });
        }

        if ($advancedGoogleMap.data('map_style')) {
            avdGoogleMap.addStyle({
                styledMapName: 'Custom Map',
                styles: $advancedGoogleMap.data('map_style'),
                mapTypeId: 'map_style'
            });
            avdGoogleMap.setStyle('map_style');
        }

        $(map_lists).bind("click", function (e) {
            var mapList;
            var dataSettings = $(this).data("settings");
            mapList = new GMaps({
                el: dataSettings.el,
                lat: dataSettings.lat,
                lng: dataSettings.lng,
                title: dataSettings.title,
                zoom: map_settings.zoom,
            });

			markerImage = dataSettings.image !== undefined ? dataSettings.image[0]: "";
            mapList.addMarker({
                lat: dataSettings.lat,
                lng: dataSettings.lng,
                title: dataSettings.title,
                icon: dataSettings.icon,
                infoWindow: {
                    content: createMarkerContent(dataSettings, markerImage),
                },
            });

            if ($advancedGoogleMap.data('map_style')) {
                mapList.addStyle({
                    styledMapName: 'Custom Map',
                    styles: $advancedGoogleMap.data('map_style'),
                    mapTypeId: 'map_style'
                });
                mapList.setStyle('map_style');
            }
        });

        $(map_search_form).submit(function (e) {
            e.preventDefault();
            let searchValue = $(map_search_text_box).val().toLowerCase();
            $(map_lists).filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
            });
        });

        $(map_search_text_box).keyup(function () {
            let searchValue = $(this).val().toLowerCase();
            $(map_lists).filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
            });
        });
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-gmap.default', widgetAvdGoogleMap);
    });
}(jQuery, window.elementorFrontend));
