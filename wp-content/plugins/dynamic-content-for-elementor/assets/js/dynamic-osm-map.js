jQuery(window).on('elementor/frontend/init', () => {
	class DynamicOsmMap extends elementorModules.frontend.handlers.Base {
		getDefaultSettings() {
			return {
				selectors: {
					mapWrapper: '.dce-osm-wrapper',
				},
			};
		}

		getDefaultElements() {
			const selectors = this.getSettings('selectors');
			return {
				$mapWrapper: this.$element.find(selectors.mapWrapper),
			};
		}

		loadLayers() {
			// build layer OSM
			let OpenStreetMap_HOT = L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Tiles style by <a href="https://www.hotosm.org/" target="_blank">Humanitarian OpenStreetMap Team</a> hosted by <a href="https://openstreetmap.fr/" target="_blank">OpenStreetMap France</a>'
			});

			this.defaultLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
			})

			let OpenStreetMap_Cycle = L.tileLayer('https://{s}.tile-cyclosm.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png', {
				attribution: '<a href="https://github.com/cyclosm/cyclosm-cartocss-style/releases" title="CyclOSM - Open Bicycle render">CyclOSM</a> | Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
			});

			this.baseMaps = {
				"hot": OpenStreetMap_HOT,
				"osm": this.defaultLayer,
				"cycle": OpenStreetMap_Cycle,
			};
		}

		/**
		 * Start From here
		 */
		initialization() {
			// init cluster
			// Ref : https://github.com/Leaflet/Leaflet.markercluster#defaults
			this.markerClusterGroup = L.markerClusterGroup({
				showCoverageOnHover: true, // When you mouse over a cluster it shows the bounds of its markers.
				zoomToBoundsOnClick: true, //  When you click a cluster we zoom to its bounds.
				spiderfyOnMaxZoom: true, // When you click a cluster at the bottom zoom level we spiderfy it so you can see all of its markers. (Note: the spiderfy occurs at the current zoom level if all items within the cluster are still clustered at the maximum zoom level or at zoom specified by disableClusteringAtZoom option)
				removeOutsideVisibleBounds: true, // Clusters and markers too far from the viewport are removed from the map for performance.

				iconCreateFunction: function(cluster) {
					return L.divIcon({
						html: '<b style="font-size:20px;background-color:#003bff6e;border-radius:50%;padding:25px">' + cluster.getChildCount() + '</b>',
						className: 'dce_cluster',
						iconSize: L.point(40, 40)
					});
				},
			});

			this.loadLayers();

			// next step
			// get and transform data into LatLon Object
			this.getDataRepeater();
		}

		buildMap() {
			// https://leafletjs.com/reference.html#map-option
			// build map center Europe with standard zoom will be override
			this.map = L.map(this.elements.$mapWrapper[0], {
				layers: [this.baseMaps[this.elementSettings.map_type]],
				zoomControl: ('yes' == this.elementSettings.zoom_control) ? true : false,
				boxZoom: ('yes' == this.elementSettings.box_zoom) ? true : false,
				doubleClickZoom: ('yes' == this.elementSettings.double_click_zoom) ? true : false,
				dragging: ('yes' == this.elementSettings.dragging) ? true : false,

			})
				.setView([45.43444, 12.33808], this.elementSettings.zoom.size);

			if ('yes' == this.elementSettings.enable_layers_group) {
				L.control.layers(this.baseMaps).addTo(this.map);
			}

			if ('yes' == this.elementSettings.enable_map_scale) {
				L.control.scale({
					maxWidth: this.elementSettings.scale_panel_width.size,
					metric: ('yes' == this.elementSettings.scale_panel_metric) ? true : false,
					imperial: ('yes' == this.elementSettings.scale_panel_imperial) ? true : false,
				}).addTo(this.map);
			}

			if ('yes' == this.elementSettings.enable_marker_cluster_group) {
				this.map.addLayer(this.markerClusterGroup);
			}
		}

		/**
		 * Build LatLon Object from backend repeater
		 */
		async getDataRepeater() {

			let dataRepeater = this.elements.$mapWrapper[0].getAttribute('data_repeater') || [];
			this.dataRepeater = JSON.parse(dataRepeater);

			// all latlon data converted from repeater
			this.latLonObject = await this.buildData();

			// build markers
			this.markers = await this.createMarkers();

			if ('yes' == this.elementSettings.enable_circles) {
				this.circles = await this.createCircles();
			}

			//init default map
			this.buildMap();

			// set zoom from backend
			this.map.setZoom(this.elementSettings.zoom.size);

			// set Circles
			if (this.circles != undefined) {
				this.setCircle();
			}

			// fire fitBound only if have more then one data object
			if (this.latLonObject.length == 1) {
				this.markers[0].addTo(this.map);
				this.map.setView([this.markers[0].getLatLng().lat, this.markers[0].getLatLng().lng], this.getElementSettings('zoom').size);
				(this.circles) && this.circles[0].addTo(this.map);
			} else {
				this.fireFitBound();
			}

		}

		/**
		 * active circles
		 */
		setCircle() {
			this.circles.map((val) => {
				val.addTo(this.map);
			})
		}

		fireFitBound() {
			var featureGroup = L.featureGroup(this.markers).addTo(this.map);
			this.map.fitBounds(featureGroup.getBounds(), { padding: [30, 30] });
		}

		/**
		 * Create Circles from  LatLon Object this.latLonObject
		 */
		createCircles() {

			const promises = this.latLonObject.map(async(obj) => {

				let data = await this.buildCircle(obj);
				if ("OK" == data.status) {
					return data.circle;
				};
				if ("NOT" == data.status) {
					console.log('Error to create marker');
				}
			});
			return Promise.all(promises);

		}

		/**
		 * Build Circle
		 */
		async buildCircle(obj) {
			return new Promise((resolve, reject) => {
				var circle = L.circle({ lon: obj.lon, lat: obj.lat }, {
					radius: this.elementSettings.radius_circles.size,
					color: this.elementSettings.color_circles,
					weight: this.elementSettings.weight_circles.size,
					opacity: this.elementSettings.opacity_circles.size
				})
				if (circle) {
					resolve({ status: "OK", circle });
				} else {
					resolve({ status: "NOT", circle: undefined });
				}
			})
		}

		buildCustomMarker(marker_obj) {
			var custom_icon = L.icon({
				iconUrl: marker_obj.image_url,
				iconSize: [marker_obj.width_marker, marker_obj.height_marker],

			});
			return custom_icon;
		}

		/**
		 * Create markers from  LatLon Object this.latLonObject
		 */
		createMarkers() {
			const promises = this.latLonObject.map(async(obj, index) => {
				let custom_icon = null;
				let custom_infowindow_text = null;
				// custom Markers

				let custom_marker = ('address' == this.elementSettings.map_data_type) ? 'custom_marker_address' : 'custom_marker_latlon';
				custom_marker = this.dataRepeater[index][custom_marker];

				let image_marker = ('address' == this.elementSettings.map_data_type) ? 'image_marker_address' : 'image_marker_latlon';
				image_marker = this.dataRepeater[index][image_marker];

				// custom InfoWindow
				let custom_infowindow = ('address' == this.elementSettings.map_data_type) ? 'custom_infowindow_address' : 'custom_infowindow_latlon';
				custom_infowindow = this.dataRepeater[index][custom_infowindow];

				if ("yes" == custom_marker && "" != image_marker.url) {
					let height_marker_address = ('address' == this.elementSettings.map_data_type) ? 'height_marker_address' : 'height_marker_latlon';
					let height_marker = this.dataRepeater[index][height_marker_address];

					let width_marker_address = ('address' == this.elementSettings.map_data_type) ? 'width_marker_address' : 'width_marker_latlon';
					let width_marker = this.dataRepeater[index][width_marker_address];

					custom_icon = this.buildCustomMarker({ image_url: image_marker.url, height_marker: height_marker.size, width_marker: width_marker.size });
				}

				if ("yes" == custom_infowindow) {
					custom_infowindow_text = ('address' == this.elementSettings.map_data_type) ? 'text_infowindow_address' : 'text_infowindow_latlon';
					custom_infowindow_text = this.dataRepeater[index][custom_infowindow_text];

				}

				let data = await this.buildMarker(obj, custom_icon, custom_infowindow_text);
				if ("OK" == data.status) {
					return data.marker;
				};
				if ("NOT" == data.status) {
					console.log('Error to create marker');
				}
			});
			return Promise.all(promises);
		}


		/**
		 * Build Marker
		 */
		async buildMarker(obj, custom_icon, custom_infowindow_text) {

			let optionObj = {};
			if (custom_icon) {
				optionObj.icon = custom_icon
			}
			return new Promise((resolve, reject) => {

				var marker = L.marker({ lon: obj.lon, lat: obj.lat }, optionObj)

				this.markerClusterGroup.addLayer(marker);
				marker.on('click', (e) => {

					var infoWindow = (custom_infowindow_text) ? custom_infowindow_text : `Lat: ${e.latlng.lat} , Lon : ${e.latlng.lng}`;
					marker.bindPopup(infoWindow);
					var popup = marker.getPopup();
					if (popup.isOpen()) {
						marker.openPopup();
					}
				})
				if (marker) {
					resolve({ status: "OK", marker });
				} else {
					resolve({ status: "NOT", marker: null });
				}
			})

			return marker;
		}

		getLatLonFromAddress(address) {
			return new Promise((resolve, reject) => {

				const url = location.protocol + '//nominatim.openstreetmap.org/search?format=jsonv2&q=' + address;
				fetch(url)
					.then((response) => response.json())
					.then((data) => {
						if (data.length > 0) {
							let latlon = { lat: data[0].lat, lon: data[0].lon }
							resolve({ status: "OK", results: latlon });
						} else {
							reject({ status: "NOT", results: "empty address" });
						}
					});
			})
		}

		async buildData() {

			if ('address' == this.elementSettings.map_data_type) {

				const promises = this.dataRepeater.map(async(val) => {

					let data = await this.getLatLonFromAddress(val.rep_address);
					if ("OK" == data.status) {
						return data.results;
					}
					if ("NOT" == data.status) {
						console.error('Error build data from address')
						console.log(data.results);
					}

				});
				return Promise.all(promises);

			}
			if ('latlon' == this.elementSettings.map_data_type) {

				let data = [];
				this.dataRepeater.map((val) => {
					data.push({ lat: val.rep_lat, lon: val.rep_lon })
				});
				return data;
			}
		}

		onInit() {
			super.onInit();
			const elementSettings = this.getElementSettings();
			this.elementSettings = elementSettings;

			// initialization Map and Layers
			this.initialization();
		}

		onElementChange(propertyName) {
			if (propertyName === 'zoom') {
				this.map.setZoom(this.getElementSettings('zoom').size);
				return;
			}
		}
	}

	const addHandler = ($element) => {
		elementorFrontend.elementsHandler.addHandler(DynamicOsmMap, { $element, });
	};
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-osm-map.default', addHandler);
});
