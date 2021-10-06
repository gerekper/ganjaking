<?php
/**
 * Admin handler class.
 *
 * @package WC_Shipping_Per_Product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin handler.
 */
class WC_Shipping_Per_Product_Admin {

	/**
	 * @var WC_Shipping_Per_Product_Init
	 */
	protected $per_product;
	/**
	 * The maximum number of rules to display in the product editor.
	 * If a product has more rules than this, the rules will need to
	 * be managed offsite and imported (overridden).
	 *
	 * @var int
	 */
	private static $rule_count_limit = 200;

	/**
	 * Constructor.
	 *
	 * @param WC_Shipping_Per_Product_Init $per_product Referance to init object.
	 */
	public function __construct( WC_Shipping_Per_Product_Init $per_product ) {
		$this->per_product = $per_product;
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'woocommerce_product_options_shipping', array( $this, 'product_options' ) );
		add_action( 'woocommerce_variation_options', array( $this, 'variation_options' ), 10, 3 );
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'product_after_variable_attributes' ), 10, 3 );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save' ) );
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation' ), 10, 2 );
		add_action( 'woocommerce_product_duplicate', array( $this, 'duplicate_rules' ), 10, 2 );
		add_action( 'wp_ajax_wc_shipping_per_product_export_rules', array( $this, 'export_rules' ) );
	}

	/**
	 * Scripts and styles.
	 */
	public function admin_enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'wc-shipping-per-product-styles', plugins_url( 'assets/css/admin.css', PER_PRODUCT_SHIPPING_FILE ) );
		wp_register_script( 'wc-shipping-per-product', plugins_url( 'assets/js/shipping-per-product' . $suffix . '.js', PER_PRODUCT_SHIPPING_FILE ), array( 'jquery' ), PER_PRODUCT_SHIPPING_VERSION, true );

		wp_localize_script( 'wc-shipping-per-product', 'wc_shipping_per_product_params', array(
			'i18n_no_row_selected' => __( 'No row selected', 'woocommerce-shipping-per-product' ),
			'i18n_product_id'      => __( 'Product ID', 'woocommerce-shipping-per-product' ),
			'i18n_country_code'    => __( 'Country Code', 'woocommerce-shipping-per-product' ),
			'i18n_state'           => __( 'State/County Code', 'woocommerce-shipping-per-product' ),
			'i18n_postcode'        => __( 'Zip/Postal Code', 'woocommerce-shipping-per-product' ),
			'i18n_cost'            => __( 'Cost', 'woocommerce-shipping-per-product' ),
			'i18n_item_cost'       => __( 'Item Cost', 'woocommerce-shipping-per-product' ),
		) );
	}

	/**
	 * Output product options
	 */
	public function product_options() {
		global $post, $wpdb;

		wp_enqueue_script( 'wc-shipping-per-product' );

		echo '</div><div class="options_group per_product_shipping">';

		woocommerce_wp_checkbox( array(
			'id'          => '_per_product_shipping',
			'label'       => __( 'Per-product shipping', 'woocommerce-shipping-per-product' ),
			'description' => __( 'Enable per-product shipping cost', 'woocommerce-shipping-per-product' ),
		) );

		$this->output_rules();
	}

	/**
	 * Output variation options.
	 *
	 * @param int     $loop           Loop index.
	 * @param array   $variation_data Variation data.
	 * @param WP_Post $variation      Post instance of variation.
	 */
	public function variation_options( $loop, $variation_data, $variation ) {
		wp_enqueue_script( 'wc-shipping-per-product' );
		?>
		<label><input type="checkbox" class="checkbox enable_per_product_shipping" name="_per_variation_shipping[<?php echo $variation->ID; ?>]" <?php checked( get_post_meta( $variation->ID, '_per_product_shipping', true ), 'yes' ); ?> /> <?php _e( 'Per-variation shipping', 'woocommerce-shipping-per-product' ); ?></label>
		<?php
	}

	/**
	 * Show Rules.
	 *
	 * @param int     $loop           Loop index.
	 * @param array   $variation_data Variation data.
	 * @param WP_Post $variation      Post instance of variation.
	 */
	public function product_after_variable_attributes( $loop, $variation_data, $variation ) {
		echo '<tr class="per_product_shipping per_variation_shipping"><td colspan="2">';
		$this->output_rules( $variation->ID );
		echo '</td></tr>';
	}

	/**
	 * Output rules table.
	 *
	 * @param int $post_id Post ID.
	 */
	public function output_rules( $post_id = 0 ) {
		global $post, $wpdb;

		if ( ! $post_id ) {
			$post_id = $post->ID;
		}

		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_per_product_shipping_rules WHERE product_id = %d ORDER BY rule_order;", $post_id ) );
		?>
		<div class="rules per_product_shipping_rules">

			<?php
			if ( false !== $this->per_product->use_legacy_shipping_method() ) {
				woocommerce_wp_checkbox(
					array(
						'id'          => '_per_product_shipping_add_to_all[' . $post_id . ']',
						'label'       => __( 'Adjust Shipping Costs', 'woocommerce-shipping-per-product' ),
						'description' => __( 'Add per-product shipping cost to all shipping method rates?', 'woocommerce-shipping-per-product' ),
						'value'       => get_post_meta( $post_id, '_per_product_shipping_add_to_all', true ),
					)
				);
			}
			?>

			<table class="widefat">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th><?php _e( 'Country Code', 'woocommerce-shipping-per-product' ); ?>&nbsp;<a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'A 2 digit country code, e.g. US. Leave blank to apply to all.', 'woocommerce-shipping-per-product' ) ); ?>">[?]</a></th>
						<th><?php _e( 'State/County Code', 'woocommerce-shipping-per-product' ); ?>&nbsp;<a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'A 2 digit state code, e.g. AL. Leave blank to apply to all.', 'woocommerce-shipping-per-product' ) ); ?>">[?]</a></th>
						<th><?php _e( 'Zip/Postal Code', 'woocommerce-shipping-per-product' ); ?>&nbsp;<a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Postcode for this rule. Wildcards (*) can be used. Leave blank to apply to all areas.', 'woocommerce-shipping-per-product' ) ); ?>">[?]</a></th>
						<th class="cost"><?php _e( 'Line Cost (Excl. Tax)', 'woocommerce-shipping-per-product' ); ?>&nbsp;<a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Decimal cost for the line as a whole.', 'woocommerce-shipping-per-product' ) ); ?>">[?]</a></th>
						<th class="item_cost"><?php _e( 'Item Cost (Excl. Tax)', 'woocommerce-shipping-per-product' ); ?>&nbsp;<a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Decimal cost for the item (multiplied by qty).', 'woocommerce-shipping-per-product' ) ); ?>">[?]</a></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th colspan="6">
							<?php if ( $count < self::$rule_count_limit ) : ?>
								<a href="#" class="button button-primary insert" data-postid="<?php echo esc_attr( $post_id ); ?>"><?php _e( 'Insert row', 'woocommerce-shipping-per-product' ); ?></a>
							<?php endif; ?>

							<?php if ( $count <= self::$rule_count_limit ) : ?>
								<a href="#" class="button remove"><?php _e( 'Remove row', 'woocommerce-shipping-per-product' ); ?></a>
							<?php endif; ?>

							<a href="#" class="button export" data-postid="<?php echo esc_attr( $post_id ); ?>"><?php _e( 'Export CSV', 'woocommerce-shipping-per-product' ); ?></a>
							<a href="<?php echo esc_url( admin_url( 'admin.php?import=woocommerce_per_product_shipping_csv' ) ); ?>" class="button import"><?php _e( 'Import CSV', 'woocommerce-shipping-per-product' ); ?></a>
							<a href="<?php echo esc_url( admin_url( 'admin.php?import=woocommerce_per_product_shipping_csv&override_product_id=' . absint( $post_id ) ) ); ?>" class="button import"><?php _e( 'Import CSV (override)', 'woocommerce-shipping-per-product' ); ?></a>
						</th>
					</tr>
				</tfoot>
				<tbody>
					<?php
					if ( $count <= self::$rule_count_limit ) {
						$rules = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_per_product_shipping_rules WHERE product_id = %d ORDER BY rule_order;", $post_id ) );

						foreach ( $rules as $rule ) {
						?>
							<tr>
								<td class="sort">&nbsp;<input type="hidden" value="<?php echo esc_attr( $rule->rule_order ); ?>" name="per_product_order[<?php echo esc_attr( $post_id ); ?>][<?php echo esc_attr( $rule->rule_id ); ?>]" /></td>
								<td class="country"><input type="text" maxlength="2" value="<?php echo esc_attr( $rule->rule_country ); ?>" placeholder="*" name="per_product_country[<?php echo esc_attr( $post_id ); ?>][<?php echo esc_attr( $rule->rule_id ); ?>]" /></td>
								<td class="state"><input type="text" maxlength="2" value="<?php echo esc_attr( $rule->rule_state ); ?>" placeholder="*" name="per_product_state[<?php echo esc_attr( $post_id ); ?>][<?php echo esc_attr( $rule->rule_id ); ?>]" /></td>
								<td class="postcode"><input type="text" value="<?php echo esc_attr( $rule->rule_postcode ); ?>" placeholder="*" name="per_product_postcode[<?php echo esc_attr( $post_id ); ?>][<?php echo esc_attr( $rule->rule_id ); ?>]" /></td>
								<td class="cost"><input type="text" class="wc_input_price input-text regular-input" value="<?php echo esc_attr( $rule->rule_cost ); ?>" placeholder="0.00" name="per_product_cost[<?php echo esc_attr( $post_id ); ?>][<?php echo esc_attr( $rule->rule_id ); ?>]" /></td>
								<td class="item_cost"><input type="text" class="wc_input_price input-text regular-input" value="<?php echo esc_attr( $rule->rule_item_cost ); ?>" placeholder="0.00" name="per_product_item_cost[<?php echo esc_attr( $post_id ); ?>][<?php echo esc_attr( $rule->rule_id ); ?>]" /></td>
							</tr>
							<?php
						}
					} else {
						?>
						<tr>
							<td colspan="6" style="text-align:center;padding:50px 1%;">
								<?php
								printf(
										__( '%1$sNOTICE:%2$s Only tables with %3$d rules or fewer can be edited within the product editor. %4$sPlease export your rules, modify as needed, and use the "Import CSV (override)" button to update this product\'s rules.', 'woocommerce-shipping-per-product' ),
										'<h2>',
										'</h2>',
										self::$rule_count_limit,
										'<br>'
								);
								?>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Save.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save( $post_id ) {
		// Enabled or Disabled.
		$enabled              = ! empty( $_POST['_per_product_shipping'] );
		$saved_enable_setting = get_post_meta( $post_id, '_per_product_shipping', true ) === 'yes';
		$this->clear_shipping_cache_if_necessary( $enabled, $saved_enable_setting );

		if ( $enabled ) {
			update_post_meta( $post_id, '_per_product_shipping', 'yes' );
			update_post_meta( $post_id, '_per_product_shipping_add_to_all', ! empty( $_POST['_per_product_shipping_add_to_all'][ $post_id ] ) ? 'yes' : 'no' );
		} else {
			delete_post_meta( $post_id, '_per_product_shipping' );
			delete_post_meta( $post_id, '_per_product_shipping_add_to_all' );
		}

		$countries  = ! empty( $_POST['per_product_country'][ $post_id ] ) ? $_POST['per_product_country'][ $post_id ] : array();
		$states     = ! empty( $_POST['per_product_state'][ $post_id ] ) ? $_POST['per_product_state'][ $post_id ] : array();
		$postcodes  = ! empty( $_POST['per_product_postcode'][ $post_id ] ) ? $_POST['per_product_postcode'][ $post_id ] : array();
		$costs      = ! empty( $_POST['per_product_cost'][ $post_id ] ) ? $_POST['per_product_cost'][ $post_id ] : array();
		$item_costs = ! empty( $_POST['per_product_item_cost'][ $post_id ] ) ? $_POST['per_product_item_cost'][ $post_id ] : array();
		$order      = ! empty( $_POST['per_product_order'][ $post_id ] ) ? $_POST['per_product_order'][ $post_id ] : array();
		if ( ! empty( $countries ) ) {
			$data = compact( 'countries', 'states', 'postcodes', 'costs', 'item_costs', 'order' );
			$this->save_product_rules( $post_id, $data );
		}
	}

	/**
	 * Replaces the aseterisks with emtpy string.
	 *
	 * @param string $rule Rule.
	 *
	 * @return string
	 */
	public function replace_aseterisk( $rule ) {
		if ( ! empty( $rule ) && '*' === $rule ) {
			return '';
		}

		return $rule;
	}

	/**
	 * Save a variation.
	 *
	 * @param int $post_id ID of the variation being saved.
	 * @param int $index   Index.
	 */
	public function save_variation( $post_id, $index ) {
		$enabled    = isset( $_POST['_per_variation_shipping'][ $post_id ] );
		$countries  = ! empty( $_POST['per_product_country'][ $post_id ] ) ? $_POST['per_product_country'][ $post_id ] : array();
		$states     = ! empty( $_POST['per_product_state'][ $post_id ] ) ? $_POST['per_product_state'][ $post_id ] : array();
		$postcodes  = ! empty( $_POST['per_product_postcode'][ $post_id ] ) ? $_POST['per_product_postcode'][ $post_id ] : array();
		$costs      = ! empty( $_POST['per_product_cost'][ $post_id ] ) ? $_POST['per_product_cost'][ $post_id ] : array();
		$item_costs = ! empty( $_POST['per_product_item_cost'][ $post_id ] ) ? $_POST['per_product_item_cost'][ $post_id ] : array();
		$order      = ! empty( $_POST['per_product_order'][ $post_id ] ) ? $_POST['per_product_order'][ $post_id ] : array();
		$saved_enable_setting = get_post_meta( $post_id, '_per_product_shipping', true ) === 'yes';

		$this->clear_shipping_cache_if_necessary( $enabled, $saved_enable_setting );
		
		if ( $enabled ) {
			update_post_meta( $post_id, '_per_product_shipping', 'yes' );
			update_post_meta( $post_id, '_per_product_shipping_add_to_all', ! empty( $_POST['_per_product_shipping_add_to_all'][ $post_id ] ) ? 'yes' : 'no' );

			$data = compact( 'countries', 'states', 'postcodes', 'costs', 'item_costs', 'order' );
			$this->save_product_rules( $post_id, $data );
		} else {
			delete_post_meta( $post_id, '_per_product_shipping' );
			delete_post_meta( $post_id, '_per_product_shipping_add_to_all' );
		}
	}

	/**
	 * Save product rules.
	 *
	 * @since 2.2.9
	 * @version 2.2.9
	 *
	 * @param int   $product_id Product ID.
	 * @param array $data       Data.
	 */
	private function save_product_rules( $product_id, $data ) {
		global $wpdb;

		$data = wp_parse_args(
			$data,
			array(
				'countries'  => array(),
				'states'     => array(),
				'postcodes'  => array(),
				'costs'      => array(),
				'item_costs' => array(),
				'order'      => array(),
			)
		);

		$countries  = $data['countries'];
		$states     = $data['states'];
		$postcodes  = $data['postcodes'];
		$costs      = $data['costs'];
		$item_costs = $data['item_costs'];
		$rule_order = $data['order'];

		foreach ( $countries as $key => $value ) {
			if ( 'new' === $key ) {
				foreach ( $value as $new_key => $new_value ) {
					$has_column_with_value = (
						! empty( $countries[ $key ][ $new_key ] )
						|| ! empty( $states[ $key ][ $new_key ] )
						|| ! empty( $postcodes[ $key ][ $new_key ] )
						|| ( isset( $costs[ $key ][ $new_key ] ) && is_numeric( $costs[ $key ][ $new_key ] ) )
						|| ( isset( $item_costs[ $key ][ $new_key ] ) && is_numeric( $item_costs[ $key ][ $new_key ] ) )
					);

					if ( $has_column_with_value ) {
						$wpdb->insert(
							$wpdb->prefix . 'woocommerce_per_product_shipping_rules',
							array(
								'rule_country'   => esc_attr( $this->replace_aseterisk( $countries[ $key ][ $new_key ] ) ),
								'rule_state'     => esc_attr( $this->replace_aseterisk( $states[ $key ][ $new_key ] ) ),
								'rule_postcode'  => esc_attr( $this->replace_aseterisk( $postcodes[ $key ][ $new_key ] ) ),
								'rule_cost'      => esc_attr( $costs[ $key ][ $new_key ] ),
								'rule_item_cost' => esc_attr( $item_costs[ $key ][ $new_key ] ),
								'rule_order'     => absint( $rule_order[ $key ][ $new_key ] ),
								'product_id'     => absint( $product_id ),
							)
						);
					}
				}
			} else {
				$has_column_with_value = (
					! empty( $countries[ $key ] )
					|| ! empty( $states[ $key ] )
					|| ! empty( $postcodes[ $key ] )
					|| ( isset( $costs[ $key ] ) && is_numeric( $costs[ $key ] ) )
					|| ( isset( $item_costs[ $key ] ) && is_numeric( $item_costs[ $key ] ) )
				);

				if ( $has_column_with_value ) {
					$wpdb->update(
						$wpdb->prefix . 'woocommerce_per_product_shipping_rules',
						array(
							'rule_country'   => esc_attr( $this->replace_aseterisk( $countries[ $key ] ) ),
							'rule_state'     => esc_attr( $this->replace_aseterisk( $states[ $key ] ) ),
							'rule_postcode'  => esc_attr( $this->replace_aseterisk( $postcodes[ $key ] ) ),
							'rule_cost'      => esc_attr( $costs[ $key ] ),
							'rule_item_cost' => esc_attr( $item_costs[ $key ] ),
							'rule_order'     => absint( $rule_order[ $key ] ),
						),
						array(
							'product_id' => absint( $product_id ),
							'rule_id'    => absint( $key ),
						)
					);
				} else {
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_per_product_shipping_rules WHERE product_id = %d AND rule_id = %s;", absint( $product_id ), absint( $key ) ) );
				}
			}
		}
	}

	/**
	 * Duplicate rules when duplicating product.
	 *
	 * @since 2.2.9
	 * @version 2.2.9
	 *
	 * @param WC_Product $duplicate Duplicated product.
	 * @param WC_Product $product   Original product.
	 */
	public function duplicate_rules( $duplicate, $product ) {
		$product   = wc_get_product( $product );
		$duplicate = wc_get_product( $duplicate );
		if ( ! $product || ! $duplicate ) {
			return;
		}

		if ( $this->is_per_product_shipping_enabled( $product ) ) {
			$this->duplicate_rules_query( $duplicate->get_id(), $product->get_id() );
		}

		// Variations may have their own rules. So, if current product is not
		// a variable, we're done duplicating.
		if ( ! $product->is_type( 'variable' ) ) {
			return;
		}

		$original_variation_ids  = $product->get_children();
		$duplicate_variation_ids = $duplicate->get_children();

		// Duplciate rules for each variation.
		foreach ( $original_variation_ids as $index => $variation_id ) {
			if ( empty( $duplicate_variation_ids[ $index ] ) ) {
				continue;
			}

			if ( $this->is_per_product_shipping_enabled( wc_get_product( $variation_id ) ) ) {
				$this->duplicate_rules_query( $duplicate_variation_ids[ $index ], $variation_id );
			}
		}
	}

	/**
	 * Checks whether a given `$product` enables per product shipping.
	 *
	 * @since 2.2.9
	 * @version 2.2.9
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @return bool
	 */
	private function is_per_product_shipping_enabled( $product ) {
		return 'yes' === $product->get_meta( '_per_product_shipping' );
	}

	/**
	 * Runs query to duplicate the rules from `$product_id` to `$duplciate_id`.
	 *
	 * @since 2.2.9
	 * @version 2.2.9
	 *
	 * @param int $duplicate_id Duplicated product ID.
	 * @param int $product_id   Original product ID.
	 *
	 * @return int|false Number of rows affected/selected or false on error.
	 */
	private function duplicate_rules_query( $duplicate_id, $product_id ) {
		global $wpdb;

		return $wpdb->query(
			$wpdb->prepare(
				"
				INSERT INTO {$wpdb->prefix}woocommerce_per_product_shipping_rules
					( product_id, rule_country, rule_state, rule_postcode,
					rule_cost, rule_item_cost, rule_order )
				SELECT %d, rule_country, rule_state, rule_postcode, rule_cost,
					rule_item_cost, rule_order
				FROM {$wpdb->prefix}woocommerce_per_product_shipping_rules
				WHERE product_id = %d;
				",
				$duplicate_id,
				$product_id
			)
		);
	}

	/**
	 * Clear shipping cache if it is necessary.
	 * Do not clear when option was disabled and new is also disable.
	 * Any other needs clearing.
	 *
	 * @since 2.2.16
	 * @version 2.2.16
	 *
	 * @param int $enabled is per-product enabled in this save action.
	 * @param int $saved_enable_setting per-product saved value.
	 */
	private function clear_shipping_cache_if_necessary( $enabled, $saved_enable_setting ) {
		global $wpdb;

		// If per product was disabled and it still is we don't need to clear cache.
		if ( false === $saved_enable_setting && false === $enabled ) {
			return;
		}

		// Increments the transient version to invalidate cache.
		WC_Cache_Helper::get_transient_version( 'shipping', true );
	}

	/**
	 * Retrieve and send shipping rules with the given product ID
	 */
	public function export_rules() {
		$product_id = $_POST['product_id'];
		$response   = array(
			'success' => false,
		);

		// Make sure the product ID was passed
		if ( empty( $product_id ) ) {
			wp_send_json( $response );
		}

		global $wpdb;
		$response['rules'] = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_per_product_shipping_rules WHERE product_id = %d ORDER BY rule_order;", $product_id ) );

		// If we have results, change 'success' to true
		if ( ! empty( $response['rules'] ) ) {
			$response['success'] = true;
		}

		wp_send_json( $response );
	}
}
