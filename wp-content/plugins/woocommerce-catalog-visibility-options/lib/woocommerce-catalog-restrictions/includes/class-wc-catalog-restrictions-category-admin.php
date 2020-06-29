<?php

class WC_Catalog_Restrictions_Category_Admin {

	public static $instance;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new WC_Catalog_Restrictions_Category_Admin();
		}

		return self::$instance;
	}

	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'on_admin_scripts' ), 99 );
		add_action( 'product_cat_add_form_fields', array( $this, 'add_category_fields' ) );
		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_category_fields' ), 10, 2 );
		add_action( 'created_term', array( $this, 'category_field_save' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'category_field_save' ), 10, 3 );
		add_filter( 'manage_edit-product_cat_columns', array( $this, 'cat_columns' ) );
		add_filter( 'manage_product_cat_custom_column', array( $this, 'cat_column' ), 10, 3 );
	}

	function on_admin_scripts() {
		global $wc_catalog_restrictions;
		$screen = get_current_screen();
		if ( strpos( $screen->id, 'product_cat' ) !== false ) :
			wp_enqueue_style( 'wc-product-restrictions-admin', $wc_catalog_restrictions->plugin_url() . '/assets/css/admin.css' );
			wp_enqueue_script( 'wc-product-restrictions-admin', $wc_catalog_restrictions->plugin_url() . '/assets/js/admin.js' );
			wp_enqueue_script( 'woocommerce_admin' );
			wp_enqueue_script( 'jquery-ui-sortable' );

		endif;
	}

	function add_category_fields() {
		global $woocommerce, $wc_catalog_restrictions, $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$all_roles            = $wp_roles->roles;
		$restricted           = 'public';
		$current_restrictions = array();
		?>
        <div id="wc_catalog_restrictions" class="form-field">

            <label><?php _e( 'Role Restrictions', 'wc_catalog_restrictions' ); ?></label>

            <label for="_wc_restrictions"><?php _e( 'Which customer roles can view and purchase products in this category?', 'wc_catalog_restrictions' ); ?></label>
            <select name="_wc_restrictions" id="_wc_restrictions">
                <option value="no-restriction-setting" <?php selected( $restricted, 'no-restriction-setting' ); ?>><?php _e( 'Not Configured', 'wc_catalog_restrictions' ); ?></option>
                <option value="public" <?php selected( $restricted, 'public' ); ?>><?php _e( 'Everyone', 'wc_catalog_restrictions' ); ?></option>
                <option value="restricted" <?php selected( $restricted, 'restricted' ); ?>><?php _e( 'Specific Roles', 'wc_catalog_restrictions' ); ?></option>
            </select>

            <div id="wc_catalog_restrictions_roles_container" style="<?php echo( $restricted == 'restricted' ? 'display:block;' : 'display:none;' ); ?>">
                <p class="title"><?php _e( "Choose the roles that can view this product", 'wc_catalog_restrictions' ); ?></p>
				<?php $chunks = array_chunk( $all_roles, ceil( count( $all_roles ) / 3 ), true ); ?>
				<?php foreach ( $chunks as $chunk ) : ?>
                    <ul class="list-column">
						<?php foreach ( $chunk as $role_id => $role ) : ?>
							<?php $role_checked = in_array( $role_id, $current_restrictions ) ? 'checked="checked"' : ''; ?>
                            <li>
                                <label for="role_<?php echo esc_attr( $role_id ); ?>" class="selectit">
                                    <input <?php echo $role_checked; ?> type="checkbox" id="role_<?php echo esc_attr( $role_id ); ?>" name="wc_restrictions_allowed[]" value="<?php echo esc_attr( $role_id ); ?>"/><?php echo $role['name']; ?>
                                </label>
                            </li>
						<?php endforeach; ?>
                    </ul>
				<?php endforeach; ?>

            </div>

            <div class="clearfix"></div>
        </div>
        <div id="wc_catalog_restrictions_location" class="form-field">
			<?php if ( $wc_catalog_restrictions->get_setting( '_wc_restrictions_locations_enabled', 'no' ) == 'yes' ) : ?>
				<?php
				$location_restriction = 'public';
				$current_locations    = array();
				?>

                <label for="_wc_restrictions_location"><?php _e( 'What locations should this category be enabled for?', 'wc_catalog_restrictions' ); ?></label>

                <select name="_wc_restrictions_location" id="_wc_restrictions_location">
                    <option value="no-restriction-setting" <?php selected( $location_restriction, 'no-restriction-setting' ); ?>><?php _e( 'Not Configured', 'wc_catalog_restrictions' ); ?></option>
                    <option value="public" <?php selected( $location_restriction, 'public' ); ?>><?php _e( 'All Locations', 'wc_catalog_restrictions' ); ?></option>
                    <option value="restricted" <?php selected( $location_restriction, 'restricted' ); ?>><?php _e( 'Specific Locations', 'wc_catalog_restrictions' ); ?></option>
                </select>

                <div id="wc_catalog_restrictions_locations_container" class="woocommerce_options_panel" style="<?php echo( $location_restriction == 'restricted' ? 'display:block;' : 'display:none;' ); ?>">
                    <p class="form-field">
                        <label for="wc_restrictions_locations"><?php _e( 'Target Locations', 'wc_catalog_restrictions' ); ?></label>
                        <select name="wc_restrictions_locations[]" class="" multiple="multiple" data-placeholder="<?php _e( 'Search for a country&hellip;', 'woocommerce' ); ?>">
							<?php woocommerce_catalog_restrictions_country_multiselect_options( $current_locations ); ?>
                        </select>
                        <img style="width:16px;height:16px;" class="help_tip" data-tip='<?php _e( 'Choose locations for this category.  Only users who select a matching location will be able to view and purchase products in this category.', 'wc_catalog_restrictions' ) ?>' src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png"/>
                    </p>
                </div>
			<?php endif; ?>
        </div>
		<?php
	}

	function edit_category_fields( $term, $taxonomy ) {
		global $woocommerce, $wc_catalog_restrictions, $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$all_roles = $wp_roles->roles;


		$restricted           = get_term_meta( $term->term_id, '_wc_restrictions', true );
		$current_restrictions = get_term_meta( $term->term_id, '_wc_restrictions_allowed', false );


		if ( ! $current_restrictions ) {
			$current_restrictions = array();
		}

		$locations_enabled = $wc_catalog_restrictions->get_setting( '_wc_restrictions_locations_enabled', 'no' ) == 'yes';

		$current_purchase_restrictions_type = get_term_meta( $term->term_id, '_wc_restrictions_purchase', true );
		$current_price_restrictions_type    = get_term_meta( $term->term_id, '_wc_restrictions_price', true );

		$current_purchase_restrictions = get_term_meta( $term->term_id, '_wc_restrictions_purchase_roles', true );
		$current_purchase_restrictions = empty( $current_purchase_restrictions ) ? array() : $current_purchase_restrictions;

		$current_purchase_location_restrictions = get_term_meta( $term->term_id, '_wc_restrictions_purchase_locations', true );
		$current_purchase_location_restrictions = empty($current_purchase_location_restrictions) ? array() : $current_purchase_location_restrictions;

		$current_price_restrictions             = get_term_meta( $term->term_id, '_wc_restrictions_price_roles', true );
		$current_price_restrictions             = empty( $current_price_restrictions ) ? array() : $current_price_restrictions;

		$current_price_location_restrictions = get_term_meta( $term->term_id, '_wc_restrictions_price_locations', true );
		$current_price_location_restrictions = empty($current_price_location_restrictions) ? array() : $current_price_location_restrictions;

		$search_for_location_text = get_option( '_wc_restrictions_locations_type' ) == 'states' ?
			__( 'Search for a location&hellip;', 'wc_catalog_restrictions' ) :
			__( 'Search for a country&hellip;', 'wc_catalog_restrictions' );

		?>
        <tr class="form-field">
            <th scope="row" valign="top">
                <label><?php _e( 'Role Visibility Rules', 'wc_catalog_restrictions' ); ?></label></th>
            <td>
                <div id="wc_catalog_restrictions" class="form-field">
                    <label for="_wc_restrictions"><?php _e( 'Choose the type of visibility rule to use with products in this category.', 'wc_catalog_restrictions' ); ?></label><br/>
                    <select name="_wc_restrictions" id="_wc_restrictions">
                        <option value="no-restriction-setting"><?php _e( 'None', 'wc_catalog_restrictions' ); ?></option>
                        <option value="restricted" <?php selected( $restricted, 'restricted' ); ?>><?php _e( 'Show to Specific Roles', 'wc_catalog_restrictions' ); ?></option>
                        <option value="public" <?php selected( $restricted, 'public' ); ?>><?php _e( 'Show to Everyone', 'wc_catalog_restrictions' ); ?></option>
                    </select>
                    <img style="width:16px;height:16px;" class="help_tip" data-tip='<?php _e( 'Use to Show To Everyone to force products in this category to always be displayed, regardless of the customers role. Use Show to Specific Roles to choose which roles will see products in this category.' ); ?>' src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png"/>
                    <div id="wc_catalog_restrictions_roles_container" style="<?php echo( $restricted == 'restricted' ? 'display:block;' : 'display:none;' ); ?>">
                        <p class="title"><?php _e( "Choose the roles that can view this product", 'wc_catalog_restrictions' ); ?></p>
						<?php $chunks = array_chunk( $all_roles, ceil( count( $all_roles ) / 3 ), true ); ?>
						<?php foreach ( $chunks as $chunk ) : ?>
                            <ul class="list-column">
								<?php foreach ( $chunk as $role_id => $role ) : ?>
									<?php $role_checked = in_array( $role_id, $current_restrictions ) ? 'checked="checked"' : ''; ?>
                                    <li>
                                        <label for="role_<?php echo esc_attr( $role_id ); ?>" class="selectit">
                                            <input <?php echo $role_checked; ?> type="checkbox" id="role_<?php echo esc_attr( $role_id ); ?>" name="wc_restrictions_allowed[]" value="<?php echo esc_attr( $role_id ); ?>"/><?php echo $role['name']; ?>
                                        </label>
                                    </li>
								<?php endforeach; ?>
                            </ul>
						<?php endforeach; ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </td>
        </tr>
		<?php if ( $locations_enabled ) : ?>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label><?php _e( 'Location Visibility Rules', 'wc_catalog_restrictions' ); ?></label></th>
                <td>
                    <div id="wc_catalog_restrictions_location" class="form-field">

						<?php
						$location_restriction = get_term_meta( $term->term_id, '_wc_restrictions_location', true );
						$current_locations    = get_term_meta( $term->term_id, '_wc_restrictions_locations', false );
						?>

                        <label for="_wc_restrictions_location"><?php _e( 'Choose the type of location rule to use for products in this category.', 'wc_catalog_restrictions' ); ?></label>
                        <br/>
                        <select name="_wc_restrictions_location" id="_wc_restrictions_location">
                            <option value="no-restriction-setting"><?php _e( 'None', 'wc_catalog_restrictions' ); ?></option>
                            <option value="restricted" <?php selected( $location_restriction, 'restricted' ); ?>><?php _e( 'Specific Locations', 'wc_catalog_restrictions' ); ?></option>
                            <option value="public" <?php selected( $location_restriction, 'public' ); ?>><?php _e( 'Any Location', 'wc_catalog_restrictions' ); ?></option>
                        </select>
                        <img style="width:16px;height:16px;" class="help_tip" data-tip='<?php _e( 'Use to Any Location to force products in this category to always be displayed, regardless of the customers location. Use Specific Locations to choose which locations prodcuts in this category will be enabled for.' ); ?>' src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png"/>

                        <div id="wc_catalog_restrictions_locations_container" class="woocommerce_options_panel" style="<?php echo( $location_restriction == 'restricted' ? 'display:block;' : 'display:none;' ); ?>">
                            <p class="form-field">
                                <label for="wc_restrictions_locations"><?php _e( 'Target Locations', 'wc_catalog_restrictions' ); ?></label>
                                <select name="wc_restrictions_locations[]" class="" multiple="multiple" data-placeholder="<?php _e( 'Search for a country&hellip;', 'woocommerce' ); ?>">
									<?php woocommerce_catalog_restrictions_country_multiselect_options( $current_locations ); ?>
                                </select>
                                <img style="width:16px;height:16px;" class="help_tip" data-tip='<?php _e( 'Choose locations for this category.  Only users who select a matching location will be able to view and purchase products in this category.', 'wc_catalog_restrictions' ) ?>' src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png"/>
                            </p>
                        </div>

                    </div>
                </td>
            </tr>
		<?php endif; ?>

        <tr class="form-field">
            <th scope="row" valign="top"> <label for="wc_restrictions_purchase_roles"><?php _e( "Choose the roles that can purchase in this category.", 'wc_catalog_restrictions' ); ?></label>
            </th>
            <td>
                <div class="options_group">

					<?php

					$purchase_options = array();
					if ( $locations_enabled ) {
						$purchase_options = array(
							'inherit'    => __( 'Use Store Settings', 'wc_catalog_restrictions' ),
							'public'               => __( 'Everyone', 'wc_catalog_restrictions' ),
							'restricted'           => __( 'Specific Roles', 'wc_catalog_restrictions' ),
							'locations_allowed'    => __( 'Locations who can purchase', 'wc_catalog_restrictions' ),
							'locations_restricted' => __( 'Locations who can not purchase', 'wc_catalog_restrictions' )
						);
					} else {
						$purchase_options = array(
							'inherit'    => __( 'Use Store Settings', 'wc_catalog_restrictions' ),
							'public'     => __( 'Everyone', 'wc_catalog_restrictions' ),
							'restricted' => __( 'Specific Roles', 'wc_catalog_restrictions' )
						);
					}

					woocommerce_catalog_restrictions_wp_select( array(
						'id'          => '_wc_restrictions_purchase',
						'label'       => __( 'Who can purchase products in this category', 'wc_catalog_restrictions' ),
						'options'     => $purchase_options,
						'std'         => 'inherit',
						'desc_tip'    => true,
						'value'       => $current_purchase_restrictions_type,
						'description' => __( 'Select "Specific Roles" or "Specific Locations (if enabled) to restrict purchasing.  Select "Everyone" to override category settings.' )
					) );
					?>

                    <div id="wc_catalog_restrictions_purchase_roles_container" class="wc_restrictions_options_panel" style="<?php echo( $current_purchase_restrictions_type == 'restricted' ? 'display:block;' : 'display:none;' ); ?>">
                        <p class="form-field">
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
							<?php echo wc_help_tip( __( 'Choose locations that can or can not purchase in this category.', 'wc_catalog_restrictions' ) ) ?>
                            <br />
                            <select style="width: 50%;" name="wc_restrictions_purchase_locations[]" class="multiselect wc-enhanced-select" multiple="multiple" data-placeholder="<?php echo $search_for_location_text; ?>">
								<?php woocommerce_catalog_restrictions_country_multiselect_options( $current_purchase_location_restrictions ); ?>
                            </select>

                        </p>
                    </div>

                </div>
            </td>

        </tr>

        <tr class="form-field">
            <th scope="row" valign="top"> <label for="wc_restrictions_price_roles"><?php _e( "Choose the roles that can view prices in this category.", 'wc_catalog_restrictions' ); ?></label>
            </th>
            <td>
                <div class="options_group">

					<?php

					$price_options = array();
					if ( $locations_enabled ) {
						$price_options = array(
							'inherit'    => __( 'Use Store Settings', 'wc_catalog_restrictions' ),
							'public'               => __( 'Everyone', 'wc_catalog_restrictions' ),
							'restricted'           => __( 'Specific Roles', 'wc_catalog_restrictions' ),
							'locations_allowed'    => __( 'Locations who can view prices', 'wc_catalog_restrictions' ),
							'locations_restricted' => __( 'Locations who can not view prices', 'wc_catalog_restrictions' )
						);
					} else {
						$price_options = array(
							'inherit'    => __( 'Use Store Settings', 'wc_catalog_restrictions' ),
							'public'     => __( 'Everyone', 'wc_catalog_restrictions' ),
							'restricted' => __( 'Specific Roles', 'wc_catalog_restrictions' )
						);
					}

					woocommerce_catalog_restrictions_wp_select( array(
						'id'          => '_wc_restrictions_price',
						'label'       => __( 'Who can view prices in this category', 'wc_catalog_restrictions' ),
						'options'     => $price_options,
						'std'         => 'inherit',
						'desc_tip'    => true,
						'value'       => $current_price_restrictions_type,
						'description' => __( 'Select "Specific Roles" or "Specific Locations (if enabled) to restrict purchasing.  Select "Everyone" to override category settings.' )
					) );
					?>

                    <div id="wc_catalog_restrictions_prices_roles_container" class="wc_restrictions_options_panel" style="<?php echo( $current_price_restrictions_type == 'restricted' ? 'display:block;' : 'display:none;' ); ?>">
                        <p class="form-field">
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
							<?php echo wc_help_tip( __( 'Choose the locations that can or can not view prices in this category.', 'wc_catalog_restrictions' ) ) ?>
                            <br />
                            <select style="width: 50%;" name="wc_restrictions_price_locations[]" class="multiselect wc-enhanced-select" multiple="multiple" data-placeholder="<?php echo $search_for_location_text; ?>">
								<?php woocommerce_catalog_restrictions_country_multiselect_options( $current_price_location_restrictions ); ?>
                            </select>

                        </p>
                    </div>

                </div>
            </td>

        </tr>

		<?php
	}

	function category_field_save( $term_id, $tt_id, $taxonomy ) {

		$restrictions_enabled = isset( $_POST['_wc_restrictions'] ) ? $_POST['_wc_restrictions'] : false;

		if ( empty( $restrictions_enabled ) || $restrictions_enabled == 'no-restriction-setting' ) {
			delete_term_meta( $term_id, '_wc_restrictions' );
			delete_term_meta( $term_id, '_wc_restrictions_allowed' );
		} else {
			update_term_meta( $term_id, '_wc_restrictions', $restrictions_enabled );

			delete_term_meta( $term_id, '_wc_restrictions_allowed' );
			if ( $restrictions_enabled == 'restricted' ) {
				$restrictions = isset( $_POST['wc_restrictions_allowed'] ) ? $_POST['wc_restrictions_allowed'] : array( '' );
				foreach ( $restrictions as $role ) {
					add_term_meta( $term_id, '_wc_restrictions_allowed', $role );
				}
			}
		}


		$restrictions_location_enabled = isset( $_POST['_wc_restrictions_location'] ) ? $_POST['_wc_restrictions_location'] : false;

		if ( empty( $restrictions_location_enabled ) || $restrictions_location_enabled == 'no-restriction-setting' ) {
			delete_term_meta( $term_id, '_wc_restrictions_location' );
			delete_term_meta( $term_id, '_wc_restrictions_locations' );
		} else {
			update_term_meta( $term_id, '_wc_restrictions_location', $restrictions_location_enabled );

			delete_term_meta( $term_id, '_wc_restrictions_locations' );
			if ( $restrictions_location_enabled == 'restricted' ) {
				$restrictions = isset( $_POST['wc_restrictions_locations'] ) ? $_POST['wc_restrictions_locations'] : array( '' );
				foreach ( $restrictions as $location ) {
					add_term_meta( $term_id, '_wc_restrictions_locations', $location );
				}
			}
		}

		$purchase_roles_allowed = filter_input( INPUT_POST, '_wc_restrictions_purchase' );
		update_term_meta(  $term_id,'_wc_restrictions_purchase', $purchase_roles_allowed );
		if ( $purchase_roles_allowed == 'inherit' ) {
			delete_term_meta(  $term_id,'_wc_restrictions_purchase_roles' );
		} elseif ( $purchase_roles_allowed == 'restricted' ) {
			$proles = isset( $_POST['wc_restrictions_purchase_roles'] ) ? $_POST['wc_restrictions_purchase_roles'] : array( '' );
			update_term_meta(  $term_id,'_wc_restrictions_purchase_roles', $proles );
			delete_term_meta(  $term_id,'_wc_restrictions_purchase_locations' );
		} elseif ( $purchase_roles_allowed == 'locations_allowed' || $purchase_roles_allowed == 'locations_restricted' ) {
			$plocations = isset( $_POST['wc_restrictions_purchase_locations'] ) ? $_POST['wc_restrictions_purchase_locations'] : array( '' );
			update_term_meta(  $term_id,'_wc_restrictions_purchase_locations', $plocations );
			delete_term_meta(  $term_id,'_wc_restrictions_purchase_roles' );
		}


		$price_roles_allowed = filter_input( INPUT_POST, '_wc_restrictions_price' );
		update_term_meta(  $term_id,'_wc_restrictions_price', $price_roles_allowed );
		if ( $price_roles_allowed == 'inherit' ) {
			delete_term_meta(  $term_id,'_wc_restrictions_price_roles' );
		} elseif ( $price_roles_allowed == 'restricted' ) {
			$proles = isset( $_POST['wc_restrictions_price_roles'] ) ? $_POST['wc_restrictions_price_roles'] : array( '' );
			update_term_meta(  $term_id,'_wc_restrictions_price_roles', $proles );
			delete_term_meta(  $term_id,'_wc_restrictions_price_locations' );
		} elseif ( $price_roles_allowed == 'locations_allowed' || $price_roles_allowed == 'locations_restricted' ) {
			$plocations = isset( $_POST['wc_restrictions_price_locations'] ) ? $_POST['wc_restrictions_price_locations'] : array( '' );
			update_term_meta(  $term_id,'_wc_restrictions_price_locations', $plocations );
			delete_term_meta(  $term_id,'_wc_restrictions_price_roles' );
		}

	}

	function cat_columns( $columns ) {
		$new_columns                 = array();
		if ( isset( $columns['cb'] ) ) {
			$new_columns['cb'] = $columns['cb'];
			unset( $columns['cb'] );
		}

		$new_columns['restrictions'] = __( 'Restrictions', 'wc_catalog_restrictions' );

		return array_merge( $new_columns, $columns );
	}

	function cat_column( $columns, $column, $id ) {
		if ( $column == 'restrictions' ) {

			$restricted           = get_term_meta( $id, '_wc_restrictions', true );
			$current_restrictions = get_term_meta( $id, '_wc_restrictions_allowed', false );

			if ( ! $restricted ) {

			} elseif ( $restricted == 'public' ) {
				$columns .= 'Everyone has access';
			} else {
				if ( count( $current_restrictions ) == 1 ) {
					if ( ! empty( $current_restrictions[0] ) ) {
						$columns .= sprintf( __( '%s role has access', 'wc_catalog_restrictions' ), count( $current_restrictions ) );
					} else {
						$columns .= __( 'No one has access', 'wc_catalog_restrictions' );
					}
				} else {
					$columns .= sprintf( __( '%s roles have access', 'wc_catalog_restrictions' ), count( $current_restrictions ) );
				}
			}
		}

		return $columns;
	}

}
