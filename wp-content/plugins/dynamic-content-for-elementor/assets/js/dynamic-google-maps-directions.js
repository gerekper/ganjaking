import * as google_helpers from "./modules/google-api-module-helpers.js";
jQuery(window).on('elementor/frontend/init dynamicooo/google-maps-init', () => {
    class DynamicMapDistance extends elementorModules.frontend.handlers.Base {

        getDefaultSettings() {
            return {};
        }

        getDefaultElements() {
            return {
                google_utility: google_helpers,
                travel_mode: this.getElementSettings().travel_mode.toUpperCase(),
                customMarkers: {
                    start: {
                        url: (this.getElementSettings().departure_marker_image != undefined) ? this.getElementSettings().departure_marker_image.url : "",
                        title: this.getElementSettings().departure_marker_title,
                        label: this.getElementSettings().departure_marker_label,
                        size: (this.getElementSettings().departure_marker_size != undefined) ? this.getElementSettings().departure_marker_size.size : 30,
                    },
                    end: {
                        url: (this.getElementSettings().destination_marker_image != undefined) ? this.getElementSettings().destination_marker_image.url : "",
                        title: this.getElementSettings().destination_marker_title,
                        label: this.getElementSettings().destination_marker_label,
                        size: (this.getElementSettings().destination_marker_size != undefined) ? this.getElementSettings().destination_marker_size.size : 30,

                    }
                },
                map: this.$element.find(".map")[0],
                map_name: this.getElementSettings().map_name,
                wrapper: this.$element.find(".map")[0],
                mapParams: {},
                zoom: this.getElementSettings().zoom.size,
                mapType: this.getElementSettings().map_type,
                use_geolocation_as: this.getElementSettings().use_geolocation_as,
                distance_geolocation_zoom: this.getElementSettings().distance_geolocation_zoom,
                distance_geolocation_change_zoom: (this.getElementSettings().distance_geolocation_change_zoom == "" || this.getElementSettings().distance_geolocation_change_zoom == undefined) ? false : true,
                geolocation: Boolean(this.getElementSettings().geolocation),
                geolocation_button_text: this.getElementSettings().geolocation_button_text,
                markers: Boolean(this.getElementSettings().markers),
                infoWindow: Boolean(this.getElementSettings().info_window),
                scrollwheel: Boolean(this.getElementSettings().scrollwheel),
                maptypecontrol: Boolean(this.getElementSettings().maptypecontrol),
                pancontrol: Boolean(this.getElementSettings().pancontrol),
                rotatecontrol: Boolean(this.getElementSettings().rotatecontrol),
                scalecontrol: Boolean(this.getElementSettings().scalecontrol),
                streetviewcontrol: Boolean(this.getElementSettings().streetviewcontrol),
                zoomcontrol: Boolean(this.getElementSettings().zoomcontrol),
                fullscreenControl: Boolean(this.getElementSettings().fullscreenControl),
                locations: {
                    start: {
                        address: "",
                        lat: 45.4408474,
                        long: 12.3155151,
                    },
                    end: {
                        address: "",
                        lat: 45.4600113,
                        long: 9.1797373,
                    }
                }
            };
        }

        refreshInfo() {

            setTimeout(() => {

                // recovery all dynamic tag info
                let $all_ext_info = jQuery('body div#dce-directions-info');
                jQuery($all_ext_info).map((index, e) => {

                    // check if map name is equal to data tag id
                    if (this.elements.map_name == e.getAttribute('data-tag-name')) {

                        // if dynamic tag found take data
                        let dataraw = jQuery(e).find('span').attr('data-directions')
                        let data = JSON.parse(dataraw);

                        this.map_name = data.map_name;
                        this.loading_text = data.loading_text;
                        this.option = data.option;
                        // dynamic ext element to fire
                        let $element_tag = jQuery('body div#dce-directions-info[data-tag-name="' + this.map_name + '"]')

                        // get data from specific map
                        var alldivs = document.querySelectorAll('div.map');
                        jQuery(alldivs).map((index, el) => {
                            if (this.map_name == el.getAttribute('data-map-name')) {
                                this.data_info_elevation_distance_converted = el.getAttribute('distance_info');
                                this.data_info_elevation_distance_converted = JSON.parse(this.data_info_elevation_distance_converted);
                            }
                        });

                        setTimeout(() => {
                            $element_tag.html('');

                            switch (this.option) {
                                case 'miles':
                                    $element_tag.html(this.data_info_elevation_distance_converted.distance_in_miles.toFixed(2));
                                    break;
                                case 'km':
                                    $element_tag.html(this.data_info_elevation_distance_converted.distance_in_kilometers.toFixed(2));
                                    break;
                                case 'text':
                                    $element_tag.html(this.data_info_elevation_distance_converted.duration_text);
                                    break;
                                case 'minutes':
                                    $element_tag.html(this.data_info_elevation_distance_converted.duration_value);
                                    break;
                                case 'mode':
                                    $element_tag.html(this.data_info_elevation_distance_converted.travel);
                                    break;
                            }
                            $element_tag.append("<span class='distance dce-directions-info' data-directions='" + dataraw + "'></span>");
                        }, 4000)
                    }

                });

            }, 1000)
        }

        refreshInstruction() {

            setTimeout(() => {

                // retrieve all dynamic tag info
                let $all_ext_info = jQuery('body div#dce-gm-directions-instructions');
                jQuery($all_ext_info).map((index, e) => {
                    if (this.elements.map_name == e.getAttribute('data-tag-name')) {
                        let map_name = e.getAttribute('data-tag-name');

                        // If a dynamic tag is found, take data
                        var alldivs = document.querySelectorAll('div.map');
                        let distance_instructions = [];
                        jQuery(alldivs).map((index, el) => {
                            if (map_name == el.getAttribute('data-map-name')) {
                                distance_instructions = el.getAttribute('distance_instructions');
                                distance_instructions = JSON.parse(distance_instructions);
                            }
                        });
                        let $element_tag = jQuery('body div#dce-gm-directions-instructions[data-tag-name="' + map_name + '"]')
                        if (distance_instructions.length > 0) {
                            $element_tag.html("");
                            distance_instructions.map((val, index) => {
                                $element_tag.append(
                                    `<p class="single_instruction">${val.instructions}</p>`
                                );
                            })
                        }
                    }
                })


            }, 4000)
        }

        /**
         * Set global configuration - position for init map
         * @returns void
         */
        setConfigurations() {

            // check if map is undefined
            if (this.elements.map === undefined) {
                return console.error('Map not defined')
            }

            // get position from backend object
            let positions = this.elements.map.getAttribute('data-positions' + this.getID()) || [];
            positions = JSON.parse(positions)

            // Exit if the maps doesn't have positions or have less of 2
            if (positions.length < 2) {
                console.error('Default addresses activated')
            }

            // build data start
            if (positions[0]) {
                this.elements.locations.start.address = positions[0].address;
                this.elements.locations.start.lat = parseFloat(positions[0].lat) || 0;
                this.elements.locations.start.long = parseFloat(positions[0].lng) || 0;
            }
            // build data end
            if (positions[1]) {
                this.elements.locations.end.address = positions[1].address;
                this.elements.locations.end.lat = parseFloat(positions[1].lat) || 0;
                this.elements.locations.end.long = parseFloat(positions[1].lng) || 0;
            }
        }

        /**
         * Set global parameters - position for init map
         * @returns void
         */
        setMapParams() {
            this.elements.mapParams = {
                mapTypeId: this.elements.mapType,
                zoom: this.elements.zoom,
                scrollwheel: this.elements.scrollwheel,
                mapTypeControl: this.maptypecontrol,
                panControl: this.elements.pancontrol,
                rotateControl: this.elements.rotatecontrol,
                scaleControl: this.elements.scalecontrol,
                streetViewControl: this.elements.streetviewcontrol,
                zoomControl: this.elements.zoomcontrol,
                fullscreenControl: this.elements.fullscreenControl,
                center: {
                    lat: this.elements.locations.start.lat,
                    lng: this.elements.locations.start.long,
                },
            };
        }

        /**
         * Set global parameters - position for init map
         * @returns void
         */
        async buildDirection() {

            if ('address' == this.getElementSettings().map_data_type) {
                if (this.elements.locations.start.address.length === 0 || this.elements.locations.end.address.length === 0) {
                    console.error('Address not found');
                    return;
                }
            }
            // init direction check and get data latlong or Address return data
            try {
                const data = await this.setOriginalFinalPosition();

                // set the center map to origin position if get address string
                if ('address' == this.getElementSettings().map_data_type) {

                    const origin_results_obj = await this.elements.google_utility.findLatLongCb(data.results.origin_address);
                    if ("OK" == origin_results_obj.status) {
                        this.elements.mapParams.center = {
                            lat: origin_results_obj.results.geometry.location.lat(),
                            lng: origin_results_obj.results.geometry.location.lng()
                        }
                    }
                }

                // check data status
                if ("OK" == data.status) {
                    // Init the MAP with the start object (latLng or formatted address )
                    try {
                        var mapRequest = await this.elements.google_utility.makeGoogleMap(this.elements.map, this.elements.mapParams);

                        var map = mapRequest.map;
                        // build standard data to send
                        var data_to_send = {
                            map: map,
                            results: {
                                data_obj: {
                                    start: data.results.origin_address,
                                    end: data.results.final_address,
                                }
                            }
                        }

                        // check if geoLocalization is active and change start or end position
                        // return {lat lng} Obj
                        this.geoLocalization(map, async(data) => {
                            // check data status
                            if (data.status == "OK") {

                                // apply geoLocalization to start point or end point
                                if ("departure" == this.elements.use_geolocation_as) {
                                    data_to_send.results.data_obj.start = data.geo_position;
                                } else if ("destination" == this.elements.use_geolocation_as) {
                                    data_to_send.results.data_obj.end = data.geo_position;
                                }

                                // get new map with new param with geoLocalization
                                var mapRequest = await this.elements.google_utility.makeGoogleMap(this.elements.map, this.elements.mapParams);

                                var map = mapRequest.map;
                                data_to_send.map = map;

                                this.fireDirectionServices(data_to_send);

                            }
                        });

                        //without localization
                        this.fireDirectionServices(data_to_send);

                    } catch (error) {
                        // map error
                        console.error(error.message);
                    }

                } // status ok
            } catch (error) {
                console.error(error.message);
            }

        }

        /**
         * Set origin and final address Type Address || lat/lng from widget
         * @param {status, {}} callback 
         * @returns latLong Obj  || formattedAddress
         */
        async setOriginalFinalPosition() {
            return new Promise(async(resolve, reject) => {

                var origin_address;
                var final_address;

                if ('address' == this.getElementSettings().map_data_type) {

                    if (typeof this.elements.locations.start.address !== 'string') { return; }

                    // get first async address
                    try {
                        const data_start = await this.elements.google_utility.findLatLongCb(this.elements.locations.start.address);
                        if (data_start.status == "OK") {

                            // data_start.results => results api object
                            this.elements.locations.start.address = data_start.results;

                            // fire address 2
                            if (typeof this.elements.locations.end.address !== 'string') { return; }
                            // get second async address
                            const data_end = await this.elements.google_utility.findLatLongCb(this.elements.locations.end.address);
                            if (data_end.status == "OK") {

                                // data_end.results => results api object
                                this.elements.locations.end.address = data_end.results;

                                origin_address = this.elements.locations.start.address.formatted_address;
                                final_address = this.elements.locations.end.address.formatted_address;

                                resolve({ status: "OK", results: { origin_address, final_address } });
                            }
                        }
                    } catch (error) {
                        console.error(error.message);
                    }

                } else if (this.getElementSettings().map_data_type == 'latlng') {
                    var origin_address = { lat: this.elements.locations.start.lat, lng: this.elements.locations.start.long };
                    var final_address = { lat: this.elements.locations.end.lat, lng: this.elements.locations.end.long };
                    resolve({ status: "OK", results: { origin_address, final_address } });
                }
            })

        }

        /**
         * Check if Geolocation is active ad fire address
         * @param {*} map 
         * @param {*} callback 
         */
        geoLocalization(map, callback) {
            if (this.elements.geolocation) {
                var data = {
                        map: map,
                        buttons: {
                            geo_button_text: this.elements.geolocation_button_text,
                            id: this.getID()
                        },
                    }
                this.elements.google_utility.geoLocalization(data, (data_geo_result) => {
					if ("OK" == data_geo_result.status) {
						// reset map 
						data.map = this.elements.google_utility.resetMap(this.elements.map, this.elements.mapParams)
						// change global data start latlng
						callback({ status: "OK", geo_position: data_geo_result.position });

					}
					if ("NOCLICK" == data_geo_result.status) {
						callback({ status: "NOCLICK", geo_position: null });
					}
				})
            }
        }

        /**
         * Fire direction services and if enabled add custom markers
         * Show instructions if enabled
         * Show info repeaters if enabled
         * @param {*} map 
         * @param {*} origin_address 
         * @param {*} final_address 
         */
        fireDirectionServices(data_to_send) {
            data_to_send.suppressMarkers = this.elements.markers;
            data_to_send.suppressInfoWindows = this.elements.info_window;

            this.elements.google_utility.fireDirectionServices(this.elements.travel_mode, data_to_send, (directions) => {

                if ("OK" == directions.status) {
                    var result = directions.results.result;
                    var map = data_to_send.map;
                    jQuery('#result span#loading').hide();

                    var distance = result.routes[0].legs[0].distance;
                    var duration = result.routes[0].legs[0].duration;
                    var distance_in_kilometers = distance.value / 1000;
                    var distance_in_miles = distance.value / 1609.34;
                    var duration_text = duration.text;
                    var duration_value = duration.value;
                    let distance_info = {
                            distance_in_miles,
                            distance_in_kilometers,
                            duration_text,
                            duration_value,
                            travel: this.elements.travel_mode,
                        }
                    // insert distance info in attr distance_info for widget map distance info
                    jQuery(this.elements.wrapper).attr('distance_info', JSON.stringify(distance_info))
                    // insert the instruction in arr distance instructions for widget map distance instructions
                    jQuery(this.elements.wrapper).attr('distance_instructions', JSON.stringify(result.routes[0].legs[0].steps))

                    // change markers if enabled
                    if (this.elements.markers) {
                        var leg = result.routes[0].legs[0];

                        this.makeMarker(
                            'start',
                            map,
                            leg.start_location, { url: this.elements.customMarkers.start.url, size: [30, 30] },
                            this.elements.customMarkers.start.title,
                            this.elements.customMarkers.start.label,
                            this.elements.customMarkers.start.size
                        );

                        this.makeMarker(
                            'end',
                            map,
                            leg.end_location, { url: this.elements.customMarkers.end.url, size: [30, 30] },
                            this.elements.customMarkers.end.title,
                            this.elements.customMarkers.end.label,
                            this.elements.customMarkers.end.size
                        );

                    }

                    this.refreshInstruction();
                    this.refreshInfo();

                }
            })

            return;
        }

        makeMarker(point, map, position, urlSizeObj, title, label, size) {
            var icon = {
                url: urlSizeObj.url,
                anchor: new google.maps.Point(0, 0),
                scaledSize: new google.maps.Size(size, size),
            }
            var marker = new google.maps.Marker({
                position: position,
                draggable: false,
                map: map,
                icon: icon,
                title: title,
                label: label,
                animation: google.maps.Animation.DROP,
            });

            this.addInfoWindow(point, marker, position, map);
        }

        async addInfoWindow(point, marker, location, map) {

            var infoWindowContent = "";
            // check if infoWindow is enabled
            if (this.elements.info_window) {
                if ("start" == point) {
                    infoWindowContent = this.getElementSettings().departure_info_window
                } else if ("end" == point) {
                    infoWindowContent = this.getElementSettings().destination_info_window
                }
            } else {
                // not enabled. Retrieve default address
                try {
                    var latlng = new google.maps.LatLng(location.lat(), location.lng());
                    const address = await this.elements.google_utility.findAddressCb(latlng);
                    if ("OK" == address.status) {
                        infoWindowContent = address.results.formatted_address;
                    }
                } catch (error) {
                    console.error(error.message);
                }
            }
            // infoWindows fire
            setTimeout(() => {
                var infowindow = new google.maps.InfoWindow();
                infowindow.setContent(infoWindowContent);
                infowindow.setPosition(location);

                // add marker custom event listener
                marker.addListener("click", (e) => {
                    infowindow.open({
                        anchor: marker,
                        map,
                        shouldFocus: false,
                    });
                });
            }, 2000)
        }

        onInit() {
            super.onInit();

            // setConfigurations
            this.setConfigurations();
            // setParams
            this.setMapParams();
            // Fire Map
            if (this.elements.map !== undefined) {
                this.buildDirection();
            }
        }
    }

    const addHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(DynamicMapDistance, { $element });
    };
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-google-maps-directions.default', addHandler);
});
