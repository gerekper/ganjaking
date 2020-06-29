<?php

$search_for_location_text = get_option( '_wc_restrictions_locations_type' ) == 'states' ?
	__( 'Search for a state / region / territory&hellip;', 'wc_catalog_restrictions' ) :
	__( 'Search for a country&hellip;', 'wc_catalog_restrictions' );
?>

<div id="wc_catalog_restrictions" class="panel woocommerce_options_panel wc-metaboxes-wrapper hidden">

    <div class="wc-metabox">
		<?php if ( $locations_enabled == 'yes' ) : ?>
            <div class="options_group">
				<?php
				woocommerce_wp_select( array(
						'id'          => '_wc_restrictions_location',
						'label'       => __( 'What locations can view this product?', 'wc_catalog_restrictions' ),
						'options'     => array(
							'inherit'    => __( 'Use Category Settings', 'wc_catalog_restrictions' ),
							'public'     => __( 'All Locations', 'wc_catalog_restrictions' ),
							'restricted' => __( 'Specific Locations', 'wc_catalog_restrictions' )
						),
						'std'         => 'inherit',
						'desc_tip'    => true,
						'description' => __( 'Choose if you would like to limit this product to specific locations.', 'wc_catalog_restricitons' )
					)
				);
				?>


                <div id="wc_catalog_restrictions_locations_container" class="wc_restrictions_options_panel" style="<?php echo( get_post_meta( $object->get_id(), '_wc_restrictions_location', true ) == 'restricted' ? 'display:block;' : 'display:none;' ); ?>">
                    <p class="form-field">
                        <label for="wc_restrictions_locations"><?php _e( 'Target Locations', 'wc_catalog_restrictions' ); ?></label>
						<?php echo wc_help_tip( 'Choose locations for this product.  Only users who select a matching location will be able to view and purchase this product.' ) ?>
                        <select style="width: 50%;" name="wc_restrictions_locations[]" class="multiselect wc-enhanced-select" multiple="multiple" data-placeholder="<?php echo $search_for_location_text; ?>">
							<?php woocommerce_catalog_restrictions_country_multiselect_options( $current_locations ); ?>
                        </select>
                    </p>
                </div>

            </div>
		<?php endif; ?>

        <div class="options_group">
			<?php
			woocommerce_wp_select( array(
				'id'          => '_wc_restrictions',
				'label'       => __( 'Who can view this product', 'wc_catalog_restrictions' ),
				'options'     => array(
					'inherit'    => __( 'Use Category Settings', 'wc_catalog_restrictions' ),
					'public'     => __( 'Everyone', 'wc_catalog_restrictions' ),
					'restricted' => __( 'Specific Roles', 'wc_catalog_restrictions' )
				),
				'std'         => 'inherit',
				'desc_tip'    => true,
				'description' => __( 'If you would like to only show this product to users who are in certian roles select "Specific Roles"', 'wc_catalog_restrictions' )
			) );
			?>


            <div id="wc_catalog_restrictions_roles_container" class="wc_restrictions_options_panel" style="<?php echo( $current_role_restrictions_type == 'restricted' ? 'display:block;' : 'display:none;' ); ?>">
                <p class="form-field">
                    <label for="wc_restrictions_allowed"><?php _e( "Choose the roles that can view this product", 'wc_catalog_restrictions' ); ?></label>
                    <select id="wc_restrictions_allowed" name="wc_restrictions_allowed[]" style="width: 50%;" class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No roles selected', 'wc_catalog_restrictions' ); ?>">
						<?php
						if ( $all_roles ) {
							foreach ( $all_roles as $role_id => $role ) {
								echo '<option value="' . esc_attr( $role_id ) . '"' . selected( in_array( $role_id, $current_restrictions ), true, false ) . '>' . esc_html( $role['name'] ) . '</option>';
							}
						}
						?>
                    </select>
                </p>
            </div>
        </div>
        <div class="options_group">

			<?php

			$purchase_options = array();
			if ( $locations_enabled ) {
				$purchase_options = array(
					'inherit'              => __( 'Use Category Settings', 'wc_catalog_restrictions' ),
					'public'               => __( 'Everyone', 'wc_catalog_restrictions' ),
					'restricted'           => __( 'Specific Roles', 'wc_catalog_restrictions' ),
					'locations_allowed'    => __( 'Allowed Locations', 'wc_catalog_restrictions' ),
					'locations_restricted' => __( 'Restricted Locations', 'wc_catalog_restrictions' )
				);
			} else {
				$purchase_options = array(
					'inherit'    => __( 'Use Category Settings', 'wc_catalog_restrictions' ),
					'public'     => __( 'Everyone', 'wc_catalog_restrictions' ),
					'restricted' => __( 'Specific Roles', 'wc_catalog_restrictions' )
				);
			}

			woocommerce_wp_select( array(
				'id'          => '_wc_restrictions_purchase',
				'label'       => __( 'Who can purchase this product', 'wc_catalog_restrictions' ),
				'options'     => $purchase_options,
				'std'         => 'inherit',
				'desc_tip'    => true,
				'description' => __( 'Select "Specific Roles" or "Specific Locations (if enabled) to restrict purchasing.  Select "Everyone" to override category settings.' )
			) );
			?>

            <div id="wc_catalog_restrictions_purchase_roles_container" class="wc_restrictions_options_panel" style="<?php echo( $object->get_meta( '_wc_restrictions_purchase', true ) == 'restricted' ? 'display:block;' : 'display:none;' ); ?>">
                <p class="form-field">
                    <label for="wc_restrictions_purchase_roles"><?php _e( "Choose the roles that can purchase this product", 'wc_catalog_restrictions' ); ?></label>
                    <select id="wc_restrictions_purchase_roles" name="wc_restrictions_purchase_roles[]" style="width: 50%;" class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No roles selected', 'wc_catalog_restrictions' ); ?>">
						<?php
						if ( $all_roles ) {
							foreach ( $all_roles as $role_id => $role ) {
								echo '<option value="' . esc_attr( $role_id ) . '"' . selected( in_array( $role_id, $current_purchase_restrictions ), true, false ) . '>' . esc_html( $role['name'] ) . '</option>';
							}
						}
						?>
                    </select>
                </p>
            </div>

            <div id="wc_catalog_restrictions_purchase_locations_container" class="wc_restrictions_options_panel" style="<?php echo( $current_purchase_restrictions_type == 'locations_allowed' || $current_purchase_restrictions_type == 'locations_restricted' ? 'display:block;' : 'display:none;' ); ?>">
                <p class="form-field">
                    <label for="wc_restrictions_purchase_locations"><?php _e( 'Target Locations', 'wc_catalog_restrictions' ); ?></label>
					<?php echo wc_help_tip( __( 'Choose locations for this product.  Only users who select a matching location will be able to purchase this product.', 'wc_catalog_restrictions' ) ) ?>
                    <select style="width: 50%;" name="wc_restrictions_purchase_locations[]" class="multiselect wc-enhanced-select" multiple="multiple" data-placeholder="<?php echo $search_for_location_text; ?>">
						<?php woocommerce_catalog_restrictions_country_multiselect_options( $current_purchase_location_restrictions ); ?>
                    </select>

                </p>
            </div>

        </div>

        <div class="options_group">


			<?php

			$price_options = array();
			if ( $locations_enabled ) {
				$price_options = array(
					'inherit'              => __( 'Use Category Settings', 'wc_catalog_restrictions' ),
					'public'               => __( 'Everyone', 'wc_catalog_restrictions' ),
					'restricted'           => __( 'Specific Roles', 'wc_catalog_restrictions' ),
					'locations_allowed'    => __( 'Allowed Locations', 'wc_catalog_restrictions' ),
					'locations_restricted' => __( 'Restricted Locations', 'wc_catalog_restrictions' )
				);
			} else {
				$price_options = array(
					'inherit'    => __( 'Use Category Settings', 'wc_catalog_restrictions' ),
					'public'     => __( 'Everyone', 'wc_catalog_restrictions' ),
					'restricted' => __( 'Specific Roles', 'wc_catalog_restrictions' )
				);
			}

			woocommerce_wp_select( array(
				'id'          => '_wc_restrictions_price',
				'label'       => __( 'Who can view prices', 'wc_catalog_restrictions' ),
				'options'     => $price_options,
				'std'         => 'inherit',
				'desc_tip'    => true,
				'description' => __( 'Select "Specific Roles" or "Specific Locations (if enabled) to restrict prices.  Select "Everyone" to override category settings.' )
			) );
			?>

            <div id="wc_catalog_restrictions_prices_roles_container" class="wc_restrictions_options_panel" style="<?php echo( get_post_meta( $object->get_id(), '_wc_restrictions_price', true ) == 'restricted' ? 'display:block;' : 'display:none;' ); ?>">
                <p class="form-field">
                    <label for="wc_restrictions_price_roles"><?php _e( "Choose the roles that can view this products price", 'wc_catalog_restrictions' ); ?></label>
                    <select id="wc_restrictions_price_roles" name="wc_restrictions_price_roles[]" style="width: 50%;" class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No roles selected', 'wc_catalog_restrictions' ); ?>">
						<?php
						if ( $all_roles ) {
							foreach ( $all_roles as $role_id => $role ) {
								echo '<option value="' . esc_attr( $role_id ) . '"' . selected( in_array( $role_id, $current_price_restrictions ), true, false ) . '>' . esc_html( $role['name'] ) . '</option>';
							}
						}
						?>
                    </select>
                </p>
            </div>

            <div id="wc_catalog_restrictions_prices_locations_container" class="wc_restrictions_options_panel" style="<?php echo( $current_price_restrictions_type == 'locations_allowed' || $current_price_restrictions_type == 'locations_restricted' ? 'display:block;' : 'display:none;' ); ?>">
                <p class="form-field">
                    <label for="wc_restrictions_price_locations"><?php _e( 'Target Locations', 'wc_catalog_restrictions' ); ?></label>
					<?php echo wc_help_tip( __( 'Choose locations for this product.  Only users who select a matching location will be able to view this product\'s price.', 'wc_catalog_restrictions' ) ) ?>
                    <select style="width: 50%;" name="wc_restrictions_price_locations[]" class="multiselect wc-enhanced-select" multiple="multiple" data-placeholder="<?php echo $search_for_location_text; ?>">
						<?php woocommerce_catalog_restrictions_country_multiselect_options( $current_price_location_restrictions ); ?>
                    </select>

                </p>
            </div>

        </div>
    </div>
</div>