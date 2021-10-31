<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/******************************/
/* Include Sidebars Generator */
/******************************/
$plugin = PORTO_PLUGINS . '/sidebar-generator/sidebar_generator.php';
include_once $plugin;

/**
 * Include Elementor Compatibility class
 */
if ( defined( 'ELEMENTOR_VERSION' ) ) {
	include_once PORTO_PLUGINS . '/compatibility/class-porto-elementor-compatibility.php';
	if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
		include_once PORTO_PLUGINS . '/compatibility/class-porto-elementor-pro-compatibility.php';
	}
}

/**
 * Include WPSEO Compatibility class
 */
if ( defined( 'WPSEO_VERSION' ) ) {
	include_once PORTO_PLUGINS . '/compatibility/class-porto-wpseo-compatibility.php';
}

/**
 * Initialize TGM plugins
 */
if ( current_user_can( 'manage_options' ) ) {
	class PortoTGMPlugins {

		/**
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		protected $plugins = array(
			array(
				'name'      => 'Elementor',
				'slug'      => 'elementor',
				'required'  => false,
				'url'       => 'elementor/elementor.php',
				'image_url' => PORTO_PLUGINS_URI . '/images/elementor.png',
			),
			array(
				'name'      => 'Visual Composer',
				'slug'      => 'visualcomposer',
				'required'  => false,
				'url'       => 'visualcomposer/plugin-wordpress.php',
				'image_url' => PORTO_PLUGINS_URI . '/images/visualcomposer.png',
			),
			array(
				'name'      => 'WooCommerce',
				'slug'      => 'woocommerce',
				'required'  => false,
				'url'       => 'woocommerce/woocommerce.php',
				'image_url' => PORTO_PLUGINS_URI . '/images/woocommerce.png',
			),
			array(
				'name'      => 'Contact Form 7',
				'slug'      => 'contact-form-7',
				'required'  => false,
				'url'       => 'contact-form-7/wp-contact-form-7.php',
				'image_url' => PORTO_PLUGINS_URI . '/images/contact_form_7.png',
			),
			array(
				'name'      => 'Dynamic Featured Image',
				'slug'      => 'dynamic-featured-image',
				'required'  => false,
				'url'       => 'dynamic-featured-image/dynamic-featured-image.php',
				'image_url' => PORTO_PLUGINS_URI . '/images/dynamic_featured_image.png',
			),
			array(
				'name'       => 'MailPoet Newsletters',
				'slug'       => 'wysija-newsletters',
				'required'   => false,
				'url'        => 'wysija-newsletters/index.php',
				'image_url'  => PORTO_PLUGINS_URI . '/images/mailpoet_newsletter.png',
				'visibility' => 'hidden',
			),
			array(
				'name'       => 'WPForms Lite',
				'slug'       => 'wpforms-lite',
				'required'   => false,
				'url'        => 'wpforms-lite/wpforms.php',
				'image_url'  => PORTO_PLUGINS_URI . '/images/sullie-vc.png',
				'visibility' => 'hidden',
			),
			array(
				'name'       => 'Social Slider Feed',
				'slug'       => 'instagram-slider-widget',
				'required'   => false,
				'url'        => 'instagram-slider-widget/instaram_slider.php',
				'image_url'  => PORTO_PLUGINS_URI . '/images/instagram_slider_widget.png',
				'visibility' => 'hidden',
			),

			array(
				'name'       => 'Regenerate Thumbnails',
				'slug'       => 'regenerate-thumbnails',
				'required'   => false,
				'url'        => 'regenerate-thumbnails/regenerate-thumbnails.php',
				'image_url'  => PORTO_PLUGINS_URI . '/images/regenerate_thumbnails.png',
				'visibility' => 'hidden',
			),
			array(
				'name'       => 'YITH Woocommerce Wishlist',
				'slug'       => 'yith-woocommerce-wishlist',
				'required'   => false,
				'url'        => 'yith-woocommerce-wishlist/init.php',
				'image_url'  => PORTO_PLUGINS_URI . '/images/yith_wishlist.png',
				'visibility' => 'hidden',
			),
			array(
				'name'       => 'YITH Woocommerce Ajax Product Filter',
				'slug'       => 'yith-woocommerce-ajax-navigation',
				'required'   => false,
				'url'        => 'yith-woocommerce-ajax-navigation/init.php',
				'image_url'  => PORTO_PLUGINS_URI . '/images/yith_ajax_filter.png',
				'visibility' => 'hidden',
			),
			array(
				'name'       => 'YITH Woocommerce Ajax Search',
				'slug'       => 'yith-woocommerce-ajax-search',
				'required'   => false,
				'url'        => 'yith-woocommerce-ajax-search/init.php',
				'image_url'  => PORTO_PLUGINS_URI . '/images/yith_ajax_search.png',
				'visibility' => 'hidden',
			),
			array(
				'name'       => 'YITH WooCommerce Compare',
				'slug'       => 'yith-woocommerce-compare',
				'required'   => false,
				'url'        => 'yith-woocommerce-compare/init.php',
				'image_url'  => PORTO_PLUGINS_URI . '/images/yithemes-icon.png',
				'visibility' => 'hidden',
			),
			array(
				'name'       => 'Dokan',
				'slug'       => 'dokan-lite',
				'required'   => false,
				'url'        => 'dokan-lite/dokan.php',
				'image_url'  => PORTO_PLUGINS_URI . '/images/dokan-logo.png',
				'visibility' => 'hidden',
			),
			array(
				'name'       => 'WCFM - WooCommerce Multivendor Marketplace',
				'slug'       => 'wc-multivendor-marketplace',
				'required'   => false,
				'url'        => 'wc-multivendor-marketplace/wc-multivendor-marketplace.php',
				'image_url'  => PORTO_PLUGINS_URI . '/images/wcfmmp.png',
				'visibility' => 'hidden',
			),
			array(
				'name'       => 'WCFM - WooCommerce Frontend Manager',
				'slug'       => 'wc-frontend-manager',
				'required'   => false,
				'url'        => 'wc-frontend-manager/wc_frontend_manager.php',
				'image_url'  => PORTO_PLUGINS_URI . '/images/wcfmmp.png',
				'visibility' => 'hidden',
			),
			array(
				'name'       => 'Customizer Search',
				'slug'       => 'customizer-search',
				'required'   => false,
				'url'        => 'customizer-search/customizer-search.php',
				'image_url'  => PORTO_PLUGINS_URI . '/images/plugins.png',
				'visibility' => 'hidden',
			),
			array(
				'name'       => 'WP Sitemap Page',
				'slug'       => 'wp-sitemap-page',
				'required'   => false,
				'url'        => 'wp-sitemap-page/wp-sitemap-page.php',
				'image_url'  => PORTO_PLUGINS_URI . '/images/wp_sitemap_page.png',
				'visibility' => 'hidden',
			),
			array(
				'name'       => 'WP Super Cache',
				'slug'       => 'wp-super-cache',
				'required'   => false,
				'url'        => 'wp-super-cache/wp-cache.php',
				'visibility' => 'speed_wizard',
				'desc'       => 'This plugin generates static html files from your dynamic WordPress blog.',
			),
			array(
				'name'       => 'Fast Velocity Minify',
				'slug'       => 'fast-velocity-minify',
				'required'   => false,
				'url'        => 'fast-velocity-minify/fvm.php',
				'visibility' => 'speed_wizard',
				'desc'       => 'This plugin reduces HTTP requests by merging CSS & Javascript files into groups of files, while attempting to use the least amount of files as possible.',
			),
		);

		public function __construct() {

			/*************************/
			/* TGM Plugin Activation */
			/*************************/
			$plugin = PORTO_PLUGINS . '/tgm-plugin-activation/class-tgm-plugin-activation.php';
			if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
				require_once $plugin;
			}

			add_action( 'tgmpa_register', array( $this, 'porto_register_required_plugins' ) );

			add_filter( 'tgmpa_notice_action_links', array( $this, 'porto_update_action_links' ), 10, 1 );

			if ( defined( 'GEODIRECTORY_VERSION' ) ) {
				$this->plugins = array_merge(
					$this->plugins,
					array(
						array(
							'name'      => 'GeoDirectory Porto Theme Compatibility Pack',
							'slug'      => 'geodirectory-porto-theme-compatibility-pack',
							'source'    => PORTO_PLUGINS_URI . '/geodirectory-porto-theme-compatibility-pack.zip',
							'required'  => true,
							'version'   => '1.0.0',
							'image_url' => PORTO_PLUGINS_URI . '/images/geodirectory_porto_pack.png',
						),
					)
				);
			}

			$this->plugins = $this->get_plugins_list();
		}

		public function porto_register_required_plugins() {

			// disable visual composer automatic update
			global $vc_manager;
			if ( $vc_manager ) {
				$vc_updater = $vc_manager->updater();
				if ( $vc_updater ) {
					remove_action( 'wp_ajax_nopriv_vc_check_license_key', array( $vc_updater, 'checkLicenseKeyFromRemote' ) );
				}
			}

			/**
			 * Array of configuration settings. Amend each line as needed.
			 * If you want the default strings to be available under your own theme domain,
			 * leave the strings uncommented.
			 * Some of the strings are added into a sprintf, so see the comments at the
			 * end of each line for what each argument will be.
			 */
			$config = array(
				'domain'       => 'porto',          // Text domain - likely want to be the same as your theme.
				'default_path' => '',                          // Default absolute path to pre-packaged plugins
				'menu'         => 'install-required-plugins',  // Menu slug
				'has_notices'  => true,                        // Show admin notices or not
				'is_automatic' => true,                       // Automatically activate plugins after installation or not
				'message'      => '',                          // Message to output right before the plugins table
				'strings'      => array(
					'page_title'                      => __( 'Install Required Plugins', 'porto' ),
					'menu_title'                      => __( 'Install Plugins', 'porto' ),
					/* translators: %s: plugin name */
					'installing'                      => __( 'Installing Plugin: %s', 'porto' ), // %1$s = plugin name
					'oops'                            => __( 'Something went wrong with the plugin API.', 'porto' ),
					/* translators: %s: plugin name */
					'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'porto' ), // %1$s = plugin name(s)
					'notice_can_install_recommended'  => '',
					/* translators: %s: plugin name */
					'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'porto' ), // %1$s = plugin name(s)
					/* translators: %s: plugin name */
					'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'porto' ), // %1$s = plugin name(s)
					'notice_can_activate_recommended' => '',
					/* translators: %s: plugin name */
					'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'porto' ), // %1$s = plugin name(s)
					/* translators: %s: plugin name */
					'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'porto' ),
					/* translators: %s: plugin name */
					'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'porto' ), // %1$s = plugin name(s)
					'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'porto' ),
					'activate_link'                   => _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'porto' ),
					'return'                          => __( 'Return to Required Plugins Installer', 'porto' ),
					'plugin_activated'                => __( 'Plugin activated successfully.', 'porto' ),
					/* translators: %s: dashboard link */
					'complete'                        => __( 'All plugins installed and activated successfully. %s', 'porto' ), // %1$s = dashboard link
					'nag_type'                        => 'updated', // Determines admin notice type - can only be 'updated' or 'error'
				),
			);

			tgmpa( $this->plugins, $config );
		}

		public function get_plugins_list() {
			// get transient
			$plugins = get_site_transient( 'porto_plugins' );
			if ( false === $plugins && function_exists( 'Porto' ) && Porto()->is_registered() ) {
				$plugins = $this->update_plugins_list();
			}
			if ( ! $plugins ) {
				return $this->plugins;
			}
			return array_merge( $plugins, $this->plugins );
		}

		private function update_plugins_list() {

			require_once PORTO_PLUGINS . '/importer/importer-api.php';
			$importer_api = new Porto_Importer_API();
			$args         = $importer_api->generate_args( false );
			$url          = $importer_api->get_url( 'plugins_version' );
			if ( isset( $args['code'] ) ) {
				$url = add_query_arg( 'code', $args['code'], $url );
			}
			$plugins = $importer_api->get_response( $url );
			if ( ! $plugins || is_wp_error( $plugins ) ) {
				if ( is_wp_error( $plugins ) ) {
					set_transient( 'porto_purchase_code_error_msg', $plugins->get_error_message(), HOUR_IN_SECONDS * 24 * 7 );
				}
				set_site_transient( 'porto_plugins', array(), HOUR_IN_SECONDS * 24 * 7 );
				return false;
			}
			delete_transient( 'porto_purchase_code_error_msg' );
			setcookie( 'porto_dismiss_code_error_msg', '', time() - 3600 );

			foreach ( $plugins as $key => $plugin ) {
				$args['plugin']               = $plugin['slug'];
				$plugins[ $key ]['source']    = add_query_arg( $args, $importer_api->get_url( 'plugins' ) );
				$plugins[ $key ]['image_url'] = PORTO_PLUGINS_URI . '/images/' . $args['plugin'] . '.png';
			}

			// set transient
			set_site_transient( 'porto_plugins', $plugins, 7 * 24 * HOUR_IN_SECONDS );
			return $plugins;
		}

		public function porto_update_action_links( $action_links ) {
			$url = add_query_arg(
				array(
					'page' => 'porto-setup-wizard',
					'step' => 'default_plugins',
				),
				self_admin_url( 'admin.php' )
			);
			foreach ( $action_links as $key => $link ) {
				if ( $link ) {
					$link                 = preg_replace( '/<a([^>]*)href="([^"]*)"/i', '<a$1href="' . esc_url( $url ) . '"', $link );
					$action_links[ $key ] = $link;
				}
			}
			return $action_links;
		}
	}

	$portoTGMPlugins = new PortoTGMPlugins();

	// disable master slider auto update
	add_filter( 'masterslider_disable_auto_update', '__return_true' );

	if ( ! function_exists( 'is_plugin_activate' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	if ( class_exists( 'WooCommerce' ) ) :
		add_action( 'admin_init', 'porto_include_woo_templates' );

		function porto_include_woo_templates() {
			include_once( WC()->plugin_path() . '/includes/wc-template-functions.php' );
		}
	endif;
}

add_filter( 'pre_update_option_ultimate_smooth_scroll', 'porto_update_smooth_scroll_option', 10, 3 );
function porto_update_smooth_scroll_option( $value, $old_value, $option ) {
	if ( 'enable' == $value ) {
		update_option( 'ultimate_smooth_scroll_compatible', 'enable' );
	} else {
		update_option( 'ultimate_smooth_scroll_compatible', 'disable' );
	}
	return $value;
}
add_filter( 'option_ultimate_smooth_scroll', 'porto_get_smooth_scroll_option', 10, 2 );
function porto_get_smooth_scroll_option( $value ) {
	if ( 'enable' == $value ) {
		update_option( 'ultimate_smooth_scroll_compatible', 'enable' );
	} else {
		update_option( 'ultimate_smooth_scroll_compatible', 'disable' );
	}
	return $value;
}

/**
 * Force Visual Composer to initialize as "built into the theme". This will hide certain tabs under the Settings->Visual Composer page
 */
add_action( 'vc_before_init', 'porto_vc_set_as_theme' );
function porto_vc_set_as_theme() {
	if ( function_exists( 'vc_set_as_theme' ) ) {
		vc_set_as_theme();
	}
}
