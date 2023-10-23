<?php
/**
 * Plugin banners class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 3.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Banners' ) ) {
	/**
	 * Items class.
	 * The class manage all plugin endpoints items.
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_Banners {

		/**
		 * Items array
		 *
		 * @since 3.0.0
		 * @var array | null
		 */
		private $banners = null;

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  3.0.0
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_shortcode( 'yith_wcmap_banner', array( $this, 'banner_shortcode' ) );
		}

		/**
		 * Get items method
		 *
		 * @since  3.0.0
		 * @return array
		 */
		public function get_banners() {
			if ( is_null( $this->banners ) ) {
				$this->init_banners();
			}

			/**
			 * APPLY_FILTERS: yith_wcmap_get_banners
			 *
			 * Filters the banners list.
			 *
			 * @param array $banners Banners list.
			 *
			 * @return array
			 */
			return apply_filters( 'yith_wcmap_get_banners', $this->banners );
		}

		/**
		 * Get default banners
		 *
		 * @since 3.0.0
		 * @return array
		 */
		public static function get_default_banners() {

			$banners = array(
				array(
					'name'          => _x( 'Downloads', 'Default banner title', 'yith-woocommerce-customize-myaccount-page' ),
					'text'          => _x( 'Check your available downloads', 'Default banner "downloads" text', 'yith-woocommerce-customize-myaccount-page' ),
					'icon_type'     => 'default',
					'icon'          => 'download',
					'show_counter'  => 'yes',
					'counter_type'  => 'downloads',
					'link'          => 'endpoint',
					'link_endpoint' => 'downloads',
				),
				array(
					'name'          => _x( 'Orders', 'Default banner title', 'yith-woocommerce-customize-myaccount-page' ),
					'text'          => _x( 'Check the history of all your orders and download the invoices', 'Default banner "orders" text', 'yith-woocommerce-customize-myaccount-page' ),
					'icon_type'     => 'default',
					'icon'          => 'shopping-cart',
					'show_counter'  => 'yes',
					'counter_type'  => 'orders',
					'link'          => 'endpoint',
					'link_endpoint' => 'orders',
				),
				array(
					'name'          => _x( 'Account info', 'Default banner title', 'yith-woocommerce-customize-myaccount-page' ),
					'text'          => _x( 'Edit your personal info like name, e-mail address and password.', 'Default banner "account info" text', 'yith-woocommerce-customize-myaccount-page' ),
					'icon_type'     => 'default',
					'icon'          => 'user',
					'link'          => 'endpoint',
					'link_endpoint' => 'edit-account',
				),
				array(
					'name'          => _x( 'Your address', 'Default banner title', 'yith-woocommerce-customize-myaccount-page' ),
					'text'          => _x( 'Edit and update your address info before to purchase!', 'Default banner "your address" text', 'yith-woocommerce-customize-myaccount-page' ),
					'icon_type'     => 'default',
					'icon'          => 'address-card-o',
					'link'          => 'endpoint',
					'link_endpoint' => 'edit-address',
				),
				array(
					'name'          => _x( 'Payment methods', 'Default banner title', 'yith-woocommerce-customize-myaccount-page' ),
					'text'          => _x( 'Manage your payment methods and your credit cards.', 'Default banner "your address" text', 'yith-woocommerce-customize-myaccount-page' ),
					'icon_type'     => 'default',
					'icon'          => 'credit-card-alt',
					'link'          => 'endpoint',
					'link_endpoint' => 'payment-methods',
				),
			);

			// Merge with default.
			foreach ( $banners as $key => &$banner ) {
				$banner = array_merge( self::get_default_banner_options(), $banner );
			}

			return $banners;
		}

		/**
		 * Get default banner options
		 *
		 * @since 3.0.0
		 * @return array
		 */
		public static function get_default_banner_options() {
			return array(
				'name'              => '',
				'icon_type'         => 'empty',
				'icon'              => '',
				'custom_icon'       => '',
				'custom_icon_width' => 120,
				'width'             => '250',
				'text'              => '',
				'colors'            => array(
					'text'             => '#1f1f1f',
					'background'       => '#ffffff',
					'border'           => '#707070',
					'text_hover'       => '#1f1f1f',
					'background_hover' => '#ffffff',
					'border_hover'     => '#707070',
				),
				'show_counter'      => 'no',
				'counter_type'      => '',
				'counter_colors'    => array(
					'background' => '#29ac8f',
					'text'       => '#ffffff',
				),
				'link'              => 'empty',
				'link_endpoint'     => '',
				'link_url'          => '',
				'visibility'        => 'all',
				'usr_roles'         => array(),
			);
		}

		/**
		 * Init banners
		 *
		 * @since  3.0.0
		 * @param boolean $force Force the init.
		 * @depreacted
		 */
		public function init( $force = false ) {
			$this->init_banners( $force );
		}

		/**
		 * Init banners
		 *
		 * @since  3.0.0
		 * @param boolean $force Force the init.
		 */
		public function init_banners( $force = false ) {

			if ( ! empty( $this->banners ) && ! $force ) {
				return;
			}

			$customer = get_current_user_id();

			// get saved banners.
			$saved_banners = get_option( 'yith_wcmap_banners', self::get_default_banners() );
			if ( ! empty( $saved_banners ) && is_array( $saved_banners ) ) {

				foreach ( $saved_banners as $banner ) {
					// Compute link option.
					switch ( $banner['link'] ) {
						case 'endpoint':
							$banner['link'] = wc_get_endpoint_url( $banner['link_endpoint'], '', wc_get_page_permalink( 'myaccount' ) );
							break;
						case 'url':
							$banner['link'] = esc_url( $banner['link_url'] );
							break;
						default:
							$banner['link'] = '';
							break;
					}

					// Compute counter.
					$banner['counter'] = false;
					if ( 'yes' === $banner['show_counter'] && $customer ) {
						switch ( $banner['counter_type'] ) {
							case 'orders':
								/**
								 * APPLY_FILTERS: yith_wcmap_counter_banner_orders
								 *
								 * Filters the order count in the banner.
								 *
								 * @param int $order_count Order count.
								 *
								 * @return int
								 */
								$banner['counter'] = apply_filters( 'yith_wcmap_counter_banner_orders', wc_get_customer_order_count( $customer ) );
								break;
							case 'downloads':
								$downloads         = wc_get_customer_download_permissions( $customer );
								$banner['counter'] = count( $downloads );
								break;
							default:
								$counter_type = str_replace( '-', '_', $banner['counter_type'] );

								/**
								 * APPLY_FILTERS: yith_wcmap_banner_{$counter_type}_counter_value
								 *
								 * Filters the total count of the banner counter depending on the counter type.
								 * <code>$counter_type</code> will be replaced with the counter type of the banner.
								 *
								 * @param int $count    Total count.
								 * @param int $customer User ID.
								 *
								 * @return int
								 */
								$banner['counter'] = apply_filters( "yith_wcmap_banner_{$counter_type}_counter_value", 0, $customer );
								break;
						}
					}

					// Unset useless.
					unset( $banner['link_url'], $banner['link_endpoint'], $banner['show_counter'], $banner['counter_type'] );

					$key                   = yith_wcmap_sanitize_item_key( $banner['name'] );
					$this->banners[ $key ] = $banner;
				}
			}
		}

		/**
		 * Init admin banners hooks
		 *
		 * @since  3.0.0
		 */
		public function admin_init() {

			if ( ! class_exists( 'YITH_WCMAP_Admin' ) || ! isset( $_REQUEST['page'] ) || sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) !== YITH_WCMAP_Admin::PANEL_PAGE ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			$banners = $this->get_banners();
			if ( empty( $banners ) ) {
				return;
			}

			// Banner button for item content editor.
			add_action( 'media_buttons', array( $this, 'editor_button' ) );
			add_action( 'admin_footer', array( $this, 'banners_modal' ) );
		}

		/**
		 * Banner shortcode
		 *
		 * @since 3.0.0
		 * @param array $atts Array of attributes.
		 * @return string
		 */
		public function banner_shortcode( $atts ) {
			$atts = shortcode_atts(
				array(
					'ids' => '',
				),
				$atts
			);

			if ( empty( $atts['ids'] ) ) {
				return '';
			}

			// Validate passed banners id and get the banners.
			/**
			 * APPLY_FILTERS: yith_wcmap_banners_key
			 *
			 * Filters the key of the banners to display.
			 *
			 * @param array $banners_key Key of the banners to display.
			 *
			 * @param array
			 */
			$banners_key      = apply_filters( 'yith_wcmap_banners_key', array_flip( array_unique( explode( ',', $atts['ids'] ) ) ) );
			$banners          = array_intersect_key( $this->get_banners(), $banners_key );
			$arranged_banners = array();

			if ( empty( $banners ) ) {
				return '';
			}

			foreach ( $banners_key as $key => $value ) {
				if ( isset( $banners[ $key ] ) && $this->can_banner_be_shown( $banners[ $key ] ) ) {
					$arranged_banners[ $key ] = $banners[ $key ];
				}
			}

			$banners_style = $this->create_banners_style( $arranged_banners );
			$html          = '<style type="text/css">' . $banners_style . '</style>';

			ob_start();
			wc_get_template( 'ywcmap-myaccount-banners.php', array( 'banners' => $arranged_banners ), '', YITH_WCMAP_DIR . 'templates/' );
			$html .= ob_get_clean();

			return $html;
		}

		/**
		 * Check if passed banner could be shown or not
		 *
		 * @since 3.1.0
		 * @param array $banner The banner to check.
		 * @return boolean
		 */
		protected function can_banner_be_shown( $banner ) {
			if ( isset( $banner['visibility'] ) && 'all' !== $banner['visibility'] && ! empty( $banner['usr_roles'] ) ) {
				// Get current user and set user role.
				$current_user = wp_get_current_user();
				$user_role    = (array) $current_user->roles;

				$res = array_intersect( $user_role, $banner['usr_roles'] );
				return ! empty( $res );
			}
			return true;
		}

		/**
		 * Create rules for banners
		 *
		 * @since 3.0.0
		 * @param array $banners Banners.
		 * @return string
		 */
		public function create_banners_style( $banners ) {
			$style       = '';
			$style_array = array();

			foreach ( $banners as $key => $banner ) {
				$key           = '.yith-wcmap-banner.banner-' . urldecode( $key );
				$style_array[] = array(
					$key                             => array(
						'flex'         => "0 1 {$banner['width']}px",
						'border-color' => $banner['colors']['border'],
						'background'   => $banner['colors']['background'],
						'color'        => $banner['colors']['text'],
					),
					$key . ':hover'                  => array(
						'border-color' => $banner['colors']['border_hover'],
						'background'   => $banner['colors']['background_hover'],
						'color'        => $banner['colors']['text_hover'],
					),
					$key . ' .banner-counter'        => array(
						'background' => $banner['counter_colors']['background'],
						'color'      => $banner['counter_colors']['text'],
					),
					$key . ' .banner-icon-counter i' => array(
						'font-size' => intval( $banner['custom_icon_width'] ) . 'px',
					),
				);
			}

			foreach ( $style_array as $style_rules ) {
				foreach ( $style_rules as $selector => $rules ) {
					$style .= $selector . '{';
					foreach ( $rules as $rule => $value ) {
						$style .= $rule . ':' . $value . ';';
					}
					$style .= '}';
				}
			}

			return $style;
		}

		/**
		 * Add banner editor button
		 *
		 * @since 3.0.0
		 * @param string $editor_id Editor ID.
		 * @return void
		 */
		public function editor_button( $editor_id = 'content' ) {
			printf(
				'<button type="button" class="insert-banner-button button" data-editor="%s">%s</button>',
				esc_attr( $editor_id ),
				esc_html_x( 'Add banner', 'Admin editor button label', 'yith-woocommerce-customize-myaccount-page' )
			);
		}

		/**
		 * Banners modal
		 *
		 * @since 3.0.0
		 * @return void
		 */
		public function banners_modal() {
			$banners = array();
			foreach ( $this->get_banners() as $key => $banner ) {
				$banners[ $key ] = $banner['name'];
			}

			include YITH_WCMAP_DIR . '/includes/admin/views/banners-modal.php';
		}
	}
}
