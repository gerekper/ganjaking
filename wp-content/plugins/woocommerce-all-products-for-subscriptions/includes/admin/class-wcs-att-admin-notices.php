<?php
/**
 * WCS_ATT_Admin_Notices class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce All Products For Subscriptions
 * @since    2.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin notices handling.
 *
 * @class    WCS_ATT_Admin_Notices
 * @version  3.1.5
 */
class WCS_ATT_Admin_Notices {

	/**
	 * Notices presisting on the next request.
	 * @var array
	 */
	public static $meta_box_notices = array();

	/**
	 * Notices displayed on the current request.
	 * @var array
	 */
	public static $admin_notices = array();

	/**
	 * Maintenance notices displayed on every request until cleared.
	 * @var array
	 */
	public static $maintenance_notices = array();

	/**
	 * Dismissible notices displayed on the current request.
	 * @var array
	 */
	public static $dismissed_notices = array();

	/**
	 * Array of maintenance notice types - name => callback.
	 * @var array
	 */
	private static $maintenance_notice_types = array(
		'welcome' => 'welcome_notice'
	);

	/**
	 * Constructor.
	 */
	public static function init() {

		self::$maintenance_notices = get_option( 'wcsatt_maintenance_notices', array() );

		self::$dismissed_notices = get_user_meta( get_current_user_id(), 'wcsatt_dismissed_notices', true );
		self::$dismissed_notices = empty( self::$dismissed_notices ) ? array() : self::$dismissed_notices;

		// Show meta box notices.
		add_action( 'admin_notices', array( __CLASS__, 'output_notices' ) );
		// Save meta box notices.
		add_action( 'shutdown', array( __CLASS__, 'save_notices' ), 100 );

		if ( function_exists( 'WC' ) ) {
			// Show maintenance notices.
			add_action( 'admin_print_styles', array( __CLASS__, 'hook_maintenance_notices' ) );
		}
	}

	/**
	 * Add a notice/error.
	 *
	 * @param  string   $text
	 * @param  mixed    $args
	 * @param  boolean  $save_notice
	 */
	public static function add_notice( $text, $args, $save_notice = false ) {

		if ( is_array( $args ) ) {
			$type          = $args[ 'type' ];
			$dismiss_class = isset( $args[ 'dismiss_class' ] ) ? $args[ 'dismiss_class' ] : false;
		} else {
			$type          = $args;
			$dismiss_class = false;
		}

		$notice = array(
			'type'          => $type,
			'content'       => $text,
			'dismiss_class' => $dismiss_class
		);

		if ( $save_notice ) {
			self::$meta_box_notices[] = $notice;
		} else {
			self::$admin_notices[] = $notice;
		}
	}

	/**
	 * Checks if a maintenance notice is visible.
	 *
	 * @param  string  $notice_name
	 * @return boolean
	 */
	public static function is_maintenance_notice_visible( $notice_name ) {
		return in_array( $notice_name, self::$maintenance_notices );
	}

	/**
	 * Checks if a dismissible notice has been dismissed in the past.
	 *
	 * @param  string  $notice_name
	 * @return boolean
	 */
	public static function is_dismissible_notice_dismissed( $notice_name ) {
		return in_array( $notice_name, self::$dismissed_notices );
	}

	/**
	 * Save errors to an option.
	 */
	public static function save_notices() {
		update_option( 'wcsatt_meta_box_notices', self::$meta_box_notices );
		update_option( 'wcsatt_maintenance_notices', self::$maintenance_notices );
	}

