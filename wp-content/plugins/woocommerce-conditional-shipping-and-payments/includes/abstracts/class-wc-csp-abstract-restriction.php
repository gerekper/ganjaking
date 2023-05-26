<?php
/**
 * WC_CSP_Restriction class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Restriction class.
 *
 * @class    WC_CSP_Restriction
 * @version  1.15.0
 */
class WC_CSP_Restriction extends WC_Settings_API {

	/** @var string Unique ID for the Restriction - must be set */
	public $id;

	/** @var string Restriction title */
	public $title;

	/** @var string Restriction description */
	public $description;

	/**
	 * @var array Restriction types supported
	 *
	 * If the restriction needs to hook itself into 'woocommerce_add_to_cart_validation', 'woocommerce_check_cart_items', 'woocommerce_update_cart_validation', or 'woocommerce_after_checkout_validation',
	 * if must declare support for the 'add-to-cart', 'cart', 'cart-update', or 'checkout' validation types
	 * and implement the 'WC_CSP_Add_To_Cart_Restriction', 'WC_CSP_Cart_Restriction', 'WC_CSP_Update_Cart_Restriction', or 'WC_CSP_Checkout_Restriction' interfaces.
	 */
	public $validation_types;

	/** @var array Restriction has options in product write panels */
	public $has_admin_product_fields;

	/** @var array Restriction has global options */
	public $has_admin_global_fields;

	/** @var array Restriction supports multiple rules */
	public $supports_multiple;

	/** @var array Restriction conditions */
	public $conditions;

	/** @var string Restricted array key */
	public $restricted_key;

	/**
	 * Restriction title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Restriction description.
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Shop hook(s) where the restriction validates itself (add-to-cart, cart, update-cart, checkout).
	 *
	 * @return array
	 */
	public function get_validation_types() {
		return $this->validation_types;
	}

	/**
	 * If the restriction has options on the product Restrictions write-panel.
	 *
	 * @return bool
	 */
	public function has_admin_product_fields() {
		return $this->has_admin_product_fields;
	}

	/**
	 * Display options on the product Restrictions write-panel.
	 *
	 * By default expects fields posted inside an indexed array.
	 *
	 * @param  int    $index    restriction fields array index
	 * @param  string $options  metabox options
	 * @return string
	 */
	public function get_admin_product_fields_html( $index, $options ) {
		return false;
	}

	/**
	 * Validate, process and return posted product metabox options.
	 *
	 * By default expects all fields posted inside an indexed array.
	 *
	 * @param  array  $posted
	 * @return array
	 */
	public function process_admin_product_fields( $posted ) {
		return $posted;
	}

	/**
	 * If the restriction has options on the global Restrictions page.
	 *
	 * @return bool
	 */
	public function has_admin_global_fields() {
		return $this->has_admin_global_fields;
	}

	/**
	 * Display options on the global Restrictions sections.
	 *
	 * By default expects fields posted inside an indexed array.
	 *
	 * @param  int    $index    restriction fields array index
	 * @param  string $options  metabox options
	 * @return string
	 */
	public function get_admin_global_fields_html( $index, $options ) {
		return false;
	}

	/**
	 * Validate, process and return global settings.
	 *
	 * By default expects fields posted inside an indexed array.
	 *
	 * @param  array  $posted_data
	 * @return array
	 */
	public function process_admin_global_fields( $posted_data ) {
		return $posted_data;
	}

