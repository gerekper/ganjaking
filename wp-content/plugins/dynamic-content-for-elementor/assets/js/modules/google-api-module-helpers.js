/**
 * Make object Map
 * @param {DomElement} element
 * @param {object} params 
 * @returns  {object}
 */

let makeGoogleMap = (element, params) => {
	return new Promise((resolve, reject) => {
		try {
			var map = new google.maps.Map(element, params);
			resolve({ status: "OK", map });
		} catch (error) {
			reject({ status: "MAP FAILED", message: "Error load map" + error.message });
		}
	})
}

/**
 * Get result address obj from address string
 * @param {string} address 
 * @returns {object}
 */
let findLatLongCb = (address) => {
    return new Promise((resolve, reject) => {
        var geocoder = new google.maps.Geocoder();

        geocoder.geocode({ 'address': address }, (results, status) => {
            if (status == google.maps.GeocoderStatus.OK) {
                resolve({ status: "OK", results: results[0] });
            } else {
                reject({ status: "NOT", results: results });
            }
        });
    })

}

/**
 * Ger result address obj from address latLng obj
 * @param {lat, lng} location 
 * @returns {object}
 */
let findAddressCb = (location) => {
	return new Promise((resolve, reject) => {
		var geocoder = new google.maps.Geocoder();

		geocoder.geocode({ 'location': location }, (results, status) => {
			if (status == google.maps.GeocoderStatus.OK) {
				resolve({ status: "OK", results: results[0] });
			} else {
				reject({ status: "NOT", results: results });
			}
		});
	})

}

/**
 * Get Direction Object from data
 * @param {boolean} travel_mode 
 * @param {object} data_obj 
 * @param {Function} callback 
 * * @returns {object}
 */
let fireDirectionServices = (travel_mode, data_obj, callback) => {

    var suppress_markers = (undefined != data_obj.suppressMarkers) ? data_obj.suppressMarkers : false;
    var suppress_infoWindows = (undefined != data_obj.suppressInfoWindows) ? data_obj.suppressInfoWindows : false;
    var map = data_obj.map;

    var directionsService = new google.maps.DirectionsService();
    var directionsDisplay = new google.maps.DirectionsRenderer({
        suppressMarkers: suppress_markers, // delete default marker
        suppressInfoWindows: suppress_infoWindows,
        preserveViewport: false, // disable block zoom
        draggable: false,
        map,
    });

    var request = {
        // origin type => LatLng | String | google.maps.Place
        origin: data_obj.results.data_obj.start,
        destination: data_obj.results.data_obj.end,
        travelMode: google.maps.TravelMode[travel_mode],
        unitSystem: google.maps.UnitSystem.IMPERIAL
    }

    directionsService.route(request, (result, status) => {

        if (status == google.maps.DirectionsStatus.OK) {
            jQuery('#result span#loading').hide();
            // display route
            directionsDisplay.setDirections(result)
            callback({ status: "OK", results: { result } });
        } else if ("ZERO_RESULTS" == status) {
            callback({ status: status, results: { result } });
            console.error(status);
        }
    })
}

/**
 * Get Elevation Object from data single call data position
 * @param {object} data 
 * @param {Function} callback 
 */
let displayLocationElevation = (data, callback) => {

        const elevator = new google.maps.ElevationService();
        const infowindow_start = new google.maps.InfoWindow({});
        const infowindow_end = new google.maps.InfoWindow({});

        infowindow_start.open(data.map);
        infowindow_end.open(data.map);
        // Initiate the location request
        elevator
            .getElevationForLocations({
                locations: [data.results.data_obj.start],
            })
            .then(({ results }) => {
                infowindow_start.setPosition(data.results.data_obj.start);
                // Retrieve the first result
                if (results[0]) {
                    infowindow_start.setContent(
                        "The elevation at this point lat: <b>" + data.results.data_obj.start.lat + "</b> - lng: <b>" + data.results.data_obj.start.lng + "</b> <br>is <b>" +
                        Math.round(results[0].elevation * 100) / 100 + ' meters.' +
                        "</b> meters."
                    );
                    var marker__start = new google.maps.Marker({
                        position: data.results.data_obj.start,
                        map: data.map
                    });
                    google.maps.event.addListener(marker__start, 'click', () => {
                        infowindow_start.open(data.map);
                    });
                }
                callback({ status: "OK", results: results, data: data })
            })
            .catch((e) => {
                    console.error("Elevation service failed due to: " + e);
                    infowindow_start.setContent("Elevation service failed due to: " + e);
                    infowindow_end.setContent("Elevation service failed due to: " + e);
                    callback({ status: "OK", message: "Elevation service failed due to: " + e })
                }

            );
    }
    /**
     * Get Elevation Object from data multiple call data positions
     * @param {object} data 
     * @param {Function} callback 
     */
