<?php
/**
 * Frontend class premium
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMAP_Frontend_Premium', false ) ) {
	/**
	 * Frontend class premium.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMAP_Frontend_Premium extends YITH_WCMAP_Frontend_Extended {

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {
			parent::__construct();

			add_action( 'template_redirect', array( $this, 'is_wc_memberships_teams' ), 200 );

			add_filter( 'yith_wcmap_menu_items_initialized', array( $this, 'premium_items_init' ), 10, 1 );
			add_filter( 'yith_wcmap_is_menu_item_visible', array( $this, 'check_menu_item_visibility' ), 10, 3 );
			add_action( 'yith_wcmap_print_endpoints_group', array( $this, 'print_items_group' ), 10, 2 );
			// Add custom CSS variables.
			add_filter( 'yith_wcmap_custom_css_variables', array( $this, 'premium_css_variables' ), 10, 1 );
			// Redirect to the default endpoint.
			add_action( 'template_redirect', array( $this, 'redirect_to_default' ), 150 );
			// Prevent redirect to dashboard in Customize section using Smart Email plugin.
			add_filter( 'yith_wcmap_no_redirect_to_default', array( $this, 'fix_issue_with_smartemail_plugin' ) );
		}

		/**
		 * Init security class
		 *
		 * @since 3.12.0
		 * @return void
		 */
		protected function init_security_class() {
			$this->security = new YITH_WCMAP_Security_Premium();
		}

		/**
		 * Enqueue scripts and styles
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function enqueue_scripts() {
			if ( ! $this->is_myaccount ) {
				return;
			}

			parent::enqueue_scripts();
			wp_enqueue_style( 'font-awesome', YITH_WCMAP_ASSETS_URL . '/css/font-awesome.min.css', array(), YITH_WCMAP_VERSION ); // Font awesome.
		}

		/**
		 * Init items premium
		 *
		 * @since  3.12.0
		 * @param array $items THe menu items.
		 */
		public function premium_items_init( $items ) {
			$this->menu_items = YITH_WCMAP()->items->get_items();
			// First register string for translations then remove disable.
			foreach ( $items as $item => &$options ) {
				// Check if child is active.
				if ( isset( $options['children'] ) ) {
					foreach ( $options['children'] as $child_item => $child_options ) {

						if ( ! $this->is_item_visible( $child_item, $child_options ) ) {
							unset( $options['children'][ $child_item ] );
							continue;
						}

						// Get translated label.
						$options['children'][ $child_item ]['label'] = $this->get_string_translated( $child_item, $child_options['label'] );
						if ( ! empty( $child_options['url'] ) ) {
							$options['children'][ $child_item ]['url'] = $this->get_string_translated( $child_item . '_url', $child_options['url'] );
						}
						if ( ! empty( $child_options['content'] ) ) {
							$options['children'][ $child_item ]['content'] = $this->get_string_translated( $child_item . '_content', $child_options['content'] );
						}
					}
				}

				if ( ! empty( $options['url'] ) ) {
					$options['url'] = $this->get_string_translated( $item . '_url', $options['url'] );
				}
			}

			return $items;
		}

		/**
		 * Print items group on front menu
		 *
		 * @since  3.12.0
		 * @param string $group   The items group to print.
		 * @param array  $options The items group options.
		 */
		public function print_items_group( $group, $options ) {

			$classes = array( 'group-' . $group );
			$current = yith_wcmap_get_current_endpoint();

			if ( ! empty( $options['class'] ) ) {
				$classes[] = $options['class'];
			}

			// Options for style tab.
			if ( 'horizontal' === get_option( 'yith_wcmap_menu_position', 'vertical-left' ) ) {
				// Force option open to true.
				$options['open'] = false;
			} else {
				// Check in child and add class active.
				foreach ( $options['children'] as $child_key => $child ) {
					if ( isset( $child['slug'] ) && $child_key === $current && WC()->query->get_current_endpoint() ) {
						$options['open'] = true;
						break;
					}
				}
			}

			$class_icon = $options['open'] ? 'fa-chevron-up' : 'fa-chevron-down';

			/**
			 * APPLY_FILTERS: yith_wcmap_endpoints_group_class
			 *
			 * Filters the CSS classes for the group in the menu.
			 *
			 * @param array  $classes CSS classes.
			 * @param string $group   Group key.
			 * @param array  $options Item options.
			 *
			 * @return array
			 */
			$classes = apply_filters( 'yith_wcmap_endpoints_group_class', $classes, $group, $options );

			// Build args array.
			/**
			 * APPLY_FILTERS: yith_wcmap_print_endpoints_group_group
			 *
			 * Filters the array of arguments needed to print the group.
			 *
			 * @param array  $args Array of arguments.
			 *
			 * @return array
			 */
			$args = apply_filters(
				'yith_wcmap_print_endpoints_group_group',
				array(
					'options'    => $options,
					'classes'    => $classes,
					'class_icon' => $class_icon,
				)
			);

			wc_get_template( 'ywcmap-myaccount-menu-group.php', $args, '', YITH_WCMAP_DIR . 'templates/' );
		}

		/**
		 * Css custom premium variables
		 *
		 * @since  3.12.0
		 * @param array $variables An array of css variables to use as custom style.
		 */
		public function premium_css_variables( $variables ) {
			// Logout button colors.
			$logout_colors = get_option(
				'yith_wcmap_logout_button_color',
				array(
					'text_normal'       => '#ffffff',
					'text_hover'        => '#ffffff',
					'background_normal' => '#c0c0c0',
					'background_hover'  => '#333333',
				)
			);

			$variables['logout-text-color']             = $logout_colors['text_normal'];
			$variables['logout-text-color-hover']       = $logout_colors['text_hover'];
			$variables['logout-background-color']       = $logout_colors['background_normal'];
			$variables['logout-background-color-hover'] = $logout_colors['background_hover'];

			// Menu items colors.
			$items_text_colors = get_option(
				'yith_wcmap_text_color',
				array(
					'normal' => '#777777',
					'hover'  => '#000000',
					'active' => '#000000',
				)
			);

			$variables['items-text-color']        = $items_text_colors['normal'];
			$variables['items-text-color-hover']  = $items_text_colors['hover'];
			$variables['items-text-color-active'] = isset( $items_text_colors['active'] ) ? $items_text_colors['active'] : $items_text_colors['hover'];

			// Menu items background.
			$items_background_colors = get_option(
				'yith_wcmap_background_color',
				array(
					'normal' => '#ffffff',
					'hover'  => '#ffffff',
					'active' => '#ffffff',
				)
			);

			$variables['items-background-color']        = $items_background_colors['normal'];
			$variables['items-background-color-hover']  = $items_background_colors['hover'];
			$variables['items-background-color-active'] = $items_background_colors['active'];
			// Menu font size.
			$variables['font-size'] = absint( get_option( 'yith_wcmap_font_size', 16 ) ) . 'px';
			// Menu background.
			$variables['menu-background'] = get_option( 'yith_wcmap_menu_background_color', '#f4f4f4' );
			// Menu border color.
			$variables['menu-border-color'] = get_option( 'yith_wcmap_menu_border_color', '#e0e0e0' );

			// Modern menu border color yith_wcmap_menu_item_shadow_color.
			$items_border_colors = get_option(
				'yith_wcmap_menu_item_border_color',
				array(
					'normal' => '#eaeaea',
					'hover'  => '#cceae9',
					'active' => '#cceae9',
				)
			);

			$variables['items-border-color']        = $items_border_colors['normal'];
			$variables['items-border-color-hover']  = $items_border_colors['hover'];
			$variables['items-border-color-active'] = $items_border_colors['active'];
			// Modern menu shadow color yith_wcmap_menu_item_shadow_color.
			$items_shadow_colors = get_option(
				'yith_wcmap_menu_item_shadow_color',
				array(
					'normal' => 'rgba(114, 114, 114, 0.16)',
					'hover'  => 'rgba(3,163,151,0.16)',
					'active' => 'rgba(3,163,151,0.16)',
				)
			);

			$variables['items-shadow-color']        = $items_shadow_colors['normal'];
			$variables['items-shadow-color-hover']  = $items_shadow_colors['hover'];
			$variables['items-shadow-color-active'] = $items_shadow_colors['active'];

			// Avatar style.
			$avatar_options = get_option( 'yith_wcmap_avatar', array() );
			if ( ! empty( $avatar_options['border_radius'] ) ) {
				$variables['avatar-border-radius'] = ( intval( $avatar_options['border_radius'] ) * 5 ) . '%';
			}

			// Items padding.
			$items_padding = get_option( 'yith_wcmap_items_padding', array() );
			if ( ! empty( $items_padding['dimensions'] ) ) {
				// Build item padding values.
				foreach ( $items_padding['dimensions'] as &$value ) {
					$value .= ! empty( $items_padding['unit'] ) ? $items_padding['unit'] : 'px';
				}
				$variables['menu-items-padding'] = implode( ' ', $items_padding['dimensions'] ) . ';';
			}

			return $variables;
		}

		/**
		 * Redirect to default endpoint
		 *
		 * @access public
		 * @since  1.0.4
		 */
		public function redirect_to_default() {

			// Exit if not my account.
			if ( ! $this->is_myaccount || ! is_array( $this->menu_items ) ) {
				return;
			}

			$current_endpoint = yith_wcmap_get_current_endpoint();
			// If a specific endpoint is required return.
			/**
			 * APPLY_FILTERS: yith_wcmap_no_redirect_to_default
			 *
			 * Filters whether to not redirect to the default endpoint.
			 *
			 * @param bool $no_redirect_to_default Whether to not redirect to the default endpoint.
			 *
			 * @return bool
			 */
			if ( 'dashboard' !== $current_endpoint || apply_filters( 'yith_wcmap_no_redirect_to_default', false ) ) {
				return;
			}

			$default_endpoint = get_option( 'yith-wcmap-default-endpoint', 'dashboard' );

			// Let's third part filter default endpoint.
			/**
			 * APPLY_FILTERS: yith_wcmap_default_endpoint
			 *
			 * Filters the default endpoint.
			 *
			 * @param string $default_endpoint Default endpoint key.
			 *
			 * @return string
			 */
			$default_endpoint = apply_filters( 'yith_wcmap_default_endpoint', $default_endpoint );
			$url              = wc_get_page_permalink( 'myaccount' );

			// Otherwise if I'm not in my account yet redirect to default.
			if ( ! get_option( 'yith_wcmap_is_my_account', true ) && ! isset( $_REQUEST['elementor-preview'] ) && $current_endpoint !== $default_endpoint ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( 'dashboard' !== $default_endpoint ) {
					$url = wc_get_endpoint_url( $default_endpoint, '', $url );
				}
				wp_safe_redirect( $url );
				exit;
			}
		}

		/**
		 * Premium template args
		 *
		 * @since  3.12.0
		 * @param array $args An array of template arguments.
		 * @return array
		 */
		public function add_menu_template_args( $args ) {

			// Build wrap id and class.
			$position = get_option( 'yith_wcmap_menu_position', 'vertical-left' );
			$layout   = get_option( 'yith_wcmap_menu_layout', 'simple' );
			$classes  = array(
				'position-' . $position,
				'layout-' . $layout,
				'position-' . ( 'vertical-left' === $position ? 'left' : 'right' ), // Backward compatibility.
			);

			$args = array_merge(
				$args,
				array(
					'wrap_classes'  => implode( ' ', $classes ),
					'wrap_id'       => 'horizontal' === $position ? 'my-account-menu-tab' : 'my-account-menu',
					'avatar_upload' => YITH_WCMAP_Avatar::can_upload_avatar(),
					'avatar_size'   => YITH_WCMAP_Avatar::get_avatar_default_size(),
				)
			);

			return $args;
		}

		/**
		 * Prevent redirect to dashboard in Customize section using Smart Email plugin
		 *
		 * @param boolean $value True for redirect, false otherwise.
		 * @return bool
		 */
		public function fix_issue_with_smartemail_plugin( $value ) {
			if ( isset( $_GET['sa_smart_emails'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$value = true;
			}

			return $value;
		}

		/**
		 * Check if is a WooCommerce Memberships Teams Endpoint and it needs a different menu
		 *
		 * @since  2.5.0
		 * @return boolean
		 */
		public function is_wc_memberships_teams() {
			if ( ! class_exists( 'WC_Memberships_For_Teams_Loader' ) ) {
				return false;
			}

			$teams_area = wc_memberships_for_teams()->get_frontend_instance()->get_teams_area_instance();
			if ( $teams_area->is_teams_area_section() ) {
				remove_action( 'woocommerce_account_navigation', array( $this, 'add_my_account_menu' ), 10 );
				add_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation', 10 );

				return true;
			}

			return false;
		}

		/**
		 * Add single endpoint menu arguments
		 *
		 * @since  3.12.0
		 * @param array  $args    The item endpoint arguments.
		 * @param string $item    The item to print.
		 * @param array  $options The item options.
		 * @return array
		 */
		public function add_single_endpoint_args( $args, $item, $options ) {
			if ( ! isset( $options['url'] ) ) {
				// Set AJAX class.
				if ( 'yes' === get_option( 'yith_wcmap_enable_ajax_navigation', 'no' ) ) {
					$args['classes'][] = 'has-ajax-navigation';
				}
			} else {
				$args['url'] = esc_url( $options['url'] );
			}

			return $args;
		}

		/**
		 * Check menu item visibility premium
		 *
		 * @since 3.12.0
		 * @param boolean $visible True if item is visible, false otherwise.
		 * @param string  $item    The item to check.
		 * @param array   $options The item options.
		 * @return boolean
		 */
		public function check_menu_item_visibility( $visible, $item, $options ) {
			if ( $visible ) {
				// Get current user and set user role.
				$current_user = wp_get_current_user();
				$user_role    = (array) $current_user->roles;

				if ( isset( $options['visibility'] ) && 'roles' === $options['visibility'] && isset( $options['usr_roles'] ) && $this->hide_by_usr_roles( $options['usr_roles'], $user_role ) ) {
					$visible = false;
				}
			}
			return $visible;
		}

		/**
		 * Hide field based on current user role
		 *
		 * @access protected
		 * @since  2.0.0
		 * @param array $roles             The roles valid.
		 * @param array $current_user_role The customer roles.
		 * @return boolean
		 */
		protected function hide_by_usr_roles( $roles, $current_user_role ) {
			// Return if $roles is empty.
			/**
			 * APPLY_FILTERS: yith_wcmap_skip_check_for_administrators
			 *
			 * Filters whether skip the role check for administrators.
			 *
			 * @param bool $skip_check_for_administrators Whether skip the role check for administrators or not.
			 *
			 * @return bool
			 */
			if ( apply_filters( 'yith_wcmap_skip_check_for_administrators', true ) && ( empty( $roles ) || current_user_can( 'administrator' ) ) ) {
				return false;
			}

			// Check if current user can.
			$intersect = array_intersect( $roles, $current_user_role );
			if ( ! empty( $intersect ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Manage endpoint account content based on plugin/endpoint options
		 *
		 * @since  3.0.0
		 * @return void
		 */
		public function manage_account_content() {

			// Get active endpoint.
			$endpoint = $this->get_current_endpoint();
			if ( empty( $endpoint ) ) {
				return;
			}

			// Check in custom content.
			if ( ! empty( $endpoint['content'] ) ) {

				switch ( $endpoint['content_position'] ) {
					case 'before':
						add_action( 'woocommerce_account_content', array( $this, 'print_endpoint_content' ), 5 );
						break;
					case 'after':
						add_action( 'woocommerce_account_content', array( $this, 'print_endpoint_content' ), 20 );
						break;
					case 'override':
						remove_action( 'woocommerce_account_content', 'woocommerce_account_content' );
						add_action( 'woocommerce_account_content', array( $this, 'print_endpoint_content' ) );
						break;
				}
			}
		}
	}
}
