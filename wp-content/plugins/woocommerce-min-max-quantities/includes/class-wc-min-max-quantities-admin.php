<?php

/**
 * WC_Min_Max_Quantities_Admin class.
 */
class WC_Min_Max_Quantities_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Init settings
		$this->settings = array(
			array( 'name' => __( 'Min/Max Quantities', 'woocommerce-min-max-quantities' ), 'type' => 'title', 'desc' => '', 'id' => 'minmax_quantity_options' ),
			array(
				'name'              => __( 'Minimum Order Qty', 'woocommerce-min-max-quantities' ),
				'desc'              => __( 'The minimum allowed quantity of items in an order.', 'woocommerce-min-max-quantities' ),
				'id'                => 'woocommerce_minimum_order_quantity',
				'type'              => 'number',
				'desc_tip'          => true,
				'custom_attributes' => array(
					'min'  => 0,
					'step' => 1,
				),
			),
			array(
				'name'              => __( 'Maximum Order Qty', 'woocommerce-min-max-quantities' ),
				'desc'              => __( 'The maximum allowed quantity of items in an order.', 'woocommerce-min-max-quantities' ),
				'id'                => 'woocommerce_maximum_order_quantity',
				'type'              => 'number',
				'desc_tip'          => true,
				'custom_attributes' => array(
					'min'  => 0,
					'step' => 1,
				),
			),
			array(
				'name'              => __( 'Minimum Order Value', 'woocommerce-min-max-quantities' ),
				'desc'              => __( 'The minimum allowed value of items in an order.', 'woocommerce-min-max-quantities' ),
				'id'                => 'woocommerce_minimum_order_value',
				'type'              => 'number',
				'desc_tip'          => true,
				'custom_attributes' => array(
					'min'  => 0,
					'step' => 1,
				),
			),
			array(
				'name'              => __( 'Maximum Order Value', 'woocommerce-min-max-quantities' ),
				'desc'              => __( 'The maximum allowed value of items in an order.', 'woocommerce-min-max-quantities' ),
				'id'                => 'woocommerce_maximum_order_value',
				'type'              => 'number',
				'desc_tip'          => true,
				'custom_attributes' => array(
					'min'  => 0,
					'step' => 1,
				),
			),
			array( 'type' => 'sectionend', 'id' => 'minmax_quantity_options' ),
		);

		add_filter( 'woocommerce_products_general_settings', array( $this, 'add_settings'  ), 60 );

		add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_settings' ), 10, 2 );

		add_action( 'woocommerce_variation_options', array( &$this, 'variation_options' ), 10, 3 );
		add_action( 'woocommerce_product_after_variable_attributes', array( &$this, 'variation_panel' ), 10, 3 );

		// Meta
		add_action( 'woocommerce_product_options_general_product_data', array( &$this, 'write_panel' ) );

		add_action( 'woocommerce_process_product_meta', array( &$this, 'write_panel_save' ) );

		// Category level
		add_action( 'created_term', array( $this, 'category_fields_save' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'category_fields_save' ), 10, 3 );
		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_category_fields' ), 10, 2 );
		add_action( 'product_cat_add_form_fields', array( $this, 'add_category_fields' ) );
		add_filter( 'manage_edit-product_cat_columns', array( $this, 'product_cat_columns' ) );
		add_filter( 'manage_product_cat_custom_column', array( $this, 'product_cat_column' ), 10, 3 );
	}

	/**
	 * add admin settings
	 *
	 * @access public
	 * @since 2.3.0
	 * @return array $settings
	 */
	public function add_settings( $settings ) {
		$new_settings = array_merge( $settings, $this->settings );

		return apply_filters( 'wc_min_max_quantity_admin_settings', $new_settings );
	}

	/**
	 * admin_settings function.
	 *
	 * @access public
	 * @return void
	 */
	function admin_settings() {
		woocommerce_admin_fields( $this->settings );
	}

	/**
	 * save_admin_settings function.
	 *
	 * @access public
	 * @return void
	 */
	function save_admin_settings() {
		woocommerce_update_options( $this->settings );
	}

	/**
	 * write_panel function.
	 *
	 * @access public
	 * @return void
	 */
	function write_panel() {
		echo '<div class="options_group">';

		woocommerce_wp_text_input( array( 'id' => 'minimum_allowed_quantity', 'label' => __( 'Minimum quantity', 'woocommerce-min-max-quantities' ), 'description' => __( 'Enter a quantity to prevent the user buying this product if they have fewer than the allowed quantity in their cart', 'woocommerce-min-max-quantities' ), 'desc_tip' => true, 'type' => 'number', 'custom_attributes' => array( 'min' => 0, 'step' => 1 ) ) );

		woocommerce_wp_text_input( array( 'id' => 'maximum_allowed_quantity', 'label' => __( 'Maximum quantity', 'woocommerce-min-max-quantities' ), 'description' => __( 'Enter a quantity to prevent the user buying this product if they have more than the allowed quantity in their cart', 'woocommerce-min-max-quantities' ), 'desc_tip' => true, 'type' => 'number', 'custom_attributes' => array( 'min' => 0, 'step' => 1 ) ) );

		woocommerce_wp_text_input( array( 'id' => 'group_of_quantity', 'label' => __( 'Group of...', 'woocommerce-min-max-quantities' ), 'description' => __( 'Enter a quantity to only allow this product to be purchased in groups of X', 'woocommerce-min-max-quantities' ), 'desc_tip' => true, 'type' => 'number', 'custom_attributes' => array( 'min' => 0, 'step' => 1 ) ) );

		woocommerce_wp_checkbox( array( 'id' => 'allow_combination', 'label' => __( 'Combine variations', 'woocommerce-min-max-quantities' ), 'description' => __( 'Combine all variations together when checking the min/max rules above.', 'woocommerce-min-max-quantities' ) ) );

		woocommerce_wp_checkbox( array( 'id' => 'minmax_do_not_count', 'label' => __( 'Order rules: Do not count', 'woocommerce-min-max-quantities' ), 'description' => __( 'Don\'t count this product against your minimum order quantity/value rules. See the <a href="https://docs.woocommerce.com/document/minmax-quantities/#section-3" target="_blank">documentation</a> for full details.', 'woocommerce-min-max-quantities' ) ) );

		woocommerce_wp_checkbox( array( 'id' => 'minmax_cart_exclude', 'label' => __( 'Order rules: Exclude', 'woocommerce-min-max-quantities' ), 'description' => __( 'Exclude this product from minimum order quantity/value rules. If this is the only item in the cart, rules will not apply. See the <a href="https://docs.woocommerce.com/document/minmax-quantities/#section-3" target="_blank">documentation</a> for full details.', 'woocommerce-min-max-quantities' ) ) );

		woocommerce_wp_checkbox( array( 'id' => 'minmax_category_group_of_exclude', 'label' => __( 'Category rules: Exclude', 'woocommerce-min-max-quantities' ), 'description' => __( 'Exclude this product from category group-of-quantity rules. This product will not be counted towards category groups.', 'woocommerce-min-max-quantities' ) ) );

		echo '</div>';

    	$js = "
			jQuery( function( $ ) {
				var allow_combination_field = $( '.allow_combination_field' );

				if ( 'variable' === $( '#product-type' ).val() ) {
					allow_combination_field.show();
				} else {
					allow_combination_field.hide();
				}

				$( document.body ).on( 'woocommerce-product-type-change', function( evt, value, variation ) {
					if ( 'variable' === value ) {
						allow_combination_field.show();
					} else {
						allow_combination_field.hide();
					}
				} );

				$( '#woocommerce-product-data' ).on( 'woocommerce_variations_loaded', function() {
					var min_max_rules_options = $( '.checkbox.min_max_rules' ).parents( '.woocommerce_variable_attributes' ).find( '.min_max_rules_options' );

					if ( $( '.checkbox.min_max_rules' ).is( ':checked' ) ) {
						min_max_rules_options.show();
					} else {
						min_max_rules_options.hide();
					}
				});

				$( '.woocommerce_variations' ).on( 'change', '.checkbox.min_max_rules', function() {
					var min_max_rules_options = $( this ).parents( '.woocommerce_variable_attributes' ).find( '.min_max_rules_options' );

					if ( $( this ).is( ':checked' ) ) {
						min_max_rules_options.show();
					} else {
						min_max_rules_options.hide();
					}
				});
			});
		";

    	if ( function_exists( 'wc_enqueue_js' ) ) {
    		wc_enqueue_js( $js );
    	} else {
    		WC()->add_inline_js( $js );
    	}
    }

    /**
     * write_panel_save variations.
     *
     * @access public
     * @param mixed $post_id
     * @return void
     */
    public function save_variation_settings( $variation_id, $i ) {
		$min_max_rules            = isset( $_POST['min_max_rules'] ) ? array_map( 'sanitize_text_field', $_POST['min_max_rules'] ) : null;

		$minimum_allowed_quantity = isset( $_POST['variation_minimum_allowed_quantity'] ) ? array_map( 'sanitize_text_field', $_POST['variation_minimum_allowed_quantity'] ) : '';

		$maximum_allowed_quantity = isset( $_POST['variation_maximum_allowed_quantity'] ) ? array_map( 'sanitize_text_field', $_POST['variation_maximum_allowed_quantity'] ) : '';

		$group_of_quantity        = isset( $_POST['variation_group_of_quantity'] ) ? array_map( 'sanitize_text_field', $_POST['variation_group_of_quantity'] ) : '';

		$minmax_do_not_count      = isset( $_POST['variation_minmax_do_not_count'] ) ? array_map( 'sanitize_text_field', $_POST['variation_minmax_do_not_count'] ) : null;

		$minmax_cart_exclude      = isset( $_POST['variation_minmax_cart_exclude'] ) ? array_map( 'sanitize_text_field', $_POST['variation_minmax_cart_exclude'] ) : null;

		$minmax_category_group_of_exclude = isset( $_POST['variation_minmax_category_group_of_exclude'] ) ? array_map( 'sanitize_text_field', $_POST['variation_minmax_category_group_of_exclude'] ) : null;

		if ( isset( $min_max_rules[ $i ] ) ) {
			update_post_meta( $variation_id, 'min_max_rules', 'yes' );

		} else {
			update_post_meta( $variation_id, 'min_max_rules', 'no' );

		}

		update_post_meta( $variation_id, 'variation_minimum_allowed_quantity', $minimum_allowed_quantity[ $i ] );
		update_post_meta( $variation_id, 'variation_maximum_allowed_quantity', $maximum_allowed_quantity[ $i ] );
		update_post_meta( $variation_id, 'variation_group_of_quantity', $group_of_quantity[ $i ] );

		if ( isset( $minmax_do_not_count[ $i ] ) ) {
			update_post_meta( $variation_id, 'variation_minmax_do_not_count', 'yes' );

		} else {
			update_post_meta( $variation_id, 'variation_minmax_do_not_count', 'no' );

		}

		if ( isset( $minmax_cart_exclude[ $i ] ) ) {
			update_post_meta( $variation_id, 'variation_minmax_cart_exclude', 'yes' );

		} else {
			update_post_meta( $variation_id, 'variation_minmax_cart_exclude', 'no' );

		}

		if ( isset( $minmax_category_group_of_exclude[ $i ] ) ) {
			update_post_meta( $variation_id, 'variation_minmax_category_group_of_exclude', 'yes' );

		} else {
			update_post_meta( $variation_id, 'variation_minmax_category_group_of_exclude', 'no' );

		}

		// Increments the transient version to invalidate cache.
		WC_Cache_Helper::get_transient_version( 'wc_min_max_group_quantity', true );
    }

    /**
     * write_panel_save function.
     *
     * @access public
     * @param mixed $post_id
     * @return void
     */
    function write_panel_save( $post_id ) {
    	$product = wc_get_product( $post_id );

		// simple product save 2.1+ - 2.3
		if ( isset( $_POST['minimum_allowed_quantity'] ) ) {
			update_post_meta( $post_id, 'minimum_allowed_quantity', esc_attr( $_POST['minimum_allowed_quantity'] ) );
		}

		if ( isset( $_POST['maximum_allowed_quantity'] ) ) {
			update_post_meta( $post_id, 'maximum_allowed_quantity', esc_attr( $_POST['maximum_allowed_quantity'] ) );
		}

		if ( isset( $_POST['group_of_quantity'] ) ) {
			update_post_meta( $post_id, 'group_of_quantity', esc_attr( $_POST['group_of_quantity'] ) );
		}

		if ( $product->is_type( 'variable' ) ) {
			update_post_meta( $post_id, 'allow_combination', empty( $_POST['allow_combination'] ) ? 'no' : 'yes' );
		}

		update_post_meta( $post_id, 'minmax_do_not_count', empty( $_POST['minmax_do_not_count'] ) ? 'no' : 'yes' );

		update_post_meta( $post_id, 'minmax_cart_exclude', empty( $_POST['minmax_cart_exclude'] ) ? 'no' : 'yes' );

		update_post_meta( $post_id, 'minmax_category_group_of_exclude', empty( $_POST['minmax_category_group_of_exclude'] ) ? 'no' : 'yes' );

		// Increments the transient version to invalidate cache.
		WC_Cache_Helper::get_transient_version( 'wc_min_max_group_quantity', true );
    }

    /**
     * variation_options function.
     *
     * @access public
     * @return void
     */
    function variation_options( $loop, $variation_data, $variation ) {
	$min_max_rules = get_post_meta( $variation->ID, 'min_max_rules', true );
	?>
		<label><input type="checkbox" class="checkbox min_max_rules" name="min_max_rules[<?php echo $loop; ?>]" <?php if ( $min_max_rules ) checked( $min_max_rules, 'yes' ); ?> /> <?php _e( 'Min/Max Rules', 'woocommerce-min-max-quantities' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Enable this option to override min/max settings at variation level', 'woocommerce-min-max-quantities' ) ); ?>" href="#">[?]</a></label>
    	<?php
    }

    /**
     * variation_panel function.
     *
     * @access public
     * @param mixed $loop
     * @param mixed $variation_data
     * @return void
     */
    function variation_panel( $loop, $variation_data, $variation ) {
    	$min_max_rules = get_post_meta( $variation->ID, 'min_max_rules', true );

    	if ( isset( $min_max_rules ) && 'no' === $min_max_rules ) {
    		$visible = 'style="display:none"';

    	} else {
    		$visible = '';

    	}

	$min_qty                   = get_post_meta( $variation->ID, 'variation_minimum_allowed_quantity', true );
	$max_qty                   = get_post_meta( $variation->ID, 'variation_maximum_allowed_quantity', true );
	$group_of                  = get_post_meta( $variation->ID, 'variation_group_of_quantity', true );
	$do_not_count              = get_post_meta( $variation->ID, 'variation_minmax_do_not_count', true );
	$cart_exclude              = get_post_meta( $variation->ID, 'variation_minmax_cart_exclude', true );
	$category_group_of_exclude = get_post_meta( $variation->ID, 'variation_minmax_category_group_of_exclude', true );
	?>
	<div class="min_max_rules_options" <?php echo $visible; ?>>
		<p class="form-row form-row-first">
			<label><?php _e( 'Minimum quantity', 'woocommerce-min-max-quantities' ); ?>
			<input type="number" min="0" step="1" size="5" name="variation_minimum_allowed_quantity[<?php echo $loop; ?>]" value="<?php if ( $min_qty ) echo esc_attr( $min_qty ); ?>" /></label>
		</p>

		<p class="form-row form-row-last">
			<label><?php _e( 'Maximum quantity', 'woocommerce-min-max-quantities' ); ?>
			<input type="number" min="0" step="1" size="5" name="variation_maximum_allowed_quantity[<?php echo $loop; ?>]" value="<?php if ( $max_qty ) echo esc_attr( $max_qty ); ?>" /></label>
		</p>

		<p class="form-row form-row-first">
			<label><?php _e( 'Group of...', 'woocommerce-min-max-quantities' ); ?>
			<input type="number" min="0" step="1" size="5" name="variation_group_of_quantity[<?php echo $loop; ?>]" value="<?php if ( $group_of ) echo esc_attr( $group_of ); ?>" /></label>
		</p>

		<p class="form-row form-row-last">
			<label><input type="checkbox" class="checkbox" name="variation_minmax_do_not_count[<?php echo $loop; ?>]" <?php if ( $do_not_count ) checked( $do_not_count, 'yes' ) ?> /> <?php _e( 'Order rules: Do not count', 'woocommerce-min-max-quantities' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Don\'t count this product against your minimum order quantity/value rules.', 'woocommerce-min-max-quantities' ) ); ?>" href="#">[?]</a></label>

			<label><input type="checkbox" class="checkbox" name="variation_minmax_cart_exclude[<?php echo $loop; ?>]" <?php if ( $cart_exclude ) checked( $cart_exclude, 'yes' ) ?> /> <?php _e( 'Order rules: Exclude', 'woocommerce-min-max-quantities' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Exclude this product from minimum order quantity/value rules. If this is the only item in the cart, rules will not apply.', 'woocommerce-min-max-quantities' ) ); ?>" href="#">[?]</a></label>

			<label><input type="checkbox" class="checkbox" name="variation_minmax_category_group_of_exclude[<?php echo $loop; ?>]" <?php if ( $category_group_of_exclude ) checked( $category_group_of_exclude, 'yes' ) ?> /> <?php _e( 'Category group-of rules: Exclude', 'woocommerce-min-max-quantities' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Exclude this product from category group-of-quantity rules. This product will not be counted towards category groups.', 'woocommerce-min-max-quantities' ) ); ?>" href="#">[?]</a></label>
		</p>
	</div>
	<?php
    }

	/**
	 * Category thumbnail fields.
	 *
	 * @access public
	 * @return void
	 */
	function add_category_fields() {
		global $woocommerce;
		?>
		<div class="form-field">
			<label><?php _e( 'Group of...', 'woocommerce-min-max-quantities' ); ?></label>
			<input type="number" min="0" step="1" size="5" name="group_of_quantity" />
			<p class="description"><?php _e( 'Enter a quantity to only allow products in this category to be purchased in groups of X', 'woocommerce-min-max-quantities' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Edit category thumbnail field.
	 *
	 * @access public
	 * @param mixed $term Term (category) being edited
	 * @param mixed $taxonomy Taxonomy of the term being edited
	 * @return void
	 */
	function edit_category_fields( $term, $taxonomy ) {
		global $woocommerce;

		$display_type = version_compare( WC_VERSION, '3.6', 'ge' ) ? get_term_meta( $term->term_id, 'group_of_quantity', true ) : get_woocommerce_term_meta( $term->term_id, 'group_of_quantity', true );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Group of...', 'woocommerce-min-max-quantities' ); ?></label></th>
			<td>
				<input type="number" min="0" step="1" size="5" name="group_of_quantity" value="<?php echo $display_type; ?>" />
				<p class="description"><?php _e( 'Enter a quantity to only allow products in this category to be purchased in groups of X', 'woocommerce-min-max-quantities' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * woocommerce_category_fields_save function.
	 *
	 * @access public
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param mixed $taxonomy Taxonomy of the term being saved
	 * @return void
	 */
	function category_fields_save( $term_id, $tt_id, $taxonomy ) {
		if ( isset( $_POST['group_of_quantity'] ) ) {
			if ( version_compare( WC_VERSION, '3.6', 'ge' ) ) {
				update_term_meta( $term_id, 'group_of_quantity', esc_attr( $_POST['group_of_quantity'] ) );
			} else {
				update_woocommerce_term_meta( $term_id, 'group_of_quantity', esc_attr( $_POST['group_of_quantity'] ) );
			}

			// Increments the transient version to invalidate cache.
			WC_Cache_Helper::get_transient_version( 'wc_min_max_group_quantity', true );
		}
	}

	/**
	 * product_cat_columns function.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return void
	 */
	function product_cat_columns( $columns ) {
		$columns['groupof'] = __( 'Purchasable in...', 'woocommerce-min-max-quantities' );

		return $columns;
	}

	/**
	 * product_cat_column function.
	 *
	 * @access public
	 * @param mixed $columns
	 * @param mixed $column
	 * @param mixed $id
	 * @return void
	 */
	function product_cat_column( $columns, $column, $id ) {
		global $woocommerce;

		if ( $column == 'groupof' ) {

			$groupof = version_compare( WC_VERSION, '3.6', 'ge' ) ? get_term_meta( $id, 'group_of_quantity', true ) : get_woocommerce_term_meta( $id, 'group_of_quantity', true );

			if ( $groupof ) {
				$columns .= __( 'Groups of', 'woocommerce-min-max-quantities' ) . ' ' . absint( $groupof );

			} else {
				$columns .= '&ndash;';

			}
		}

		return $columns;
	}

}

$WC_Min_Max_Quantities_Admin = new WC_Min_Max_Quantities_Admin();
