/**
 * Start advanced gmap widget script
 */
;(function ($, elementor) {
	'use strict';
	//Adavanced Google Map
	var widgetAvdGoogleMap = function ($scope, $) {

		var $advancedGoogleMap = $scope.find('.bdt-advanced-gmap'),
		 $GmapWrapper = $scope.find('.bdt-advanced-map'),

			map_settings = $advancedGoogleMap.data('map_settings'),
			markers = $advancedGoogleMap.data('map_markers'),
			map_lists = $scope.find('ul.bdt-gmap-lists div.bdt-gmap-list-item'),
			map_search_form = $scope.find('.bdt-search'),
			map_search_text_box = $scope.find('.bdt-search-input'),
			map_form = $scope.find('.bdt-gmap-search-wrapper > form');
			let listMarker,markupPhone, markupWebsite, markupPlace, markupTitle, markupContent;


		if (!$advancedGoogleMap.length) {
			return;
		}
		$GmapWrapper.removeAttr("style");
		var avdGoogleMap = new GMaps(map_settings);
		for (var i in markers) {
			listMarker = (markers[i].image !== undefined) ? markers[i].image[0]: markers[i].image;
			markupWebsite = (markers[i].website !== undefined) ? `<a href="${markers[i].website}">${markers[i].website}</a>`: '';
			markupPhone = (markers[i].phone !== undefined)? `<a href="tel:${markers[i].phone}">${markers[i].phone}</a>`: '';
			markupContent = (markers[i].content !== undefined) ? `<span class="bdt-tooltip-content">${markers[i].content}</span><br>`: '';
			markupPlace = (markers[i].place !== undefined) ? `<h5 class="bdt-tooltip-place">${markers[i].place}</h5>`: '';
			markupTitle = (markers[i].title !== undefined) ? `<h4 class="bdt-tooltip-title">${markers[i].title}</h4>`: '';
			var content = `<div class="bdt-map-tooltip-view">
						<div class="bdt-map-tooltip-view-inner">
							<div class="bdt-map-tooltip-top-image">
							<img class="bdt-map-image" src="${listMarker}" alt="" />
							</div>
							<div class="bdt-map-tooltip-bottom-footer">
								${markupTitle}
								${markupPlace}
								${markupContent}
								${markupWebsite}
								${markupPhone}
							</div>
						</div>
						</div>`;
			avdGoogleMap.addMarker({
				lat: markers[i].lat,
				lng: markers[i].lng,
				title: markers[i].title,
				icon: markers[i].icon,
				infoWindow: {
					content: content

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
							avdGoogleMap.setCenter(
								latlng.lat(),
								latlng.lng()
							);
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
			var dataSettings = $(this).data("settings"),
			mapList = new GMaps({
				el: dataSettings.el,
				lat: dataSettings.lat,
				lng: dataSettings.lng,
				title: dataSettings.title,
				zoom: map_settings.zoom,
			});


			// console.log(dataSettings.icon);
			listMarker = (dataSettings.image !== undefined) ? dataSettings.image[0]: dataSettings.image;
			markupTitle= (dataSettings.title !== undefined) ?  `<h4 class="bdt-tooltip-title">${dataSettings.title}</h4>`: '';
			markupPlace = (dataSettings.place !== undefined) ? `<h5 class="bdt-tooltip-place">${dataSettings.place}</h5>`: '';
			markupContent =  (dataSettings.content !== undefined) ?  `<span class="bdt-tooltip-content">${dataSettings.content}</span><br>`:'';
			markupWebsite = (dataSettings.website !== undefined) ? `<a href="${dataSettings.website}">${dataSettings.website}</a>`: '';
			markupPhone = (dataSettings.phone !== undefined)? `<a href="tel:${dataSettings.phone}">${dataSettings.phone}</a>`: '';

			var content = `<div class="bdt-map-tooltip-view">
							<div class="bdt-map-tooltip-view-inner">
								<div class="bdt-map-tooltip-top-image">
								<img class="bdt-map-image" src="${listMarker}" alt="" />
								</div>
								<div class="bdt-map-tooltip-bottom-footer">
										${markupTitle}
										${markupPlace}
										${markupContent}
										${markupWebsite}
										${markupPhone}
								</div>
							</div>
						</div>`
			mapList.addMarker({
					lat: dataSettings.lat,
					lng: dataSettings.lng,
					title: dataSettings.title,
					icon: dataSettings.icon,
					infoWindow: {
					content: content,
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


		/**
			 * binding event for search form
			 */
		$(map_search_form).submit(function (e) {
			e.preventDefault();
			let searchValue = $(map_search_text_box).val().toLowerCase();
			$(map_lists).filter(function () {
				$(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1)
			});
		});
		/**
		 * bind search event on key press
		 */
		$(map_search_text_box).keyup(function () {
			let searchValue = $(this).val().toLowerCase();
			$(map_lists).filter(function () {
				$(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1)
			});
		});

	};
	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-gmap.default', widgetAvdGoogleMap);
	});

}(jQuery, window.elementorFrontend));

/**
 * End advanced gmap widget script
 */
