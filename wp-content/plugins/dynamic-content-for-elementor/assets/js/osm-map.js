jQuery( window ).on( 'elementor/frontend/init', () => {
	class OsmMap extends elementorModules.frontend.handlers.Base {
 		getDefaultSettings() {
 			return {
 				selectors: {
 					mapWrapper: '.dce-osm-wrapper',
 				},
 			};
 		}

 		getDefaultElements() {
 			const selectors = this.getSettings( 'selectors' );
 			return {
 				$mapWrapper: this.$element.find( selectors.mapWrapper ),
 			};
 		}

		getCoordFromAdress(address, callback) {
			const url = location.protocol + '//nominatim.openstreetmap.org/search?format=json&q=' + address;
			jQuery.get(url, data => {
				let lon = 0;
				let lat = 0;
				if (data.length) {
					lon = data[0].lon;
					lat = data[0].lat;
				}
				callback(lon, lat);
			});
		}

 		onInit() {
 			super.onInit();
 			const elementSettings = this.getElementSettings();
			this.getCoordFromAdress(elementSettings.address, (lon, lat) => {
				this.map = L.map(this.elements.$mapWrapper[0]).setView({lon: lon, lat: lat}, elementSettings.zoom.size );
				this.marker = L.marker({lon: lon, lat: lat});
				this.marker.addTo(this.map);
				L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					maxZoom: 19,
					attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
				}).addTo(this.map);
 			});
		}

 		onElementChange( propertyName ) {
			if ( propertyName === 'zoom' ) {
				this.map.setZoom( this.getElementSettings('zoom').size);
				return;
			}
			if ( propertyName === 'address' ) {
				this.getCoordFromAdress( this.getElementSettings('address'), (lon, lat) => {
					this.map.removeLayer(this.marker); // remove previous marker.
					this.map.setView({lon: lon, lat: lat}, this.getElementSettings('zoom').size );
					this.marker = L.marker({lon: lon, lat: lat});
					this.marker.addTo(this.map);
				});
				return;
			}
 		}
	}


	const addHandler = ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( OsmMap, { $element, } );
	};
	elementorFrontend.hooks.addAction( 'frontend/element_ready/dce-osm-map.default', addHandler );
});
