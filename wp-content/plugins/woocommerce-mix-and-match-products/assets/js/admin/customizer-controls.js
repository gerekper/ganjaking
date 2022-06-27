( function() {
	'use strict';

    wp.customize.bind( 'ready', function() {

		// Detect when the Mix and Match panel is expanded so we can preview a Mix and Match product.
		wp.customize.section( 'wc_mnm', function( section ) {

			section.expanded.bind( function( isExpanding ) {

				// Value of isExpanding will = true if you're entering the section, false if you're leaving it.
				if ( isExpanding ) {

					// Only send the previewer to the product page, if we're not already on it.
                    var current_url = wp.customize.previewer.previewUrl();
					current_url = current_url.includes( WC_MNM_CONTROLS.product_page );

					if ( ! current_url ) {
						wp.customize.previewer.send( 'wc-mnm-open-product', { expanded: isExpanding } );
					}

				}
			} );

		} );

	} );

    wp.customize.bind( 'ready', function() {
        
        // Add callback for when the wc_mnm_number_columns setting exists.
        wp.customize( 'wc_mnm_layout', function( setting ) {
            var isDisplayed, controlActiveState;

            /**
             * Determine whether the setting is visible.
             *
             * @returns {boolean} Is displayed?
             */
            isDisplayed = function() {
                return 'grid' === setting.get();
            };

            /**
             * Update a control's active state according to the wc_mnm_number_columns setting's value.
             *
             * @param {wp.customize.Control} control Customizer Control.
             */
            controlActiveState = function( control ) {
                var setActiveState = function() {
                    control.active.set( isDisplayed() );
                };

                // FYI: With the following we can eliminate all of our PHP active_callback code.
                control.active.validate = isDisplayed;

                // Set initial active state.
                setActiveState();

                /*
                * Update activate state whenever the setting is changed.
                * Even when the setting does have a refresh transport where the
                * server-side active callback will manage the active state upon
                * refresh, having this JS management of the active state will
                * ensure that controls will have their visibility toggled
                * immediately instead of waiting for the preview to load.
                * This is especially important if the setting has a postMessage
                * transport where changing the setting wouldn't normally cause
                * the preview to refresh and thus the server-side active_callbacks
                * would not get invoked.
                */
                setting.bind( setActiveState );
            };

            // Call controlActiveState on the wc_mnm_number_columns and controls when they exist.
            wp.customize.control( 'wc_mnm_number_columns', controlActiveState );

        } );

        

    } );

}() );