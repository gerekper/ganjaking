<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements admin features of YITH WooCommerce Subscription
 *
 * @class   YITH_WC_Subscription_Admin
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Subscription_Admin' ) ) {

	class YITH_WC_Subscription_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Subscription_Admin
		 */

		protected static $instance;

		/**
		 * @var $_panel Panel Object
		 */
		protected $_panel;

		/**
		 * @var $_premium string Premium tab template file name
		 */
		protected $_premium = 'premium.php';

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-subscription/';

		/**
		 * @var string Official Plugin Demo
		 */
		protected $_premium_live = 'https://plugins.yithemes.com/yith-woocommerce-subscription/';

		/**
		 * @var string Panel page
		 */
		protected $_panel_page = 'yith_woocommerce_subscription';

		/**
		 * @var string Doc Url
		 */
		public $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-subscription/';

		/**
		 * @var string Official plugin support page
		 */
		protected $_support = 'https://wordpress.org/support/plugin/yith-woocommerce-subscription';

		/**
		 * @var YITH_YWSBS_Subscriptions_List_Table
		 */
		public $cpt_obj_subscriptions;

		/**
		 * @var YITH_YWSBS_Activities_List_Table
		 */
		public $cpt_obj_activities;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Subscription_Admin
		 * @since 1.0.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}



		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function __construct() {

			$this->create_menu_items();

			$this->_support = function_exists( 'yith_get_premium_support_url' ) ? yith_get_premium_support_url() : 'https://yithemes.com/my-account/support/dashboard/';

			if ( ! class_exists( 'WP_List_Table' ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
			}
			require_once YITH_YWSBS_INC . 'admin/class.ywsbs-subscriptions-list-table.php';
			require_once YITH_YWSBS_INC . 'admin/class.ywsbs-activities-list-table.php';

			// Add action links.
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWSBS_DIR . '/' . basename( YITH_YWSBS_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// Custom styles and javascripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 20 );

			// Product editor.
			add_filter( 'product_type_options', array( $this, 'add_type_options' ) );
			add_action( 'woocommerce_variation_options', array( $this, 'add_type_variation_options' ), 10, 3 );

			// Custom fields for single product.
			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_custom_fields_for_single_products' ) );
			add_action( 'woocommerce_product_options_shipping', array( $this, 'add_custom_fields_for_shipping_products' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_custom_fields_for_single_products' ), 10, 2 );

			// Custom fields for variation.
			add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_custom_fields_for_variation_products' ), 14, 3 );
			add_action( 'woocommerce_save_product_variation', array( $this, 'save_custom_fields_for_variation_products' ), 10 );

			add_action( 'admin_init', array( $this, 'change_url_to_sendback' ), 10 );

			/* Ajax save new amounts on subscription */
			add_action( 'wp_ajax_ywsbs_save_items', array( $this, 'save_new_amounts' ) );
			add_action( 'wp_ajax_nopriv_ywsbs_save_items', array( $this, 'save_new_amounts' ) );
			add_action( 'wp_ajax_ywsbs_recalculate', array( $this, 'recalculate' ) );
			add_action( 'wp_ajax_nopriv_ywsbs_recalculate', array( $this, 'recalculate' ) );

			// Sanitize the options before that are saved
			add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize_value_option' ), 20, 3 );

			add_action( 'plugins_loaded', array( $this, 'load_privacy_dpa' ), 20 );

			// add the column failed payments and membership
			add_filter( 'manage_shop_order_posts_columns', array( $this, 'add_order_post_columns' ), 20 );
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'add_order_posts_custom_column' ) );
			// add the column subscription on order list
			add_filter( 'manage_shop_order_posts_columns', array( $this, 'manage_shop_order_columns' ), 20 );
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'show_subscription_ref' ) );

			add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'add_order_details' ), 10, 1 );

		}



		/**
		 * Includes Privacy DPA Class.
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function load_privacy_dpa() {
			if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
				require_once YITH_YWSBS_INC . 'class.yith-wc-subscription-privacy-dpa.php';
			}
		}

		/**
		 * Called by hook admin_init to change the send back link
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function change_url_to_sendback() {
			global $pagenow;

			if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'ywsbs_subscription' ) {
				wp_safe_redirect( admin_url( 'admin.php?page=yith_woocommerce_subscription' ) );
				exit;
			}
		}

		/**
		 * Add a product type option in single product editor
		 *
		 * @access public
		 *
		 * @param $types
		 *
		 * @return array
		 * @since 1.0.0
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_type_options( $types ) {
			$types['ywsbs_subscription'] = array(
				'id'            => '_ywsbs_subscription',
				'wrapper_class' => 'show_if_simple',
				'label'         => __( 'Subscription', 'yith-woocommerce-subscription' ),
				'description'   => __( 'Create a subscription for this product', 'yith-woocommerce-subscription' ),
				'default'       => 'no',
			);

			return $types;
		}

		/**
		 * Add a product type option in variable product editor
		 *
		 * @access public
		 *
		 * @param $loop
		 * @param $variation_data
		 * @param $variation
		 *
		 * @return string
		 * @since 1.0.0
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_type_variation_options( $loop, $variation_data, $variation ) {

			$is_subscription = yit_get_prop( $variation, '_ywsbs_subscription' );
			$checked         = checked( $is_subscription, 'yes', false );
			echo '<label><input type="checkbox" class="checkbox variable_ywsbs_subscription" name="variable_ywsbs_subscription[' . $loop . ']" ' . $checked . ' /> ' . __( 'Subscription', 'yith-woocommerce-subscription' ) . ' <a class="tips" data-tip="' . __( 'Sell this variable product as a subscription product.', 'yith-woocommerce-subscription' ) . '" href="#">[?]</a></label>';

		}

		/**
		 * Add the field
		 *
		 * @since 1.4
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_custom_fields_for_shipping_products() {
			global $thepostid;
			$product                  = wc_get_product( $thepostid );
			$_ywsbs_one_time_shipping = yit_get_prop( $product, '_ywsbs_one_time_shipping' );

			woocommerce_wp_checkbox(
				array(
					'id'            => '_ywsbs_one_time_shipping',
					'value'         => $_ywsbs_one_time_shipping,
					'wrapper_class' => 'show_if_simple show_if_variable',
					'label'         => __( 'One time shipping', 'yith-woocommerce-subscription' ),
					'description'   => __( 'Check it if you want recurring payments without shipping.', 'yith-woocommerce-subscription' ),
				)
			);
		}

		/**
		 * Add custom fields for single product
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_custom_fields_for_single_products() {

			global $thepostid;

			$product = wc_get_product( $thepostid );

			$_ywsbs_price_is_per      = yit_get_prop( $product, '_ywsbs_price_is_per' );
			$_ywsbs_price_time_option = yit_get_prop( $product, '_ywsbs_price_time_option' );

			$_ywsbs_trial_per         = yit_get_prop( $product, '_ywsbs_trial_per' );
			$_ywsbs_trial_time_option = yit_get_prop( $product, '_ywsbs_trial_time_option' );

			$_ywsbs_fee        = yit_get_prop( $product, '_ywsbs_fee' );
			$_ywsbs_max_length = yit_get_prop( $product, '_ywsbs_max_length' );

			$_ywsbs_max_pause          = yit_get_prop( $product, '_ywsbs_max_pause' );
			$_ywsbs_max_pause_duration = yit_get_prop( $product, '_ywsbs_max_pause_duration' );

			$max_lengths = ywsbs_get_max_length_period();

			?>

			<div class="options_group show_if_simple ywsbs-general-section">
				<h4 class="ywsbs-title-section"><?php _e( 'Subscription Settings', 'yith-woocommerce-subscription' ); ?></h4>

				<p class="form-field ywsbs_price_is_per">
					<label for="_ywsbs_price_is_per"><?php _e( 'Price is per', 'yith-woocommerce-subscription' ); ?></label>
					<span class="wrap">
						<input type="number" class="ywsbs-short"  name="_ywsbs_price_is_per" id="_ywsbs_price_is_per" value="<?php echo esc_attr( $_ywsbs_price_is_per ); ?>"/>
						<select id="_ywsbs_price_time_option" name="_ywsbs_price_time_option" class="select ywsbs-with-margin" >
						<?php
						foreach ( ywsbs_get_time_options() as $key => $value ) :
							$select = selected( $_ywsbs_price_time_option, $key, false );
							echo '<option value="' . $key . '" ' . $select . ' data-max="' . $max_lengths[ $key ] . '" data-text="' . $value . '">' . $value . '</option>';
						endforeach;
						?>
					</select>
					</span>
					<?php echo wc_help_tip( __( 'Add the duration of the subscription', 'yith-woocommerce-subscription' ) ); ?>
				</p>

				<?php
				$time_opt = ( $_ywsbs_price_time_option ) ? $_ywsbs_price_time_option : 'days';

				$description = sprintf( '<span>%s</span> (%s <span class="max-l">%d</span>)', $time_opt, __( 'Max: ', 'yith-woocommerce-subscription' ), $max_lengths[ $time_opt ] );
				?>

				<p class="form-field ywsbs_max_length">
					<label for="_ywsbs_max_length"><?php _e( 'Max Length:', 'yith-woocommerce-subscription' ); ?></label>
					<input type="number" class="ywsbs-short" name="_ywsbs_max_length" id="_ywsbs_max_length"
						   value="<?php echo esc_attr( $_ywsbs_max_length ); ?>" style="float: left; width:15%; "/>
					<span class="description"><?php echo $description; ?></span>
					<?php echo wc_help_tip( __( 'Leave it empty for unlimited subscription', 'yith-woocommerce-subscription' ) ); ?>
				</p>

				<p class="form-field ywsbs_trial_per">
					<label for="_ywsbs_trial_per"><?php _e( 'Trial period', 'yith-woocommerce-subscription' ); ?></label>
					<input type="number" class="ywsbs-short" name="_ywsbs_trial_per" id="_ywsbs_trial_per"
						   value="<?php echo esc_attr( $_ywsbs_trial_per ); ?>"/>
					<select id="_ywsbs_trial_time_option" name="_ywsbs_trial_time_option" class="select ywsbs-with-margin">
						<?php
						foreach ( ywsbs_get_time_options() as $key => $value ) :
							$select = selected( $_ywsbs_trial_time_option, $key );
							echo '<option value="' . $key . '" ' . $select . '>' . $value . '</option>';
						endforeach;
						?>
					</select>
				</p>

				<?php
				woocommerce_wp_text_input(
					array(
						'id'        => '_ywsbs_fee',
						'value'     => esc_attr( $_ywsbs_fee ),
						'label'     => sprintf( __( 'Sign-up fee (%s)', 'yith-woocommerce-subscription' ), get_woocommerce_currency_symbol() ),
						'type'      => 'text',
						'data_type' => 'decimal',
						'class'     => 'ywsbs-short',
					)
				);

				woocommerce_wp_text_input(
					array(
						'id'          => '_ywsbs_max_pause',
						'value'       => esc_attr( $_ywsbs_max_pause ),
						'label'       => sprintf( __( 'Max Number of pauses', 'yith-woocommerce-subscription' ), get_woocommerce_currency_symbol() ),
						'type'        => 'number',
						'desc_tip'    => true,
						'description' => __( 'Leave empty if you do not want to allow pauses', 'yith-woocommerce-subscription' ),
						'class'       => 'ywsbs-short',
					)
				);

				woocommerce_wp_text_input(
					array(
						'id'          => '_ywsbs_max_pause_duration',
						'value'       => esc_attr( $_ywsbs_max_pause_duration ),
						'label'       => sprintf( __( 'Max duration of pauses', 'yith-woocommerce-subscription' ), get_woocommerce_currency_symbol() ),
						'type'        => 'number',
						'description' => __( 'days', 'yith-woocommerce-subscription' ),
						'class'       => 'ywsbs-short',
					)
				);
				?>
			</div>

			<?php

		}

		/**
		 * Save custom fields for single product
		 *
		 * @param $post_id
		 * @param $post
		 *
		 * @return void
		 * @since   1.0.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function save_custom_fields_for_single_products( $post_id, $post ) {

			if ( isset( $_POST['product-type'] ) && $_POST['product-type'] == 'variable' ) {
				$this->reset_custom_field_for_product( $post_id );
				return;
			}

			$product              = wc_get_product( $post_id );
			$args                 = array();
			$manual_fields_saving = array( '_ywsbs_subscription', '_ywsbs_one_time_shipping', '_ywsbs_max_length' );
			$custom_fields        = array_diff( $this->_get_custom_fields_list(), $manual_fields_saving );

			$args['_ywsbs_subscription']      = isset( $_POST['_ywsbs_subscription'] ) ? 'yes' : 'no';
			$args['_ywsbs_one_time_shipping'] = isset( $_POST['_ywsbs_one_time_shipping'] ) ? 'yes' : 'no';

			if ( isset( $_POST['_ywsbs_price_time_option'] ) && isset( $_POST['_ywsbs_max_length'] ) ) {
				$max_length                = ywsbs_validate_max_length( $_POST['_ywsbs_max_length'], $_POST['_ywsbs_price_time_option'] );
				$args['_ywsbs_max_length'] = $max_length;
			}

			foreach ( $custom_fields as $meta ) {
				if ( isset( $_POST[ $meta ] ) ) {
					$args[ $meta ] = $_POST[ $meta ];
				}
			}

			$args && yit_save_prop( $product, $args, false, true );

		}

		/**
		 * Add custom fields for variation products
		 *
		 * @param $loop
		 * @param $variation_data
		 * @param $variation
		 *
		 * @since   1.0.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_custom_fields_for_variation_products( $loop, $variation_data, $variation ) {

			$_ywsbs_price_is_per      = yit_get_prop( $variation, '_ywsbs_price_is_per' );
			$_ywsbs_price_time_option = yit_get_prop( $variation, '_ywsbs_price_time_option' );

			$_ywsbs_trial_per         = yit_get_prop( $variation, '_ywsbs_trial_per' );
			$_ywsbs_trial_time_option = yit_get_prop( $variation, '_ywsbs_trial_time_option' );

			$_ywsbs_fee = yit_get_prop( $variation, '_ywsbs_fee' );

			$_ywsbs_max_length = yit_get_prop( $variation, '_ywsbs_max_length' );

			$_ywsbs_max_pause          = yit_get_prop( $variation, '_ywsbs_max_pause' );
			$_ywsbs_max_pause_duration = yit_get_prop( $variation, '_ywsbs_max_pause_duration' );
			$_ywsbs_switchable         = yit_get_prop( $variation, '_ywsbs_switchable' );
			$checked_switchable        = checked( $_ywsbs_switchable, 'yes', false );
			$_ywsbs_prorate_length     = yit_get_prop( $variation, '_ywsbs_prorate_length' );
			$checked_prorate_length    = checked( $_ywsbs_prorate_length, 'yes', false );

			$_ywsbs_gap_payment  = yit_get_prop( $variation, '_ywsbs_gap_payment' );
			$checked_gap_payment = checked( $_ywsbs_gap_payment, 'yes', false );

			$_ywsbs_switchable_priority = yit_get_prop( $variation, '_ywsbs_switchable_priority' );

			$_ywsbs_switchable_priority = ( empty( $_ywsbs_switchable_priority ) && $_ywsbs_switchable == 'yes' ) ? $loop : $_ywsbs_switchable_priority;

			$max_lengths = ywsbs_get_max_length_period();
			?>
			<div class="ywsbs_subscription_variation_products">
				<h3><?php _e( 'Subscription Settings', 'yith-woocommerce-subscription' ); ?></h3>

				<p class="form-row form-row-first variable_ywsbs_price_is_per">
					<label for="_ywsbs_price_is_per" class="ywsbs-block"><?php _e( 'Price is per', 'yith-woocommerce-subscription' ); ?></label>
					<input type="number" class="variable_ywsbs_price_is_per ywsbs-short" name="variable_ywsbs_price_is_per[<?php echo $loop; ?>]" value="<?php echo esc_attr( $_ywsbs_price_is_per ); ?>"/>
					<select id="_ywsbs_price_time_option" name="variable_ywsbs_price_time_option[<?php echo $loop; ?>]" class="select ywsbs-with-margin ywsbs-short">
						<?php
						foreach ( ywsbs_get_time_options() as $key => $value ) :
							$select = selected( $_ywsbs_price_time_option, $key );
							echo '<option value="' . $key . '" ' . $select . ' data-max="' . $max_lengths[ $key ] . '" data-text="' . $value . '">' . $value . '</option>';
						endforeach;
						?>
					</select>
				</p>

				<p class="form-row form-row-last variable_ywsbs_max_length">
					<label for="_ywsbs_max_length"><?php _e( 'Max Length:', 'yith-woocommerce-subscription' ); ?></label>
					<?php echo wc_help_tip( __( 'Leave empty for unlimited subscription', 'yith-woocommerce-subscription' ) ); ?>
					<input type="number"  name="variable_ywsbs_max_length[<?php echo $loop; ?>]"  value="<?php echo esc_attr( $_ywsbs_max_length ); ?>"/>
					<span class="description"><span><?php echo $time_opt = ( $_ywsbs_price_time_option ) ? $_ywsbs_price_time_option : 'days'; ?></span> <?php printf( __( '(Max: <span class="max-l">%d</span>)', 'yith-woocommerce-subscription' ), $max_lengths[ $time_opt ] ); ?></span>
				</p>

				<p class="form-row form-row-first variable_ywsbs_trial_per">
					<label for="_ywsbs_trial_per" class="ywsbs-block"><?php _e( 'Trial period', 'yith-woocommerce-subscription' ); ?></label>
					<input type="number" class="variable_ywsbs_trial_per ywsbs-short" name="variable_ywsbs_trial_per[<?php echo $loop; ?>]" value="<?php echo esc_attr( $_ywsbs_trial_per ); ?>"/>
					<select id="_ywsbs_trial_time_option" name="variable_ywsbs_trial_time_option[<?php echo $loop; ?>]" class="select ywsbs-with-margin ywsbs-short">
						<?php
						foreach ( ywsbs_get_time_options() as $key => $value ) :
							$select = selected( $_ywsbs_trial_time_option, $key );
							echo '<option value="' . $key . '" ' . $select . '>' . $value . '</option>';
						endforeach;
						?>
					</select>
				</p>

				<p class="form-row form-row-last variable_ywsbs_fee">
					<label for="_ywsbs_fee"><?php printf( __( 'Sign-up fee (%s)', 'yith-woocommerce-subscription' ), get_woocommerce_currency_symbol() ); ?></label>
					<input type="text" class="short wc_input_decimal" size="5" name="variable_ywsbs_fee[<?php echo $loop; ?>]"
						   value="<?php echo esc_attr( $_ywsbs_fee ); ?>"/>
				</p>

				<p class="form-row form-row-first variable_ywsbs_pauses">
					<label><?php _e( 'Max Number of pauses', 'yith-woocommerce-subscription' ); ?></label>
					<?php echo wc_help_tip( __( 'Leave empty if you do not want to allow pauses', 'yith-woocommerce-subscription' ) ); ?>
					<input type="number" class="short"  name="variable_ywsbs_max_pause[<?php echo $loop; ?>]"
						   value="<?php echo esc_attr( $_ywsbs_max_pause ); ?>"/>
				</p>

				<p class="form-row form-row-last variable_ywsbs_pauses">
					<label><?php _e( 'Max duration of pauses', 'yith-woocommerce-subscription' ); ?></label>
					<input type="number" class="short" size="5"
						   name="variable_ywsbs_max_pause_duration[<?php echo $loop; ?>]"
						   value="<?php echo esc_attr( $_ywsbs_max_pause_duration ); ?>"/>
					<span class="description"><?php _e( 'days', 'yith-woocommerce-subscription' ); ?></span>
				</p>

				<p class="form-row form-row-first variable_ywsbs_switchable">
					<label><?php _e( 'Allow switch to this variation', 'yith-woocommerce-subscription' ); ?></label>
					<input type="checkbox"
						   name="variable_ywsbs_switchable[<?php echo $loop; ?>]" <?php echo $checked_switchable; ?> />
				</p>

				<p class="form-row form-row-last variable_ywsbs_switchable">
					<label><?php _e( 'Priority', 'yith-woocommerce-subscription' ); ?></label>
					<?php echo wc_help_tip( __( 'This field allows you to set a hierarchy in the subscription variations. For example, if you switch from a variation with a lower priority to another with higher priority, the switching process is regarded as an upgrade.', 'yith-woocommerce-subscription' ) ); ?>
					<input type="number" class="short" size="5"
						   name="variable_ywsbs_switchable_priority[<?php echo $loop; ?>]"
						   value="<?php echo $_ywsbs_switchable_priority; ?>"/>
				</p>

				<p class="form-row form-row-first variable_ywsbs_switchable">
					<label><?php _e( 'Change duration', 'yith-woocommerce-subscription' ); ?></label>
					<input type="checkbox"
						   name="variable_ywsbs_prorate_length[<?php echo $loop; ?>]" <?php echo $checked_prorate_length; ?> />
					<?php
					echo wc_help_tip(
						__(
							'This field allows you to change the duration of the
                     subscription considering also the time the customer has benefitted of the subscription',
							'yith-woocommerce-subscription'
						)
					);
					?>
				</p>

				<p class="form-row form-row-last variable_ywsbs_switchable">
					<label><?php _e( 'Permit catch up payment when upgrading', 'yith-woocommerce-subscription' ); ?></label>
					<input type="checkbox"
						   name="variable_ywsbs_gap_payment[<?php echo $loop; ?>]" <?php echo $checked_gap_payment; ?> />
					<?php
					echo wc_help_tip(
						__(
							'This field allows users
                    to catch up with payments of the previous subscription',
							'yith-woocommerce-subscription'
						)
					);
					?>
				</p>

			</div>
			<?php
		}

		/**
		 * Save custom fields for variation products
		 *
		 * @param $variation_id
		 *
		 * @return bool
		 * @since   1.0.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function save_custom_fields_for_variation_products( $variation_id ) {

			// reset custom field for the parent product
			if ( isset( $_POST['product_id'] ) ) {
				$this->reset_custom_field_for_product( $_POST['product_id'] );
			}

			$variation            = wc_get_product( $variation_id );
			$args                 = array();
			$manual_fields_saving = array(
				'_ywsbs_subscription',
				'_ywsbs_switchable',
				'_ywsbs_prorate_length',
				'_ywsbs_gap_payment',
				'_ywsbs_max_length',
			);
			$custom_fields        = array_diff( $this->_get_custom_fields_list(), $manual_fields_saving );

			if ( isset( $_POST['variable_post_id'] ) && ! empty( $_POST['variable_post_id'] ) ) {
				$current_variation_index = array_search( $variation_id, $_POST['variable_post_id'] );
			}

			if ( $current_variation_index === false ) {
				return false;
			}

			$args['_ywsbs_subscription']   = isset( $_POST['variable_ywsbs_subscription'][ $current_variation_index ] ) ? 'yes' : 'no';
			$args['_ywsbs_switchable']     = isset( $_POST['variable_ywsbs_switchable'][ $current_variation_index ] ) ? 'yes' : 'no';
			$args['_ywsbs_prorate_length'] = isset( $_POST['variable_ywsbs_prorate_length'][ $current_variation_index ] ) ? 'yes' : 'no';
			$args['_ywsbs_gap_payment']    = isset( $_POST['variable_ywsbs_gap_payment'][ $current_variation_index ] ) ? 'yes' : 'no';

			if ( isset( $_POST['variable_ywsbs_max_length'][ $current_variation_index ] ) && isset( $_POST['variable_ywsbs_price_time_option'][ $current_variation_index ] ) ) {
				$max_length                = ywsbs_validate_max_length( $_POST['variable_ywsbs_max_length'][ $current_variation_index ], $_POST['variable_ywsbs_price_time_option'][ $current_variation_index ] );
				$args['_ywsbs_max_length'] = $max_length;
			}

			foreach ( $custom_fields as $meta ) {
				if ( isset( $_POST[ 'variable' . $meta ][ $current_variation_index ] ) ) {
					$args[ $meta ] = $_POST[ 'variable' . $meta ][ $current_variation_index ];
				}
			}

			$args && yit_save_prop( $variation, $args, false, true );

		}

		/**
		 * Reset custom field
		 *
		 * @access public
		 *
		 * @param $product_id
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		private function reset_custom_field_for_product( $product_id ) {

			$product       = wc_get_product( $product_id );
			$custom_fields = $this->_get_custom_fields_list();

			foreach ( $custom_fields as $cf ) {
				yit_delete_prop( $product, $cf );
			}

			isset( $_POST['_ywsbs_one_time_shipping'] ) && yit_save_prop( $product, '_ywsbs_one_time_shipping', 'yes' );
		}


		/**
		 * Return the list of custom fields relative to subscription.
		 *
		 * @return mixed|void
		 * @since 1.4
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		private function _get_custom_fields_list() {
			$custom_fields = array(
				'_ywsbs_subscription',
				'_ywsbs_price_is_per',
				'_ywsbs_price_time_option',
				'_ywsbs_max_length',
				'_ywsbs_fee',
				'_ywsbs_trial_per',
				'_ywsbs_trial_time_option',
				'_ywsbs_switchable',
				'_ywsbs_prorate_length',
				'_ywsbs_gap_payment',
				'_ywsbs_switchable_priority',
				'_ywsbs_max_pause',
				'_ywsbs_max_pause_duration',
				'_ywsbs_one_time_shipping',
			);

			return apply_filters( 'ywsbs_custom_fields_list', $custom_fields );
		}

		/**
		 * Enqueue styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since 1.0.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function enqueue_styles_scripts() {

			if ( ywsbs_check_valid_admin_page( YITH_WC_Subscription()->post_name ) || ( isset( $_GET['page'] ) && $_GET['page'] == 'yith_woocommerce_subscription' ) ) {

				wp_enqueue_style( 'yith_ywsbs_backend', YITH_YWSBS_ASSETS_URL . '/css/backend.css', array( 'woocommerce_admin_styles', 'jquery-ui-style' ), YITH_YWSBS_VERSION );
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'yith_ywsbs_timepicker', YITH_YWSBS_ASSETS_URL . '/js/jquery-ui-timepicker-addon.min.js', array( 'jquery' ), YITH_YWSBS_VERSION, true );

				wp_enqueue_script(
					'yith_ywsbs_admin',
					YITH_YWSBS_ASSETS_URL . '/js/ywsbs-admin' . YITH_YWSBS_SUFFIX . '.js',
					array(
						'jquery',
						'yith_ywsbs_timepicker',
					),
					YITH_YWSBS_VERSION,
					true
				);

				wp_enqueue_script( 'jquery-blockui', YITH_YWSBS_ASSETS_URL . '/js/jquery.blockUI.min.js', array( 'jquery' ), false, true );

				wp_localize_script(
					'yith_ywsbs_admin',
					'yith_ywsbs_admin',
					array(
						'ajaxurl'                    => admin_url( 'admin-ajax.php' ),
						'block_loader'               => apply_filters( 'yith_ywsbs_block_loader_admin', YITH_YWSBS_ASSETS_URL . '/images/block-loader.gif' ),
						'time_format'                => apply_filters( 'ywsbs_time_format', 'Y-m-d H:i:s' ),
						'copy_billing'               => __( 'Copy billing information to shipping information? This will remove any currently entered shipping information.', 'yith-woocommerce-subscription' ),
						'load_billing'               => __( "Load the customer's billing information? This will remove any currently entered billing information.", 'yith-woocommerce-subscription' ),
						'no_customer_selected'       => __( 'User is not registered', 'yith-woocommerce-subscription' ),
						'get_customer_details_nonce' => wp_create_nonce( 'get-customer-details' ),
						'save_item_nonce'            => wp_create_nonce( 'save-item-nonce' ),
						'recalculate_nonce'          => wp_create_nonce( 'recalculate_nonce' ),
						'load_shipping'              => __( "Load the customer's shipping information? This will remove any currently entered shipping information.", 'yith-woocommerce-subscription' ),
					)
				);
			}

			if ( ywsbs_check_valid_admin_page( 'product' ) ) {
				wp_enqueue_style( 'yith_ywsbs_product', YITH_YWSBS_ASSETS_URL . '/css/ywsbs-product-editor.css', null, YITH_YWSBS_VERSION );
				wp_enqueue_script( 'yith_ywsbs_product', YITH_YWSBS_ASSETS_URL . '/js/ywsbs-product-editor' . YITH_YWSBS_SUFFIX . '.js', array( 'jquery' ), YITH_YWSBS_VERSION, true );
			}
		}

		/**
		 * Save a new amount on subscription from subscription detail.
		 *
		 * @since 1.4.5
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function save_new_amounts() {
			check_ajax_referer( 'save-item-nonce', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_REQUEST['subscription_id'] ) ) {
				wp_die( -1 );
			}

			parse_str( $_REQUEST['items'], $posted );
			$subscription = ywsbs_get_subscription( $_REQUEST['subscription_id'] );
			$subscription->update_prices( $posted );

			include YITH_YWSBS_TEMPLATE_PATH . '/admin/metabox/metabox_subscription_product.php';
			wp_die();
		}


		/**
		 * Recalculate the taxes from the total amounts.
		 *
		 * @since 1.4.5
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function recalculate() {
			check_ajax_referer( 'recalculate_nonce', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_REQUEST['subscription_id'] ) ) {
				wp_die( -1 );
			}

			$subscription = ywsbs_get_subscription( $_REQUEST['subscription_id'] );
			$subscription->recalculate_prices();

			include YITH_YWSBS_TEMPLATE_PATH . '/admin/metabox/metabox_subscription_product.php';
			wp_die();

		}

		/**
		 * Create Menu Items
		 *
		 * Print admin menu items
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		private function create_menu_items() {

			// Add a panel under YITH Plugins tab
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'yith_ywsbs_subscriptions_tab', array( $this, 'subscriptions_tab' ) );
			add_action( 'yith_ywsbs_activities_tab', array( $this, 'activities_tab' ) );
			add_action( 'yith_ywsbs_premium_tab', array( $this, 'premium_tab' ) );
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      /YIT_Plugin_Panel_WooCommerce class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'subscriptions' => __( 'Subscriptions', 'yith-woocommerce-subscription' ),
				'activities'    => __( 'Activities', 'yith-woocommerce-subscription' ),
				'general'       => __( 'Settings', 'yith-woocommerce-subscription' ),
			);

			if ( yith_check_privacy_enabled() ) {
				$admin_tabs['privacy'] = __( 'Privacy', 'yith-woocommerce-subscription' );
			}

			$admin_tabs = apply_filters( 'ywsbs_register_panel_tabs', $admin_tabs );

			$args = array(
				'create_menu_page' => apply_filters( 'ywsbs_register_panel_create_menu_page', true ),
				'parent_slug'      => '',
				'page_title'       => 'YITH WooCommerce Subscription',
				'menu_title'       => 'Subscription',
				'capability'       => apply_filters( 'ywsbs_register_panel_capabilities', 'manage_options' ),
				'parent'           => '',
				'parent_page'      => apply_filters( 'ywsbs_register_panel_parent_page', 'yith_plugin_panel' ),
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_YWSBS_DIR . '/plugin-options',
				'position'         => apply_filters( 'ywsbs_register_panel_position', null ),
			);

			// enable shop manager to see Subscriptions
			if ( get_option( 'ywsbs_enable_shop_manager', 'no' ) == 'yes' ) {
				add_filter( 'option_page_capability_yit_' . $args['parent'] . '_options', array( $this, 'change_capability' ) );
				$args['capability'] = 'manage_woocommerce';
			}

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel' ) ) {
				require_once YITH_YWSBS_DIR . '/plugin-fw/lib/yit-plugin-panel.php';
			}

			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once YITH_YWSBS_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function premium_tab() {
			$premium_tab_template = YITH_YWSBS_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once $premium_tab_template;
			}
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return mixed
		 * @use      plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			if ( function_exists( 'yith_add_action_links' ) ) {
				$links = yith_add_action_links( $links, $this->_panel_page, true );
			}

			return $links;
		}


		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $new_row_meta_args
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @param string            $init_file
		 *
		 * @return   Array
		 * @since    1.6.5
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWSBS_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']       = YITH_YWSBS_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing . '?refer_id=1030585';
		}

		/**
		 * Modify the capability
		 *
		 * @param $capability
		 *
		 * @return string
		 */
		function change_capability( $capability ) {
			return 'manage_woocommerce';
		}

		/**
		 * Subscriptions List Table
		 *
		 * Load the subscriptions on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function subscriptions_tab() {
			$this->cpt_obj_subscriptions = new YITH_YWSBS_Subscriptions_List_Table();

			$subscriptions_tab = YITH_YWSBS_TEMPLATE_PATH . '/admin/subscriptions-tab.php';

			if ( file_exists( $subscriptions_tab ) ) {
				include_once $subscriptions_tab;
			}
		}

		/**
		 * Activities List Table
		 *
		 * Load the activites on admin page
		 *
		 * @return   void
		 * @since    1.0.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function activities_tab() {
			$this->cpt_obj_activities = new YITH_YWSBS_Activities_List_Table();

			$activities_tab = YITH_YWSBS_TEMPLATE_PATH . '/admin/activities-tab.php';

			if ( file_exists( $activities_tab ) ) {
				include_once $activities_tab;
			}
		}

		/**
		 * Add subscription column
		 *
		 * @param $columns
		 * @return array
		 * @since 1.4.5
		 */
		public function manage_shop_order_columns( $columns ) {

			$order_items = array( 'subscription_ref' => __( 'Subscription', 'yith-woocommerce-subscription' ) );
			$ref_pos     = array_search( 'order_date', array_keys( $columns ) );
			$columns     = array_slice( $columns, 0, $ref_pos + 1, true ) + $order_items + array_slice( $columns, $ref_pos + 1, count( $columns ) - 1, true );

			return $columns;
		}

		/**
		 * Show the subscription number inside the order list.
		 *
		 * @param $column
		 * @return void
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function show_subscription_ref( $column ) {
			if ( 'subscription_ref' == $column ) {
				global $post, $the_order;

				if ( empty( $the_order ) || yit_get_prop( $the_order, 'id' ) !== $post->ID ) {
					$the_order = wc_get_order( $post->ID );
				}

				$subscriptions = $the_order->get_meta( 'subscriptions' );
				if ( $subscriptions ) {
					$links = array();
					foreach ( $subscriptions as $subscription_id ) {
						$links[] = sprintf( '<a href="%s">#%d</a>', get_edit_post_link( $subscription_id ), apply_filters( 'yswbw_subscription_number', $subscription_id ) );
					}

					if ( $links ) {
						echo implode( ', ', $links );
					}
				} else {
					echo '';
				}
			}
		}

		/**
		 * @param $columns
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function add_order_post_columns( $columns ) {
			if ( ! isset( $_GET['post_status'] ) || $_GET['post_status'] != 'wc-' . YWSBS_Subscription_Order()->get_renew_order_status() ) {
				return $columns;
			}
			foreach ( $columns as $key => $column ) {
				$new_columns[ $key ] = $column;
				if ( $key == 'order_date' ) {
					if ( defined( 'YITH_WCMBS_PREMIUM' ) ) {
						$new_columns['membership'] = __( 'Membership Status', 'yith-woocommerce-subscription' );
					}
					$new_columns['failed_payment'] = __( 'Failed Attempts', 'yith-woocommerce-subscription' );
				}
			}

			return $new_columns;
		}


		/**
		 * @param $column
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function add_order_posts_custom_column( $column ) {

			if ( ! isset( $_GET['post_status'] ) || $_GET['post_status'] != 'wc-' . YWSBS_Subscription_Order()->get_renew_order_status() ) {
				return;
			}

			// wc-pending-renewal
			global $post, $the_order;

			if ( empty( $the_order ) || yit_get_prop( $the_order, 'id' ) !== $post->ID ) {
				$the_order = wc_get_order( $post->ID );
			}

			$subscriptions = $the_order->get_meta( 'subscriptions' );
			if ( empty( $subscriptions ) ) {
				return;
			}

			$subscription = ywsbs_get_subscription( $subscriptions[0] );

			switch ( $column ) {
				case 'membership':
					if ( function_exists( 'YWSBS_Membership' ) ) {
						echo YWSBS_Membership()->subscription_column_default( '', $subscription->post, 'membership' );
					}
					break;
				case 'failed_payment':
					$failed_attempts        = $subscription->has_failed_attempts();
					$date_of_attempt_string = '';

					if ( isset( $failed_attempts['max_failed_attempts'] ) ) {
						if ( $failed_attempts['num_of_failed_attempts'] > 0 && $failed_attempts['num_of_failed_attempts'] < $failed_attempts['max_failed_attempts'] ) {
							$date_of_attempt = yit_get_prop( $the_order, 'next_payment_attempt' );
							if ( empty( $date_of_attempt ) || $date_of_attempt <= current_time( 'timestamp' ) ) {
								$date_of_attempt = intval( $subscription->payment_due_date ) + ( ( $failed_attempts['day_between_attempts'] * DAY_IN_SECONDS ) * ( $failed_attempts['num_of_failed_attempts'] ) );
							}
							$date_of_attempt_string = empty( $date_of_attempt ) ? '' : date_i18n( get_option( 'date_format' ), $date_of_attempt );
						}
						echo $failed_attempts['num_of_failed_attempts'] . '/' . $failed_attempts['max_failed_attempts'] . '<br>' . $date_of_attempt_string;
					}
					break;
			}
		}

		/**
		 * Sanitize the option of type 'relative_date_selector' before that are saved.
		 *
		 * @param $value
		 * @param $option
		 * @param $raw_value
		 *
		 * @return array
		 * @since 1.4
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function sanitize_value_option( $value, $option, $raw_value ) {

			if ( isset( $option['id'] ) && in_array( $option['id'], array( 'ywsbs_trash_pending_subscriptions', 'ywsbs_trash_cancelled_subscriptions' ) ) ) {
				$raw_value = maybe_unserialize( $raw_value );
				$value     = wc_parse_relative_date_option( $raw_value );
			}

			return $value;
		}

		/**
		 * @param WC_Order $order
		 */
		public function add_order_details( $order ) {

			$is_renew = $order->get_meta( 'is_a_renew' );
			wp_enqueue_script( 'jquery-ui-dialog' );
			if ( 'yes' == $is_renew ) {
				?>
				<div id="yith_subscrption_modal" title="<?php _e( 'Warning!', 'yith-woocommerce-watermark' ); ?>" style="text-align: center;">
					<p><?php _e( 'This is a renew order, we not recommended to change manually the order status, because this could be cause issues in the subscription payments, are you sure ?', 'yith-woocommerce-subscription' ); ?></p>
				</div>
				<script type="text/javascript">
					jQuery(document).ready(function($){
					   var  dialog =  $( "#yith_subscrption_modal" ).dialog({
								resizable: false,
								autoOpen: false,
								height: "auto",
								width: 400,
								modal: true,
								buttons: {
									Ok: function() {
										$( this ).dialog( "close" );

									}
								}
							});
						$('#order_status').on('select2:select',function(e){
							dialog.dialog("open");
						});
					});
				</script>
				<?php
			}
		}


	}
}

/**
 * Unique access to instance of YITH_WC_Subscription_Admin class
 *
 * @return \YITH_WC_Subscription_Admin
 */
function YITH_WC_Subscription_Admin() {
	return YITH_WC_Subscription_Admin::get_instance();
}