	/**
	 * Show any stored error messages.
	 */
	public static function output_notices() {

		$saved_notices = get_option( 'wcsatt_meta_box_notices', array() );
		$notices       = $saved_notices + self::$admin_notices;

		if ( ! empty( $notices ) ) {

			foreach ( $notices as $notice ) {

				$notice_classes = array( 'wcsatt_notice', 'notice', 'notice-' . $notice[ 'type' ] );
				$dismiss_attr   = $notice[ 'dismiss_class' ] ? 'data-dismiss_class="' . $notice[ 'dismiss_class' ] . '"' : '';

				if ( $notice[ 'dismiss_class' ] ) {
					$notice_classes[] = $notice[ 'dismiss_class' ];
					$notice_classes[] = 'is-dismissible';
				}

				echo '<div class="' . implode( ' ', $notice_classes ) . '"' . $dismiss_attr . '>';
				echo wpautop( $notice[ 'content' ] );
				echo '</div>';
			}

			if ( function_exists( 'wc_enqueue_js' ) ) {
				wc_enqueue_js( "
					jQuery( function( $ ) {
						jQuery( '.wcsatt_notice .notice-dismiss' ).on( 'click', function() {

							var data = {
								action: 'woocommerce_dismiss_satt_notice',
								notice: jQuery( this ).parent().data( 'dismiss_class' ),
								security: '" . wp_create_nonce( 'wcsatt_dismiss_notice_nonce' ) . "'
							};

							jQuery.post( '" . WC()->ajax_url() . "', data );
						} );
					} );
				" );
			}

			// Clear.
			delete_option( 'wcsatt_meta_box_notices' );
		}
	}

	/**
	 * Show maintenance notices.
	 */
	public static function hook_maintenance_notices() {

		if ( ! current_user_can( 'manage_woocommerce' ) || ! class_exists( 'WCS_ATT_Admin' ) ) {
			return;
		}

		foreach ( self::$maintenance_notice_types as $notice_name => $callback ) {
			if ( self::is_maintenance_notice_visible( $notice_name ) ) {
				call_user_func( array( __CLASS__, $callback ) );
			}
		}
	}

	/**
	 * Add a dimissible notice/error.
	 *
	 * @param  string   $text
	 * @param  mixed    $args
	 */
	public static function add_dismissible_notice( $text, $args ) {
		if ( ! isset( $args[ 'dismiss_class' ] ) || ! self::is_dismissible_notice_dismissed( $args[ 'dismiss_class' ] ) ) {
			self::add_notice( $text, $args );
		}
	}

	/**
	 * Remove a dismissible notice.
	 *
	 * @param  string  $notice_name
	 */
	public static function remove_dismissible_notice( $notice_name ) {

		// Remove if not already removed.
		if ( ! self::is_dismissible_notice_dismissed( $notice_name ) ) {
			self::$dismissed_notices = array_merge( self::$dismissed_notices, array( $notice_name ) );
			update_user_meta( get_current_user_id(), 'wcsatt_dismissed_notices', self::$dismissed_notices );
			return true;
		}

		return false;
	}

	/**
	 * Add a maintenance notice to be displayed.
	 *
	 * @param  string  $notice_name
	 */
	public static function add_maintenance_notice( $notice_name ) {

		// Add if not already there.
		if ( ! self::is_maintenance_notice_visible( $notice_name ) ) {
			self::$maintenance_notices = array_merge( self::$maintenance_notices, array( $notice_name ) );
			return true;
		}

		return false;
	}

	/**
	 * Remove a maintenance notice.
	 *
	 * @param  string  $notice_name
	 */
	public static function remove_maintenance_notice( $notice_name ) {

		// Remove if there.
		if ( self::is_maintenance_notice_visible( $notice_name ) ) {
			self::$maintenance_notices = array_diff( self::$maintenance_notices, array( $notice_name ) );
			return true;
		}

		return false;
	}

	/**
	 * Add 'welcome' notice.
	 */
	public static function welcome_notice() {

		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';
		$show_on_screens = array(
			'dashboard',
			'plugins',
		);

		// Onboarding notices should only show on the main dashboard, and on the plugins screen.
		if ( ! in_array( $screen_id, $show_on_screens, true ) ) {
			return;
		}

		ob_start();

		?>
		<div class="sw-welcome-icon"></div>
		<h2 class="sw-welcome-title"><?php esc_attr_e( 'Ready to make your products available on subscription?', 'woocommerce-all-products-for-subscriptions' ); ?></h2>
		<p class="sw-welcome-text"></span><?php esc_attr_e( 'Thank you for installing All Products for WooCommerce Subscriptions.', 'woocommerce-all-products-for-subscriptions' ); ?><br/><?php esc_attr_e( 'Let\'s start by adding some subscription plans to an existing product.', 'woocommerce-all-products-for-subscriptions' ); ?></p>
		<div class="sw-expanding-button-container">
			<div class="sw-expanding-button sw-expanding-button--large sw-select2-autoinit">
				<span class="sw-title"><?php echo _x( 'Let\'s go!', 'onboarding button text', 'woocommerce-all-products-for-subscriptions' ); ?></span>
				<select class="sw-select2-search--products" id="satt_product_search" name="satt_product" data-swtheme="apfs" data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce-all-products-for-subscriptions' ); ?>" data-action="woocommerce_json_search_satt_onboarding" multiple="multiple" data-limit="200">
					<option></option>
				</select>
			</div><?php
		?></div><?php

		$notice = ob_get_clean();

		self::add_dismissible_notice( $notice, array( 'type' => 'native', 'dismiss_class' => 'welcome' ) );

		wp_enqueue_style( 'woocommerce_admin_styles' );
		wp_enqueue_script( 'wc-enhanced-select' );
		wc_enqueue_js( '

			// Initialize selectWoo.
			jQuery( document.body ).trigger( "wc-enhanced-select-init" );

			var focus_timer,
				link    = "' . admin_url( 'post.php?post=%p&action=edit&wcsatt_onboarding=1' ) . '",
				$button = jQuery( ".wcsatt_notice" ).find( ".sw-expanding-button-container" ),
				$select = jQuery( ".wcsatt_notice" ).find( "select" ),
				$body   = jQuery( document.body );

			$button.on( "click", function( e ) {

				e.stopPropagation();

				clearTimeout( focus_timer );

				var $this  = jQuery( this ),
					$input = $this.find( ".select2-search__field" );

				$this.addClass( "sw-expanding-button-container--open" );

				focus_timer = setTimeout( function() {
					$input.focus();
				}, 700 );

				$select.one( "change", function() {

					$this.find( ".select2-container" ).hide();
					$this.removeClass( "sw-expanding-button-container--open" );
					$this.addClass( "sw-expanding-button-container--closed" );

					setTimeout( function() {
						window.location.href = link.replace( "%p", $select.val() );
					}, 500 );
				} );

			} );

			$body.on( "click", ".select2-container", function( e ) {
				e.stopPropagation();
			} );

			$body.on( "click", function() {
				$button.removeClass( "sw-expanding-button-container--open" );
			} );

		' );
	}

	/**
	 * Add 'cart onboarding' notice.
	 */
	public static function add_cart_plans_onboarding_notice() {

		$settings_link = WC_Subscriptions_Admin::settings_tab_url();

		ob_start();

		?>
		<p><?php _e( 'Awesome &ndash; this product is now available on subscription!', 'woocommerce-all-products-for-subscriptions' ); ?></p>
		<p class="onboarding-details"><?php echo sprintf( __( 'Did you know that you can also use <strong>All Products for WooCommerce Subscriptions</strong> to offer subscription options on the cart page?</br>For details, check out the <a href="%1$s">documentation</a>. Then, configure cart subscription plans <a href="%2$s">here</a>.', 'woocommerce-all-products-for-subscriptions' ), WCS_ATT::DOCS_URL, $settings_link ); ?></p>
		<?php

		$notice = ob_get_clean();

		self::add_notice( $notice, array( 'type' => 'native' ), true );
	}

	/**
	 * Add 'cart onboarding' admin note.
	 *
	 * @since 3.1.5
	 */
	public static function add_cart_plans_onboarding_admin_note() {

		if ( ! class_exists( 'Automattic\WooCommerce\Admin\Notes\WC_Admin_Notes' ) ) {
			return;
		}

		if ( ! WCS_ATT_Core_Compatibility::is_wc_version_gte( '4.0' ) ) {
			return;
		}

		$settings_link = WC_Subscriptions_Admin::settings_tab_url();

		ob_start();

		?>
		<p><?php _e( 'Awesome &ndash; you just made your first product available on subscription!', 'woocommerce-all-products-for-subscriptions' ); ?></p>
		<p><?php _e( 'Did you know that you can also offer subscription options <strong>on the cart page</strong>? For details, check out the All Products for WooCommerce Subscriptions documentation, or go ahead and add some cart subscription plans now.', 'woocommerce-all-products-for-subscriptions' ); ?></p>
		<?php

		$notice_content = ob_get_clean();
		$note_name      = 'wcsatt_first_product_note';

		// First, see if we've already created this kind of note so we don't do it again.
		$data_store = WC_Data_Store::load( 'admin-note' );
		$note_ids   = $data_store->get_notes_with_name( $note_name );
		if ( ! empty( $note_ids ) ) {
			return;
		}

		// Otherwise, add the note.
		$note = new Automattic\WooCommerce\Admin\Notes\WC_Admin_Note();
		$note->set_title( __( 'All Products for WooCommerce Subscriptions', 'woocommerce-all-products-for-subscriptions' ) );
		$note->set_content( $notice_content );

		$note->set_type( Automattic\WooCommerce\Admin\Notes\WC_Admin_Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_icon( 'scheduled' );
		$note->set_name( $note_name );

		$note->add_action(
			'settings',
			__( 'Add Cart Plans', 'woocommerce-all-products-for-subscriptions' ),
			$settings_link,
			Automattic\WooCommerce\Admin\Notes\WC_Admin_Note::E_WC_ADMIN_NOTE_ACTIONED,
			true
		);

		$note->add_action(
			'settings',
			__( 'Learn More', 'woocommerce-all-products-for-subscriptions' ),
			WCS_ATT::DOCS_URL
		);

		$note->save();
	}

	/**
	 * Dismisses a notice.
	 *
	 * @param  string  $notice
	 */
	public static function dismiss_notice( $notice ) {
		if ( isset( self::$maintenance_notice_types[ $notice ] ) ) {
			return self::remove_maintenance_notice( $notice );
		} else {
			return self::remove_dismissible_notice( $notice );
		}
	}
}

WCS_ATT_Admin_Notices::init();