	/**
	 * Display metaboxes on the global Restrictions sections.
	 *
	 * @return string
	 */
	public function get_admin_global_metaboxes_html() {

		$global_restrictions = $this->get_global_restriction_data( 'edit' );

		// Generate data hash for dirty checking.
		$data_hash = md5( json_encode( $global_restrictions ) );

		?><tr><td>
		<div id="restrictions_data" class="panel csp_global_panel woocommerce_options_panel wc-metaboxes-wrapper postbox <?php echo empty( $global_restrictions ) && empty( $_GET[ 'add_rule' ] ) ? 'restrictions_data--empty' : ''; ?> <?php echo esc_attr( WC_CSP_Core_Compatibility::get_versions_class() ); ?>">
			<div class="inside">

				<div class="hr-section hr-section--conditions-and temp-placeholder">
					<?php esc_html_e( 'And', 'woocommerce-conditional-shipping-and-payments' ); ?>
				</div>

				<p class="toolbar toolbar--top">
					<select style="display:none;" name="_restriction_type" class="restriction_type">
						<?php
						echo '<option value="' . esc_attr( $this->id ) . '"></option>';
						?>
					</select>

					<span class="bulk_toggle_wrapper <?php echo empty( $global_restrictions ) ? 'disabled' : ''; ?>">
						<a href="#" class="expand_all"><?php esc_html_e( 'Expand all', 'woocommerce' ); ?></a>
						<a href="#" class="close_all"><?php esc_html_e( 'Close all', 'woocommerce' ); ?></a>
					</span>
				</p>

				<div class="woocommerce_restrictions wc-metaboxes ui-sortable <?php echo esc_attr( $this->id ); ?>" data-hash="<?php echo esc_attr( $data_hash ); ?>">
					<?php

					if ( ! empty( $_GET[ 'add_rule' ] ) ) {
						$this->get_admin_global_metaboxes_content( -1, array() );
					}

					if ( ! empty( $global_restrictions ) ) {

						foreach ( $global_restrictions  as $index => $restriction_data ) {
							$this->get_admin_global_metaboxes_content( $index, $restriction_data );
						}

					} 

					// Empty state.
					?><div class="woocommerce_restrictions__boarding">
						<div class="woocommerce_restrictions__boarding__message">
							<h3><?php echo esc_html( $this->title ); ?></h3>
							<p><?php esc_html_e( 'No restrictions found. Add some now?', 'woocommerce-conditional-shipping-and-payments' ); ?></p>
						</div>
					</div>
				</div>
				<p class="submit toolbar toolbar--footer borderless <?php echo empty( $global_restrictions ) && empty( $_GET[ 'add_rule' ] ) ? 'restriction_data--empty' : '' ; ?>">
					<button id="woocommerce-add-global-restriction" type="button" class="button button-secondary add_restriction"><?php esc_html_e( 'Add Restriction', 'woocommerce-conditional-shipping-and-payments' ); ?></button>
					<button name="save" class="button button-primary woocommerce-save-button" type="submit" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
				</p>
			</div>
		</div>
		</td></tr><?php
	}

	/**
	 * Get restriction content for admin product metaboxes.
	 *
	 * Product restriction content is always in metaboxes.
	 *
	 * @param  int    $index
	 * @param  array  $options
	 * @param  bool   $ajax
	 * @return str
	 */
	public function get_admin_product_metaboxes_content( $index, $options = array(), $ajax = false ) {

		$restriction_id = $this->id;

		if ( isset( $options[ 'index' ] ) ) {
			$count = $options[ 'index' ] + 1;
		} else {
			$count = $index + 1;
		}

		// Add active key if not exists.
		if ( ! isset( $options[ 'enabled' ] ) ) {
			// By default active.
			$options[ 'enabled' ] = 'yes';
		}

		?>
		<div class="<?php echo $ajax ? 'woocommerce_restriction--added ' : ''; ?>woocommerce_restriction woocommerce_restriction_<?php echo esc_attr( $restriction_id ); ?> wc-metabox <?php echo ! $ajax ? 'closed' : 'open'; ?>" data-restriction_id="<?php echo esc_attr( $restriction_id ); ?>" data-index="<?php echo esc_attr( $index ); ?>">
			<h3>
				<div class="restriction_title">
					<?php
					$toggle_class = ( 'yes' === $options[ 'enabled' ] ) ? 'woocommerce-input-toggle--enabled' : 'woocommerce-input-toggle--disabled';
					?>
					<span id="active-toggle" class="woocommerce-input-toggle <?php echo esc_attr( $toggle_class ); ?>"></span>
					<span class="restriction_title_index_container">#<span class="restriction_title_index"><?php echo (int) $count; ?></span></span> <?php
						echo esc_html( $this->get_title() );

						if ( ! $ajax ) {
							echo wp_kses_post( '  &nbsp;&ndash;&nbsp;  ' . '<span class="restriction_title_inner">' . $this->get_options_description( $options ) . '</span>' );
						}
					?>
				</div>
				<div class="handle">
					<div class="handle-item toggle-item" aria-label="<?php esc_attr_e( 'Click to toggle', 'woocommerce' ); ?>"></div>
					<div class="handle-item sort-item" aria-label="<?php esc_attr_e( 'Drag and drop to set order', 'woocommerce-conditional-shipping-and-payments' ); ?>"></div>
					<a href="#" class="remove_row delete"><?php esc_html_e( 'Remove', 'woocommerce' ); ?></a>
				</div>

			</h3>
			<div class="woocommerce_restriction_data wc-metabox-content" <?php echo ! $ajax ? 'style="display:none;"' : '' ; ?>>
				<input type="hidden" name="restriction[<?php echo (int) $index; ?>][enabled]" class="enabled" value="<?php echo esc_attr( $options[ 'enabled' ] ); ?>"/>
				<input type="hidden" name="restriction[<?php echo (int) $index; ?>][position]" class="position" value="<?php echo (int) $index; ?>"/>
				<input type="hidden" name="restriction[<?php echo (int) $index; ?>][restriction_id]" class="restriction_id" value="<?php echo esc_attr( $restriction_id ); ?>"/>
				<?php
				$this->get_admin_product_fields_html( $index, $options );
				do_action( 'woocommerce_csp_admin_product_fields', $this->id, $index, $options );
				?>
			</div>
		</div>
		<?php

	}