let displayLocationElevation_v2 = (data, callback) => {
    const elevator = new google.maps.ElevationService();
    let results_obj = [];
    let markers = [];
    let infowindow = [];

    // cycle data latLng
    jQuery.map(data.results.data_obj, (value, index_infowindow) => {
        var i = index_infowindow;

        infowindow[i] = new google.maps.InfoWindow({});
        infowindow[i].open(data.map);

        // get elevation
        elevator
            .getElevationForLocations({
                locations: [value],
            })
            .then(({ results }) => {

                infowindow[i].setPosition(value);
                // Push result in the global array
                results_obj[i] = results[0]

                if (results[0]) {
                    infowindow[i].setContent(
                        "The elevation at this point lat: <b>" + value.lat + "</b> - lng: <b>" + value.lng + "</b> <br>is <b>" +
                        Math.round(results[0].elevation * 100) / 100 + ' meters.' +
                        "</b> meters."
                    );
                    markers[i] = new google.maps.Marker({
                        position: value,
                        map: data.map
                    });
                    google.maps.event.addListener(markers[i], 'click', () => {
                        infowindow[i].open(data.map);
                    });
                }
            })
            .catch((e) => {
                    console.error("Elevation service failed due to: " + e);
                    infowindow[i].setContent("Elevation service failed due to: " + e);
                    callback({ status: "FAILED", message: "Elevation service failed due to: " + e })
                }
            );

    })
    callback({ results_obj: results_obj, data_in: data, infowindow: infowindow, markers: markers });
}

/**
 * Get Elevation Object from data multiple call data positions
 * @param {object} data 
 * @param {Function} callback 
 */
let displayLocationElevationAlongaPath = (data, meter = false) => {

    return new Promise((resolve, reject) => {
        data.map.setCenter(data.results[0]);
        data.map.setMapTypeId('terrain');
        data.map.setZoom(data.zoom)

        const elevator_ = new google.maps.ElevationService();

        var sample_points = (data.elevation_points != undefined) ? data.elevation_points : 3;
        elevator_
            .getElevationAlongPath({
                path: data.results,
                samples: sample_points
            })
            .then((data_results) => {
                if (meter) {
                    var meter_points = data_results.results.map((value) => {
                        return Math.round(value.elevation * 100) / 100
                    })
                } else {
                    var meter_points = data_results.results.map((value) => {
                        return {
                            lat: value.location.lat(),
                            lng: value.location.lng()
                        }
                    })
                }
                resolve({ status: "OK", meter_points: meter_points });
            })
            .catch((e) => {
                reject({ status: "ELEVATION FAILED", message: "Error load elevation along path" + e.message });
            });
    })
}

/**
 * Check if Polyline is active ad fire it
 * @param {object} data 
 * data.point -> [{lat,lng},{lat,lng}]
 */
