<?php

/**
 * WC_Min_Max_Quantities_Admin class.
 */
class WC_Min_Max_Quantities_Admin {

	/**
	 * Plugin settings.
	 */
	public $settings = array();

	/**
	 * Constructor
	 */
	public function __construct() {

		// Init settings
		$this->settings = array(
			array( 'name' => __( 'Min/Max Order Rules', 'woocommerce-min-max-quantities' ), 'type' => 'title', 'desc' => '', 'id' => 'minmax_quantity_options' ),
			array(
				'name'              => __( 'Minimum Order Quantity', 'woocommerce-min-max-quantities' ),
				'desc'              => __( 'The minimum quantity of items required in an order.', 'woocommerce-min-max-quantities' ),
				'id'                => 'woocommerce_minimum_order_quantity',
				'type'              => 'number',
				'desc_tip'          => true,
				'custom_attributes' => array(
					'min'  => 0,
					'step' => 1,
				),
			),
			array(
				'name'              => __( 'Maximum Order Quantity', 'woocommerce-min-max-quantities' ),
				'desc'              => __( 'The maximum quantity of items allowed in an order.', 'woocommerce-min-max-quantities' ),
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
				'desc'              => __( 'The minimum value of an order.', 'woocommerce-min-max-quantities' ),
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
				'desc'              => __( 'The maximum value of an order.', 'woocommerce-min-max-quantities' ),
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
		add_filter( 'pre_insert_term', array( $this, 'validate_category_group_of' ), 10, 2 );
		add_action( 'created_term', array( $this, 'category_fields_save' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'category_fields_save' ), 10, 3 );
		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_category_fields' ), 10, 2 );
		add_action( 'product_cat_add_form_fields', array( $this, 'add_category_fields' ) );
		add_filter( 'manage_edit-product_cat_columns', array( $this, 'product_cat_columns' ) );
		add_filter( 'manage_product_cat_custom_column', array( $this, 'product_cat_column' ), 10, 3 );

		// Enqueue scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 11 );

		// Add a notice if Min/Max quantities are not compatible with the "Group of" option.
		add_action( 'admin_notices', array( $this, 'maybe_add_group_of_notice' ), 0 );
	}

	/**
	 * Αdd global admin settings.
	 *
	 * @since 2.3.0
	 * @return array $settings
	 */
	public function add_settings( $settings ) {
		$new_settings = array_merge( $settings, $this->settings );

		/**
		 * Use this filter to introduce additional Min/Max Quantities settings.
		 *
		 * @since 2.3.0
		 *
		 * @param  array  $new_settings
		 */
		return apply_filters( 'wc_min_max_quantity_admin_settings', $new_settings );
	}

	/**
	 * Display global admin settings.
	 *
	 * @return void
	 */
	public function admin_settings() {
		woocommerce_admin_fields( $this->settings );
	}

	/**
	 * Save global admin settings.
	 *
	 * @return void
	 */
	public function save_admin_settings() {
		woocommerce_update_options( $this->settings );
	}

	/**
	 * Admin writepanel scripts.
	 */
	public function admin_scripts() {
		$instance = WC_Min_Max_Quantities::get_instance();
		$suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'wc-mmq-admin-product-panel', $instance->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery' ), WC_MIN_MAX_QUANTITIES );
		wp_enqueue_script( 'wc-mmq-admin-product-panel' );
		wp_enqueue_style( 'wc-mmq-admin', $instance->plugin_url() . '/assets/css/admin/admin.css', '', WC_MIN_MAX_QUANTITIES );
	}

	/**
	 * Output product-level Quantity rules.
	 *
	 * @return void
	 */
	public function write_panel() {
		global $post;

		echo '<div class="options_group" id="min_max_settings">';

		echo '<div class="hr-section hr-section-components">' . esc_html__( 'Quantity rules', 'woocommerce-min-max-quantities' ) . '</div>';

		woocommerce_wp_text_input( array( 'id' => 'minimum_allowed_quantity', 'label' => __( 'Minimum quantity', 'woocommerce-min-max-quantities' ), 'description' => __( 'Enter a minimum required quantity for this product.', 'woocommerce-min-max-quantities' ), 'desc_tip' => true, 'type' => 'number', 'custom_attributes' => array( 'min' => 0, 'step' => 1 ) ) );

		woocommerce_wp_text_input( array( 'id' => 'maximum_allowed_quantity', 'label' => __( 'Maximum quantity', 'woocommerce-min-max-quantities' ), 'description' => __( 'Enter a maximum allowed quantity for this product.', 'woocommerce-min-max-quantities' ), 'desc_tip' => true, 'type' => 'number', 'custom_attributes' => array( 'min' => 0, 'step' => 1 ) ) );

		woocommerce_wp_text_input( array( 'id' => 'group_of_quantity', 'label' => __( 'Group of', 'woocommerce-min-max-quantities' ), 'description' => __( 'Enter a value to require customers to purchase this product in multiples.', 'woocommerce-min-max-quantities' ), 'desc_tip' => true, 'type' => 'number', 'custom_attributes' => array( 'min' => 0, 'step' => 1 ) ) );

		woocommerce_wp_checkbox( array( 'id' => 'allow_combination', 'label' => __( 'Combine variations', 'woocommerce-min-max-quantities' ), 'desc_tip' => true, 'description' => __( 'Enable this option to combine the quantities of all purchased variations when checking \'Minimum/Maximum quantity\' and \'Group of\' rules. <strong>Note:</strong> Cannot be used together with variation-level quantity rules.', 'woocommerce-min-max-quantities' ) ) );

		if ( 'yes' === get_post_meta( $post->ID, 'minmax_do_not_count', true ) ) {
			woocommerce_wp_checkbox( array( 'id' => 'minmax_do_not_count', 'label' => __( 'Don\'t count in Order rules', 'woocommerce-min-max-quantities' ), 'desc_tip' => true, 'description' => __( 'Don\'t count the quantity and cost of this product when checking order rules.', 'woocommerce-min-max-quantities' ) ) );
		}

		$exclude_cart_rules     = get_post_meta( $post->ID, 'minmax_cart_exclude', true );
		$exclude_category_rules = get_post_meta( $post->ID, 'minmax_category_group_of_exclude', true );
		$group_of_quantity      = get_post_meta( $post->ID, 'group_of_quantity', true );

		?><p class="form-field">
			<label><?php esc_html_e( 'Exclude from', 'woocommerce-min-max-quantities' ); ?></label>
			<span class="exclude_checkbox_wrapper">
				<input type="checkbox" class="checkbox exclude_cart_rules"<?php echo ( 'yes' === $exclude_cart_rules ? ' checked="checked"' : '' ); ?> name="minmax_cart_exclude" <?php echo ( 'yes' === $exclude_cart_rules ? 'value="1"' : '' ); ?>/>
				<span class="labelspan"><?php esc_html_e( 'Order rules', 'woocommerce-min-max-quantities' ); ?></span><?php echo wc_help_tip( __( 'Exclude this product from order rules (minimum/maximum quantity and value).', 'woocommerce-min-max-quantities' ) ); ?>
			</span>
			<span class="exclude_checkbox_wrapper">
				<input type="checkbox" <?php echo ( 'yes' !== $exclude_category_rules && '' !== $group_of_quantity ? ' disabled' : '' ); ?> class="checkbox exclude_category_rules"<?php echo ( 'yes' === $exclude_category_rules || '' !== $group_of_quantity ? ' checked="checked"' : '' ); ?> name="minmax_category_group_of_exclude" <?php echo ( 'yes' === $exclude_category_rules ? 'value="1"' : '' ); ?>/>
				<span class="labelspan"><?php esc_html_e( 'Category rules', 'woocommerce-min-max-quantities' ); ?></span><?php echo wc_help_tip( __( 'Exclude this product from category \'Group of\' quantity rules. <strong>Note: </strong>If a product-level \'Group of\' quantity rule is defined, then the product is automatically excluded from category \'Group of\' rules.', 'woocommerce-min-max-quantities' ) ); ?>
			</span>
		</p><?php

		echo '</div>';

	}

	/**
	 * Save variation-level quantity rules.
	 *
	 * @param mixed $post_id
	 * @return void
	 */
	public function save_variation_settings( $variation_id, $i ) {

		// WooCommerce core debt.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$min_max_rules                    = isset( $_POST[ 'min_max_rules' ] ) ? array_map( 'sanitize_text_field', $_POST[ 'min_max_rules' ] ) : array();
		$minimum_allowed_quantity         = isset( $_POST[ 'variation_minimum_allowed_quantity' ] ) ? array_map( 'sanitize_text_field', $_POST[ 'variation_minimum_allowed_quantity' ] ) : array();
		$maximum_allowed_quantity         = isset( $_POST[ 'variation_maximum_allowed_quantity' ] ) ? array_map( 'sanitize_text_field', $_POST[ 'variation_maximum_allowed_quantity' ] ) : array();
		$group_of_quantity                = isset( $_POST[ 'variation_group_of_quantity' ] ) ? array_map( 'sanitize_text_field', $_POST[ 'variation_group_of_quantity' ] ) : array();
		$minmax_do_not_count              = isset( $_POST[ 'variation_minmax_do_not_count' ] ) ? array_map( 'sanitize_text_field', $_POST[ 'variation_minmax_do_not_count' ] ) : array();
		$minmax_cart_exclude              = isset( $_POST[ 'variation_minmax_cart_exclude' ] ) ? array_map( 'sanitize_text_field', $_POST[ 'variation_minmax_cart_exclude' ] ) : array();
		$minmax_category_group_of_exclude = isset( $_POST[ 'variation_minmax_category_group_of_exclude' ] ) ? array_map( 'sanitize_text_field', $_POST[ 'variation_minmax_category_group_of_exclude' ] ) : array();

		if ( isset( $min_max_rules[ $i ] ) ) {
			update_post_meta( $variation_id, 'min_max_rules', 'yes' );

		} else {
			update_post_meta( $variation_id, 'min_max_rules', 'no' );

		}

		/*
		 * Sanitization
		 */

		// If the Group of quantity is an empty string, 0 or null, then save an empty string.
		if ( empty( $group_of_quantity[ $i ] ) ) {
			$group_of_quantity[ $i ] = '';
		}

		// If the Minimum quantity is an empty string, 0 or null, then save an empty string.
		if ( empty( $minimum_allowed_quantity[ $i ] ) ) {
			$minimum_allowed_quantity[ $i ] = '';
		}

		// If the Maximum quantity is an empty string, 0 or null, then save an empty string.
		if ( empty( $maximum_allowed_quantity[ $i ] ) ) {
			$maximum_allowed_quantity[ $i ] = '';
		}

		update_post_meta( $variation_id, 'variation_group_of_quantity', $group_of_quantity[ $i ] );

		// If the Max Quantity is not a multiple of Group of and is also less than the Min Quantity, show only 1 notice.
		$max_notice_displayed = false;

		if ( '' !== $group_of_quantity[ $i ] ) {

			$group_of_quantity[ $i ] = absint( $group_of_quantity[ $i ] );

			// Validate Minimum Quantity value based on the variation's Group of quantity.
			if ( '' !== $minimum_allowed_quantity[ $i ] ) {
				$minimum_allowed_quantity[ $i ] = absint( $minimum_allowed_quantity[ $i ] );
				$adjusted_min_quantity          = WC_Min_Max_Quantities::adjust_min_quantity( $minimum_allowed_quantity[ $i ], $group_of_quantity[ $i ] );

				if ( $adjusted_min_quantity !== $minimum_allowed_quantity[ $i ] ) {

					/* translators: %1$s: Product name, %2$d: Group of quantity, %3$d: Invalid min quantity, %4$d: Adjusted min quantity */
					WC_Admin_Meta_Boxes::add_error( sprintf( __( 'The <strong>Minimum Quantity</strong> of <strong>%1$s</strong> must be a multiple of <strong>%2$d</strong>. Its value has been adjusted from <strong>%3$d</strong> to <strong>%4$d</strong>.', 'woocommerce-min-max-quantities' ), get_the_title( $variation_id ), $group_of_quantity[ $i ], $minimum_allowed_quantity[ $i ], $adjusted_min_quantity ) );

					$minimum_allowed_quantity[ $i ] = $adjusted_min_quantity;
				}
			}

			// Validate Maximum Quantity value based on the variation's Group of and Minimum quantity.
			if ( '' !== $maximum_allowed_quantity[ $i ] ) {

				// Cast to int only when the Maximum Quantity is not equal to the empty string to avoid saving 0 instead of an empty string.
				$maximum_allowed_quantity[ $i ] = absint( $maximum_allowed_quantity[ $i ] );
				$adjusted_max_quantity          = WC_Min_Max_Quantities::adjust_max_quantity( $maximum_allowed_quantity[ $i ], $group_of_quantity[ $i ], $minimum_allowed_quantity[ $i ] );

				if ( $adjusted_max_quantity !== $maximum_allowed_quantity[ $i ] ) {

					/* translators: %1$s: Product name, %2$d: Group of quantity, %3$d: Invalid max quantity, %4$d: Adjusted max quantity */
					WC_Admin_Meta_Boxes::add_error( sprintf( __( 'The <strong>Maximum Quantity</strong> of <strong>%1$s</strong> must be a multiple of <strong>%2$d</strong>. Its value has been adjusted from <strong>%3$d</strong> to <strong>%4$d</strong>.', 'woocommerce-min-max-quantities' ), get_the_title( $variation_id ), $group_of_quantity[ $i ], $maximum_allowed_quantity[ $i ], $adjusted_max_quantity ) );

					$maximum_allowed_quantity[ $i ] = $adjusted_max_quantity;
					$max_notice_displayed           = true;
				}
			}
		}

		if (  '' !== $maximum_allowed_quantity[ $i ] && '' !== $minimum_allowed_quantity[ $i ] ) {

			$minimum_allowed_quantity[ $i ] = absint( $minimum_allowed_quantity[ $i ] );
			$maximum_allowed_quantity[ $i ] = absint( $maximum_allowed_quantity[ $i ] );

			if ( $maximum_allowed_quantity[ $i ] < $minimum_allowed_quantity[ $i ] ) {
				$maximum_allowed_quantity[ $i ] = $minimum_allowed_quantity[ $i ];

				if ( ! $max_notice_displayed ) {
					/* translators: Product name */
					WC_Admin_Meta_Boxes::add_error( sprintf( __( 'The <strong>Maximum Quantity</strong> of <strong>%s</strong> was not valid and has been adjusted. Please enter a positive number equal to or higher than the <strong>Minimum Quantity</strong>, or leave the <strong>Maximum Quantity</strong> field empty for an unlimited maximum quantity.', 'woocommerce-min-max-quantities' ), get_the_title( $variation_id ) ) );
				}
			}
		}

		update_post_meta( $variation_id, 'variation_minimum_allowed_quantity', $minimum_allowed_quantity[ $i ] );
		update_post_meta( $variation_id, 'variation_maximum_allowed_quantity', $maximum_allowed_quantity[ $i ] );

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

		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Save product-level Quantity rules.
	 *
	 * @param mixed $post_id
	 * @return void
	 */
	public function write_panel_save( $post_id ) {
		$product = wc_get_product( $post_id );

		if ( empty( $_POST[ 'woocommerce_meta_nonce' ] ) || ! wp_verify_nonce( wc_clean( wp_unslash( $_POST[ 'woocommerce_meta_nonce' ] ) ), 'woocommerce_save_data' ) ) {
			return;
		}

		if ( isset( $_POST['group_of_quantity'] ) ) {

			// If the Group of quantity is an empty string, 0 or null, then save an empty string.
			if ( empty( $_POST['group_of_quantity'] ) ) {
				$_POST['group_of_quantity'] = '';
			}

			$group_of_quantity = wc_clean( $_POST['group_of_quantity'] );

			update_post_meta( $post_id, 'group_of_quantity', $group_of_quantity );
		} else {
			$group_of_quantity = '';
		}

		if ( isset( $_POST['minimum_allowed_quantity'] ) ) {

			// If the Minimum quantity is an empty string, 0 or null, then save an empty string.
			if ( empty( $_POST['minimum_allowed_quantity'] ) ) {
				$_POST['minimum_allowed_quantity'] = '';
			}

			$min_quantity = wc_clean( $_POST['minimum_allowed_quantity'] );

		} else {
			$min_quantity = '';
		}

		$max_notice_displayed = false;

		if ( isset( $_POST['maximum_allowed_quantity'] ) ) {

			// If the Maximum quantity is an empty string, 0 or null, then save an empty string.
			if ( empty( $_POST['maximum_allowed_quantity'] ) ) {
				$_POST['maximum_allowed_quantity'] = '';
			}

			$max_quantity = wc_clean( $_POST['maximum_allowed_quantity'] );

		} else {
			$max_quantity = '';
		}

		if ( '' !== $group_of_quantity ) {
			$group_of_quantity = absint( $group_of_quantity );

			if ( '' !== $min_quantity ) {

				$min_quantity          = absint( $min_quantity );
				$adjusted_min_quantity = WC_Min_Max_Quantities::adjust_min_quantity( $min_quantity, $group_of_quantity );

				if ( $adjusted_min_quantity !== $min_quantity ) {

					/* translators: %1$s: Product name, %2$d: Group of quantity, %3$d: Invalid min quantity, %4$d: Adjusted min quantity */
					WC_Admin_Meta_Boxes::add_error( sprintf( __( 'The <strong>Minimum Quantity</strong> of <strong>%1$s</strong> must be a multiple of <strong>%2$d</strong>. Its value has been adjusted from <strong>%3$d</strong> to <strong>%4$d</strong>.', 'woocommerce-min-max-quantities' ), get_the_title( $post_id ), $group_of_quantity, $min_quantity, $adjusted_min_quantity ) );

					$min_quantity = $adjusted_min_quantity;
				}
			}

			if ( '' !== $max_quantity ) {
				$max_quantity          = absint( $max_quantity );
				$adjusted_max_quantity = WC_Min_Max_Quantities::adjust_max_quantity( $max_quantity, $group_of_quantity, $min_quantity);

				if ( $adjusted_max_quantity !== $max_quantity ) {

					/* translators: %1$s: Product name, %2$d: Group of quantity, %3$d: Invalid max quantity, %4$d: Adjusted max quantity */
					WC_Admin_Meta_Boxes::add_error( sprintf( __( 'The <strong>Maximum Quantity</strong> of <strong>%1$s</strong> must be a multiple of <strong>%2$d</strong>. Its value has been adjusted from <strong>%3$d</strong> to <strong>%4$d</strong>.', 'woocommerce-min-max-quantities' ), get_the_title( $post_id ), $group_of_quantity, $max_quantity, $adjusted_max_quantity ) );

					$max_quantity         = $adjusted_max_quantity;
					$max_notice_displayed = true;
				}
			}
		}

		if ( '' !== $max_quantity && '' !== $min_quantity) {

			$min_quantity = absint( $min_quantity );
			$max_quantity = absint( $max_quantity );

			if ( $max_quantity < $min_quantity ) {
				$max_quantity = $min_quantity;

				if ( ! $max_notice_displayed ) {
					/* translators: Product name */
					WC_Admin_Meta_Boxes::add_error( sprintf( __( 'The <strong>Maximum Quantity</strong> of <strong>%s</strong> was not valid and has been adjusted. Please enter a positive number equal to or higher than the <strong>Minimum Quantity</strong>, or leave the <strong>Maximum Quantity</strong> field empty for an unlimited maximum quantity.', 'woocommerce-min-max-quantities' ), get_the_title( $post_id ) ) );
				}
			}
		}

		update_post_meta( $post_id, 'minimum_allowed_quantity', $min_quantity );
		update_post_meta( $post_id, 'maximum_allowed_quantity', $max_quantity );

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
	 * Save variation-level quantity rules.
	 *
	 * @return void
	 */
	public function variation_options( $loop, $variation_data, $variation ) {
		$min_max_rules = get_post_meta( $variation->ID, 'min_max_rules', true );
		?><label class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Use this option to override \'Minimum/Maximum quantity\' and \'Group of\' rules at variation level. Can be checked only when <strong>Product Data > General > Combine Variations</strong> is disabled.', 'woocommerce-min-max-quantities' ) ); ?>"><input type="checkbox" class="checkbox min_max_rules" name="min_max_rules[<?php echo esc_attr( $loop ); ?>]" <?php
			if ( $min_max_rules ) {
				checked( $min_max_rules, 'yes' );
			} ?> /> <?php esc_html_e( 'Quantity rules', 'woocommerce-min-max-quantities' );
			?></label>
		<?php
	}

	/**
	 * Display variation-level Quantity rules.
	 *
	 * @param mixed $loop
	 * @param mixed $variation_data
	 * @return void
	 */
	public function variation_panel( $loop, $variation_data, $variation ) {
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
		<div class="min_max_rules_options" <?php echo $visible; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<p class="form-row form-row-first">
				<label class="variation_minimum_allowed_quantity_label"><?php esc_html_e( 'Minimum quantity', 'woocommerce-min-max-quantities' ); ?>
					<input class="variation_minimum_allowed_quantity" type="number" min="0" step="1" size="5" name="variation_minimum_allowed_quantity[<?php echo esc_attr( $loop ); ?>]" value="<?php
						if ( $min_qty ) {
							echo esc_attr( $min_qty );
						}
					?>" />
				</label>
			</p>

		<p class="form-row form-row-last">
			<label class="variation_maximum_allowed_quantity_label"><?php esc_html_e( 'Maximum quantity', 'woocommerce-min-max-quantities' ); ?>
				<input class="variation_maximum_allowed_quantity" type="number" min="0" step="1" size="5" name="variation_maximum_allowed_quantity[<?php echo esc_attr( $loop ); ?>]" value="<?php
					if ( $max_qty ) {
						echo esc_attr( $max_qty );
					}
				?>" />
			</label>
		</p>

		<p class="form-row form-row-first">
			<label class="variation_group_of_quantity_label"><?php esc_html_e( 'Group of', 'woocommerce-min-max-quantities' ); ?>
				<input class="variation_group_of_quantity" type="number" min="0" step="1" size="5" name="variation_group_of_quantity[<?php echo esc_attr( $loop ); ?>]" value="<?php
					if ( $group_of ) {
						echo esc_attr( $group_of );
					}
				?>" />
			</label>
		</p>

		<p class="form-row form-row-last options">
			<?php if ( 'yes' === $do_not_count ) {
				?><label>
					<input type="checkbox" class="checkbox" name="variation_minmax_do_not_count[<?php echo esc_attr( $loop ); ?>]" <?php
						if ( $do_not_count ) {
							checked( $do_not_count, 'yes' );
						}
					?> /> <?php esc_html_e( 'Don\'t count in Order rules', 'woocommerce-min-max-quantities' ); ?><?php echo wc_help_tip( __( 'Don\'t count the quantity and cost of this variation when checking order rules.', 'woocommerce-min-max-quantities' ) ); ?>
				</label><?php
			}
			?><label>
				<input type="checkbox" class="checkbox" name="variation_minmax_cart_exclude[<?php echo esc_attr( $loop ); ?>]" <?php
					if ( $cart_exclude ) {
						checked( $cart_exclude, 'yes' );
					}
				?> /> <?php esc_html_e( 'Exclude from Order rules', 'woocommerce-min-max-quantities' ); ?><?php echo wc_help_tip( __( 'Exclude this variation from order rules (minimum/maximum quantity and value).', 'woocommerce-min-max-quantities' ) ); ?>
			</label>
			<label><input type="checkbox" class="checkbox variation_minmax_category_group_of_exclude" name="variation_minmax_category_group_of_exclude[<?php echo esc_attr( $loop ); ?>]" <?php echo ( 'yes' === $category_group_of_exclude || '' !== $group_of ? ' checked="checked"' : '' ); ?> <?php echo ( 'yes' !== $category_group_of_exclude && '' !== $group_of ? ' disabled' : '' ); ?> /> <?php esc_html_e( 'Exclude from Category rules', 'woocommerce-min-max-quantities' ); ?><?php echo wc_help_tip( __( 'Exclude this variation from category \'Group of\' quantity rules.  <strong>Note: </strong>If a variation-level \'Group of\' quantity rule is defined, then the variation is automatically excluded from category \'Group of\' rules.', 'woocommerce-min-max-quantities' ) ); ?></label>
		</p>
	</div>
	<?php
	}

	/**
	 * Category thumbnail fields.
	 *
	 * @return void
	 */
	public function add_category_fields() {
		global $woocommerce;
		?>
		<div class="form-field">
			<label><?php esc_html_e( 'Group of', 'woocommerce-min-max-quantities' ); ?></label>
			<input type="number" min="0" step="1" size="5" name="group_of_quantity" />
			<p class="description"><?php esc_html_e( 'Enter a value to require customers to purchase products from this category in multiples.', 'woocommerce-min-max-quantities' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Edit category thumbnail field.
	 *
	 * @param mixed $term Term (category) being edited
	 * @param mixed $taxonomy Taxonomy of the term being edited
	 * @return void
	 */
	public function edit_category_fields( $term, $taxonomy ) {
		$display_type = get_term_meta( $term->term_id, 'group_of_quantity', true );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Group of', 'woocommerce-min-max-quantities' ); ?></label></th>
			<td>
				<input type="number" min="0" step="1" size="5" name="group_of_quantity" value="<?php echo esc_attr( $display_type ); ?>" />
				<p class="description"><?php esc_html_e( 'Enter a value to require customers to purchase products from this category in multiples.', 'woocommerce-min-max-quantities' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Validates category Group of value when creating a new category.
	 *
	 * @param string|WP_Error $term     The term name to add, or a WP_Error object if there's an error.
	 * @param string          $taxonomy Taxonomy slug.
	 * @return string|WP_Error
	 */
	public function validate_category_group_of( $term, $taxonomy ) {

		if ( 'product_cat' !== $taxonomy ) {
			return $term;
		}

		// phpcs:ignore
		if ( isset( $_POST[ 'group_of_quantity' ] ) && (int) $_POST[ 'group_of_quantity' ] < 0 ) {

			$message = __( 'The <strong>Group of</strong> value of this category could not be saved. Please enter a positive number, or leave the <strong>Group of</strong> field empty.', 'woocommerce-min-max-quantities' );
			$term    = new WP_Error( 'mmq_invalid_category_group_of', $message );
		}

		return $term;
	}

	/**
	 * Save 'Group of' category field.
	 *
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param mixed $taxonomy Taxonomy of the term being saved
	 * @return void
	 */
	public function category_fields_save( $term_id, $tt_id, $taxonomy ) {

		// phpcs:ignore
		if ( isset( $_POST[ 'group_of_quantity' ] ) ) {

			// phpcs:ignore
			$group_of_quantity = wc_clean( $_POST[ 'group_of_quantity' ] );

			// If the Group of quantity is an empty string, 0 or null, then save an empty string.
			if ( empty( $group_of_quantity ) ) {
				$group_of_quantity = '';
			} elseif ( (int) $group_of_quantity < 0 ) {
				$category_title    = get_term( $term_id )->name;
				$group_of_quantity = '';

				/* translators: Category name */
				WC_Admin_Meta_Boxes::add_error( sprintf( __( 'The <strong>Group of</strong> value of <strong>%s</strong> could not be saved. Please enter a positive number, or leave the <strong>Group of</strong> field empty.', 'woocommerce-min-max-quantities' ), $category_title ) );
			}

			update_term_meta( $term_id, 'group_of_quantity', $group_of_quantity );

			// Increments the transient version to invalidate cache.
			WC_Cache_Helper::get_transient_version( 'wc_min_max_group_quantity', true );
		}
	}

	/**
	 * Add new column in categories overview table.
	 *
	 * @param mixed $columns
	 * @return void
	 */
	public function product_cat_columns( $columns ) {
		$columns['groupof'] = __( 'Group of', 'woocommerce-min-max-quantities' );

		return $columns;
	}

	/**
	 * Populate the 'Group of' category column.
	 *
	 * @param mixed $columns
	 * @param mixed $column
	 * @param mixed $id
	 * @return void
	 */
	public function product_cat_column( $columns, $column, $id ) {
		if ( 'groupof' === $column ) {

			$groupof = get_term_meta( $id, 'group_of_quantity', true );

			if ( $groupof ) {
				$columns .= absint( $groupof );

			} else {
				$columns .= '&ndash;';

			}
		}

		return $columns;
	}

	/**
	 * Backwards compatibility: Add a notice if Minimum/Maximum quantities are not compatible with the "Group of" option.
	 */
	public function maybe_add_group_of_notice() {

		global $post_id;

		// Get admin screen ID.
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( 'product' !== $screen_id ) {
			return;
		}

		// Cast both empty and 0 values to 0 -- skip adding notices for these values.
		$group_of_quantity = absint( get_post_meta( $post_id, 'group_of_quantity', true ) );
		$min_quantity      = absint( get_post_meta( $post_id, 'minimum_allowed_quantity', true ) );
		$max_quantity      = absint( get_post_meta( $post_id, 'maximum_allowed_quantity', true ) );

		// Both empty and zero Maximum Quantity values should be skipped.
		if ( 0 !== $max_quantity && $max_quantity < $min_quantity ) {
			/* translators: %1$s: Product name, %2$s: Group of quantity */
			$notice = sprintf( __( 'The <strong>Maximum Quantity</strong> of <strong>%1$s</strong> must be a positive number equal to or higher than the <strong>Minimum Quantity</strong>. Please adjust its value and save your changes.', 'woocommerce-min-max-quantities' ), get_the_title( $post_id ) );
			$this->output_notice( $notice, 'warning' );
			return;
		}

		if ( 0 !== $group_of_quantity ) {

			$adjusted_min_quantity = WC_Min_Max_Quantities::adjust_min_quantity( $min_quantity, $group_of_quantity );
			$adjusted_max_quantity = WC_Min_Max_Quantities::adjust_max_quantity( $max_quantity, $group_of_quantity );

			if ( $min_quantity !== $adjusted_min_quantity ) {
				/* translators: %1$s: Product name, %2$s: Group of quantity */
				$notice = sprintf( __( 'The <strong>Minimum Quantity</strong> of <strong>%1$s</strong> must be a multiple of <strong>%2$d</strong>. Please adjust its value and save your changes.', 'woocommerce-min-max-quantities' ), get_the_title( $post_id ), $group_of_quantity );
				$this->output_notice( $notice, 'warning' );
			}

			if ( $max_quantity !== $adjusted_max_quantity ) {
				/* translators: %1$s: Product name, %2$s: Group of quantity */
				$notice = sprintf( __( 'The <strong>Maximum Quantity</strong> of <strong>%1$s</strong> must be a multiple of <strong>%2$d</strong>. Please adjust its value and save your changes.', 'woocommerce-min-max-quantities' ), get_the_title( $post_id ), $group_of_quantity );
				$this->output_notice( $notice, 'warning' );
			}
		}

		// For Variable Products, also check variations with individual Minimum/Maximum rules.
		if ( 'variable' === WC_Product_Factory::get_product_type( $post_id ) ) {
			$product    = wc_get_product( $post_id );
			$variations = $product->get_children();

			foreach ( $variations as $variation_id ) {

				if ( get_post_meta( $variation_id, 'min_max_rules' ) ) {

					// Cast both empty and 0 values to 0 -- skip adding notices for these values.
					$group_of_quantity = absint( get_post_meta( $variation_id, 'variation_group_of_quantity', true ) );
					$min_quantity      = absint( get_post_meta( $variation_id, 'variation_minimum_allowed_quantity', true ) );
					$max_quantity      = absint( get_post_meta( $variation_id, 'variation_maximum_allowed_quantity', true ) );

					if ( 0 !== $max_quantity && $max_quantity < $min_quantity ) {
						/* translators: %1$s: Product name, %2$s: Group of quantity */
						$notice = sprintf( __( 'The <strong>Maximum Quantity</strong> of <strong>%s</strong> must be a positive number equal to or higher than the <strong>Minimum Quantity</strong>. Please adjust its value and save your changes.', 'woocommerce-min-max-quantities' ), get_the_title( $variation_id ) );
						$this->output_notice( $notice, 'warning' );
						return;
					}

					if ( 0 !== $group_of_quantity ) {
						$adjusted_min_quantity = WC_Min_Max_Quantities::adjust_min_quantity( $min_quantity, $group_of_quantity );
						$adjusted_max_quantity = WC_Min_Max_Quantities::adjust_max_quantity( $max_quantity, $group_of_quantity );

						if ( $min_quantity !== $adjusted_min_quantity ) {
							/* translators: %1$s: Variation name, %2$s: Group of quantity */
							$notice = sprintf( __( 'The <strong>Minimum Quantity</strong> of <strong>%1$s</strong> must be a multiple of <strong>%2$d</strong>. Please adjust its value and save your changes.', 'woocommerce-min-max-quantities' ), get_the_title( $variation_id ), $group_of_quantity );
							$this->output_notice( $notice, 'warning' );
						}

						if ( $max_quantity !== $adjusted_max_quantity ) {
							/* translators: %1$s: Variation name, %2$s: Group of quantity */
							$notice = sprintf( __( 'The <strong>Maximum Quantity</strong> of <strong>%1$s</strong> must be a multiple of <strong>%2$d</strong>. Please adjust its value and save your changes.', 'woocommerce-min-max-quantities' ), get_the_title( $variation_id ), $group_of_quantity );
							$this->output_notice( $notice, 'warning' );
						}
					}
				}
			}
		}
	}

	/**
	 * Prints warning messages in the admin area.
	 *
	 * @param string $content
	 * @param string $type
	 * @return void
	 */
	public function output_notice( $content, $type ) {
		echo '<div class="notice notice-' . esc_attr( $type ) . '">';
		echo wpautop( wp_kses_post( $content ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}

}

$WC_Min_Max_Quantities_Admin = new WC_Min_Max_Quantities_Admin();
