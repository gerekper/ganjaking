<?php
/**
 * WooCommerce Branding
 *
 * Rebrand WooCommerce with your own name and logo.
 *
 * @class 		woocommerce_branding
 * @package		WooCommerce
 * @category	Extensions
 * @author		WooThemes
 *
 *
 * Table Of Contents
 *
 * __construct()
 * add_tab()
 * settings_tab_action()
 * add_settings_fields()
 * init_form_fields()
 * save_settings()
 * get_tab_in_view()
 * get_settings()
 * run_rebranding()
 * rebrand_admin_menu()
 * rebrand_admin_settings()
 * replace_brand_name()
 * override_css()
 */
class WC_Branding {

	var $base_file;
	var $settings_tabs;
	var $current_tab;
	var $fields = array();

    /**
     * Constructor
     **/
    function __construct( $file ) {
		global $woocommerce;

		$this->base_file = $file;
		$this->current_tab = ( isset($_GET['tab'] ) ) ? $_GET['tab'] : 'general';

		$this->settings_tabs = array( 'branding' => __( 'Branding', 'wc_branding' ) );

		// Setup the stored settings to be used in the plugin.
		$this->get_settings();

		// Rebrand the admin menu.
		add_action( 'admin_menu', array( $this, 'rebrand_admin_menu' ), 10 );
		add_action( 'admin_init', array( $this, 'rebrand_admin_settings' ), 10 );

		// Load in a CSS file to remove CSS overrides.
		add_action( 'admin_enqueue_scripts', array( $this, 'override_css' ), 10 );

		// Init late functions
		add_action( 'init', array($this, 'init') );

		// Get text
		if ( defined( 'WPLANG' ) && WPLANG ) {
			add_action( 'init', array( $this, 'replace_woocommerce_text' ), 10 );
		} else {
			add_filter( 'gettext', array( $this, 'replace_woocommerce_text_gettext' ), 10, 3 );
		}

		add_filter( 'all_plugins',  array( $this, 'all_plugins' ) );

		// Icons
		add_action( 'admin_head', array( $this, 'icons' ) );

		// New shortcode names, without woocommerce_ prefix
		add_shortcode( 'cart', 'get_woocommerce_cart' );
		add_shortcode( 'checkout', 'get_woocommerce_checkout' );
		add_shortcode( 'order_tracking', 'get_woocommerce_order_tracking' );
		add_shortcode( 'my_account', 'get_woocommerce_my_account' );
		add_shortcode( 'edit_address', 'get_woocommerce_edit_address' );
		add_shortcode( 'change_password', 'get_woocommerce_change_password' );
		add_shortcode( 'view_order', 'get_woocommerce_view_order' );
		add_shortcode( 'pay', 'get_woocommerce_pay' );
		add_shortcode( 'thankyou', 'get_woocommerce_thankyou' );
		add_shortcode( 'messages', 'messages_shortcode' );

		// Screen IDs
		if ( true === apply_filters( 'woocommerce_branding_rename_screen_ids', false ) ) {
			add_filter( 'woocommerce_reports_screen_id', array( $this, 'reports_screen_id' ) );
			add_filter( 'woocommerce_subscriptions_screen_id', array( $this, 'subscriptions_screen_id' ) );
			add_filter( 'woocommerce_screen_ids', array( $this, 'screen_ids' ), 50 );
		}
	}

	function reports_screen_id() {
		return sanitize_title_with_dashes( $this->settings['woocommerce_branding_name'] ) . '_page_woocommerce_reports';
	}

	function subscriptions_screen_id() {
		return sanitize_title_with_dashes( $this->settings['woocommerce_branding_name'] ) . '_page_subscriptions';
	}