let addPolyline = (data) => {
    /**
     * BACKWARD_CLOSED_ARROW 
     * BACKWARD_OPEN_ARROW
     * CIRCLE
     * FORWARD_CLOSED_ARROW
     * FORWARD_OPEN_ARROW
     * https://developers.google.com/maps/documentation/javascript/symbols
     */

    const symbolStart = {
        path: data.elevation_polyline_start_symbol,
        anchor: { x: data.elevation_polyline_start_symbol_position_x.size, y: data.elevation_polyline_start_symbol_position_y.size },
        fillColor: data.elevation_polyline_start_symbol_fillcolor,
        fillOpacity: data.elevation_polyline_start_symbol_fillopacity,
        rotation: data.elevation_polyline_start_symbol_rotation.size,
        scale: data.elevation_polyline_start_symbol_scale.size,
        strokeColor: data.elevation_polyline_start_symbol_strokecolor,
        strokeOpacity: data.elevation_polyline_start_symbol_strokeopacity,
        strokeWeight: data.elevation_polyline_start_symbol_strokeweight,
    };

    const intermediaSymbol = {
        path: google.maps.SymbolPath[data.elevation_polyline_symbol_path],
        anchor: { x: 0, y: 0 },
        fillColor: data.elevation_polyline_svg_fillcolor, // inside
        fillOpacity: 1,
        rotation: data.elevation_polyline_svg_rotation.size,
        strokeColor: data.elevation_polyline_svg_strokecolor, // border
        strokeOpacity: 1,
        strokeWeight: data.elevation_polyline_svg_strokeweight,
    };

    const symbolEnd = {
        path: data.elevation_polyline_end_symbol,
        anchor: { x: data.elevation_polyline_end_symbol_position_x.size, y: data.elevation_polyline_end_symbol_position_y.size },
        fillColor: data.elevation_polyline_end_symbol_fillcolor,
        fillOpacity: data.elevation_polyline_end_symbol_fillopacity,
        rotation: data.elevation_polyline_end_symbol_rotation.size,
        scale: data.elevation_polyline_end_symbol_scale.size,
        strokeColor: data.elevation_polyline_end_symbol_strokecolor,
        strokeOpacity: data.elevation_polyline_end_symbol_strokeopacity,
        strokeWeight: data.elevation_polyline_end_symbol_strokeweight,
    };

    //Polyline || Polygon ref: https://developers.google.com/maps/documentation/javascript/shapes#polygons
    // https://developers.google.com/android/reference/com/google/android/gms/maps/model/Polyline
    var line = new google.maps.Polyline({
        path: data.point,
        editable: false,
        strokeColor: data.elevation_polyline_strokecolor,
        strokeWeight: data.elevation_polyline_strokeweight,
        clickable: false,
        strokeOpacity: data.elevation_polyline_strokeopacity,
        icons: [{
                icon: symbolStart,
                offset: data.elevation_polyline_start_symbol_offset + "%",
            },
            {
                icon: intermediaSymbol,
                repeat: data.data_repeat_offset + '%',
            },
            {
                icon: symbolEnd,
                offset: data.elevation_polyline_end_symbol_offset + "%",
            },
        ],
        map: data.map,
    });
    // set custom icon

    if (data.elevation_polyline_svg_animation) {
        let count = 0;
        window.setInterval(() => {
            count = (count + 1) % 200;

            const icons = line.get("icons");

            icons[data.elevation_polyline_svg_symbol_animation].offset = count / 2 + "%";
            line.set("icons", icons);
        }, data.elevation_polyline_svg_animation_speed);
    }

}

/**
 * Check if Geolocation is active ad fire address
 * @param {object} map 
 * @param {Function} callback 
 */
let geoLocalization = (dataObj, callback) => {
	const locationButton = document.createElement("button");
	locationButton.textContent = dataObj.buttons.geo_button_text;
	locationButton.id = (dataObj.buttons.id) ? dataObj.buttons.id : "get_button";

	locationButton.classList.add("custom-map-control-button");
	dataObj.map.controls[google.maps.ControlPosition.TOP_CENTER].push(locationButton);
	locationButton.addEventListener("click", () => {

		if (navigator.geolocation) {

			navigator.geolocation.getCurrentPosition(
				(position) => {
					const pos = {
						lat: position.coords.latitude,
						lng: position.coords.longitude,
					};
					callback({ status: "OK", position: pos });

					dataObj.map.setCenter(pos);
				},
				() => {
					this.handleLocationError(map, true, new google.maps.InfoWindow(), map.getCenter());
				}
			);
		} else {
			// Browser doesn't support Geolocation
			this.handleLocationError(map, false, new google.maps.InfoWindow(), map.getCenter());
		}
	});
	callback({ status: "NOCLICK" });

}

/**
 * 
 * @param {DomElement} mapElement 
 * @param {object} mapParam 
 * @returns 
 */
let resetMap = (mapElement, mapParam) => {
    let map = new google.maps.Map(mapElement, mapParam);
    return map;
}

export {
    makeGoogleMap,
    findLatLongCb,
    findAddressCb,
    fireDirectionServices,
    displayLocationElevation,
    displayLocationElevation_v2,
    displayLocationElevationAlongaPath,
    geoLocalization,
    addPolyline,
    resetMap
};