	/**
	 * Get restriction content for admin global metaboxes.
	 *
	 * Global restrictions do not necessarily need metaboxes.
	 *
	 * @param  str    $index
	 * @param  array  $options
	 * @param  bool   $ajax
	 * @return str
	 */
	public function get_admin_global_metaboxes_content( $index, $options = array(), $ajax = false ) {

		$restriction_id = $this->id;

		if ( isset( $options[ 'index' ] ) ) {
			$count = $options[ 'index' ] + 1;
		} else {
			$count = $index + 1;
		}

		// Add active key if not exists.
		if ( ! isset( $options[ 'enabled' ] ) ) {
			// By default active.
			$options[ 'enabled' ] = 'yes';
		}

		$editing_rule = isset( $_GET[ 'view_rule' ] ) && absint( $_GET[ 'view_rule' ] ) === absint( $index );
		$adding_rule  = ! empty( $_GET[ 'add_rule' ] ) && $index === -1;

		$state = 'closed';

		if ( $ajax || $editing_rule || $adding_rule ) {
			$state = 'open';
		}

		?>
		<div class="<?php echo $ajax ? 'woocommerce_restriction--added ' : ''; ?>woocommerce_restriction woocommerce_restriction_<?php echo esc_attr( $restriction_id ); ?> wc-metabox <?php echo esc_attr( $state ); ?>" data-restriction_id="<?php echo esc_attr( $restriction_id ); ?>" data-index="<?php echo esc_attr( $index ); ?>">
			<h3>
				<div class="restriction_title">
					<?php
					$toggle_class = ( 'yes' === $options[ 'enabled' ] ) ? 'woocommerce-input-toggle--enabled' : 'woocommerce-input-toggle--disabled';
					?>
					<span id="active-toggle" class="woocommerce-input-toggle <?php echo esc_attr( $toggle_class ); ?>"></span>
					<?php echo wp_kses_post( sprintf( __( '<span class="restriction_title_index_container">#<span class="restriction_title_index">%1$s</span></span> <span class="restriction_title_inner">%2$s</span>', 'woocommerce-conditional-shipping-and-payments' ), absint( $count + ( empty( $_GET[ 'add_rule' ] ) ? 0 : 1 ) ), esc_html( $this->get_options_description( $options ) ) )); ?>

				</div>
				<div class="handle">
					<div class="handle-item toggle-item" aria-label="<?php esc_attr_e( 'Click to toggle', 'woocommerce' ); ?>"></div>
					<div class="handle-item sort-item" aria-label="<?php esc_attr_e( 'Drag and drop to set order', 'woocommerce-conditional-shipping-and-payments' ); ?>"></div>
					<a href="#" class="remove_row delete"><?php esc_html_e( 'Remove', 'woocommerce' ); ?></a>
				</div>
			</h3>
			<div class="woocommerce_restriction_data wc-metabox-content" <?php echo $state === 'closed' ? 'style="display:none;"' : '' ; ?>>
				<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][enabled]" class="enabled" value="<?php echo esc_attr( $options[ 'enabled' ] ); ?>"/>
				<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][position]" class="position" value="<?php echo esc_attr( $index ); ?>"/>
				<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][restriction_id]" class="restriction_id" value="<?php echo esc_attr( $restriction_id ); ?>"/>
				<?php
				$this->get_admin_global_fields_html( $index, $options );
				do_action( 'woocommerce_csp_admin_global_fields', $this->id, $index, $options );
				?>
			</div>
		</div>
		<?php

	}

	/**
	 * Validate, process and return global options as required by 'update_global_restriction_data'.
	 *
	 * By default expects all fields posted inside an indexed 'restriction' array.
	 *
	 * @return array
	 */
	public function process_global_restriction_data() {

		if ( isset( $_POST[ 'restriction' ] ) ) {
			$posted_restrictions_data = wp_unslash( $_POST[ 'restriction' ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}

		$count            = 0;
		$loop             = 0;
		$restriction_data = array();

		if ( isset( $posted_restrictions_data ) ) {

			uasort( $posted_restrictions_data, array( $this, 'cmp' ) );

			foreach ( $posted_restrictions_data as &$posted_restriction_data ) {

				$posted_restriction_data[ 'index' ] = $loop + 1;

				$processed_data = $this->process_admin_global_fields( $posted_restriction_data );

				if ( $processed_data ) {

					$processed_data[ 'restriction_id' ] = $this->id;
					$processed_data[ 'index' ]          = $count;
					$processed_data[ 'enabled' ]        = ( $posted_restriction_data[ 'enabled' ] === 'yes' ) ? 'yes' : 'no';
					$processed_data                     = apply_filters( 'woocommerce_csp_process_admin_global_fields', $processed_data, $posted_restriction_data, $this->id );

					$processed_data[ 'wc_26_shipping' ] = 'yes';

					$restriction_data[ $count ] = $processed_data;
					$count++;
				}

				$loop++;
			}

			return $restriction_data;
		}

		return false;
	}

	/**
	 * Update global restriction settings.
	 *
	 * All settings are stored in the 'woocommerce_restrictions_global_settings' option by default.
	 *
	 * @return void
	 */
	public function update_global_restriction_data() {

		$restriction_data = get_option( 'wccsp_restrictions_global_settings', array() );

		$processed_data = $this->process_global_restriction_data();

		if ( ! $processed_data ) {
			unset( $restriction_data[ $this->id ] );
		} else {
			$restriction_data[ $this->id ] = $processed_data;
		}

		update_option( 'wccsp_restrictions_global_settings', $restriction_data );
	}

	/**
	 * Delete restriction rule based on given index.
	 *
	 * @since  1.4.0
	 * @param  int  $index
	 * @return bool
	 */
	public function delete_global_restriction_rule( $index ) {

		$restriction_data = get_option( 'wccsp_restrictions_global_settings', array() );
		$active_rules     = isset( $restriction_data[ $this->id ] ) ? $restriction_data[ $this->id ] : array();

		// Check out-of-bounds.
		if ( $index < 0 || $index >= count( $active_rules ) ) {
			return false;
		}

		unset( $active_rules[ $index ] );
		$active_rules = array_values( $active_rules );

		// Update index key.
		$loop = 0;
		foreach ( $active_rules as $key => $rule ) {
			$active_rules[ $key ][ 'index' ] = $loop++;
		}

		if ( empty( $active_rules ) ) {
			unset( $restriction_data[ $this->id ] );
		} else {
			$restriction_data[ $this->id ] = $active_rules;
		}

		return update_option( 'wccsp_restrictions_global_settings', $restriction_data );
	}

	/**
	 * Sort posted restriction data.
	 */
    public function cmp( $a, $b ) {

	    if ( $a[ 'position' ] == $b[ 'position' ] ) {
	        return 0;
	    }

	    return ( $a[ 'position' ] < $b[ 'position' ] ) ? -1 : 1;
	}

	/**
	 * Retrieves product restriction data.
	 *
	 * @param  int|WC_Product  $product
	 * @param  string          $context
	 * @return array
	 */
	public function get_product_restriction_data( $product, $context = 'view' ) {

		$disable_product_restrictions = get_option( 'wccsp_restrictions_disable_product', false );
		$restriction_data             = null;

		if ( 'view' === $context && 'yes' === $disable_product_restrictions ) {
			return array();
		}

		if ( is_object( $product ) ) {
			$product_id = WC_CSP_Core_Compatibility::get_product_id( $product );
			$product    = $product_id === WC_CSP_Core_Compatibility::get_id( $product ) ? $product : wc_get_product( $product_id );
		} else {
			$product_id = absint( $product );
			$product    = wc_get_product( $product_id );
		}

		if ( 'view' === $context ) {
			$restriction_data = WC_CSP_Helpers::cache_get( $this->id . '_' . $product_id, 'product_restriction_data' );
		}

		if ( is_null( $restriction_data ) ) {

			$restriction_data = array();
			$restriction_meta = WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) && $product ? $product->get_meta( '_wccsp_restrictions', true ) : get_post_meta( $product_id, '_wccsp_restrictions', true );

			$restrictions = WC_CSP()->restrictions->maybe_update_restriction_data( $restriction_meta, 'product' );

			if ( $restrictions ) {
				foreach ( $restrictions as $restriction ) {
					if ( $restriction[ 'restriction_id' ] == $this->id ) {

						$is_disabled = ! empty( $restriction[ 'enabled' ] ) && 'no' === $restriction[ 'enabled' ];

						if ( 'view' === $context ) {

							// Omit rule if restriction is disabled in view context.
							if ( $is_disabled ) {
								continue;
							}

							if ( ! empty( $restriction[ 'conditions' ] ) ) {
								$restriction[ 'conditions' ] = WC_CSP()->conditions->get_optimized_conditions_data( $restriction[ 'conditions' ] );
							}
						}

						$restriction_data[] = $restriction;
					}
				}
			}

			if ( 'view' === $context ) {
				WC_CSP_Helpers::cache_set( $this->id . '_' . $product_id, $restriction_data, 'product_restriction_data' );
			}
		}

		return 'view' === $context ? apply_filters( 'woocommerce_csp_product_restriction_data', $restriction_data, $this ) : $restriction_data;
	}

	/**
	 * Retrieves global restriction data.
	 *
	 * @param  string  $context
	 * @return array
	 */
	public function get_global_restriction_data( $context = 'view' ) {

		$disable_global_restrictions = get_option( 'wccsp_restrictions_disable_global', false );
		$restriction_data            = null;

		if ( 'view' === $context ) {

			if ( 'yes' === $disable_global_restrictions ) {
				return array();
			}

			$restriction_data = WC_CSP_Helpers::cache_get( $this->id, 'global_restriction_data' );
		}

		if ( is_null( $restriction_data ) ) {

			$restriction_data    = array();
			$global_restrictions = WC_CSP()->restrictions->maybe_update_restriction_data( get_option( 'wccsp_restrictions_global_settings', false ), 'global' );

			if ( $global_restrictions && isset( $global_restrictions[ $this->id ] ) ) {
				foreach ( $global_restrictions[ $this->id ] as $restriction ) {

					$is_disabled = ! empty( $restriction[ 'enabled' ] ) && 'no' === $restriction[ 'enabled' ];

					if ( 'view' === $context ) {

						// Omit rule if restriction is disabled in view context.
						if ( $is_disabled ) {
							continue;
						}

						if ( ! empty( $restriction[ 'conditions' ] ) ) {
							$restriction[ 'conditions' ] = WC_CSP()->conditions->get_optimized_conditions_data( $restriction[ 'conditions' ] );
						}
					}

					$restriction_data[] = $restriction;
				}
			}

			if ( 'view' === $context ) {
				WC_CSP_Helpers::cache_set( $this->id, $restriction_data, 'global_restriction_data' );
			}
		}

		return 'view' === $context ? apply_filters( 'woocommerce_csp_global_restriction_data', $restriction_data, $this ) : $restriction_data;
	}

	/**
	 * Checks if all conditions of a restriction instance are true.
	 *
	 * @param  array  $restriction_data
	 * @param  array  $args
	 * @return bool
	 */
	public function check_conditions_apply( $restriction_data, $args = array() ) {

		// Conditions apply if no conditions are defined.
		if ( empty( $restriction_data[ 'conditions' ] ) ) {
			return true;
		}

		$conditions = $restriction_data[ 'conditions' ];
		$args       = array_merge( $args, array( 'restriction_data' => $restriction_data ) );

		// Otherwise, all conditions must apply to return true.
		$conditions_apply = true;

		foreach ( $conditions as $condition_key => $condition_data ) {

			if ( ! apply_filters( 'woocommerce_csp_check_condition', WC_CSP()->conditions->check_condition( $condition_data, $args ), $condition_key, $condition_data, $args, $conditions ) ) {
				$conditions_apply = false;
				break;
			}
		}

		return $conditions_apply;
	}

	/**
	 * Compiles a 'resolution' message that describes what steps can be taken to overcome a restriction based on the defined conditions.
	 *
	 * @param  array  $restriction_data
	 * @param  array  $args
	 * @return string
	 */
	public function get_conditions_resolution( $restriction_data, $args = array() ) {

		// Conditions have no resolution if no conditions are defined.
		if ( empty( $restriction_data[ 'conditions' ] ) ) {
			return false;
		}

		$conditions  = $restriction_data[ 'conditions' ];
		$args        = array_merge( $args, array( 'restriction_data' => $restriction_data ) );
		$resolutions = array();
		$string      = '';

		foreach ( $conditions as $condition_key => $condition_data ) {

			$resolution = apply_filters( 'woocommerce_csp_get_condition_resolution', WC_CSP()->conditions->get_condition_resolution( $condition_data, $args ), $condition_key, $condition_data, $args, $conditions );

			if ( false !== $resolution ) {
				$resolutions[] = $resolution;
			}
		}

		$resolutions = array_unique( $resolutions );

		if ( ! empty( $resolutions ) ) {

			if ( count( $resolutions ) == 1 ) {

				return current( $resolutions );

			} else {

				$string = current( $resolutions );

				for ( $i = 1; $i < count( $resolutions ) - 1; $i++ ) {

					/* translators: Used to stitch together a resolution meesage based on a restriction's active conditions */
					$string = sprintf( __( '%1$s, %2$s', 'woocommerce-conditional-shipping-and-payments' ), $string, $resolutions[ $i ] );
				}

				/* translators: Used to stitch together a resolution meesage based on a restriction's active conditions - last condition */
				$string = sprintf( __( '%1$s, or %2$s', 'woocommerce-conditional-shipping-and-payments' ), $string, end( $resolutions ) );

			}

		} else {

			return false;
		}

		return $string;
	}

	/**
	 * Display a short summary of the restriction's settings.
	 *
	 * @param  array  $options
	 * @return string
	 */
	public function get_options_description( $options ) {
		return '';
	}

	/**
	 * Display a short summary of what needs to change to lift this restriction.
	 *
	 * @param  array   $restriction
	 * @param  string  $context
	 * @param  array   $args
	 * @return string
	 */
	public function get_resolution_message( $restriction, $context, $args = array() ) {

		/**
		 * Filter the resolution message.
		 *
		 * @since  1.7.7
		 *
		 * @param  string  $message
		 * @param  array   $restriction
		 * @param  string  $context
		 * @param  array   $args
		 */
		return apply_filters( 'woocommerce_csp_' . $this->id . '_resolution_message', $this->get_resolution_message_content( $restriction, $context, $args ), $restriction, $context, $args );
	}

	/**
	 * Short summary of what needs to change to lift this restriction.
	 *
	 * @param  array   $restriction
	 * @param  string  $context
	 * @param  array   $args
	 * @return string
	 */
	protected function get_resolution_message_content( $restriction, $context, $args = array() ) {
		return '';
	}

	/**
	 * Show excluded methods or validate selection only.
	 *
	 * @param  array  $restriction_data
	 * @return bool
	 */
	protected function show_excluded( $restriction_data ) {
		return ! empty( $restriction_data[ 'show_excluded' ] ) && 'yes' === $restriction_data[ 'show_excluded' ];
	}

	/**
	 * Show static notices below excluded methods/gateways.
	 *
	 * @since  1.7.0
	 *
	 * @param  array  $restriction_data
	 * @return bool
	 */
	protected function show_excluded_notices( $restriction_data ) {
		return $this->show_excluded( $restriction_data ) && ! empty( $restriction_data[ 'show_excluded_notices' ] ) && 'yes' === $restriction_data[ 'show_excluded_notices' ] && ! is_cart();
	}

	/**
	 * Generate map data for each active rule.
	 *
	 * @since  1.4.0
	 *
	 * @param  array  $payload
	 * @param  array  $restriction
	 * @param  bool   $include_data
	 * @return array
	 */
	protected function generate_rules_map_data( $payload, $restriction, $include_data ) {
		return array();
	}

	/**
	 * Flatten all exclude ids into a unique array.
	 *
	 * @since  1.4.0
	 *
	 * @param  array  $maps
	 * @return array
	 */
	protected function get_unique_exclusion_ids( $maps ) {

		// Initialize store.
		$unique_ids = array();

		foreach ( $maps as $map ) {
			foreach ( $map as $rule_index => $rule_map ) {
				if ( ! empty( $rule_map ) ) {
					$unique_ids = array_merge( $rule_map, $unique_ids );
				}
			}
		}

		return array_unique( $unique_ids );
	}

	/**
	 * Evaluate all restriction rules and parse all matching indexes.
	 *
	 * @since  1.4.0
	 *
	 * @param  array  $restriction_data
	 * @param  array  $payload
	 * @param  array  $args
	 * @return array
	 */
	public function get_matching_rules_map( $restriction_data, $payload, $args = array() ) {

		$active_rules_map = array();

		// Bail out early and not run if we're on wp-admin.
		if ( is_admin() ) {
			return array();
		}

		if ( ! empty( $restriction_data ) ) {

			foreach ( $restriction_data as $i => $restriction ) {

				if ( ! empty( $restriction[ $this->restricted_key ] ) && $this->check_conditions_apply( $restriction, $args ) ) {

					if ( isset( $args[ 'include_data' ] ) ) {

						$include_data = (bool) $args[ 'include_data' ];

					} else {

						if ( isset( $args[ 'context' ] ) && 'check' === $args[ 'context' ] ) {
							$include_data = $this->show_excluded_notices( $restriction );
						} else {
							$include_data = ! $this->show_excluded( $restriction );
						}
					}

					/**
					 * 'woocommerce_csp_rule_map_include_restriction_data' filter.
					 *
					 * @since  1.7.0
					 *
					 * @param  bool   $include
					 * @param  array  $restriction_data
					 * @param  array  $payload
					 * @param  array  $args
					 */
					$include_data = apply_filters( 'woocommerce_csp_rule_map_include_restriction_data', $include_data, $restriction, $payload, $args );

					// Generate data.
					$active_rules_map[ $i ] = $this->generate_rules_map_data( $payload, $restriction, $include_data );
				}
			}
		}

		return $active_rules_map;
	}

	/**
	 * If the restriction supports multiple rule definitions.
	 * @return bool
	 */
	public function supports_multiple() {
		return $this->supports_multiple;
	}

	/**
	 * Checks if a feature is supported.
	 *
	 * @since  1.13.0
	 *
	 * @param  string  $feature
	 * @return boolean
	 */
	public function supports( $feature ) {

		if ( 'static-notices' === $feature ) {
			return ! WC_CSP_Core_Compatibility::is_block_based_checkout();
		} elseif ( 'multiple' === $feature ) {
			return $this->supports_multiple();
		}

		return true;
	}

	/**
	 * Add an extra variable in package information.
	 *
	 * @since  1.4.0
	 *
	 * @param  array   $package
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function add_extra_package_variable( &$package, $key, $value ) {

		if ( ! isset( $package[ 'csp_variables' ] ) ) {
			$package[ 'csp_variables' ] = array();
		}

		$package[ 'csp_variables' ][ $key ] = $value;
	}

	/**
	 * Get an extra's package variable value.
	 *
	 * @since  1.4.0
	 *
	 * @param  array   $package
	 * @param  string  $key
	 * @return mixed
	 */
	public static function get_extra_package_variable( $package, $key ) {

		if ( empty( $key ) || empty( $package ) || ! isset( $package['csp_variables'] ) ) {
			return false;
		}

		return isset( $package[ 'csp_variables' ][ $key ] ) ? $package[ 'csp_variables' ][ $key ] : false;
	}
}