	function screen_ids( $screen_ids ) {
		$screen_ids[] = sanitize_title_with_dashes( $this->settings['woocommerce_branding_name'] ) . '_page_woocommerce_settings';

		foreach ( $screen_ids as $screen_id )
			$screen_ids[] = str_replace( 'woocommerce_page_', sanitize_title_with_dashes( $this->settings['woocommerce_branding_name'] ) . '_page_', $screen_id );

		return $screen_ids;
	}

	function init() {
		global $woocommerce;

		if ( current_user_can( 'manage_options' ) ) {

			// Add the settings fields to each tab.
			add_action( 'woocommerce_branding_settings', array( $this, 'add_settings_fields' ), 10 );

			// Register the settings tabs with WooCommerce.
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'register_settings_tabs_array' ), 50 );

			// Run these actions when generating the settings tabs.
			foreach ( $this->settings_tabs as $name => $label ) {
				add_action( 'woocommerce_settings_' . $name, array( $this, 'settings_tab_action' ), 10 );
				add_action( 'woocommerce_update_options_' . $name, array( $this, 'save_settings' ), 10 );
			}

		}

		// Remove generator
		remove_action( 'wp_head', array($woocommerce, 'generator') );

		// CSS
		$print_css_on = array(
			'toplevel_page_' . sanitize_title_with_dashes(strtolower($this->settings['woocommerce_branding_name'])),
			sanitize_title_with_dashes(strtolower($this->settings['woocommerce_branding_name'])) . '_page_woocommerce_reports',
		);

    	foreach ( $print_css_on as $page )
    		add_action( 'admin_print_styles-'. $page, 'woocommerce_admin_css' );

	}

	/**
	 * Replaces a string in the internationalisation table with a custom value.
	 */
	function replace_woocommerce_text() {
		global $l10n;

		if ( ! is_array( $l10n ) ) {
			return;
		}

		foreach ( $l10n as $plugin_key => $plugin ) {
			foreach ( $plugin->entries as $entry_key => $entries ) {
				foreach ( $entries->translations as $key => $value ) {
					if ( stristr( $value, 'woocommerce' )  ) {
						$l10n[ $plugin_key ]->entries[ $entry_key ]->translations[ $key ] = str_ireplace( 'woocommerce', $this->settings['woocommerce_branding_name'], $value );
					}
				}
			}
		}
	}

	/**
	 * Replace a string with gettext
	 */
	function replace_woocommerce_text_gettext( $translated, $original, $domain ) {
		if ( stristr( $domain, 'woocommerce' )  ) {
			return str_ireplace( 'woocommerce', $this->settings['woocommerce_branding_name'], $translated );
		}
		return $translated;
	}

	function all_plugins( $plugins ) {
		foreach ( $plugins as $key => $value ) {
			$plugins[ $key ]['Name'] = str_replace( array( 'WooCommerce', 'woocommerce', 'Woocommerce' ), $this->settings['woocommerce_branding_name'], $plugins[ $key ]['Name'] );
			$plugins[ $key ]['Description'] = str_replace( array( 'WooCommerce', 'woocommerce', 'Woocommerce' ), $this->settings['woocommerce_branding_name'], $plugins[ $key ]['Description'] );
		}
		return $plugins;
	}

	function icons() {
		?><style type="text/css">

			<?php if ( $this->settings['woocommerce_branding_icon'] !== '' ) : ?>
				span.mce_woocommerce_shortcodes_button {
					background-image: url("<?php echo esc_attr($this->settings['woocommerce_branding_icon']); ?>") !important;
				}
				span.mce_woocommerce_shortcodes_button:before {
					display: none !important;
				}
				#adminmenu #toplevel_page_woocommerce .menu-icon-generic div.wp-menu-image:before {
					display: none !important;
				}
			<?php endif; ?>

		</style><?php
	}

	/**
	 * add_tab()
	 *
	 * Add a new tab to the WooCommerce admin.
	 *
	 * @since 1.0.0
	 */
	 function add_tab () {
	 	foreach ( $this->settings_tabs as $name => $label ) {
	 		$class = 'nav-tab';
	 		if( $this->current_tab == $name ) { $class .= ' nav-tab-active'; }
	 		if ( version_compare( WC_VERSION, '2.2.0', '<' ) ) {
				echo '<a href="' . admin_url( 'admin.php?page=woocommerce&tab=' . $name ) . '" class="' . $class . '">' . $label . '</a>';
			} else {
				echo '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . $name ) . '" class="' . $class . '">' . $label . '</a>';
			}
		}
	 } // End add_tab()

	 /**
	  * settings_tab_action()
	  *
	  * Do this when viewing our custom settings tab(s).
	  * One function for all tabs.
	  *
	  * @since 1.0.0
	  */
	  function settings_tab_action () {
	  	// Hook onto this from another function to keep things clean.
	  	do_action( 'woocommerce_branding_settings' );

	  	// Display settings for this tab (make sure to add the settings to the tab).
	  	woocommerce_admin_fields( $this->fields['branding'] );
	  } // End settings_tab_action()

	  /**
	   * add_settings_fields()
	   *
	   * Add settings fields for each tab.
	   *
	   * @since 1.0.0
	   */
	  function add_settings_fields () {
	  	global $woocommerce_settings;

	  	// Load the prepared form fields.
	  	$this->init_form_fields();

	  	if ( is_array( $this->fields ) ) {
	  		foreach ( $this->fields as $k => $v ) {
	  			$woocommerce_settings[$k] = $v;
	  		}
	  	}
	  } // End add_settings_fields()

	 /**
	   * register_settings_tabs_array()
	   *
	   * Register the settings tabs with WooCommerce.
	   *
	   * @since 1.0.15
	   */
	  public function register_settings_tabs_array ( $tabs ) {
	  	$tabs = array_merge( $tabs, $this->settings_tabs );
	  	return $tabs;
	  }

	  /**
	   * init_form_fields()
	   *
	   * Prepare form fields to be used in the various tabs.
	   *
	   * @since 1.0.0
	   */
	  function init_form_fields () {
	  	$this->fields['branding'] = apply_filters('woocommerce_branding_register_settings', array(

			array(	'name' => __( 'Branding', 'wc_branding' ), 'type' => 'title','desc' => '', 'id' => 'branding_title' ),

			array(
				'name' => __( 'Name', 'wc_branding' ),
				'desc' 		=> __( 'This sets the name to be used in all branding of the administration sections.', 'wc_branding' ),
				'tip' 		=> '',
				'id' 		=> 'woocommerce_branding_name',
				'css' 		=> '',
				'std' 		=> get_bloginfo( 'name' ),
				'type' 		=> 'text',
			),

			array(
				'name' => __( 'URL to Icon', 'wc_branding' ),
				'desc' 		=> __( 'This sets the icon to be used in all branding of the administration sections.', 'wc_branding' ),
				'tip' 		=> '',
				'id' 		=> 'woocommerce_branding_icon',
				'css' 		=> '',
				'std' 		=> '',
				'type' 		=> 'text',
			),

			array( 'type' => 'sectionend', 'id' => 'branding' )

		)
		); // End branding settings
	  } // End init_form_fields()

	  /**
	   * save_settings()
	   *
	   * Save settings in a single field in the database for each tab's fields (one field per tab).
	   *
	   * @since 1.0.0
	   */
	  function save_settings () {
	  	global $woocommerce_settings;

	  	// Make sure our settings fields are recognised.
	  	$this->add_settings_fields();

		$current_tab = $this->get_tab_in_view( current_filter(), 'woocommerce_update_options_' );
		woocommerce_update_options( $woocommerce_settings[$current_tab] );
	  } // End save_settings()

	  /**
	   * get_tab_in_view()
	   *
	   * Get the tab current in view/processing.
	   *
	   * @param string $current_filter
	   * @param string $filter_base
	   * @return string
	   * @since 1.0.0
	   */
	  function get_tab_in_view ( $current_filter, $filter_base ) {
	  	return str_replace( $filter_base, '', $current_filter );
	  } // End get_tab_in_view()

	  /**
	   * get_settings()
	   *
	   * Get settings, in a key => value pair format.
	   *
	   * @return array $settings
	   * @since 1.0.0
	   */
	  function get_settings () {
	  	$settings = array();

	  	// Make sure our settings fields are recognised.
	  	$this->add_settings_fields();

	  	$fields = $this->fields;

	  	foreach ( $fields as $k => $v ) {
	  		foreach ( $v as $i => $j ) {
	  			if ( $j['type'] == 'heading' || $j['type'] == 'sectionend' || $j['type'] == 'title' ) {
		  			unset( $fields[$i] );
		  		} else {
		  			$stored_data = get_option( $j['id'] );

		  			if ( $stored_data == '' && isset( $j['std'] ) ) {
		  				$settings[$j['id']] = $j['std'];
		  			} else {
		  				$settings[$j['id']] = $stored_data;
		  			}
		  		}
	  		}
	  	}

	  	// Store a local variable.
	  	$this->settings = $settings;

	  	return $settings;
	  } // End get_settings()

	  /**
	   * rebrand_admin_menu()
	   *
	   * Rebrand the admin menu label.
	   *
	   * @since 1.0.0
	   */
	  function rebrand_admin_menu () {
	  	global $menu;

	  	if ( is_array( $menu ) ) {
	  		foreach ( $menu as $k => $v ) {
	  			if ( $v[0] == 'WooCommerce' || $v[0] == $this->settings['woocommerce_branding_name'] ) {
	  				if ( $this->settings['woocommerce_branding_name'] != '' ) { $menu[$k][0] = $this->settings['woocommerce_branding_name']; }
	  				if ( $this->settings['woocommerce_branding_icon'] != '' ) { $menu[$k][6] = $this->settings['woocommerce_branding_icon']; }
	  				break;
	  			}
	  		}
	  	}
	  } // End rebrand_admin_menu()

	  /**
	   * rebrand_admin_settings()
	   *
	   * Rebrand the admin settings text.
	   *
	   * @since 1.0.0
	   */
	  function rebrand_admin_settings () {

	  	$tabs = array( 'general', 'page', 'catalog', 'inventory', 'shipping', 'tax', 'email', 'integration' );

	  	foreach ( $tabs as $k => $v ) {
	  		add_filter( 'woocommerce_' . $v . '_settings', array( $this, 'replace_brand_name' ), 10 );
	  	}

	  } // End rebrand_admin_settings()

	  function replace_brand_name ( $fields ) {
	  	if ( $this->settings['woocommerce_branding_name'] != '' && strtolower( $this->settings['woocommerce_branding_name'] ) != 'woocommerce' ) {
		  	foreach ( $fields as $k => $v ) {
		  		if ( isset( $v['desc'] ) ) {
		  			$fields[$k]['desc'] = str_replace( 'WooCommerce', $this->settings['woocommerce_branding_name'], $fields[$k]['desc'] );
		  		}
		  		if ( isset( $v['name'] ) ) {
		  			$fields[$k]['name'] = str_replace( 'WooCommerce', $this->settings['woocommerce_branding_name'], $fields[$k]['name'] );
		  		}
		  	}
	  	}
	  	return $fields;
	  } // End replace_brand_name()

	  /**
	   * override_css()
	   *
	   * Remove CSS overrides in the WordPress admin.
	   *
	   * @since 1.0.0
	   */
	  function override_css () {

	  	wp_register_style( 'woocommerce-branding', trailingslashit( plugins_url( basename( dirname( $this->base_file ) ) ) ) . 'assets/css/style.css', '', '1.0.0', 'screen' );

	  	if ( $this->settings['woocommerce_branding_icon'] != '' ) { wp_enqueue_style( 'woocommerce-branding' ); }
	  } // End override_css()

} // End Class
