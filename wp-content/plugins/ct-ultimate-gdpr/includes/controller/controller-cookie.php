<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CT_Ultimate_GDPR_Controller_Cookie
 *
 */
class CT_Ultimate_GDPR_Controller_Cookie extends CT_Ultimate_GDPR_Controller_Abstract {

	/**
	 *
	 */
	const ID = 'ct-ultimate-gdpr-cookie';

	/**
	 * @var mixed
	 */
	private $user_meta;

	/**
	 * @var int
	 */
	private $user_id;

	/** @var array $cookies_to_delete */
	private $cookies_to_delete = array();

	/** @var @bool true if user is from array */
	private $is_user_from_eu = true;

	/**
	 * @var
	 */
	private $attachment_id;

	/**
	 * @var string
	 */
	private $dummy_email_address = 'gdpr-dummy@createit.pl';

	/**
	 * Runs on init
	 */
	public function init() {

	    $this->maybe_login_dummy_user();
		add_action( 'pre_user_query', array( $this, 'hide_dummy_user' ) );

		if ( $this->get_option( 'cookie_check_if_user_is_from_eu' ) ) {
			$this->check_if_user_is_from_eu();
		}
		$this->grab_user_data();
		add_action( 'shutdown', array( $this, 'block_cookies' ), 0 );
		add_action( 'woocommerce_set_cart_cookies', array( $this, 'block_cookies' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts_action' ), 1 );
		add_filter( 'login_redirect', array( $this, 'fix_user_consent' ), 20, 3 );
		add_action( 'wp_ajax_ct_ultimate_gdpr_cookie_consent_give', array( $this, 'give_consent' ) );
		add_action( 'wp_ajax_nopriv_ct_ultimate_gdpr_cookie_consent_give', array( $this, 'give_consent' ) );
		add_action( 'wp_ajax_ct_ultimate_gdpr_cookie_consent_decline', array( $this, 'decline_consent' ) );
		add_action( 'wp_ajax_nopriv_ct_ultimate_gdpr_cookie_consent_decline', array( $this, 'decline_consent' ) );
		add_action( 'wp_ajax_ct_gdpr_consent_popup_close', array( $this, 'give_consent' ) );
		add_action( 'wp_ajax_nopriv_ct_gdpr_consent_popup_close', array( $this, 'give_consent' ) );
		add_action( 'wp_ajax_ct_ultimate_gdpr_cookie_get_option_text', array( $this, 'get_default_option_text' ) );
		add_action( 'wp_ajax_ct_ultimate_gdpr_cookie_get_option_text', array( $this, 'get_default_option_text' ) );
		add_action( 'wp_ajax_ct_ultimate_gdpr_cookie_prepare_scan_cookies', array( $this, 'prepare_scan_cookies' ) );
		add_action( 'wp_ajax_ct_ultimate_gdpr_cookie_scan_cookies', array( $this, 'scan_cookies' ) );
		add_filter( 'ct_ultimate_gdpr_controller_cookie_group_level', array( $this, 'get_group_level' ) );
		add_action( 'ct_ultimate_gdpr_controller_cookie_check', array( $this, 'scan_cookies' ) );
        add_filter('ct_ultimate_gdpr_controller_cookie_get_cookie_check_api_url', array($this, 'get_cookie_check_api_url'), 10, 2);
		add_action( 'admin_enqueue_scripts', array( $this, 'cookie_check_cron' ) );

		if ( is_admin() ) {
			$this->enqueue_cookie_background_image_upload_handler();
		}

		$this->should_capture_content() && ob_start( array( $this, 'capture_end' ) );

	}

	/**
	 * Prepare list of api urls to request for in JS
	 */
	public function prepare_scan_cookies() {

		// get first 100 pages
		$pages = get_posts( array(
			'post_type'      => 'page',
			'posts_per_page' => 100,
			'offset'         => 0,
			'post_status'    => 'publish',
			'order'          => 'ASC',
			'orderby'        => 'ID',
			'fields'         => 'ids',
		) );

		// get 5 lastest posts
		$posts = get_posts( array(
			'post_type'      => 'post',
			'posts_per_page' => 5,
			'offset'         => 0,
			'post_status'    => 'publish',
			'order'          => 'DESC',
			'orderby'        => 'ID',
			'fields'         => 'ids',
		) );

		// get 5 latest of each post type
		$post_types = get_post_types( array(
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'_builtin'            => false,
		) );

		$custom = array();

		foreach ( $post_types as $post_type ) {

			$custom = array_merge(
				$custom,
				get_posts(
					array(
						'post_type'      => $post_type,
						'posts_per_page' => 5,
						'offset'         => 0,
						'post_status'    => 'publish',
						'order'          => 'ASC',
						'orderby'        => 'ID',
						'fields'         => 'ids',
					)
				)
			);

		}

		$all_posts_ids = apply_filters( 'ct_ultimate_gdpr_controller_cookie_prepare_scan_cookies_posts', array_merge( $pages, $posts, $custom ) );

		// output response
		$json = array();

		foreach ( $all_posts_ids as $post_id ) {

			if ( $post_id ) {

				// create single response object
				$permalink = get_permalink( $post_id );

				// generate 'add to cart' action
				if ( get_post_type( $post_id ) == 'product' ) {
					$permalink = add_query_arg(
						array(
							'add-to-cart' => $post_id,
						),
						$permalink
					);
				}

                $object        = new stdClass();
                $license       = trim(CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value('admin_envato_key', '', CT_Ultimate_GDPR_Controller_Admin::ID));
                $object->url   = apply_filters('ct_ultimate_gdpr_controller_cookie_get_cookie_check_api_url', $permalink, $license);
                $object->label = $permalink;

				$json[] = $object;

			}

		}

		$json = apply_filters( 'ct_ultimate_gdpr_controller_cookie_prepare_scan_cookies_json', $json );

		wp_send_json( $json );

	}

	/**
	 *
	 */
	public function admin_enqueue_scripts_action() {

		wp_enqueue_script(
			'ct-ultimate-gdpr-admin',
			ct_ultimate_gdpr_url( '/assets/js/admin.js' ),
			array( 'jquery' ),
			false,
			true
		);

		wp_localize_script( 'ct-ultimate-gdpr-admin', 'ct_ultimate_gdpr_admin_translations',
			array(
				'enabled'  => esc_html__( 'Enabled', 'ct-ultimate-gdpr' ),
				'enable'   => esc_html__( 'Enable', 'ct-ultimate-gdpr' ),
				'disabled' => esc_html__( 'Disabled', 'ct-ultimate-gdpr' ),
				'disable'  => esc_html__( 'Disable', 'ct-ultimate-gdpr' ),
			)
		);

	}

	/**
	 * fix scripts tags for disable cookies
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function fix_content( $content ) {

		// img, link, script, css urls, data attributes
		static $searches = array(
			'#<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>#i',         // script tag
			'#<noscript\b[^<]*(?:(?!<\/noscript>)<[^<]*)*<\/noscript>#i',   // noscript tag
			'#<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>#i',   // noscript tag
		);

		// disable pcre jit limit to avoid returning null
		@ini_set( 'pcre.jit', false );

		$return = preg_replace_callback( $searches, array( $this, 'fix_content_callback' ), $content );

		return apply_filters( 'ct_ultimate_gdpr_controller_cookie_fix_content', $return ? $return : $content );
	}

	/**
	 * @return bool
	 */
	private function should_capture_content() {

        $ct_eu_block = apply_filters( 'ct_ultimate_gdpr_controller_cookie_block_cookies', $this->is_user_from_eu, ! empty( $this->options['cookie_block'] ), $this->is_user_from_eu );

        if ( $ct_eu_block == false)
            return false;

		if ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) {
			return false;
		}

		if ( isset( $_GET['fl_builder'] ) ) {
			// Fix: Beaver Builder Plugin (Pro Version) - white page error
			return false;
		}


		if ( $this->is_login_page() ) {
			return false;
		}

		if ( is_admin() ) {
			return false;
		}

		return true;

	}

	/**
	 * callback for fix_content() regex replace for URLs
	 *
	 * @param array $matches
	 *
	 * @return string
	 */
	public function fix_content_callback( $matches ) {

		$return = $original = $matches[0];

		$keywords = apply_filters( 'ct_ultimate_gdpr_controller_cookie_script_blacklist', array(
			CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL   => array(),
			CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY   => array(),
			CT_Ultimate_GDPR_Model_Group::LEVEL_CONVENIENCE => array(),
			CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS  => array(),
			CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING  => array(),
		) );

		$group_level = $this->get_group_level();

		foreach ( $keywords as $level => $keyword_group ) {

			if ( $level <= $group_level ) {
				continue;
			}

			foreach ( $keyword_group as $keyword ) {


				// support only fixing urls for allowed domain
				if ( stripos( $original, $keyword ) && ! stripos( $original, 'ct_ultimate_gdpr_cookie_block' ) ) {

					$return = '';
					break 2;

				}

			}

		}

		return $return;
	}

	/**
	 * stop capturing page and fix it
	 *
	 * @param string $buffer
	 *
	 * @return string
	 */
	public function capture_end( $buffer ) {
		return $this->fix_content( $buffer );
	}

	/**
	 * @param int $user_id
	 */
	public function grab_user_data( $user_id = 0 ) {

		$this->user_id   = $user_id ? $user_id : get_current_user_id();
		$this->user_meta = get_user_meta( $this->user_id, $this->get_id(), true );

	}

	/**
	 *
	 */
	private function check_if_user_is_from_eu() {

		$is_user_from_eu = true;

		if ( ! $this->is_consent_valid() ) {

			$user_ip = ct_ultimate_gdpr_get_user_ip();
			try {
                $reader       = new \IP2Location\Database(ct_ultimate_gdpr_path('vendor/GeoIP/IP2LOCATION-LITE-DB3.BIN'));
                $country_code = $reader->lookup($user_ip, \IP2Location\Database::COUNTRY_CODE);
			} catch (Exception $e) {
				$country_code = '';
			}

			if ($country_code && strlen($country_code) == 2) {

				$eu_countries = array(
					'AT',
					'BE',
					'BG',
					'HR',
					'CY',
					'CZ',
					'DK',
					'EE',
					'FI',
					'FR',
					'DE',
					'GR',
					'HU',
					'NO',
					'IE',
					'IT',
					'LV',
					'LT',
					'LU',
					'MT',
					'NL',
					'PL',
					'PT',
					'RO',
					'SK',
					'SV',
					'SI',
					'ES',
					'SE',
					'GB',
					'ZZ',
				);
				if ( ! in_array( $country_code, $eu_countries ) ) {
					$is_user_from_eu = false;
				}
			}
		}

		$is_user_from_eu       = apply_filters('ct_ugdpr_check_if_user_is_from_eu', $is_user_from_eu);
		$this->is_user_from_eu = $is_user_from_eu;
	}


	/**
	 * Save user meta consent if consent given only in cookie for an unregistered user
	 *
	 * @param $return
	 * @param $requested_redirect_to
	 * @param $user
	 *
	 * @return mixed
	 */
	public function fix_user_consent( $return, $requested_redirect_to, $user ) {

		/* set again user meta right after user logs in */
		$user_id = $user instanceof WP_User ? $user->ID : 0;
		$this->grab_user_data( $user_id );

		if ( ! empty ( $this->user_meta ) && ! empty( $this->user_meta[ $this->get_id() ] ) ) {
			return $return;
		}

		if ( ! $this->is_consent_valid() ) {
			return $return;
		}

		$cookie_consent_expire_time = $this->get_cookie( 'consent_expire_time', 0 );
		$cookie_consent_level       = (int) $this->get_cookie( 'consent_level', CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY );
		if ( $cookie_consent_expire_time ) {
			$this->give_consent( (int) $cookie_consent_expire_time, $cookie_consent_level );
		}

		return $return;

	}

	/**
	 * @param bool $force
	 */
	public function wp_enqueue_scripts_action( $force = false ) {

		/* cookie blocking script needs to be in header */
		if ( $force || $this->is_block_cookies() ) {

			wp_enqueue_script(
				'ct-ultimate-gdpr-cookie-block',
				ct_ultimate_gdpr_url( 'assets/js/cookie-block.js' ),
				array(),
				ct_ultimate_gdpr_get_plugin_version()
			);

			wp_localize_script( 'ct-ultimate-gdpr-cookie-block', 'ct_ultimate_gdpr_cookie_block',
				array(
                    'blocked'     => $this->get_cookies_to_block($this->get_group_level()),
                    'level'       => $this->get_group_level(),
				)
			);
			
		}

		$ct_popup_button_close  = $this->get_cookie( $this->id, '' );

        wp_localize_script( 'ct-ultimate-gdpr-cookie-block', 'ct_ultimate_gdpr_popup_close', array(
            'cookie_popup_button_close' => !empty( $ct_popup_button_close['popup_close_button'] )
        ) );

		if ( $force || $this->should_display_on_page( get_queried_object_id() ) ) {

			/* cookie popup features can be in footer */
			wp_enqueue_script(
				'ct-ultimate-gdpr-cookie-popup',
				ct_ultimate_gdpr_url( 'assets/js/cookie-popup.js' ),
				array( 'jquery' ),
				ct_ultimate_gdpr_get_plugin_version(),
				true
			);
			wp_enqueue_script( 'ct-ultimate-gdpr-base64'
				, ct_ultimate_gdpr_url( 'assets/js/jquery.base64.min.js' ),
				array( 'jquery' ),
				ct_ultimate_gdpr_get_plugin_version(),
				true
			);
			$read_more_url = $this->get_option( 'cookie_read_page_custom' );
            $read_more_url_new_tab = ($this->get_option( 'cookie_popup_label_read_more_new_tab' ))?"on":"off";

            if ( ! $read_more_url ) {
				$read_more_url = get_permalink( $this->get_option( 'cookie_read_page', '', 'page' ) );
			}

			if ( $read_more_url && false === stripos( $read_more_url, '//' ) ) {
				$read_more_url = set_url_scheme( "//$read_more_url" );
			}

			wp_localize_script( 'ct-ultimate-gdpr-cookie-popup', 'ct_ultimate_gdpr_cookie',
				array(
					'ajaxurl'               => admin_url( 'admin-ajax.php' ),
					'readurl'               => $read_more_url,
                    'readurl_new_tab'       => $read_more_url_new_tab,
					'consent'               => $this->is_consent_valid(),
					'reload'                => ! ! $this->get_option( 'cookie_refresh_after_save' ),
					'consent_expire_time'   => $this->get_expire_time(),
					'consent_time'          => time(),
					'consent_default_level' => $this->get_option( 'cookie_cookies_group_default', $this->get_default_group_level() ),
					'consent_accept_level'  => $this->get_option( 'cookie_cookies_group_after_accept', 5 ),
					'age_enabled'           => $this->get_option( 'age_enabled', 5 ),
                    'display_cookie_always' => $this->is_cookie_display_always(),
                    'cookie_reset_consent'  => $this->is_cookie_reset_consent(),
				)
			);

			wp_enqueue_style( 'ct-ultimate-gdpr-cookie-popup', ct_ultimate_gdpr_url( '/assets/css/cookie-popup.min.css' ) );

			// cookie custom styles
			$cookie_style = strip_tags( $this->get_option( 'cookie_style', '' ) );
			if ( $cookie_style ) {
				wp_add_inline_style( 'ct-ultimate-gdpr-cookie-popup', $cookie_style );
			}

		}

		if(!$this->is_google_fonts_disabled()) {
			wp_enqueue_style( 'ct-ultimate-gdpr-custom-fonts', ct_ultimate_gdpr_url( '/assets/css/fonts/fonts.css' ) );
		}
		
        wp_enqueue_style( 'dashicons' );

	}

	/**
	 * Fires on user settings saved
	 *
	 * @param int $custom_expire_time
	 * @param int $custom_consent_level
	 */
	public function give_consent( $custom_expire_time = 0, $custom_consent_level = 0 ) {

		$consent_id             = $custom_consent_level ? $custom_consent_level : ct_ultimate_gdpr_get_value('level_id', $this->get_request_array() ,CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY );
		$consent_level          = $custom_consent_level ? $custom_consent_level : (int) ct_ultimate_gdpr_get_value( 'level', $this->get_request_array(), CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY );
		$expire_time            = $custom_expire_time ? $custom_expire_time : $this->get_expire_time();
		$skip_cookies           = ct_ultimate_gdpr_get_value( 'skip_cookies', $this->get_request_array() );
		$ct_popup_button_close  = ct_ultimate_gdpr_get_value( 'ct_ultimate_gdpr_button_close', $this->get_request_array() );
		$time                   = time();

		// singular is used
		if(is_array($consent_id)) {
			
			// by group
			$level_1 = ['1'];  // 1 - block-all
			$level_2 = ['2'];  // 2 - essential
			$level_3 = ['2','3'];  // 3 - functionality
			$level_4 = ['2','3','4']; // 4 - analytics
			$level_5 = ['2','3','4','5']; // 5 - advertising
			// single
			$id_5 = ['5']; // 5 => 2
			$id_6 = ['6']; // 6
			$id_7 = ['7']; // 7
			$id_8 = ['8']; // 8
			// single multiple
			$id_9 = ['5','6']; // 9 => 3
			$id_10 = ['5','7']; // 9
			$id_11 = ['5','8']; // 10
			$id_12 = ['6','7']; // 11
			$id_13 = ['6','8']; // 12
			$id_14 = ['7','8']; // 13
			$id_15 = ['5','6','7']; // 15 => 4
			$id_16 = ['5','7','8']; // 14
			$id_17 = ['5','6','8']; // 15
			$id_18 = ['6','7','8']; // 16
			$id_19 = ['5','6','7','8']; // 19 => 5


			switch ($consent_id) {
				case $level_1 :
					if( empty(array_diff( $consent_id, $level_1)) ) $consent_level = 1;
					break;
				// case $level_2:
				// 	if( empty(array_diff( $consent_id, $level_2)) ) $consent_level = 2;
				// 	break;
				// case $level_3:
				// 	if( empty(array_diff( $consent_id, $level_3)) ) $consent_level = 3;
				// 	break;
				// case $level_4:
				// 	if( empty(array_diff( $consent_id, $level_4)) ) $consent_level = 4;
				// 	break;
				// case $level_5:
				// 	if( empty(array_diff( $consent_id, $level_5)) ) $consent_level = 5;
					//break;
				case $id_5:
					if( empty(array_diff( $consent_id, $id_5)) ) $consent_level = 2;
					break;
				case $id_6:
					if( empty(array_diff( $consent_id, $id_6)) ) $consent_level = 6;
					break;
				case $id_7:
					if( empty(array_diff( $consent_id, $id_7)) ) $consent_level = 7;
					break;
				case $id_8:
					if( empty(array_diff( $consent_id, $id_8)) ) $consent_level = 8;
					break;
				case $id_9:
					if( empty(array_diff( $consent_id, $id_9)) ) $consent_level = 3;
					break;
				case $id_10:
					if( empty(array_diff( $consent_id, $id_10)) ) $consent_level = 9;
					break;
				case $id_11:
					if( empty(array_diff( $consent_id, $id_11)) ) $consent_level = 10;
					break;
				case $id_12:
					if( empty(array_diff( $consent_id, $id_12)) ) $consent_level = 11;
					break;
				case $id_13:
					if( empty(array_diff( $consent_id, $id_13)) ) $consent_level = 12;
					break;
				case $id_14:
					if( empty(array_diff( $consent_id, $id_14)) ) $consent_level = 13;
					break;
				case $id_15:
					if( empty(array_diff( $consent_id, $id_15)) ) $consent_level = 4;
					break;	
				case $id_16:
					if( empty(array_diff( $consent_id, $id_16)) ) $consent_level = 14;
					break;	
				case $id_17:
					if( empty(array_diff( $consent_id, $id_17)) ) $consent_level = 15;
					break;	
				case $id_18:
					if( empty(array_diff( $consent_id, $id_18)) ) $consent_level = 16;
					break;
				case $id_19:
					if( empty(array_diff( $consent_id, $id_19)) ) $consent_level = 5;
					break;
				default: 
			}
		}

		$value = array(
			'consent_declined'    => false,
			'consent_expire_time' => $expire_time,
			'consent_level'       => $consent_level,
			'consent_time'        => $time,
			'consent_id'		  => $consent_id,
		);

		// save settings in a user meta
		if ( $this->user_id ) {
			update_user_meta( $this->user_id, $this->get_id(), $value );
		}

		$this->logger->consent( array(
			'type'       => $this->get_id(),
			'time'       => $time,
			'user_id'    => $this->user->get_current_user_id(),
			'user_ip'    => ct_ultimate_gdpr_get_permitted_user_ip(),
			'user_agent' => ct_ultimate_gdpr_get_permitted_user_agent(),
			'data'       => $value,
		) );

		if ( $skip_cookies ) {
			wp_die( 'ok' );
		}

		// save settings in a cookie
		ct_ultimate_gdpr_set_encoded_cookie( $this->get_id(), ct_ultimate_gdpr_json_encode( $value ), $expire_time, '/' );
		//for wp-rocket caching
		ct_ultimate_gdpr_set_encoded_cookie( $this->get_id() . '-level', ct_ultimate_gdpr_json_encode( $consent_level ), $expire_time, '/' );

		// delete cookies above permitted group level
		if ( $consent_level == CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL ) {
			$this->delete_cookies( array_combine( array_keys( $_COOKIE ), array_keys( $_COOKIE ) ) );
		}

		$cookies_grouped = $this->get_cookies_to_block();

		foreach ( $cookies_grouped as $group => $cookies ) {

			if ( $group > $consent_level ) {
				
				if(is_array($cookies)) {
					$this->delete_cookies( array_combine( $cookies, $cookies ) );

				}
			}
			
		}
		 
	}

	/**
	 *
	 */
	public function decline_consent() {

        $value = array(
            'consent_declined'    => false,
            'consent_expire_time' => $this->get_expire_time(),
            'consent_level'       => CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL,
        );

        setcookie( $this->get_id(), '', 1 );
        //for wp-rocket caching
        setcookie( $this->get_id() . '-level', '', 1 );

        if ( is_user_logged_in() ) {
            update_user_meta( $this->user_id, $this->get_id(), $value );
        }

        // save settings in a cookie
        ct_ultimate_gdpr_set_encoded_cookie( $this->get_id(), ct_ultimate_gdpr_json_encode( $value ), $this->get_expire_time(), '/' );
        //for wp-rocket caching
        ct_ultimate_gdpr_set_encoded_cookie( $this->get_id() . '-level', ct_ultimate_gdpr_json_encode( CT_Ultimate_GDPR_Model_Group::LEVEL_CONVENIENCE ), $this->get_expire_time(), '/' );

    }

	/**
	 * @return float|int
	 */
	private function get_expire_time() {

		if ( ! empty( $this->options['cookie_expire'] ) ) {
			return time() + (int) $this->options['cookie_expire'];
		}

		return time() + YEAR_IN_SECONDS;

	}

	/**
	 * Render cookie popup
	 */
	public function render() {

		if ( ! $this->get_option( 'cookie_show_always' ) && $this->is_consent_valid() ) {
			return;
		}

		if ( ! $this->should_display_on_page( get_queried_object_id() ) ) {
			return;
		}

		if ( $this->is_consent_declined() ) {
			return;
		}

        if ( $this->is_user_bot() ) {
            return false;
		}

		$template = $this->get_option( 'cookie_use_group_popup' ) ? 'cookie-group-popup' : 'cookie-popup';
		
		$options = array_merge( $this->get_default_options(), $this->options );

		// 1173
		// ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'cookie-template', false ), true, $options ); 
		if($this->is_cookie_single_popup_enabled()) {
			ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'cookie-template', false ), true, $options ); 
		} else {
			ct_ultimate_gdpr_locate_template('cookie-group-popup', true, $options);
		}

	}

    /**
     * Check cookie visibility always
     *
     * @return bool
     */
    public function is_cookie_display_always() {

        if($this->get_option( 'cookie_display_always' ) ) {
            return true;
        }
    }

	/**
     * Check cookie single popup enabled
     *
     * @return bool
     */
	public function is_cookie_single_popup_enabled() {
		if($this->get_option( 'cookie_single_popup' ) ) {
            return true;
        } else {
			return false;
		}
	}


    /**
     * Check cookie reset consent is enabled
     *
     * @return bool
     */
    public function is_cookie_reset_consent() {
       
        if($this->get_option( 'cookie_reset_consent' ) ) {
            return true;
        } 
    }

	
	/**
	 * Check if google fonts is enabled
	 * @return bool
	 */
	public function is_google_fonts_disabled() {
		if($this->get_option('cookie_disable_google_fonts')) {
			return true;
		}
	}


    /**
     * Check  bot/user agent
     *
     * @return bool
     */
    public function is_user_bot()
    {

        $cookie_bot_crawler = $this->get_option('cookie_popup_consent_crawler', '');
        $cookie_bot_crawler_Array = array_filter(array_map('trim', explode(',', $cookie_bot_crawler)));

        foreach ($cookie_bot_crawler_Array as $bot) {
            if (strstr(strtolower($_SERVER['HTTP_USER_AGENT']), $bot)) {
                return true;
            }
        }

        return false;

    }

	/**
	 * This function doesnt do anything at this point
	 *
	 * @return bool
	 */
	private function is_consent_declined() {

		$user_declined = false;
		if ( is_user_logged_in() && is_array( $this->user_meta ) ) {

			$user_declined = ! empty( $this->user_meta['consent_declined'] );

		}

		// this actually never happens
		$cookie_declined = $this->get_cookie( 'consent_declined', false );

		return $user_declined || $cookie_declined;

	}


	/**
	 * @param int $level If passed, a flat structure of cookienames above that level will be returned
	 *
	 * @return mixed
	 */
	public function get_cookies_to_block( $level = 0 ) {

		$cookies_to_block = apply_filters( 'ct_ultimate_gdpr_cookie_get_cookies_to_block', array(
            CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL    => array(),
            CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY    => array(),
            CT_Ultimate_GDPR_Model_Group::LEVEL_CONVENIENCE  => array(),
            CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS   => array(),
            CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING   => array(),
            CT_Ultimate_GDPR_Model_Group::LEVEL_PRIVATE_DATA => array()
		) );
	
		$cookies_names = [];

		// checking for group cookies
		if ( $level < 6 ) {

			foreach ( $cookies_to_block as $cookie_level => $cookie_group ) {
				if ( $cookie_level > $level ) {
					$cookies_names = array_merge( $cookies_names, $cookie_group );
				}
			}
			$cookies_to_block = $cookies_names;
		}
		
		// checking for granular cookies
		if ( $level > 5 ) {

			$levels = CT_Ultimate_GDPR_Model_Group::$level_id;
			$level_id = [];
			$cookie_arr = [];
			$cookie_accept = [];

			foreach( $levels as $k => $v) {
				if($level == $k) {
					$level_id = $v;
				}
			}

			$tmp = [ CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL, 5, 6, 7, 8 ];
			$mapCookie = array_map(array($this, 'map_cookie'), $tmp, $cookies_to_block);

			foreach ( $mapCookie as $cookie_level => $cookie_group ) {
				foreach($cookie_group as $k => $v) {
					if( count($v) == 0 || in_array($k, $level_id ) ) {
						continue;
					}
					$cookie_arr[] = array_unique($v);
				}
			} 

			foreach($cookie_arr as $k => $v) {
				$cookies_names = array_unique( array_merge( $cookies_names, $v ) );
			}
			$cookies_to_block = $cookies_names;
		}

		return apply_filters( 'ct_ultimate_gdpr_cookie_cookies_to_block', $cookies_to_block, $level );
	}

	
	/**
	 *
	 *
	 * @return array
	 */
	public function map_cookie($n, $m) {
		return [$n => $m];
	}

	/**
	 * Get current privacy group level (either from POST data or usermeta or cookie)
	 *
	 * @return int
	 */
	public function get_group_level() {

		if ( $this->is_giving_consent() ) {

			$consent_level = (int) ct_ultimate_gdpr_get_value( 'level', $this->get_request_array(), 0 );

			if ( $consent_level ) {
				return $consent_level;
			}

		}


		$cookie_consent_level = $this->get_cookie( 'consent_level', $this->get_option( 'cookie_cookies_group_default', $this->get_default_group_level() ) );

		if ( $this->user_meta ) {
			$meta_consent_level = ct_ultimate_gdpr_get_value( 'consent_level', $this->user_meta );
		}

		return ! empty( $meta_consent_level ) ? $meta_consent_level : $cookie_consent_level;
	}

	/**
	 * @return bool
	 */
	private function is_giving_consent() {
		return wp_doing_ajax() && 'ct_ultimate_gdpr_cookie_consent_give' == ct_ultimate_gdpr_get_value( 'action', $this->get_request_array(), false );
	}

	/**
	 * Block all cookies in backend
	 */
	public function block_cookies() {

		if ( ! $this->is_block_cookies() ) {
			return;
		}

		if ( $this->is_login_page() ) {
			return;
		}

		// check group level
		$group_level = $this->get_group_level();

		// dont block anything
		if ( $this->is_group_level_allow_all( $group_level ) ) {
			return;
		}

		/* Check for cookies in headers */
		$headers_list      = headers_list();
		$cookies_to_block  = $this->get_cookies_to_block();
		$cookies_to_delete = array();
		$all_cookies       = ! empty( $_COOKIE ) ?
			array_combine( array_keys( $_COOKIE ), array_keys( $_COOKIE ) ) : array();

		foreach ( $headers_list as $header ) {

			// is not a cookie
			if ( ! preg_match( '#Set-Cookie: (.+?)=#', $header, $matches ) ) {
				continue;
			}

			$cookie_name                 = ct_ultimate_gdpr_get_value( 1, $matches, '' );
			$all_cookies[ $cookie_name ] = $cookie_name;

			foreach ( $cookies_to_block as $level => $blacklisted_cookies ) {

				if(!is_array($blacklisted_cookies)) {	
					$blacklisted_cookies = explode(" ", $blacklisted_cookies);
				}

				// level is below accepted level, no need to block
				if ( $level <= $group_level ) {
					continue;
				}

				// is blacklisted
				if ( in_array( $cookie_name, $blacklisted_cookies ) ) {

					$cookies_to_delete[ $cookie_name ] = $cookie_name;
					continue;

				}

				// maybe is wildcard
				foreach ( $blacklisted_cookies as $blacklisted_cookie ) {

					if ( strpos( $blacklisted_cookie, '*' ) !== false ) {

						$blacklisted_cookie_name = str_replace( '*', '', $blacklisted_cookie );

						if ( strpos( $cookie_name, $blacklisted_cookie_name ) !== false ) {

							$cookies_to_delete[ $cookie_name ] = $cookie_name;
							break;

						}

					}
				}

			}

		}

		if ( $group_level > CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL ) {

			// remove selected
			$this->delete_cookies( $cookies_to_delete );

		} else {

			// delete all cookies, just to be sure
			$this->delete_cookies( $all_cookies );

		}

	}

	/**
	 * @return bool
	 */
	private function is_login_page() {
		return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) );
	}

	/**
	 * Do not block these cookies (cookiename => cookiename to prevent doubling)
	 *
	 * @return array
	 */
	private function get_cookies_whitelisted() {

		$user_whitelisted = $this->get_option( 'cookie_whitelist', ct_ultimate_gdpr_get_value( 'cookie_whitelist', $this->get_default_options() ), '', true );
		$user_whitelisted = array_filter( array_map( 'trim', explode( ',', $user_whitelisted ) ) );
		$whitelisted      = array_merge( $this->cookies_to_delete, $user_whitelisted, array('ct-ultimate-gdpr-cookie', 'ct-ultimate-gdpr-age'));
		$whitelisted      = array_combine( $whitelisted, $whitelisted );

		return apply_filters( "ct_ultimate_gdpr_controller_{$this->get_id()}_cookies_whitelisted", $whitelisted );
	}

	/**
	 * Delete selected cookies names
	 *
	 * @param $cookies
	 */
	public function delete_cookies( $cookies ) {

		// prevent 'headers sent' notices
        if (array_key_exists('HTTP_HOST', $_SERVER)) {
            if ( headers_sent() ) {
                return;
            }
        }

		// remove whitelisted cookies from deletion queue
		$cookies_whitelisted = $this->get_cookies_whitelisted();
		foreach ( $cookies_whitelisted as $whitelisted ) {
			unset( $cookies[ $whitelisted ] );
		}


		// get parent domains as well
		$domains = $this->get_all_domains( $_SERVER['HTTP_HOST'] );

		foreach ( $cookies as $cookie_name ) {

			if ( $cookie_name ) {

				// set our controll cookie only once (to prevent it being read while expired)
				if ( $cookie_name == $this->get_id() ) {

					setcookie( $cookie_name, '0', 1, '/' );
					continue;

				}

				// value should not be readable after deletion
				unset( $_COOKIE[ $cookie_name ] );

				// set expired (possibly this will not remove oookie from browser)
				foreach ( $domains as $domain ) {
					setcookie( $cookie_name, '0', 1, '/', $domain );
				}

				$this->cookies_to_delete[ $cookie_name ] = $cookie_name;
			}
		}

	}

	/** Get all domains and subdomains to catch all cookies
	 *
	 * @param $host
	 *
	 * @return array
	 */
	private function get_all_domains( $host ) {

		$host_parts = explode( '.', $host );
		$domains    = array();

		for ( $i = 0; $i < count( $host_parts ) - 1; $i ++ ) {

			$domain_parts = array();

			for ( $j = $i; $j < count( $host_parts ); $j ++ ) {

				$domain_parts[] = $host_parts[ $j ];

			}

			$domains[] = implode( '.', $domain_parts );
		}

		$domains[] = '';

		return apply_filters( 'ct_ultimate_gdpr_controller_cookie_get_all_domains', $domains, $host );

	}

	/**
	 * @return bool
	 */
	public function is_consent_valid() {

		if ( $this->is_consent_declined() ) {
			return false;
		}
        if(isset($_GET['shortcodepreview'])){
            return false;
        }

		$user_valid = false;
		if ( $this->user_id ) {

			$user_valid = (
				is_array( $this->user_meta ) &&
				! empty( $this->user_meta['consent_expire_time'] ) &&
				$this->user_meta['consent_expire_time'] > time()
			);

		}

		$cookie_date  = $this->get_cookie( 'consent_expire_time', 0 );
		$cookie_valid = (
			$cookie_date &&
			$cookie_date > time()
		);

		return $user_valid || $cookie_valid;

	}

	/**
	 * @param string $variable_name
	 * @param string $variable_default_value
	 *
	 * @return array|mixed|object|string
	 */
	private function get_cookie( $variable_name = '', $variable_default_value = '' ) {

		$value  = ct_ultimate_gdpr_get_encoded_cookie( $this->get_id() );
		$cookie = $value ? json_decode( stripslashes( $value ), true ) : array();

		if ( $variable_name ) {
			return is_array( $cookie ) && isset( $cookie[ $variable_name ] ) ? $cookie[ $variable_name ] : $variable_default_value;
		}

		return $cookie;

	}

	/**
	 * @return bool
	 */
	private function is_block_cookies()
    {

        if ($this->is_user_from_eu == true) {
            $return = !empty($this->options['cookie_block']);
        } else {
            $return = false;
        }

        // check age settings
        if (ct_ultimate_gdpr_should_age_block_features()){
            $return = true;
        }

        if (ct_ultimate_gdpr_get_value('ctpass', $_GET, false)) {
            $return = false;
        }

        return apply_filters('ct_ultimate_gdpr_controller_cookie_block_cookies', $return, !empty($this->options['cookie_block']), $this->is_user_from_eu);
    }

	/**
	 * @param $page_id
	 *
	 * @return bool
	 */
	private function should_display_on_page( $page_id ) {

		if ( $this->get_option( 'cookie_display_all' ) ) {
			return true;
		}

		if ( in_array( $page_id, $this->get_option( 'cookie_pages', array(), 'page' ) ) ) {
			return true;
		}

		if ( is_front_page() && in_array( 'front', $this->get_option( 'cookie_pages', array() ) ) ) {
			return true;
		}

		if ( is_home() && in_array( 'posts', $this->get_option( 'cookie_pages', array() ) ) ) {
			return true;
		}

		return false;

	}

	/**
	 *
	 */
	public function front_action() {
		if ( $this->is_user_from_eu == true ) {
			add_action( 'wp_footer', array( $this, 'render' ) );
		}
	}

	/**
	 *
	 */
	public function admin_action() {
	}

	/**
	 *
	 */
	public function enqueue_cookie_background_image_upload_handler() {
		if ( $this->is_request_update_background_image() ) {
			add_action( 'ct_ultimate_gdpr_after_controllers_registered', array( $this, 'update_background_image' ) );
		} elseif ( isset( $_POST['ct-ultimate-gdpr-remove-background-image'] ) ) {
			add_action( 'ct_ultimate_gdpr_after_controllers_registered', array( $this, 'remove_background_image' ) );
		}
	}

	/**
	 * @return bool
	 */
	private function is_request_update_background_image() {
		if ( ct_ultimate_gdpr_get_value( 'cookie_background_image_file', $_FILES ) && $_FILES["cookie_background_image_file"]["size"] > 0 ) {
			return true;
		}

		return false;
	}

	/**
	 *
	 */
	public function update_background_image() {
		if ( ! function_exists( 'media_handle_upload' ) ) {
			require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
		}

		$attachment_id = media_handle_upload( 'cookie_background_image_file', 0 );
		if ( ! is_wp_error( $attachment_id ) ) {
			$this->attachment_id = $attachment_id;
			$update_action_name  = 'pre_update_option_' . self::ID;
			add_action( $update_action_name, array( $this, 'update_option_add_cookie_background_image' ) );
		}
	}

    /**
     * @param array $values
     * @return array
     */
	public function update_option_add_cookie_background_image($values) {
		$values['cookie_background_image'] = $this->attachment_id;
		return $values;
	}

	/**
	 *
	 */
	public function remove_background_image() {
		$update_action_name = 'pre_update_option_' . self::ID;
		add_action( $update_action_name, array( $this, 'update_option_remove_cookie_background_image' ) );
	}

    /**
     * @param array $values
     * @return array
     */
	public function update_option_remove_cookie_background_image($values) {
		$options = $this->options;
		wp_delete_attachment( $options['cookie_background_image'] );
		$values['cookie_background_image'] = '';
		return $values;
	}

	/**
	 * @return string
	 */
	public function get_id() {
		return self::ID;
	}

	/**
	 * @return mixed|void
	 */
	protected function admin_page_action() {
		$action = ct_ultimate_gdpr_get_value('ct-ultimate-gdpr-action', $this->get_request_array());
		if ( $this->is_request_consents_log() ) {
			$this->download_consents_log($action);
		}
		if ( $this->is_request_scan_cookies() ) {
			$this->scan_cookies();
		}
        if ( $this->is_request_delete_consents_log() ) {
            $this->delete_consents_log();
        }
		if ( 'scanner-success' == ct_ultimate_gdpr_get_value( 'notice', $_GET ) ) {

		    $this->check_last_cookies_scan();

			$this->add_view_option(
				'notices',
				array(
					sprintf(
						__( "<h3>Please navigate to <a href='%s'>Service Manager</a> tab to see all detected cookies.</h3>", 'ct-ultimate-gdpr' ),
						$this->get_cookie_manager_url()
					)
				)
			);

		}

	}

	/**
	 * @return bool|mixed
	 */
	private function is_request_consents_log() {
		return ! ! ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-log', $this->get_request_array() );
	}

	/**
	 * @return bool
	 */
	private function is_request_scan_cookies() {
		return ! ! ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-check-cookies', $this->get_request_array() );
	}


    /**
     * removed all consents log
     */
    private function is_request_delete_consents_log() {
        return ! ! ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-delete-log', $this->get_request_array() );
    }

    /**
     * Delete consents logs on sql
     */
    private function delete_consents_log() {
        global $wpdb;
        $table  = $wpdb->prefix . 'ct_ugdpr_consent_log';
        $delete = $wpdb->query("TRUNCATE TABLE $table");
        return $delete;
    }

    /**
     * Download logs of all user consents
     */
    private function download_consents_log($action = 'download-csv') {

		/*global $wpdb;

        // get all user metas
        $sql = $wpdb->prepare(
            "
                SELECT user_id, meta_value
                FROM {$wpdb->usermeta}
                WHERE meta_key = %s
            ",
            $this->get_id()
        );

        $results = $wpdb->get_results( $sql, ARRAY_A );

        // default to array
        if ( ! $results ) {
            $results = array();
        }

        // create a response
        $response = '';
        foreach ( $results as $result ) {

            $id     = $result['user_id'];
            $data   = maybe_unserialize( ( $result['meta_value'] ) );
            $expire = $data['consent_expire_time'];
            $level  = $data['consent_level'];

            // either get consent given time (v1.4) or calculate it
            $created = isset( $data['consent_time'] ) ? $data['consent_time'] : ( $expire - (int) $this->get_option( 'cookie_expire', YEAR_IN_SECONDS ) );

            // format dates
            $expire  = ct_ultimate_gdpr_date( $expire );
            $created = ct_ultimate_gdpr_date( $created );

            $response .= sprintf(
                __( "user id: %d \r\nconsent level: %s \r\nconsent given: %s \r\nconsent expires: %s \r\n\r\n", 'ct-ultimate-gdpr' ),
                $id, $level, $created, $expire
            );

        }*/

		$rendered = $this->logger->render_logs( $this->logger->get_logs( $this->get_id() ) );
		
		if($action === 'download-txt'):
			// download as txt file
			header( "Content-Type: application/octet-stream" );
			header( "Content-Disposition: attachment; filename='{$this->get_id()}-logs.txt'" );
			echo $rendered;
			exit;
		else:
			// Download CSV file:
			$output = fopen( 'php://memory', 'w' );
			$header_args = array( 'id', 'type', 'user_id','user_ip','user_agent','time','data' );
			$data_arr = $this->logger->get_logs( $this->get_id() );
			if(!$data_arr) die('No Data');

			// Format data array 
			$new_data_arr = [];
			foreach($data_arr as $key => $value ){
				$new_data_arr[$key]['id'] = $value['id'];
				$new_data_arr[$key]['type'] = $value['type'];
				$new_data_arr[$key]['user_id'] = $value['user_id'];
				$new_data_arr[$key]['user_ip'] = $value['user_ip'];
				$new_data_arr[$key]['user_agent'] = $value['user_agent'];
				$new_data_arr[$key]['time'] = date("r", $value['time']);
				$new_data_arr[$key]['consent_declined'] = "";
				$new_data_arr[$key]['consent_expire_time'] = "";
				$new_data_arr[$key]['consent_level'] = "";
				$new_data_arr[$key]['consent_time'] = "";
				$new_data_arr[$key]['consent_id'] = "";
				if($value['data']){
					$data = json_decode($value['data']);
					$new_data_arr[$key]['consent_declined'] = ($data->consent_declined)? 'true' : 'false';
					$new_data_arr[$key]['consent_expire_time'] = date("r", $data->consent_expire_time);
					$new_data_arr[$key]['consent_level'] = $data->consent_level;
					$new_data_arr[$key]['consent_time'] = date("r", $data->consent_time);
					$new_data_arr[$key]['consent_id'] = is_array($data->consent_id) ? implode(",", $data->consent_id) : $data->consent_id;
				}
			}
			$data_arr = $new_data_arr;

			// Get all keys
			$header_arr = array_keys($data_arr[0]);		
			foreach($header_arr as $key => $value ){
				// Format header
				$header_arr[$key] = str_replace('_',' ', strtoupper($value));
			}
			fputcsv($output, $header_arr);
			foreach($data_arr as $key => $value ){
				// Format date
				// $data_arr[$key]['time'] = date("Y-m-d h:i:s A e", $value['time']);
				fputcsv($output, $value);
			}

			// Move back to beginning of file 
			fseek($output, 0); 
 
			// Set headers to download file rather than displayed 
			header('Content-Type: text/csv');
			header("Content-Disposition: attachment; filename={$this->get_id()}-logs.csv");
		 
			//output all remaining data on a file pointer 
			fpassthru($output); 
		endif;
		exit;

	}

	/**
	 *
	 */
	public function add_menu_page() {
		add_submenu_page(
			CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_name(),
			esc_html__( 'Cookie Consent', 'ct-ultimate-gdpr' ),
			esc_html__( 'Cookie Consent', 'ct-ultimate-gdpr' ),
			'manage_options',
			$this->get_id(),
			array( $this, 'render_menu_page' )
		);

		add_submenu_page(
			CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_name(),
			esc_html__( 'Services Manager', 'ct-ultimate-gdpr' ),
			esc_html__( 'Services Manager', 'ct-ultimate-gdpr' ),
			'manage_options',
			'edit.php?post_type=ct_ugdpr_service'
		);
	}

	/**
	 * @return string
	 */
	public function get_view_template() {
		return 'admin/admin-cookie';
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		/* Cookie section - cookie popup tab */

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-1_section-1', // ID
			esc_html__( 'Cookie popup content', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-1_section-2', // ID
			esc_html__( 'Buttons labels', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-1_section-3', // ID
			esc_html__( 'Options', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-1_section-4', // ID
			esc_html__( 'Google Analytics Tracking ID', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);
		

//      /* Cookie section - preferences tab */

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-2_section-1', // ID
			esc_html__( 'Cookie scanner settings', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-2_section-2', // ID
			esc_html__( 'Buttons styles', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-2_section-3', // ID
			esc_html__( 'Read more', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);

        add_settings_section(
            'ct-ultimate-gdpr-cookie_tab-2_section-9', // ID
            esc_html__( 'Reject All Cookies', 'ct-ultimate-gdpr' ), // Title
            null, // callback
            'ct-ultimate-gdpr-cookie' // Page
        );

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-2_section-4', // ID
			esc_html__( 'Position of the cookie notice box', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-2_section-5', // ID
			esc_html__( 'Cookie notice box', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-2_section-7', // ID
			esc_html__( 'Protection shortcode', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-2_section-6', // ID
			esc_html__( 'Custom style CSS', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);
		
		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-2_section-10', // ID
			esc_html__( 'Withdrawal Cookie Agreement', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-2_section-8', // ID
			esc_html__( 'My account disclaimer', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-2_section-11', // ID
			 esc_html__( 'Disable Google Fonts', 'ct-ultimate-gdpr' ), // Title
			 null,
			 'ct-ultimate-gdpr-cookie'
		);

//      * Cookie section - advanced tab */

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-3_section-1', // ID
			esc_html__( 'Advanced Cookies Settings', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);


		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-3_section-2', // ID
			esc_html__( 'Trigger cookie', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-3_section-4', // ID
			esc_html__( 'Modal styles', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);

		
		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-3_section-3', // ID
			esc_html__( 'Lists of features for groups', 'ct-ultimate-gdpr' ), // Title
			//null, // callback
			array($this, 'hideIndividualCookieSection'),
			'ct-ultimate-gdpr-cookie' // Page
		);
		
		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-3_section-7', // ID
			esc_html__( 'Lists of features for individual', 'ct-ultimate-gdpr' ), // Title
			//null, // callback
			array($this, 'hideGroupCookieSection'),
			'ct-ultimate-gdpr-cookie' // Page
		);
		
		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-3_section-5', // ID
			esc_html__( 'Labels', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);

		add_settings_section(
			'ct-ultimate-gdpr-cookie_tab-3_section-6', // ID
			esc_html__( 'Additional settings', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-cookie' // Page
		);	

		/* Cookie section fields */


		//TAB 1 -SECTION 1
		{
			add_settings_field(
				'cookie_content_language', // ID
				esc_html__( 'Provide default content for the selected language (remember to save changes)', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_cookie_content_language' ), // Callback
				'ct-ultimate-gdpr-cookie', // Page
				'ct-ultimate-gdpr-cookie_tab-1_section-1' // Section
			);

			add_settings_field(
				'cookie_content', // ID
				esc_html__( 'Cookie popup content', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_cookie_content' ), // Callback
				'ct-ultimate-gdpr-cookie', // Page
				'ct-ultimate-gdpr-cookie_tab-1_section-1' // Section
			);

			//TAB 1 -SECTION 2
			add_settings_field(
				'cookie_popup_label_accept', // ID
				esc_html__( "Cookie popup 'accept' button label (leave empty for default)", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_cookie_popup_label_accept' ), // Callback
				'ct-ultimate-gdpr-cookie', // Page
				'ct-ultimate-gdpr-cookie_tab-1_section-2' // Section
			);

			add_settings_field(
				'cookie_popup_label_read_more', // ID
				esc_html__( "Cookie popup 'read more' button label (leave empty for default)", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_cookie_popup_label_read_more' ), // Callback
				'ct-ultimate-gdpr-cookie', // Page
				'ct-ultimate-gdpr-cookie_tab-1_section-2' // Section
			);

			add_settings_field(
				'cookie_popup_label_settings', // ID
				esc_html__( "Cookie popup 'change settings' button label (leave empty for default)", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_cookie_popup_label_settings' ), // Callback
				'ct-ultimate-gdpr-cookie', // Page
				'ct-ultimate-gdpr-cookie_tab-1_section-2' // Section
			);

            add_settings_field(
                'cookie_popup_label_read_more_new_tab', // ID
                esc_html__( "Open \"Read More\" in a new tab", 'ct-ultimate-gdpr' ), // Title
                array( $this, 'render_field_cookie_popup_label_read_more_new_tab' ), // Callback
                'ct-ultimate-gdpr-cookie', // Page
                'ct-ultimate-gdpr-cookie_tab-1_section-2' // Section
            );

			//TAB 1 -SECTION 3
			add_settings_field(
				'cookie_display_all',
				esc_html__( 'Display cookie popup on all pages', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_display_all' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-1_section-3'
			);

			add_settings_field(
				'check_if_user_is_from_eu',
				esc_html__( 'Check if user is from European Union', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_check_if_user_is_from_eu' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-1_section-3'
			);
            
            add_settings_field(
				'cookie_reset_consent',
                sprintf(
                    wp_kses_post( __( 'Reset cookie settings after ended session', 'ct-ultimate-gdpr' ) ),
                    ''
                ),
				array( $this, 'render_field_cookie_reset_consent' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-1_section-3',
                array(
                    'hint' => sprintf(
                        wp_kses_post( __( 'Ask users to accept/reject cookies with every visit on the website', 'ct-ultimate-gdpr' ) ),
                        ''
                    ),
                )
			);

            // add_settings_field(
			// 	'cookie_reset_consent',
			// 	esc_html__( 'Reset cookie settings after ended session', 'ct-ultimate-gdpr' ),
			// 	array( $this, 'render_field_cookie_reset_consent' ),
			// 	'ct-ultimate-gdpr-cookie',
			// 	'ct-ultimate-gdpr-cookie_tab-1_section-3'
			// );

			add_settings_field(
				'cookie_pages',
				esc_html__( 'Select page where to display the cookie popup', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_pages' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-1_section-3'
			);

            add_settings_field(
                'cookie_popup_consent_crawler',
                esc_html__( 'Do not block user agents (eg. bots) containing the following texts (comma separated)', 'ct-ultimate-gdpr' ),
                array( $this, 'render_field_cookie_popup_consent_crawler' ),
                'ct-ultimate-gdpr-cookie',
                'ct-ultimate-gdpr-cookie_tab-1_section-3'
            );

            add_settings_field(
				'cookie_display_always',
				esc_html__( 'Always display cookie popup', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_display_always' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-1_section-3'
			);


			//TAB 2 - SECTION 1 - COOKIE CHECK

			add_settings_field(
				'cookie_scan_period',
				esc_html__( 'Choose how often cookie scans should be performed', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_scan_period' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-1'
			);

			add_settings_field(
				'cookie_default_level_assigned_for_inserted_cookies',
				esc_html__( 'Choose default level of new cookies', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_default_level_assigned_for_inserted_cookies' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-1'
			);

			add_settings_field(
				'cookie_whitelist',
				esc_html__( 'Cookies whitelist (never block these cookies, comma separated)', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_whitelist' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-1'
			);

			add_settings_field(
				'cookie_block',
				esc_html__( 'Block selected/all cookies until user consents', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_block' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-1'
			);

			add_settings_field(
				'cookie_expire',
				esc_html__( 'Set consent expire time [s]', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_expire' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-1'
			);


			//TAB 2 - SECTION 2 - BUTTON STYLES
			//button border color
			add_settings_field(
				'cookie_button_settings',
				esc_html__( 'Button settings by', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_button_settings' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-2'
			);

			//button shape
			add_settings_field(
				'cookie_button_shape',
				esc_html__( 'Button shape', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_button_shape' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-2'
			);

			//button size
			add_settings_field(
				'cookie_button_size',
				esc_html__( 'Button size', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_button_size' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-2'
			);

			//button text color
			add_settings_field(
				'cookie_button_text_color',
				esc_html__( 'Button text color', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_button_text_color' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-2'
			);


			//button background color
			add_settings_field(
				'cookie_button_bg_color',
				esc_html__( 'Button background color', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_button_bg_color' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-2'
			);

			//button border color
			add_settings_field(
				'cookie_button_border_color',
				esc_html__( 'Button border color', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_button_border_color' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-2'
			);

            //TAB 2 - SECTION 3 - Reject Cookie

            add_settings_field(
                'cookie_read_page',
                esc_html__( 'Cookie close modal button (an option to decline the cookies completely)', 'ct-ultimate-gdpr' ),
                array( $this, 'render_field_cookie_gear_close_box' ),
                'ct-ultimate-gdpr-cookie',
                'ct-ultimate-gdpr-cookie_tab-2_section-9'
            );


            //TAB 2 - SECTION 3 - READ MORE

			add_settings_field(
				'cookie_read_page',
				esc_html__( 'Read more page', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_read_page' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-3'
			);

			add_settings_field(
				'cookie_read_page_custom',
				esc_html__( 'Read More Custom URL', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_read_page_custom' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-3'

			);

			add_settings_field(
				'cookie_read_tabs',
				esc_html__( 'Active Tab', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_read_tabs' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-3',
				array(
					'class' => 'ct-ultimate-gdpr-hide'
				)

			);

			//TAB 2 - SECTION 4 - POSITION

			add_settings_field(
				'cookie_position',
				esc_html__( 'Position  (bottom, top and full page layout)', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_position' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-4'
			);

			add_settings_field(
				'cookie_position_distance',
				esc_html__( 'Distance from border [px]', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_position_distance' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-4'
			);


			//TAB 2 - SECTION 5 - BOX STYLES

			add_settings_field(
				'cookie_box_style',
				esc_html__( 'Box style', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_box_style' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-5'
			);

			add_settings_field(
				'cookie_box_shape',
				esc_html__( 'Box shape', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_box_shape' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-5'
			);

			add_settings_field(
				'cookie_background_color',
				esc_html__( 'Background color', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_background_color' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-5'
			);

			add_settings_field(
				'cookie_background_image',
				esc_html__( 'Background image', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_background_image' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-5'
			);

			add_settings_field(
				'cookie_text_color',
				esc_html__( 'Text color', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_text_color' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-5'
			);

			//TAB 2 - SECTION 7 - PROTECTION SHORTCODE

			add_settings_field(
				'cookie_protection_shortcode_label',
				esc_html__( 'Protection shortcode label', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_protection_shortcode_label' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-7'
			);

            //TAB 2 - SECTION 8 - MY ACCOUNT FORM

            add_settings_field(
                'cookie_my_account_disclaimer',
                esc_html__( 'My account disclaimer', 'ct-ultimate-gdpr' ),
                array( $this, 'render_field_cookie_my_account_disclaimer' ),
                'ct-ultimate-gdpr-cookie',
                'ct-ultimate-gdpr-cookie_tab-2_section-8'
            );
			
            // TAB 2 - SECTION 10 - WITHDRAWAL COOKIE AGREEMENT
            add_settings_field(
                'cookie_withdrawal_cookies_agreement',
                esc_html__( 'Heading', 'ct-ultimate-gdpr' ),
                array( $this, 'render_field_cookie_withdrawal_cookies_agreement' ),
                'ct-ultimate-gdpr-cookie',
                'ct-ultimate-gdpr-cookie_tab-2_section-10'
            );

			add_settings_field(
                'cookie_withdrawal_cookies_agreement_description',
                esc_html__( 'Description', 'ct-ultimate-gdpr' ),
                array( $this, 'render_field_cookie_withdrawal_cookies_agreement_description' ),
                'ct-ultimate-gdpr-cookie',
                'ct-ultimate-gdpr-cookie_tab-2_section-10'
            );

			add_settings_field(
				'cookie_withdrawal_cookies_agreement_button_bg_color',
				esc_html__( 'Button background color', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_withdrawal_cookies_agreement_button_bg_color' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-10'
			);

			add_settings_field(
				'cookie_withdrawal_cookies_agreement_button_text_color',
				esc_html__( 'Button text color', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_withdrawal_cookies_agreement_button_text_color' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-10'
			);

			add_settings_field(
				'cookie_withdrawal_cookies_agreement_button_border_color',
				esc_html__( 'Button border color', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_withdrawal_cookies_agreement_button_border_color' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-10'
			);

			//TAB 2 - SECTION 6 - CUSTOM STYLE CSS

			add_settings_field(
				'cookie_style',
				esc_html__( 'Custom style CSS', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_style' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-6'
			);

			//TAB 2 - SECTION 11 - DISABLE GOOGLE FONTS

			add_settings_field(
				'cookie_disable_google_fonts',
				esc_html__( 'Disable Google Fonts', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_disable_google_fonts' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-2_section-11'
			);

			//TAB 3


			//TAB 3 - SECTION 1 - ADVANCED COOKIES SETTINGS
			add_settings_field(
				'cookie_show_always',
				esc_html__( 'Show advanced cookie popup always, even when the consent is given', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_show_always' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-1'
			);

			add_settings_field(
				'cookie_use_group_popup',
				esc_html__( 'Use advanced cookie groups popup', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_use_group_popup' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-1'
			);

			add_settings_field(
				'cookie_single_popup',
				esc_html__( 'Block each cookie group individually', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_single_popup' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-1'
			);

			add_settings_field(
				'cookie_group_popup_header_content',
				esc_html__( 'Advanced cookie groups popup header content', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_header_content' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-1'
			);
			
			add_settings_field(
				'cookie_group_popup_header_content',
				esc_html__( 'Advanced cookie groups popup header content', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_header_content' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-1'
			);


			//TAB 3 - SECTION 2 - TRIGGER COOKIE

			add_settings_field(
				'cookie_settings_trigger',
				esc_html__( 'Trigger cookie settings by', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_settings_trigger' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-2'
			);

			add_settings_field(
				'cookie_trigger_modal_icon',
				esc_html__( 'Trigger cookie settings icon', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_trigger_modal_icon' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-2'
			);

			add_settings_field(
				'cookie_trigger_modal_text',
				esc_html__( 'Trigger cookie settings text', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_trigger_modal_text' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-2'
			);

			add_settings_field(
				'cookie_trigger_modal_bg',
				esc_html__( 'Trigger cookie settings background color', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_trigger_modal_bg' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-2'
			);

			add_settings_field(
				'cookie_trigger_modal_bg_shape',
				esc_html__( 'Trigger cookie settings shape', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_trigger_modal_bg_shape' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-2'
			);

			add_settings_field(
				'cookie_gear_icon_position',
				esc_html__( 'Trigger cookie settings position', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_gear_icon_position' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-2'
			);

			add_settings_field(
				'cookie_gear_icon_color',
				esc_html__( 'Trigger cookie settings color', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_gear_icon_color' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-2'
			);

			//TAB 3 - SECTION 3 - LIST OF FEATURES

			add_settings_field(
				'cookie_group_popup_features_wills_group_2',
				esc_html__( 'List of features available for Essential level (semicolon separated)', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_features_available_group_2' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-3'
			);

			add_settings_field(
				'cookie_group_popup_features_wonts_group_2',
				esc_html__( 'List of features not available for Essential level (semicolon separated)', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_features_nonavailable_group_2' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-3'
			);

			add_settings_field(
				'cookie_group_popup_features_wills_group_3',
				esc_html__( 'List of features available for Functionality level (semicolon separated)', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_features_available_group_3' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-3'
			);

			add_settings_field(
				'cookie_group_popup_features_wonts_group_3',
				esc_html__( 'List of features not available for Functionality level (semicolon separated)', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_features_nonavailable_group_3' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-3'
			);

			add_settings_field(
				'cookie_group_popup_features_wills_group_4',
				esc_html__( 'List of features available for Analytics level (semicolon separated)', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_features_available_group_4' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-3'
			);

			add_settings_field(
				'cookie_group_popup_features_wonts_group_4',
				esc_html__( 'List of features not available for Analytics level (semicolon separated)', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_features_nonavailable_group_4' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-3'
			);

			add_settings_field(
				'cookie_group_popup_features_wills_group_5',
				esc_html__( 'List of features available for Advertising level (semicolon separated)', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_features_available_group_5' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-3'
			);

			add_settings_field(
				'cookie_group_popup_features_wonts_group_5',
				esc_html__( 'List of features not available for Advertising level (semicolon separated)', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_features_nonavailable_group_5' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-3'
			);


			//TAB 3 - SECTION 4 - MODAL STYLES

			add_settings_field(
				'cookie_modal_header_color',
				esc_html__( 'Cookie modal header color', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_modal_header_color' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-4'
			);

			add_settings_field(
				'cookie_modal_text_color',
				esc_html__( 'Cookie modal text color', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_modal_text_color' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-4'
			);

			add_settings_field(
				'cookie_modal_skin',
				esc_html__( 'Cookie modal skin', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_modal_skin' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-4'
			);

            add_settings_field(
                'cookie_close_text_modal',
                esc_html__( 'Cookie close modal text (If empty, button X(close) will display. If not, it will display the text)', 'ct-ultimate-gdpr' ),
                array( $this, 'render_field_cookie_close_text_modal' ),
                'ct-ultimate-gdpr-cookie',
                'ct-ultimate-gdpr-cookie_tab-2_section-9'
            );
			//TAB 3 - SECTION 5 - LABELS


			add_settings_field(
				'cookie_group_popup_label_will',
				wp_kses_post( __( "<i>This website will</i> label", 'ct-ultimate-gdpr' ) ),
				array( $this, 'render_field_cookie_group_popup_label_will' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-5'
			);

			add_settings_field(
				'cookie_group_popup_label_wont',
				wp_kses_post( __( "<i>This website won't</i> label", 'ct-ultimate-gdpr' ) ),
				array( $this, 'render_field_cookie_group_popup_label_wont' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-5'
			);

			add_settings_field(
				'cookie_group_popup_hide_level_1',
				esc_html__( 'Hide "Block All" level features', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_hide_level_1' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-6'
			);

			add_settings_field(
				'cookie_group_popup_hide_level_2',
				esc_html__( 'Hide "Essential" level features', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_hide_level_2' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-6'
			);

			add_settings_field(
				'cookie_group_popup_hide_level_3',
				esc_html__( 'Hide "Functionality" level features', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_hide_level_3' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-6'
			);

			add_settings_field(
				'cookie_group_popup_hide_level_4',
				esc_html__( 'Hide "Analytics" level features', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_hide_level_4' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-6'
			);

			add_settings_field(
				'cookie_group_popup_hide_level_5',
				esc_html__( 'Hide "Advertising" level features', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_hide_level_5' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-6'
			);

			add_settings_field(
				'cookie_group_popup_label_block_all',
				wp_kses_post( __( "<i>Block all</i> label", 'ct-ultimate-gdpr' ) ),
				array( $this, 'render_field_cookie_group_popup_label_block_all' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-5'
			);

			add_settings_field(
				'cookie_group_popup_label_essentials',
				wp_kses_post( __( "<i>Essentials</i> label", 'ct-ultimate-gdpr' ) ),
				array( $this, 'render_field_cookie_group_popup_label_essentials' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-5'
			);

			add_settings_field(
				'cookie_group_popup_label_functionality',
				wp_kses_post( __( "<i>Functionality</i> label", 'ct-ultimate-gdpr' ) ),
				array( $this, 'render_field_cookie_group_popup_label_functionality' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-5'
			);

			add_settings_field(
				'cookie_group_popup_label_analytics',
				wp_kses_post( __( "<i>Analytics</i> label", 'ct-ultimate-gdpr' ) ),
				array( $this, 'render_field_cookie_group_popup_label_analytics' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-5'
			);

			add_settings_field(
				'cookie_group_popup_label_advertising',
				wp_kses_post( __( "<i>Advertising</i> label", 'ct-ultimate-gdpr' ) ),
				array( $this, 'render_field_cookie_group_popup_label_advertising' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-5'
			);

			//TAB 3 - SECTION 5 - ADDITIONAL SETTINGS

			add_settings_field(
				'cookie_group_popup_label_save', // ID
				esc_html__( "Advanced cookie popup 'save & close' button label (leave empty for default)", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_cookie_group_popup_label_save' ), // Callback
				'ct-ultimate-gdpr-cookie', // Page
				'ct-ultimate-gdpr-cookie_tab-3_section-6'
			);

			add_settings_field(
				'cookie_cookies_group_default',
				esc_html__( "Select default privacy group", 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_cookies_group_default' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-6'
			);

			add_settings_field(
				'cookie_refresh_after_save',
				esc_html__( "Reload page when user saves changes", 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_refresh_after_save' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-6'
			);


			add_settings_field(
				'cookie_cookies_group_after_accept',
				esc_html__( "Select privacy group after user accepts cookies in the popup", 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_cookies_group_after_accept' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-6'
			);


			//TAB 3 - SECTION 7 - LIST OF FEATURES INDIVIDUAL
			add_settings_field(
				'cookie_group_popup_features_wills_group_2_individual',
				esc_html__( 'List of features available for Essential level (semicolon separated)', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_features_available_group_2_individual' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-7'
			);

			add_settings_field(
				'cookie_group_popup_features_wills_group_3_individual',
				esc_html__( 'List of features available for Functionality level (semicolon separated)', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_features_available_group_3_individual' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-7'
			);
 
			add_settings_field(
				'cookie_group_popup_features_wills_group_4_individual',
				esc_html__( 'List of features available for Analytics level (semicolon separated)', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_features_available_group_4_individual' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-7'
			);

			add_settings_field(
				'cookie_group_popup_features_wills_group_5_individual',
				esc_html__( 'List of features available for Advertising level (semicolon separated)', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_cookie_group_popup_features_available_group_5_individual' ),
				'ct-ultimate-gdpr-cookie',
				'ct-ultimate-gdpr-cookie_tab-3_section-7'
			);

		}
	}

	/**
	 *
	 */
	public function render_field_cookie_read_page_custom() {
		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value( $field_name );

		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			esc_html( $value )
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_read_tabs() {
		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value( $field_name );

		printf(
			"<input class='ct-ultimate-gdpr-InputForTab' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			esc_html( $value )
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_read_page() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value( $field_name );

		$posts = ct_ultimate_gdpr_wpml_get_original_posts( array(
			'posts_per_page' => - 1,
			'post_type'      => ct_ultimate_gpdr_get_default_post_types(),
			'orderby'        => 'post_title',
		) );

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$field_name,
			$admin->get_field_name_prefixed( $field_name )
		);

		// empty option
		echo "<option></option>";

		/** @var WP_Post $post */
		foreach ( $posts as $post ) :

			$post_title = $post->post_title ? $post->post_title : $post->post_name;
			$post_id    = $post->ID;
			$selected   = $post_id == $value ? "selected" : '';
			echo "<option value='$post->ID' $selected>$post_title</option>";

		endforeach;

		echo '</select>';

	}

	/**
	 *
	 */
	public function render_field_cookie_scan_period() {
		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$field_value = $admin->get_option_value( $field_name );

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$field_name,
			$admin->get_field_name_prefixed( $field_name )
		);

		$values = array(
			'manual'                     => __( 'Never', 'ct-ultimate-gdpr' ),
			'ct-ultimate-gdpr-weekly'    => __( 'Weekly', 'ct-ultimate-gdpr' ),
			'ct-ultimate-gdpr-monthly'   => __( 'Monthly', 'ct-ultimate-gdpr' ),
			'ct-ultimate-gdpr-quarterly' => __( 'Quarterly', 'ct-ultimate-gdpr' )
		);

		foreach ( $values as $value => $label ) :
			$selected = $value == $field_value ? 'selected' : '';
			echo "<option value='$value' $selected>" . $label . "</option>";

		endforeach;

		echo '</select>';
	}

	/**
	 *
	 */
	public function render_field_cookie_content_language() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$field_value = $admin->get_option_value( $field_name );

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$field_name,
			$admin->get_field_name_prefixed( $field_name )
		);

		$values = array(
			''   => esc_html__( 'Select', 'ct-ultimate-gdpr' ),
			'cs' => 'Čeština',
			'de' => 'Deutsch',
			'en' => 'English',
			'es' => 'Español',
			'fr' => 'Français',
			'hr' => 'Hrvatski',
			'hu' => 'Magyar',
            'no' => 'Norwegian',
			'it' => 'Italiano',
			'nl' => 'Nederlands',
			'pl' => 'Polski',
			'pt' => 'Português',
			'ro' => 'Română',
			'ru' => 'Русский',
			'sk' => 'Slovenčina',
			'dk' => 'Danish',
            'bg' => 'Bulgarian',
            'sv' => 'Swedish'
		);

		foreach ( $values as $value => $label ) :
			$selected = $value == $field_value ? 'selected' : '';
			echo "<option value='$value' $selected>" . $label . "</option>";

		endforeach;

		echo '</select>';

		?>

<input type="text" readonly class="button button-primary" name="ct-ultimate-gdpr-cookie-content-language"
    value="<?php _e( 'Load', 'ct-ultimate-gdpr' ); ?>" />

<?php

	}

	/**
	 *
	 */
	public function render_field_cookie_cookies_group_after_accept() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$field_value = $admin->get_option_value( $field_name, 5 );

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name )
		);

		$values = array(
			CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL,
			CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY,
			CT_Ultimate_GDPR_Model_Group::LEVEL_CONVENIENCE,
			CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS,
			CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING,
		);

		foreach ( $values as $value ) :

			$selected = $value == $field_value ? 'selected' : '';
			echo "<option value='$value' $selected>" .  __(CT_Ultimate_GDPR_Model_Group::get_label( $value ),'ct-ultimate-gdpr') . "</option>";

		endforeach;

		echo '</select>';
	}


	/**
	 *
	 */
	public function render_field_cookie_default_level_assigned_for_inserted_cookies() {
		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$field_value = $admin->get_option_value( $field_name );

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$field_name,
			$admin->get_field_name_prefixed( $field_name )
		);

		$labels = CT_Ultimate_GDPR_Model_Group::get_all_labels();
		foreach ( $labels as $cookie_level => $label ) {
			$selected = $cookie_level == $field_value ? 'selected' : '';
			echo "<option value='$cookie_level' $selected>" . __($label,'ct-ultimate-gdpr') . "</option>";
		}

		echo '</select>';

	}

	/**
	 *
	 */
	public function render_field_cookie_cookies_group_default() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$field_value = $admin->get_option_value( $field_name, $this->get_default_group_level() );

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name )
		);

		$values = array(
			CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL,
			CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY,
			CT_Ultimate_GDPR_Model_Group::LEVEL_CONVENIENCE,
			CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS,
			CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING,
		);

		foreach ( $values as $value ) :

			$selected = $value == $field_value ? 'selected' : '';
			echo "<option value='$value' $selected>" . CT_Ultimate_GDPR_Model_Group::get_label( $value ) . "</option>";

		endforeach;

		echo '</select>';
	}

	public function hideIndividualCookieSection() {
		echo '<div class="list-before-individual"></div>';
	}

	public function hideGroupCookieSection() {
		echo '<div class="list-before-group"></div>';
	}
	/**
	 *
	 */
	public function render_field_cookie_expire() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, YEAR_IN_SECONDS )
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_label_save() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name )
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_popup_label_settings() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name )
		);

	}

    /**
     *
     */
    public function render_field_cookie_popup_label_read_more_new_tab() {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name( __FUNCTION__ );

        printf(
            "<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
            $admin->get_field_name( __FUNCTION__ ),
            $admin->get_field_name_prefixed( $field_name ),
            $admin->get_option_value( $field_name ) ? 'checked' : ''
        );

    }

	/**
	 *
	 */
	public function render_field_cookie_popup_label_read_more() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name )
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_popup_label_accept() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name )
		);

	}


	/**
	 *
	 */
	public function render_field_cookie_text_color() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '#ffffff' )
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_background_color() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '#ff7e27' )
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_background_image() {
		$admin          = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name     = $admin->get_field_name( __FUNCTION__ );
		$feat_image_url = wp_get_attachment_url( $admin->get_option_value_escaped( $field_name, '' ) );
		$file           = basename( $feat_image_url );
		$format_string  = "
            <input class='ct-ultimate-gdpr-field ct-cookie-background-image' type ='text' value='%s', readonly>
            <input class='ct-ultimate-gdpr-field ct-cookie-background-image' id='%s' name='%s' value='%s' style='display: none;'>
            <input class='ct-ultimate-gdpr-field ct-cookie-background-image' type='file' id='%s' name='%s' accept='image/*'/>
            <br/>
            <input class='button button-primary ct-cookie-background-image' name='ct-ultimate-gdpr-update-background-image' value='%s' type='submit'>
            <input class='button button-primary ct-cookie-background-image' name='ct-ultimate-gdpr-remove-background-image' value='%s' type='submit'>
            ";
		printf(
			$format_string,
			$file,
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '' ),
			'cookie_background_image_file',
			'cookie_background_image_file',
			esc_html__( 'Update', 'ct-ultimate-gdpr' ),
			esc_html__( 'Remove', 'ct-ultimate-gdpr' )
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_box_style() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );

		$default     = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$field_value = $admin->get_option_value_escaped( $field_name, '' );
		$field_value = $field_value ? $field_value : $default;

		$positions = array(
			'classic'       => esc_html__( 'Classic', 'ct-ultimate-gdpr' ),
			'classic_blue'  => esc_html__( 'Classic Dark', 'ct-ultimate-gdpr' ),
			'classic_light' => esc_html__( 'Classic Light', 'ct-ultimate-gdpr' ),
			'modern'        => esc_html__( 'Modern', 'ct-ultimate-gdpr' ),
            'apas_blue'     => esc_html__( 'Apas Blue', 'ct-ultimate-gdpr' ),
            'apas_black'    => esc_html__( 'Apas Black', 'ct-ultimate-gdpr' ),
            'apas_white'    => esc_html__( 'Apas White', 'ct-ultimate-gdpr' ),
            'kahk_blue'     => esc_html__( 'Kahk Blue', 'ct-ultimate-gdpr' ),
            'kahk_black'    => esc_html__( 'Kahk Black', 'ct-ultimate-gdpr' ),
            'kahk_white'    => esc_html__( 'Kahk White', 'ct-ultimate-gdpr' ),
            'oreo_blue'     => esc_html__( 'Oreo Blue', 'ct-ultimate-gdpr' ),
            'oreo_black'    => esc_html__( 'Oreo Black', 'ct-ultimate-gdpr' ),
            'oreo_white'    => esc_html__( 'Oreo White', 'ct-ultimate-gdpr' ),
            'wafer_blue'    => esc_html__( 'Wafer Blue', 'ct-ultimate-gdpr' ),
            'wafer_black'   => esc_html__( 'Wafer Black', 'ct-ultimate-gdpr' ),
            'wafer_white'   => esc_html__( 'Wafer White', 'ct-ultimate-gdpr' ),
            'jumble_blue'   => esc_html__( 'Jumble Blue', 'ct-ultimate-gdpr' ),
            'jumble_black'  => esc_html__( 'Jumble Black', 'ct-ultimate-gdpr' ),
            'jumble_white'  => esc_html__( 'Jumble White', 'ct-ultimate-gdpr' ),
            'khapse_blue'   => esc_html__( 'Khapse Blue', 'ct-ultimate-gdpr' ),
            'khapse_black'  => esc_html__( 'Khapse Black', 'ct-ultimate-gdpr' ),
            'khapse_white'  => esc_html__( 'Khapse White', 'ct-ultimate-gdpr' ),
            'tareco_blue'   => esc_html__( 'Tareco Blue', 'ct-ultimate-gdpr' ),
            'tareco_black'  => esc_html__( 'Tareco Black', 'ct-ultimate-gdpr' ),
            'tareco_white'  => esc_html__( 'Tareco White', 'ct-ultimate-gdpr' ),
            'kichel_blue'   => esc_html__( 'Kichel Blue', 'ct-ultimate-gdpr' ),
            'kichel_black'  => esc_html__( 'Kichel Black', 'ct-ultimate-gdpr' ),
            'kichel_white'  => esc_html__( 'Kichel White', 'ct-ultimate-gdpr' ),
            'macaron_blue'  => esc_html__( 'Macaron Blue', 'ct-ultimate-gdpr' ),
            'macaron_black' => esc_html__( 'Macaron Black', 'ct-ultimate-gdpr' ),
            'macaron_white' => esc_html__( 'Macaron White', 'ct-ultimate-gdpr' ),
            'wibele_blue'   => esc_html__( 'Wibele Blue', 'ct-ultimate-gdpr' ),
            'wibele_black'  => esc_html__( 'Wibele Black', 'ct-ultimate-gdpr' ),
            'wibele_white'  => esc_html__( 'Wibele White', 'ct-ultimate-gdpr' ),
		);

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name )
		);

		foreach ( $positions as $value => $label ) :

			$selected = ( $field_value == $value ) ? "selected" : '';
			echo "<option value='$value' $selected>$label</option>";

		endforeach;

		echo '</select>';

	}

	/**
	 *
	 */
	public function render_field_cookie_box_shape() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$field_value = $admin->get_option_value( $field_name );
		$positions   = array(
			'rounded' => esc_html__( 'Rounded', 'ct-ultimate-gdpr' ),
			'squared' => esc_html__( 'Squared', 'ct-ultimate-gdpr' ),
		);

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name )
		);

		foreach ( $positions as $value => $label ) :

			$selected = ( $field_value == $value ) ? "selected" : '';
			echo "<option value='$value' $selected>$label</option>";

		endforeach;

		echo '</select>';

	}


	/**
	 *
	 */
	public function render_field_cookie_button_settings() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$field_value = $admin->get_option_value( $field_name );
		$positions   = array(
			'text_only_' => esc_html__( 'Text Only', 'ct-ultimate-gdpr' ),
			'text_icon_' => esc_html__( 'Icon and Text', 'ct-ultimate-gdpr' ),
		);

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name )
		);

		foreach ( $positions as $value => $label ) :

			$selected = ( $field_value == $value ) ? "selected" : '';
			echo "<option value='$value' $selected>$label</option>";

		endforeach;

		echo '</select>';

	}

	/**
	 *
	 */
	public function render_field_cookie_button_shape() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$field_value = $admin->get_option_value( $field_name );
		$positions   = array(
			'rounded' => esc_html__( 'Rounded', 'ct-ultimate-gdpr' ),
			'squared' => esc_html__( 'Squared', 'ct-ultimate-gdpr' ),
		);

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name )
		);

		foreach ( $positions as $value => $label ) :

			$selected = ( $field_value == $value ) ? "selected" : '';
			echo "<option value='$value' $selected>$label</option>";

		endforeach;

		echo '</select>';

	}

	/**
	 *
	 */
	public function render_field_cookie_button_border_color() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );

		printf(
			"<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '#ffffff' )
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_button_text_color() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$default     = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$field_value = $admin->get_option_value_escaped( $field_name, '' );
		$field_value = $field_value ? $field_value : $default;

		printf(
			"<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$field_value
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_button_size() {


		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$default     = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$field_value = $admin->get_option_value_escaped( $field_name, '' );
		$field_value = $field_value ? $field_value : $default;

		$positions = array(
			'normal' => esc_html__( 'Normal', 'ct-ultimate-gdpr' ),
			'large'  => esc_html__( 'Large', 'ct-ultimate-gdpr' ),
		);

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name )
		);

		foreach ( $positions as $value => $label ) :

			$selected = ( $field_value == $value ) ? "selected" : '';
			echo "<option value='$value' $selected>$label</option>";

		endforeach;

		echo '</select>';

	}

	/**
	 *
	 */
	public function render_field_cookie_button_bg_color() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$default     = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$field_value = $admin->get_option_value_escaped( $field_name, '' );
		$field_value = $field_value ? $field_value : $default;

		printf(
			"<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$field_value
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_content() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );

		wp_editor(
			$admin->get_option_value( $field_name ),
			$this->get_id() . '_' . $field_name,
			array(
				'textarea_rows' => 10,
				'textarea_name' => $admin->get_field_name_prefixed( $field_name ),
			)
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_style() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='10' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '' )
		);
	}

	/**
	 * 
	 */
	public function render_field_cookie_disable_google_fonts() {
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_whitelist() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$default    = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$value      = $this->get_option( $field_name, $default );

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='10' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_features_nonavailable_group_2() {

		$default = ct_ultimate_gdpr_get_value( 'cookie_group_popup_features_nonavailable_group_2', $this->get_default_options() );

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value_escaped( $field_name, false );
		$value      = $value !== false ? $value : $default;

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_label_will() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$default    = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$value      = $admin->get_option_value_escaped( $field_name, false );
		$value      = $value !== false ? $value : $default;

		printf(
			"<input class='ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_label_wont() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$default    = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$value      = $admin->get_option_value_escaped( $field_name, false );
		$value      = $value !== false ? $value : $default;

		printf(
			"<input class='ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_label_block_all() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$default    = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$value      = $admin->get_option_value_escaped( $field_name );
		$value      = $value ? $value : $default;

		printf(
			"<input class='ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_label_essentials() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$default    = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$value      = $admin->get_option_value_escaped( $field_name );
		$value      = $value ? $value : $default;

		printf(
			"<input class='ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_label_functionality() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$default    = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$value      = $admin->get_option_value_escaped( $field_name );
		$value      = $value ? $value : $default;

		printf(
			"<input class='ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_label_analytics() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$default    = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$value      = $admin->get_option_value_escaped( $field_name );
		$value      = $value ? $value : $default;

		printf(
			"<input class='ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_label_advertising() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$default    = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$value      = $admin->get_option_value_escaped( $field_name );
		$value      = $value ? $value : $default;

		printf(
			"<input class='ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_protection_shortcode_label() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$default    = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$value      = $admin->get_option_value_escaped( $field_name );
		$value      = $value ? $value : $default;

		printf(
			"<input class='ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	public function render_field_cookie_withdrawal_cookies_agreement()
	{
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$default    = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$value      = $admin->get_option_value_escaped( $field_name );
		$value      = $value ? $value : $default;

		printf(
			"<input class='ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	public function render_field_cookie_withdrawal_cookies_agreement_description()
	{
		$default    = ct_ultimate_gdpr_get_value( 'cookie_withdrawal_cookies_agreement_description', $this->get_default_options() );
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value_escaped( $field_name, $default );

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

    /**
	 *
	 */
	public function render_field_cookie_withdrawal_cookies_agreement_button_bg_color() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$default     = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$field_value = $admin->get_option_value_escaped( $field_name, '' );
		$field_value = $field_value ? $field_value : $default;

		printf(
			"<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$field_value
		);

	}

    /**
	 *
	 */
	public function render_field_cookie_withdrawal_cookies_agreement_button_text_color() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$default     = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$field_value = $admin->get_option_value_escaped( $field_name, '' );
		$field_value = $field_value ? $field_value : $default;

		printf(
			"<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$field_value
		);

	}

    /**
	 *
	 */
	public function render_field_cookie_withdrawal_cookies_agreement_button_border_color() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$default     = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$field_value = $admin->get_option_value_escaped( $field_name, '' );
		$field_value = $field_value ? $field_value : $default;

		printf(
			"<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$field_value
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_features_available_group_2() {

		$default    = ct_ultimate_gdpr_get_value( 'cookie_group_popup_features_available_group_2', $this->get_default_options() );
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value_escaped( $field_name, false );
		$value      = $value !== false ? $value : $default;

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_features_available_group_2_individual() {

		$default    = ct_ultimate_gdpr_get_value( 'cookie_group_popup_features_available_group_2_individual', $this->get_default_options() );
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value_escaped( $field_name, false );
		$value      = $value !== false ? $value : $default;

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_features_nonavailable_group_3() {

		$default    = ct_ultimate_gdpr_get_value( 'cookie_group_popup_features_nonavailable_group_3', $this->get_default_options() );
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value_escaped( $field_name, false );
		$value      = $value !== false ? $value : $default;

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_features_available_group_3() {

		$default    = ct_ultimate_gdpr_get_value( 'cookie_group_popup_features_available_group_3', $this->get_default_options() );
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value_escaped( $field_name, false );
		$value      = $value !== false ? $value : $default;

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_features_available_group_3_individual() {

		$default    = ct_ultimate_gdpr_get_value( 'cookie_group_popup_features_available_group_3_individual', $this->get_default_options() );
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value_escaped( $field_name, false );
		$value      = $value !== false ? $value : $default;

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_features_nonavailable_group_4() {

		$default    = ct_ultimate_gdpr_get_value( 'cookie_group_popup_features_nonavailable_group_4', $this->get_default_options() );
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value_escaped( $field_name, false );
		$value      = $value !== false ? $value : $default;

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_features_available_group_4() {

		$default    = ct_ultimate_gdpr_get_value( 'cookie_group_popup_features_available_group_4', $this->get_default_options() );
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value_escaped( $field_name, false );
		$value      = $value !== false ? $value : $default;

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_features_available_group_4_individual() {

		$default    = ct_ultimate_gdpr_get_value( 'cookie_group_popup_features_available_group_4_individual', $this->get_default_options() );
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value_escaped( $field_name, false );
		$value      = $value !== false ? $value : $default;

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_features_available_group_5() {

		$default    = ct_ultimate_gdpr_get_value( 'cookie_group_popup_features_available_group_5', $this->get_default_options() );
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value_escaped( $field_name, false );
		$value      = $value !== false ? $value : $default;

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_features_available_group_5_individual() {

		$default    = ct_ultimate_gdpr_get_value( 'cookie_group_popup_features_available_group_5_individual', $this->get_default_options() );
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value_escaped( $field_name, false );
		$value      = $value !== false ? $value : $default;

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_features_nonavailable_group_5() {

		$default    = ct_ultimate_gdpr_get_value( 'cookie_group_popup_features_nonavailable_group_5', $this->get_default_options() );
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value_escaped( $field_name, false );
		$value      = $value !== false ? $value : $default;

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$value
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_cookies_group_2() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '' )
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_cookies_group_3() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '' )
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_cookies_group_4() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '' )
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_cookies_group_5() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='5' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '' )
		);
	}

	/**
	 *
	 */
	public function render_field_cookie_display_all() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_reset_consent() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

    /**
     *
     */
    public function render_field_cookie_popup_consent_crawler() {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name( __FUNCTION__ );
        printf(
            "<input type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name( __FUNCTION__ ),
            $admin->get_field_name_prefixed( $field_name ),
            $admin->get_option_value_escaped( $field_name )
        );

    }

    /**
	 *
	 */
	public function render_field_cookie_display_always() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}


    /**
	 *
	 */
	public function render_field_cookie_group_popup_hide_level_1() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_hide_level_2() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_hide_level_3() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_hide_level_4() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_hide_level_5() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_check_if_user_is_from_eu() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_refresh_after_save() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_show_always() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_use_group_popup() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_single_popup() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_block() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_pages() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$values     = $admin->get_option_value( $field_name );
		$post_types = ct_ultimate_gpdr_get_default_post_types();
		$posts      = ct_ultimate_gdpr_wpml_get_original_posts( array(
			'posts_per_page' => - 1,
			'post_type'      => $post_types,
			'orderby'        => 'post_title',
		) );

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s" size="15" multiple>',
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ) . "[]"
		);

		// default options
		echo "<option value=''></option>";

		$selected = is_array( $values ) && in_array( 'front', $values ) ? "selected" : '';
		echo "<option value='front' $selected>" . esc_html__( 'Front page', 'ct-ultimate-gdpr' ) . "</option>";

		$selected = is_array( $values ) && in_array( 'posts', $values ) ? "selected" : '';
		echo "<option value='posts' $selected>" . esc_html__( 'Posts page', 'ct-ultimate-gdpr' );

		/** @var WP_Post $post */
		foreach ( $posts as $post ) :

			$post_title = $post->post_title ? $post->post_title : $post->post_name;
			$post_id    = $post->ID;
			$selected   = is_array( $values ) && in_array( $post_id, $values ) ? "selected" : '';
			echo "<option value='$post->ID' $selected>$post_title</option>";

		endforeach;

		echo '</select>';

	}

	/**
	 *
	 */
	public function render_field_cookie_position() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$field_value = $admin->get_option_value( $field_name );
		$positions   = array(
			'bottom_left_'          => esc_html__( 'Bottom left', 'ct-ultimate-gdpr' ),
			'bottom_right_'         => esc_html__( 'Bottom right', 'ct-ultimate-gdpr' ),
			'bottom_panel_'         => esc_html__( 'Bottom panel', 'ct-ultimate-gdpr' ),
			'top_left_'             => esc_html__( 'Top left', 'ct-ultimate-gdpr' ),
			'top_right_'            => esc_html__( 'Top right', 'ct-ultimate-gdpr' ),
			'top_panel_'            => esc_html__( 'Top panel', 'ct-ultimate-gdpr' ),
			'full_layout_panel_'    => esc_html__( 'Full page layout', 'ct-ultimate-gdpr' ),
		);

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name )
		);

		foreach ( $positions as $value => $label ) :

			$selected = ( $field_value == $value ) ? "selected" : '';
			echo "<option value='$value' $selected>$label</option>";

		endforeach;

		echo '</select>';

	}

	/**
	 *
	 */
	public function render_field_cookie_position_distance() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );

		printf(
			"<input class='ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '20' )
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_settings_trigger() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$field_value = $admin->get_option_value( $field_name );
		$positions   = array(
			'text_only_' => esc_html__( 'Text Only', 'ct-ultimate-gdpr' ),
			'icon_only_' => esc_html__( 'Icon Only', 'ct-ultimate-gdpr' ),
			'text_icon_' => esc_html__( 'Icon and Text', 'ct-ultimate-gdpr' ),
		);

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name )
		);

		foreach ( $positions as $value => $label ) :

			$selected = ( $field_value == $value ) ? "selected" : '';
			echo "<option value='$value' $selected>$label</option>";

		endforeach;

		echo '</select>';

	}

	/**
	 *
	 */
	public function render_field_cookie_trigger_modal_icon() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$default     = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
		$field_value = $admin->get_option_value_escaped( $field_name );
		$field_value = $field_value ? $field_value : $default;

		echo '<div class="ct-iconpicker"><div class="ct-ip-holder">';
		printf(
			'<div class="ct-ip-icon"><i class="%s"></i></div><input type="hidden" value="%s" class="ct-icon-value ct-ultimate-gdpr-field"  id="%s" name="%s"></div>',
			$field_value,
			$field_value,
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name )
		);

		echo '<div class="ct-ip-popup ct-clearfix">
                <div class="ct-ip-search">
                <input type="text" class="ct-ip-search-input" placeholder="Search icon" />
                </div><ul>';
		$icons = ct_ultimate_gdpr_get_font_icons();

		foreach ( $icons as $value => $label ) :

			echo "<li><a href='#' data-icon='fa $value'><i class='fa $value'></i><a></li>";

		endforeach;

		echo '</ul></div></div>';
	}

	/**
	 *
	 */
	public function render_field_cookie_trigger_modal_text() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name )
		);

	}
    /**
     *
     */
    public function render_field_cookie_close_text_modal() {
        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name( __FUNCTION__ );
        printf(
            "<input type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name( __FUNCTION__ ),
            $admin->get_field_name_prefixed( $field_name ),
            $admin->get_option_value_escaped( $field_name )
        );
    }

	/**
	 *
	 */
	public function render_field_cookie_gear_icon_position() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
        $default     = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
        $field_value = $admin->get_option_value( $field_name );

        $field_value = $field_value ? $field_value : $default;

		$positions   = array(
			'top_left_'      => esc_html__( 'Top Left', 'ct-ultimate-gdpr' ),
			'top_center_'    => esc_html__( 'Top center', 'ct-ultimate-gdpr' ),
			'top_right_'     => esc_html__( 'Top Right', 'ct-ultimate-gdpr' ),
			'center_left_'   => esc_html__( 'Center Left', 'ct-ultimate-gdpr' ),
			'center_right_'  => esc_html__( 'Center Right', 'ct-ultimate-gdpr' ),
			'bottom_left_'   => esc_html__( 'Bottom Left', 'ct-ultimate-gdpr' ),
			'bottom_center_' => esc_html__( 'Bottom Center', 'ct-ultimate-gdpr' ),
			'bottom_right_'  => esc_html__( 'Bottom Right', 'ct-ultimate-gdpr' ),
		);

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name )
		);

		foreach ( $positions as $value => $label ) :

			$selected = ( $field_value == $value ) ? "selected" : '';
			echo "<option value='$value' $selected>$label</option>";

		endforeach;

		echo '</select>';

	}

	/**
	 *
	 */
	public function render_field_cookie_group_popup_header_content() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );

		wp_editor(
			$admin->get_option_value( $field_name ),
			$this->get_id() . '_' . $field_name,
			array(
				'textarea_rows' => 20,
				'textarea_name' => $admin->get_field_name_prefixed( $field_name ),
			)
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_trigger_modal_bg() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '#000000' )
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_trigger_modal_bg_shape() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$field_value = $admin->get_option_value( $field_name );
		$positions   = array(
			'round'   => esc_html__( 'Round', 'ct-ultimate-gdpr' ),
			'rounded' => esc_html__( 'Rounded', 'ct-ultimate-gdpr' ),
			'squared' => esc_html__( 'Squared', 'ct-ultimate-gdpr' ),
		);

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name )
		);

		foreach ( $positions as $value => $label ) :

			$selected = ( $field_value == $value ) ? "selected" : '';
			echo "<option value='$value' $selected>$label</option>";

		endforeach;

		echo '</select>';

	}

	/**
	 *
	 */
	public function render_field_cookie_modal_header_color() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '#595959' )
		);

	}

    public function render_field_cookie_my_account_disclaimer() {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name( __FUNCTION__ );
        $default    = ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() );
        $value      = $this->get_option( $field_name, $default );

        printf(
            "<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='3' cols='100'>%s</textarea>",
            $admin->get_field_name( __FUNCTION__ ),
            $admin->get_field_name_prefixed( $field_name ),
            $value
        );
    }

    /**
	 *
	 */
	public function render_field_cookie_modal_text_color() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '#797979' )
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_modal_skin() {

		$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$field_value = $admin->get_option_value( $field_name );
		$positions   = array(
			'default'            => esc_html__( 'Default', 'ct-ultimate-gdpr' ),
			'style-one'          => esc_html__( 'Neutral', 'ct-ultimate-gdpr' ),
			'style-two'          => esc_html__( 'Modern Blue', 'ct-ultimate-gdpr' ),
			'compact-dark-blue'  => esc_html__( 'Compact Dark Blue', 'ct-ultimate-gdpr' ),
			'compact-light-blue' => esc_html__( 'Compact Light Blue', 'ct-ultimate-gdpr' ),
			'compact-green'      => esc_html__( 'Compact Green', 'ct-ultimate-gdpr' ),
		);

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name )
		);

		foreach ( $positions as $value => $label ) :

			$selected = ( $field_value == $value ) ? "selected" : '';
			echo "<option value='$value' $selected>$label</option>";

		endforeach;

		echo '</select>';
	}

	/**
	 *
	 */
	public function render_field_cookie_gear_icon_color() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '#ffffff' )
		);

	}

	/**
	 *
	 */
	public function render_field_cookie_gear_close_box() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 * @return array
	 */
	public function get_default_options() {

		return apply_filters( "ct_ultimate_gdpr_controller_{$this->get_id()}_default_options", array(
			'cookie_box_style'                                 => "modern",
			'cookie_cookies_group_after_accept'                => 5,
			'cookie_display_all'                               => true,
			'cookie_single_popup'							   => true,
			'cookie_reset_consent'                             => false,
			'cookie_style'                                     => '',
			'cookie_expire'                                    => 31536000,
			'cookie_whitelist'                                 => 'PHPSESSID, wordpress, wp-settings-, __cfduid, ct-ultimate-gdpr-cookie, ct-ultimate-gdpr-age',
			'cookie_content'                                   => $this->get_default_cookie_content(),
			'cookie_popup_label_accept'                        => esc_html__( 'Accept', 'ct-ultimate-gdpr' ),
			'cookie_popup_label_read_more'                     => esc_html__( 'Read more', 'ct-ultimate-gdpr' ),
			'cookie_popup_label_settings'                      => esc_html__( 'Change Settings', 'ct-ultimate-gdpr' ),
			'cookie_position'                                  => 'bottom_panel_',
			'cookie_position_distance'                         => 20,
			'cookie_box_shape'                                 => 'rounded',
			'cookie_background_image'                          => '',
			'cookie_background_color'                          => '#ff7d27',
			'cookie_text_color'                                => '#ffffff',
			'cookie_button_settings'                           => 'text_icon_',
			'cookie_button_shape'                              => 'rounded',
			'cookie_button_border_color'                       => '#ffffff',
			'cookie_button_text_color'                         => '#ffffff',
			'cookie_button_bg_color'                           => '#ff7d27',
			'cookie_button_size'                               => 'normal',
			'cookie_gear_icon_position'                        => 'bottom_left_',
			'cookie_gear_icon_color'                           => '#ffffff',
			'cookie_trigger_modal_bg_shape'                    => 'round',
			'cookie_trigger_modal_bg'                          => '#000000',
			'cookie_trigger_modal_text'                        => esc_html__( 'Trigger', 'ct-ultimate-gdpr' ),
			'cookie_trigger_modal_icon'                        => 'fa fa-cog',
			'cookie_settings_trigger'                          => 'icon_only_',
			'cookie_cookies_group_default'                     => $this->get_default_group_level(),
			'cookie_group_popup_header_content'                => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'cookie-group-popup-header-content', false ) ),
			'cookie_group_popup_label_will'                    => esc_html__( 'This website will:', 'ct-ultimate-gdpr' ),
			'cookie_group_popup_label_wont'                    => esc_html__( "This website won't:", 'ct-ultimate-gdpr' ),
			'cookie_group_popup_label_save'                    => esc_html__( "Save & Close", 'ct-ultimate-gdpr' ),
			'cookie_group_popup_label_block_all'               => esc_html__( 'Block all', 'ct-ultimate-gdpr' ),
			'cookie_group_popup_label_essentials'              => esc_html__( 'Essentials', 'ct-ultimate-gdpr' ),
			'cookie_group_popup_label_functionality'           => esc_html__( 'Functionality', 'ct-ultimate-gdpr' ),
			'cookie_group_popup_label_analytics'               => esc_html__( 'Analytics', 'ct-ultimate-gdpr' ),
			'cookie_group_popup_label_advertising'             => esc_html__( 'Advertising', 'ct-ultimate-gdpr' ),
			'cookie_group_popup_features_available_group_2'    => esc_html__( "Essential: Remember your cookie permission setting; Essential: Allow session cookies; Essential: Gather information you input into a contact forms, newsletter and other forms across all pages; Essential: Keep track of what you input in a shopping cart; Essential: Authenticate that you are logged into your user account; Essential: Remember language version you selected;", 'ct-ultimate-gdpr' ),
			'cookie_group_popup_features_nonavailable_group_2' => esc_html__( "Remember your login details; Functionality: Remember social media settings; Functionality: Remember selected region and country; Analytics: Keep track of your visited pages and interaction taken; Analytics: Keep track about your location and region based on your IP number; Analytics: Keep track of the time spent on each page; Analytics: Increase the data quality of the statistics functions; Advertising: Tailor information and advertising to your interests based on e.g. the content you have visited before. (Currently we do not use targeting or targeting cookies.; Advertising: Gather personally identifiable information such as name and location;", 'ct-ultimate-gdpr' ),
			'cookie_group_popup_features_available_group_3'    => esc_html__( "Essential: Remember your cookie permission setting; Essential: Allow session cookies; Essential: Gather information you input into a contact forms, newsletter and other forms across all pages; Essential: Keep track of what you input in a shopping cart; Essential: Authenticate that you are logged into your user account; Essential: Remember language version you selected; Functionality: Remember social media settings; Functionality: Remember selected region and country;", 'ct-ultimate-gdpr' ),
			'cookie_group_popup_features_nonavailable_group_3' => esc_html__( "Remember your login details; Analytics: Keep track of your visited pages and interaction taken; Analytics: Keep track about your location and region based on your IP number; Analytics: Keep track of the time spent on each page; Analytics: Increase the data quality of the statistics functions; Advertising: Tailor information and advertising to your interests based on e.g. the content you have visited before. (Currently we do not use targeting or targeting cookies.; Advertising: Gather personally identifiable information such as name and location;", 'ct-ultimate-gdpr' ),
			'cookie_group_popup_features_available_group_4'    => esc_html__( "Essential: Remember your cookie permission setting; Essential: Allow session cookies; Essential: Gather information you input into a contact forms, newsletter and other forms across all pages; Essential: Keep track of what you input in a shopping cart; Essential: Authenticate that you are logged into your user account; Essential: Remember language version you selected; Functionality: Remember social media settings; Functionality: Remember selected region and country; Analytics: Keep track of your visited pages and interaction taken; Analytics: Keep track about your location and region based on your IP number; Analytics: Keep track of the time spent on each page; Analytics: Increase the data quality of the statistics functions;", 'ct-ultimate-gdpr' ),
			'cookie_group_popup_features_nonavailable_group_4' => esc_html__( "Remember your login details; Advertising: Use information for tailored advertising with third parties; Advertising: Allow you to connect to social sites; Advertising: Identify device you are using; Advertising: Gather personally identifiable information such as name and location", 'ct-ultimate-gdpr' ),
			'cookie_group_popup_features_available_group_5'    => esc_html__( "Essential: Remember your cookie permission setting; Essential: Allow session cookies; Essential: Gather information you input into a contact forms, newsletter and other forms across all pages; Essential: Keep track of what you input in a shopping cart; Essential: Authenticate that you are logged into your user account; Essential: Remember language version you selected; Functionality: Remember social media settings; Functionality: Remember selected region and country; Analytics: Keep track of your visited pages and interaction taken; Analytics: Keep track about your location and region based on your IP number; Analytics: Keep track of the time spent on each page; Analytics: Increase the data quality of the statistics functions; Advertising: Use information for tailored advertising with third parties; Advertising: Allow you to connect to social sitesl Advertising: Identify device you are using; Advertising: Gather personally identifiable information such as name and location", 'ct-ultimate-gdpr' ),
			'cookie_group_popup_features_nonavailable_group_5' => esc_html__( "Remember your login details", 'ct-ultimate-gdpr' ),
			'cookie_group_popup_features_available_group_2_individual' => esc_html__( "Essential: Remember your cookie permission setting; Essential: Allow session cookies; Essential: Gather information you input into a contact forms, newsletter and other forms across all pages; Essential: Keep track of what you input in a shopping cart; Essential: Authenticate that you are logged into your user account; Essential: Remember language version you selected;", 'ct-ultimate-gdpr' ),
			'cookie_group_popup_features_available_group_3_individual' => esc_html__( "Functionality: Remember social media settings; Functionality: Remember selected region and country;", 'ct-ultimate-gdpr' ),
			'cookie_group_popup_features_available_group_4_individual' => esc_html__( "Analytics: Keep track of your visited pages and interaction taken; Analytics: Keep track about your location and region based on your IP number; Analytics: Keep track of the time spent on each page; Analytics: Increase the data quality of the statistics functions;", 'ct-ultimate-gdpr' ),
			'cookie_group_popup_features_available_group_5_individual' => esc_html__( "Advertising: Tailor information and advertising to your interests based on e.g. the content you have visited before. (Currently we do not use targeting or targeting cookies.; Advertising: Gather personally identifiable information such as name and location;", 'ct-ultimate-gdpr' ),
			'cookie_modal_header_color'                        => "#595959",
			'cookie_modal_text_color'                          => "#797979",
			'cookie_modal_skin'                                => "default",
			'cookie_protection_shortcode_label'                => esc_html__( "This content requires cookies", 'ct-ultimate-gdpr' ),
			'cookie_my_account_disclaimer'                     => esc_html__( "Removal of your data will not limit in any way the system functionalities", 'ct-ultimate-gdpr' ),
			'cookie_withdrawal_cookies_agreement'			   => esc_html__( "Withdrawal Cookie Agreement", 'ct-ultimate-gdpr' ),
			'cookie_withdrawal_cookies_agreement_description'  => esc_html__( "Decide to block all cookies which we're using. You can change these settings at any time. Learn more about the cookies we use.", 'ct-ultimate-gdpr' ),
            'cookie_withdrawal_cookies_agreement_button_bg_color'        => '#ff7d27',
            'cookie_withdrawal_cookies_agreement_button_text_color'      => '#ffffff',
            'cookie_withdrawal_cookies_agreement_button_border_color'    => '#ff7d27',
			'render_field_cookie_disable_google_fonts'			=> false,
            ) );

	}

	/**
	 *
	 * @param $language
	 * @param string $option_name
	 * @param bool $as_json
	 *
	 * @return mixed
	 */
	public function get_default_option_text( $language = '', $option_name = '', $as_json = true ) {
		
		$textArray = array();

		if ( ! $language ) {
			$language = ct_ultimate_gdpr_get_value( 'language', $_POST, '' );
			if ( $language == '' ) { //do not override options when user has selected "Select" in dropdown.
				exit;
			}

			// $textArray = text array from specific language
			$dir = plugin_dir_path( __DIR__ );
			require_once( $dir . 'locale/' . $language . '.php' );

			if ( $option_name ) {

				$textArray = isset( $textArray[ $option_name ] ) ? $textArray[ $option_name ]: '';
	
			}

			if ( $as_json ) {
				wp_send_json( $textArray );
				exit;
			}
	
			return $textArray;

		}

		return $textArray;
	}

	/**
	 * Default EU cookie templates
	 *
	 * @return mixed
	 */
	private function get_default_cookie_content() {

		$default_template_array = apply_filters( 'ct_ultimate_gdpr_controller_cookie_default_cookie_content_templates',
			array(
				'ru' => 'Инфомация о<br>Для того, чтобы данный сайт работал нормально, мы иногда размещаем небольшие файлы, которые называются cookies на вашем устройстве. Многие крупные сайты поступают точно также.',
				'en' => 'Cookies<br>To make this site work properly, we sometimes place small data files called cookies on your device. Most big websites do this too.',
				'cs' => 'Cookies<br>Cookies jsou malé datové soubory, které jsou nezbytné pro správnou funkci stránek, a které proto někdy umísťujeme na váš počítač , tak jako ostatně většina velkých internetových stránek.',
				'ds' => 'Cookies<br>For at få vores website til at fungere bedst muligt lægger vi sommetider små datafiler, såkaldte cookies, på din computer. Det gør de fleste store websites.',
				'de' => 'Cookies<br>Damit dieses Internetportal ordnungsgemäß funktioniert, legen wir manchmal kleine Dateien – sogenannte Cookies – auf Ihrem Gerät ab. Das ist bei den meisten großen Websites üblich.',
				'el' => 'Cookies<br>Για να εξασφαλίσουμε τη σωστή λειτουργία του ιστότοπου, μερικές φορές τοποθετούμε μικρά αρχεία δεδομένων στον υπολογιστή σας, τα λεγόμενα «cookies». Οι περισσότεροι μεγάλοι ιστότοποι κάνουν το ίδιο.',
				'es' => 'Cookies<br>Para que este sitio funcione adecuadamente, a veces instalamos en los dispositivos de los usuarios pequeños ficheros de datos, conocidos como cookies. La mayoría de los grandes sitios web también lo hacen.',
				'et' => 'Küpsised<br>Veebisaidi nõuetekohaseks toimimiseks salvestame mõnikord teie seadmesse väikseid andmefaile – nn küpsiseid. Enamik suurtest veebisaitidest teeb seda samuti.',
				'fi' => 'Evästeet<br>Jotta tämä sivusto toimisi mahdollisimman hyvin, se tallentaa aika ajoin laitteellesi pieniä datatiedostoja, joita kutsutaan evästeiksi (cookies). Tämä on yleinen käytäntö useimmissa isoissa verkkosivustoissa.',
				'fr' => 'Cookies<br>Pour assurer le bon fonctionnement de ce site, nous devons parfois enregistrer de petits fichiers de données sur l\'équipement de nos utilisateurs.  La plupart des grands sites web font de même.',
				'ga' => 'Fianáin<br>Le go n-oibreoidh an suíomh seo i gceart, is minic a chuirimid comhaid bheaga sonraí, ar a dtugtar fianáin, ar do ghléas ríomhaireachta. Déanann an chuid is mó de na suíomhanna móra amhlaidh freisin.',
				'hr' => 'Kolačići<br>Kako bi se osigurao ispravan rad ovih web-stranica, ponekad na vaše uređaje pohranjujemo male podatkovne datoteke poznate pod nazivom kolačići. Isto čini i većina velikih web-mjesta.',
				'hu' => 'Sütik<br>E honlap megfelelő működéséhez néha „sütiknek” nevezett adatfájlokat (angolul: cookie) kell elhelyeznünk számítógépén, ahogy azt más nagy webhelyek és internetszolgáltatók is teszik.',
				'it' => 'Cookies<br>Per far funzionare bene questo sito, a volte installiamo sul tuo dispositivo dei piccoli file di dati che si chiamano "cookies". Anche la maggior parte dei grandi siti fanno lo stesso.',
				'lt' => 'Slapukai<br>Kad ši svetainė tinkamai veiktų, kartais į jūsų įrenginį ji įrašo mažas duomenų rinkmenas, vadinamuosius slapukus. Tą patį daro ir dauguma didžiųjų interneto svetainių.',
				'lv' => 'Sīkdatnes<br>Lai šī vietne pienācīgi darbotos, mēs dažreiz jūsu ierīcē izvietojam sīkdatnes (cookies). Tāpat dara arī lielākā daļa lielo tīmekļa vietņu.',
				'mt' => 'Cookies<br>Biex dan is-sit web jaħdem sew, xi drabi nqiegħdu fajls żgħar ta’ dejta fuq l-apparat tiegħek, magħrufin bħala cookies. Il-biċċa l-kbira tas-siti jagħmlu dan ukoll.',
				'nl' => 'Cookies<br>Om deze website goed te laten werken, moeten we soms kleine bestanden op uw computer zetten, zogenaamde cookies. De meeste grote websites doen dit.',
				'pl' => 'Pliki cookie<br>Aby zapewnić sprawne funkcjonowanie tego portalu, czasami umieszczamy na komputerze użytkownika (bądź innym urządzeniu) małe pliki – tzw. cookies („ciasteczka”). Podobnie postępuje większość dużych witryn internetowych.',
				'pt' => 'Cookies (testemunhos de conexão)<br>Tal como a maioria dos grandes sítios Web, para que o nosso sítio possa funcionar corretamente, instalamos pontualmente no seu computador ou dispositivo móvel pequenos ficheiros denominados cookies ou testemunhos de conexão.',
				'ro' => 'Cookie-urile<br>Pentru a asigura buna funcționare a acestui site, uneori plasăm în computerul dumneavoastră mici fișiere cu date, cunoscute sub numele de cookie-uri. Majoritatea site-urilor mari fac acest lucru.',
				'sk' => 'Súbory cookie<br>S cieľom zabezpečiť riadne fungovanie tejto webovej lokality ukladáme niekedy na vašom zariadení malé dátové súbory, tzv. cookie. Je to bežná prax väčšiny veľkých webových lokalít.',
				'sl' => 'Piškotki<br>Za pravilno delovanje tega spletišča se včasih na vašo napravo naložijo majhne podatkovne datoteke, imenovane piškotki. To se zgodi na večini večjih spletišč.',
				'sv' => 'Kakor (”cookies”)<br>För att få den här webbplatsen att fungera ordentligt skickar vi ibland små filer till din dator. Dessa filer kallas kakor eller ”cookies”. De flesta större webbplatser gör på samma sätt.',
				'dk' => 'Cookies <br> For at gøre dette websted korrekt, placerer vi nogle gange små datafiler kaldet cookies på din enhed. De fleste store hjemmesider gør det også.',
                'bg' => 'Засечени<br>За да направите този сайт работи правилно, ние понякога Поставете малък данни пила, наречена "бисквитки" на вашето устройство. Най-големите сайтове направите това също.',
                'sv' => 'Smakakor<br>För att denna webbplats ska fungera korrekt placerar vi ibland sma datafiler som kallas cookies pa din enhet. De flesta stora webbplatser gör det ocksa.',
                'no' => 'Cookies<br>Noen cookies brukes til statistikk, og andre er lagt inn av tredjepartstjenester. Ved a klikke \'OK\' aksepterer du bruk av cookies.',
			)
		);

		$lang = substr( get_locale(), 0, 2 );

		return isset( $default_template_array[ $lang ] ) ? $default_template_array[ $lang ] : $default_template_array['en'];
	}

	/**
	 * @param $group_level
	 *
	 * @return mixed
	 */
	private function is_group_level_allow_all( $group_level ) {
		return apply_filters( 'ct_ultimate_gpdr_cookie_group_level_allow_all', $group_level >= CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING );
	}

	/**
	 * @return int
	 */
	private function get_default_group_level() {
		return apply_filters( 'ct_ultimate_gdpr_controller_cookie_default_group_level', CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY );
	}

	/**
	 * @return array
	 */
	public function get_all_options() {
		return $this->options;
	}

	/**
	 *
	 */
	public function cookie_check_cron() {
		add_filter( 'cron_schedules', array( $this, 'add_cron_schedules' ) );
		if ( ! wp_get_schedule( 'ct_ultimate_gdpr_controller_cookie_check' ) ) {
			$admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
			$field_name  = $admin->get_field_name( 'render_field_cookie_scan_period' );
			$field_value = $admin->get_option_value( $field_name, false, self::ID );
			if ( $field_value != 'manual' ) {
				wp_schedule_event( time(), $field_value, 'ct_ultimate_gdpr_controller_cookie_check' );
			} else {
				wp_clear_scheduled_hook( 'ct_ultimate_gdpr_controller_cookie_check' );
			}
		}
	}

	/**
	 * @param array $schedules
	 *
	 * @return array
	 */
	public function add_cron_schedules( $schedules ) {
		$schedules['ct-ultimate-gdpr-weekly']    = array(
			'interval' => 7 * 24 * 60 * 60,
			'display'  => __( 'Every week', 'ct-ultimate-gdpr' )
		);
		$schedules['ct-ultimate-gdpr-monthly']   = array(
			'interval' => 4 * 7 * 24 * 60 * 60,
			'display'  => __( 'Every month', 'ct-ultimate-gdpr' )
		);
		$schedules['ct-ultimate-gdpr-quarterly'] = array(
			'interval' => 4 * 7 * 24 * 60 * 60,
			'display'  => __( 'Every quarter', 'ct-ultimate-gdpr' )
		);

		return $schedules;
	}

	/**
	 * Button or cron activated
	 */
	/**
	 * Button or cron activated
	 */
	public function scan_cookies() {

		if ( function_exists( 'acf' ) ) {

			// get response from cookie scanner api
			$api_response = $this->get_cookie_scanner_response();
			$this->process_scanned_cookies( $api_response );

		}
	}

	/**
	 * Parse single api response and save new cookies, display messages
	 *
	 * @param object $response
	 */
	public function process_scanned_cookies( $response = null ) {

		$site_url               = get_site_url();
		$update_database_object = new CT_Ultimate_GDPR_Update_Legacy_Options();
		$registered_cookies     = $update_database_object->get_registered_cookies_names( false );
		$services               = CT_Ultimate_GDPR_Model_Services::instance()->get_services();
		$all_cookies            = $response->cookies;
		$cookies_detected       = array();
		$cookies_saved          = array();

		// add local storage
		if ( ! empty( $response->localStorage ) ) {
			$all_cookies = array_merge( $all_cookies, $response->localStorage );
		}

		// add session storage
		if ( ! empty( $response->sessionStorage ) ) {
			$all_cookies = array_merge( $all_cookies, $response->sessionStorage );
		}

		// remove doubled entries (better not use names as key indexes)
		$cookies = array();
		foreach ( $all_cookies as $all_cookie ) {

			// check if doubled
			$doubled = false;
			foreach ( $cookies as $cookie ) {

				if ( isset( $cookie->name ) && $all_cookie->name == $cookie->name ) {
					$doubled = true;
					break;
				}

			}

			// if not doubled, then add
			if ( ! $doubled ) {
				$cookies[] = $all_cookie;
			}

		}

		foreach ( $cookies as $cookie ) {

			$inserted = $this->insert_post( $cookie, $registered_cookies, $site_url, $services );
			if ( $inserted ) {
				$cookies_saved[] = isset($cookie->name) ? $cookie->name : $cookie;
			}
			$cookies_detected[] = isset($cookie->name) ? $cookie->name : $cookie;

		}

		if ( ! empty( $response->message ) ) {

			if ( wp_doing_ajax() ) {

				wp_send_json( array(
					'success' => false,
					'message' => $response->message,
					'log'     => $response->log,
				) );

			}

			$this->add_view_option(
				! empty( $response->error ) ? 'errors' : 'warnings',
				array( $response->message )
			);

		} else {

			if ( wp_doing_ajax() ) {

				// send success message
				wp_send_json(
					array(
						'success' => true,
						'message' => sprintf(
							esc_html__( 'Cookies detected: %s, Cookies saved: %s', 'ct-ultimate-gdpr' ),
							count($cookies_detected) ? implode( ', ', $cookies_detected ) : 'N/A',
							count($cookies_saved) ? implode( ', ', $cookies_saved ) : 'N/A'
						)
					)
				);

			}

			$this->add_view_option(
				'notices',
				array(
					sprintf(
						__( "<h3>Please navigate to <a href='%s'>Service Manager</a> tab to see all detected cookies.</h3>", 'ct-ultimate-gdpr' ),
						$this->get_cookie_manager_url()
					)
				)
			);

		}

	}

	/**
	 * @return string
	 */
	private function get_cookie_manager_url() {
		return admin_url( '/edit.php?post_type=ct_ugdpr_service' );
	}

	/**
	 * @return object
	 */
	private function get_cookie_scanner_response() {

		$site_url = ct_ultimate_gdpr_get_value( 'url', $this->get_request_array(), get_site_url() );
		$this->create_dummy_user();
		$response = wp_remote_get( $site_url, array(
			'timeout' => 60,
		) );
		$this->delete_dummy_user();
		$json = is_array( $response ) ? $response['body'] : false;

		// bad json example
		//$json            = '{"result":{"error":false,"cookies":{"cookies":[{"name":"Gtest","value":"KlShCRMGQMQGSmYfdn1782R5ssGMXP8c25nSG4-fqP47M5aSEvi6wv9iMG..","domain":".hit.gemius.pl","path":"/","expires":1680998401.041616,"size":65,"httpOnly":false,"secure":false,"session":false},{"name":"adfmuid","value":"1594381673071934114","domain":".wp.pl","path":"/","expires":1534677802.922246,"size":26,"httpOnly":false,"secure":false,"session":false},{"name":"__gfp_64b","value":"swl3TzeJDEb48wX8xHzExhZqBmrcF1ninBQQygJ6JEX.g7","domain":".wp.pl","path":"/","expires":1615893802,"size":55,"httpOnly":false,"secure":false,"session":false},{"name":"PWA_adbd","value":"0","domain":".wp.pl","path":"/","expires":1592565802,"size":9,"httpOnly":false,"secure":false,"session":false},{"name":"Gdyn","value":"KlSV9MaGQMQGSmYfdn1782R5ssGMu1mcLvnxmGBJp0hZlJrxssWMAvofwyjSssXAjmlGvGGpMfIsSLx8RgTSDsCB6qlBaQG.","domain":".hit.gemius.pl","path":"/","expires":1680998400.44575,"size":100,"httpOnly":false,"secure":false,"session":false},{"name":"uid","value":"1594381673071934114","domain":".adform.net","path":"/","expires":1534677801.910189,"size":22,"httpOnly":false,"secure":false,"session":false},{"name":"fr","value":"0DFewqhapS5gp7CKy..BbKjkp...1.0.BbKjkp.","domain":".facebook.com","path":"/","expires":1537269801.738232,"size":41,"httpOnly":true,"secure":true,"session":false},{"name":"STpage","value":"sg:https%3A%2F%2Fwww.wp.pl%2F:1529493801:1686ba2e1aad5c3d2c28:v1","domain":".wp.pl","path":"/","expires":1529580201.430742,"size":70,"httpOnly":false,"secure":false,"session":false},{"name":"STvisit","value":"7c336f32a409f5476733d81660082449:d07168:1529493801:1529493801:v1","domain":".wp.pl","path":"/","expires":1529580201.225412,"size":71,"httpOnly":false,"secure":false,"session":false},{"name":"c","value":"1","domain":".www.wp.pl","path":"/","expires":1529532001.133711,"size":2,"httpOnly":false,"secure":false,"session":false},{"name":"STWP","value":"1","domain":".wp.pl","path":"/","expires":1529753001.225394,"size":5,"httpOnly":false,"secure":false,"session":false},{"name":"apnxuid","value":"0","domain":".wp.pl","path":"/","expires":1529580201,"size":8,"httpOnly":false,"secure":false,"session":false},{"name":"ust","value":"qlZKSSxJVbJSMjIwtDAwMzJQ0lHKLEnNLVayiq5WKlOyMtRRKgBRtTpgrgGEa0BNbmwtAAAA//8BAAD//w==","domain":".wp.pl","path":"/","expires":1530703401.225386,"size":87,"httpOnly":false,"secure":false,"session":false},{"name":"BDh","value":"qlYyMjC0MDBTsqpWMk1JTDUxslSyMqytBQAAAP//AQAA//8=","domain":".wp.pl","path":"/","expires":1624101801.225406,"size":51,"httpOnly":false,"secure":false,"session":false},{"name":"pvid","value":"1686ba2e1aad5c3d2c28","domain":"www.wp.pl","path":"/","expires":-1,"size":24,"httpOnly":false,"secure":false,"session":true},{"name":"","value":"","domain":"www.facebook.com","path":"/tr","expires":-1,"size":0,"httpOnly":false,"secure":false,"session":true},{"name":"BDseg","value":"light","domain":".wp.pl","path":"/","expires":1624101801.225379,"size":10,"httpOnly":false,"secure":false,"session":false},{"name":"WPab","value":"PrebidTestD","domain":".wp.pl","path":"/","expires":-1,"size":15,"httpOnly":false,"secure":false,"session":true},{"name":"gusid","value":"9e5b8215d63dd7c42f2332cc3076b5cb","domain":".wp.pl","path":"/","expires":1624101801.2254,"size":37,"httpOnly":false,"secure":false,"session":false},{"name":"fiv","value":"4.2.19","domain":"www.wp.pl","path":"/","expires":1529494699.355712,"size":9,"httpOnly":true,"secure":false,"session":false},{"name":"statid","value":"39e7f88b70d481dd58ffa0c0b9fa81cd:becb6b:1529493801:v3","domain":".wp.pl","path":"/","expires":1624101801.225341,"size":59,"httpOnly":false,"secure":false,"session":false},{"name":"sgv","value":"1529493799","domain":".wp.pl","path":"/","expires":1561029800.133673,"size":13,"httpOnly":false,"secure":false,"session":false}]},"message":""}}';

		// good json example
		//$json = '{"result":{"error":false,"cookies":[{"name":"gcma","value":"%7B%22t%22%3A1529497196757%2C%22o%22%3Afalse%7D","domain":".yieldoptimizer.com","path":"/","expires":1592569194.754683,"size":51,"httpOnly":false,"secure":false,"session":false},{"name":"ph","value":"%7B%22p%22%3A%5B39%5D%2C%22t%22%3A%5B82980%5D%7D","domain":".yieldoptimizer.com","path":"/","expires":1592569194.702148,"size":50,"httpOnly":false,"secure":false,"session":false},{"name":"dph","value":"%7B%22t%22%3A%5B82980%5D%2C%22dp%22%3A%5B1374%5D%7D","domain":".yieldoptimizer.com","path":"/","expires":1592569194.702138,"size":54,"httpOnly":false,"secure":false,"session":false},{"name":"cktst","value":"402738159","domain":".yieldoptimizer.com","path":"/","expires":1592569194.68875,"size":14,"httpOnly":false,"secure":false,"session":false},{"name":"TDCPM","value":"CAEYBSABKAIyCwjWmK7F26yrNhAFOAE.","domain":".adsrvr.org","path":"/","expires":1561033194.3735,"size":37,"httpOnly":false,"secure":false,"session":false},{"name":"CMPS","value":"2437","domain":".casalemedia.com","path":"/","expires":1537273194.321361,"size":8,"httpOnly":false,"secure":false,"session":false},{"name":"CMID","value":"WypGatHM4XcAAFJOzp0AAABF","domain":".casalemedia.com","path":"/","expires":1561033194.321317,"size":28,"httpOnly":false,"secure":false,"session":false},{"name":"_cc_aud","value":"\"ABR4nGNgYGCI1nLLYoADAA8EATY%3D\"","domain":".crwdcntrl.net","path":"/","expires":1552825194.188513,"size":39,"httpOnly":false,"secure":false,"session":false},{"name":"_cc_dc","value":"3","domain":".crwdcntrl.net","path":"/","expires":1552825194.188448,"size":7,"httpOnly":false,"secure":false,"session":false},{"name":"sa_aud_cmp","value":"","domain":"p.travelsmarter.net","path":"/","expires":1529497203.959212,"size":10,"httpOnly":false,"secure":false,"session":false},{"name":"ckid","value":"109717120173","domain":".yieldoptimizer.com","path":"/","expires":1592569194.754643,"size":16,"httpOnly":false,"secure":false,"session":false},{"name":"TART","value":"%1%enc%3A%2BCV3oHg3GhD4BdkDC0ZKn7D5YsTmwBvdHn4hdAC4bo%2FGZDgFya%2BooV5JfL875qGBAzD7p3ki7eg%3D","domain":".www.tripadvisor.it","path":"/","expires":1529929192.654729,"size":97,"httpOnly":true,"secure":false,"session":false},{"name":"TAUD","value":"LA-1529497192590-1*RDD-1-2018_06_20","domain":".tripadvisor.it","path":"/","expires":1530706792.65477,"size":39,"httpOnly":false,"secure":false,"session":false},{"name":"_cc_id","value":"96c91d5365660dc975f0a6bea5f9c72d","domain":".crwdcntrl.net","path":"/","expires":1552825194.188489,"size":38,"httpOnly":false,"secure":false,"session":false},{"name":"TDID","value":"591ee95e-6d25-4e0c-9c3e-add3d3920e82","domain":".adsrvr.org","path":"/","expires":1561033194.373454,"size":40,"httpOnly":false,"secure":false,"session":false},{"name":"TACds","value":"C.1.11900.2.2018-06-19","domain":".tripadvisor.it","path":"/","expires":-1,"size":27,"httpOnly":false,"secure":false,"session":true},{"name":"TAUnique","value":"%1%enc%3AbXlcTQRhkcH4JXegeDcaEOIVkj9KnQJ5sLRhLbuR2mUpVON0bfBQhw%3D%3D","domain":".tripadvisor.it","path":"/","expires":1592569191.92054,"size":77,"httpOnly":true,"secure":false,"session":false},{"name":"uid","value":"72d3210d-9b14-4be5-b451-8529301e0487","domain":".criteo.com","path":"/","expires":1561033193.857901,"size":39,"httpOnly":false,"secure":false,"session":false},{"name":"PMC","value":"V2*MS.69*MD.20180620*LD.20180620","domain":".www.tripadvisor.it","path":"/","expires":1592569192.654717,"size":35,"httpOnly":true,"secure":true,"session":false},{"name":"CM","value":"%1%pu_vr2%2C%2C-1%7CPremiumMobSess%2C%2C-1%7Ct4b-pc%2C%2C-1%7CSPHRSess%2C%2C-1%7CRestAds%2FRPers%2C%2C-1%7CRCPers%2C%2C-1%7CWShadeSeen%2C%2C-1%7Cpu_vr1%2C%2C-1%7CTheForkMCCPers%2C%2C-1%7CPremiumSURPers%2C%2C-1%7Ccatchsess%2C1%2C-1%7Cbrandsess%2C%2C-1%7CRestPremRSess%2C%2C-1%7CCCSess%2C%2C-1%7Csesssticker%2C%2C-1%7C%24%2C%2C-1%7Ct4b-sc%2C%2C-1%7CMC_IB_UPSELL_IB_LOGOS2%2C%2C-1%7Cb2bmcpers%2C%2C-1%7CMC_IB_UPSELL_IB_LOGOS%2C%2C-1%7CPremMCBtmSess%2C%2C-1%7CPremiumSURSess%2C%2C-1%7Csess_rev%2C%2C-1%7Csessamex%2C%2C-1%7CPremiumRRSess%2C%2C-1%7CSPMCSess%2C%2C-1%7CTheForkORSess%2C%2C-1%7CTheForkRRSess%2C%2C-1%7Cpers_rev%2C%2C-1%7CRestAds%2FRSess%2C%2C-1%7C+r_lf_1%2C%2C-1%7CPremiumMobPers%2C%2C-1%7CSPHRPers%2C%2C-1%7CRCSess%2C%2C-1%7C+r_lf_2%2C%2C-1%7Ccatchpers%2C%2C-1%7CRestAdsCCSess%2C%2C-1%7CRestPremRPers%2C%2C-1%7Cvr_npu2%2C%2C-1%7CLastPopunderId%2C104-771-null%2C-1%7Cpssamex%2C%2C-1%7CTheForkMCCSess%2C%2C-1%7Cvr_npu1%2C%2C-1%7CCCPers%2C%2C-1%7Cbrandpers%2C%2C-1%7Cb2bmcsess%2C%2C-1%7CSPMCPers%2C%2C-1%7CWarPopunder_Session%2C%2C-1%7CPremiumRRPers%2C%2C-1%7CRestAdsCCPers%2C%2C-1%7CWarPopunder_Persist%2C%2C-1%7CTheForkORPers%2C%2C-1%7Cr_ta_2%2C%2C-1%7CPremMCBtmPers%2C%2C-1%7CTheForkRRPers%2C%2C-1%7Cr_ta_1%2C%2C-1%7CSPORPers%2C%2C-1%7Cperssticker%2C%2C-1%7CCPNC%2C%2C-1%7C","domain":".tripadvisor.it","path":"/","expires":1844857192.654749,"size":1284,"httpOnly":false,"secure":false,"session":false},{"name":"SRT","value":"TART_SYNC","domain":".www.tripadvisor.it","path":"/","expires":-1,"size":12,"httpOnly":false,"secure":false,"session":true},{"name":"sa_dmp_synced","value":"1_1529497194,2_1529497193,3_1529497194","domain":"p.travelsmarter.net","path":"/","expires":1532089194.629478,"size":51,"httpOnly":false,"secure":false,"session":false},{"name":"YSC","value":"zkicIRVd2Do","domain":".youtube.com","path":"/","expires":-1,"size":14,"httpOnly":true,"secure":false,"session":true},{"name":"ab","value":"0001%3AGijIbGgGZTrF1CKMIE%2FzvbEeJP9yj9cv","domain":".agkn.com","path":"/","expires":1561033194.544059,"size":43,"httpOnly":false,"secure":false,"session":false},{"name":"TASession","value":"V2ID.6722656A5216EE867AC154A8E95CCE25*SQ.2*LS.WidgetEmbed-selfserveprop*GR.22*TCPAR.77*TBR.90*EXEX.41*ABTR.83*PHTB.91*FS.74*CPU.15*HS.recommended*ES.popularity*AS.popularity*DS.5*SAS.popularity*FPS.oldFirst*FA.1*DF.0*TRA.true","domain":".tripadvisor.it","path":"/","expires":-1,"size":234,"httpOnly":false,"secure":false,"session":true},{"name":"st_browser_id","value":"87f4a027-053e-43a5-8bc1-2928c3d9408f","domain":".travelsmarter.net","path":"/","expires":1844857193.959177,"size":49,"httpOnly":false,"secure":false,"session":false},{"name":"IDE","value":"AHWqTUkiq7en4TM0f2NrRrgjbkzM6EOyOHfO64aB0XI5Wzxy81h9m1ZZUFZLAQIf","domain":".doubleclick.net","path":"/","expires":1592569193.357423,"size":67,"httpOnly":true,"secure":false,"session":false},{"name":"VISITOR_INFO1_LIVE","value":"h1_clnqbIQ0","domain":".youtube.com","path":"/","expires":1545049192.253937,"size":29,"httpOnly":true,"secure":false,"session":false},{"name":"TASSK","value":"enc%3AAKWkBuov879gX17eYe%2FSYTxn6BnzwIWh4Izkus42SAnGAkwKKYqUIT%2FzYlb7jUnNZHuhU%2Fw1v3TuTLvgCCaI%2Fd3JQPkY21dUo15sTehgJTwtiKD94oicxOQvQqjkYbzdTg%3D%3D","domain":".www.tripadvisor.it","path":"/","expires":1545049192.654653,"size":155,"httpOnly":true,"secure":false,"session":false},{"name":"CMPRO","value":"1082","domain":".casalemedia.com","path":"/","expires":1537273194.321372,"size":9,"httpOnly":false,"secure":false,"session":false},{"name":"_cc_cc","value":"\"ACZ4nGNQsDRLtjRMMTU2MzUzM0hJtjQ3TTNINEtKTTRNs0w2N0phAIJoLbcsBgQAAEk4Cio%3D\"","domain":".crwdcntrl.net","path":"/","expires":1552825194.188501,"size":82,"httpOnly":false,"secure":false,"session":false},{"name":"ServerPool","value":"C","domain":".tripadvisor.it","path":"/","expires":-1,"size":11,"httpOnly":false,"secure":false,"session":true},{"name":"GPS","value":"1","domain":".youtube.com","path":"/","expires":1529498992.253958,"size":4,"httpOnly":false,"secure":false,"session":false},{"name":"TATravelInfo","value":"V2*A.2*MG.-1*HP.2*FL.3*RS.1","domain":".tripadvisor.it","path":"/","expires":1530706792.65474,"size":39,"httpOnly":false,"secure":false,"session":false},{"name":"__cfduid","value":"d9e9e33cd2d660ea9d2ed9794d66660411529497190","domain":".lodirizzini.altervista.org","path":"/","expires":1561033190.601281,"size":51,"httpOnly":true,"secure":false,"session":false}],"message":""}}';

		$response_object = json_decode( $json );
		$result          = $this->get_validated_scanner_response( $response_object );

		if ( function_exists( 'wc' ) ) {

			$result->cookies[] = (object) array(
				'name'  => 'woocommerce_*',
				'value' => '',
			);

		}
		$result->log = $response;

		return $result;
	}

	/**
	 *
	 */
	private function delete_dummy_user() {

		$user = get_user_by( 'login', $this->dummy_email_address );

		// remove user
		if ( $user->ID ) {

			if ( is_multisite() ) {

				if ( ! function_exists( 'wpmu_delete_user' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/ms.php' );
				}

				wpmu_delete_user( $user->ID );

			} else {

				if ( ! function_exists( 'wp_delete_user' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/user.php' );
				}

				wp_delete_user( $user->ID );

			}

		}

	}

	/**
     * Hook to hide the dummy user from admin view, so it's invisible
     *
	 * @param $user_search
	 */
	public function hide_dummy_user( $user_search ) {
		global $wpdb;
		$num = '1';
		$user_search->query_where = str_replace(
			'WHERE 1='.$num, 
			"WHERE 1=$num AND {$wpdb->users}.user_email != '{$this->dummy_email_address}'", 
			$user_search->query_where
		);
	}

	/**
	 *
	 */
	private function create_dummy_user() {

		if ( ! username_exists( $this->dummy_email_address ) ) {

		    $this->disable_mail_start();

			// Generate the password and create the user
			$password = wp_generate_password( 12, false );
			$user_id  = wp_create_user( $this->dummy_email_address, $password, $this->dummy_email_address );

			// Set the nickname
			wp_update_user(
				array(
					'ID'       => $user_id,
					'nickname' => 'cookie-detector@createit.pl'
				)
			);

			// Set the role
			$user = new WP_User( $user_id );
			$user->set_role( 'contributor' );

			$this->disable_mail_end();

		}

	}

	/**
	 * Add hook
	 */
	private function disable_mail_start(  ) {
		add_action( 'phpmailer_init', array( $this, 'disable_mail' ) );

	}

	/**
	 * Remove hook
	 */
	private function disable_mail_end(  ) {
		remove_action( 'phpmailer_init', array( $this, 'disable_mail' ) );
	}

	/**
     * Set dummy class to avoid sending emails
     *
	 * @param $phpmailer
	 */
	public function disable_mail( &$phpmailer ) {
        $phpmailer = new CT_Ultimate_GDPR_Model_Dummy();
	}

	/**
	 * Log in a dummy user in order to get more cookies omnomnom
	 *
	 * @return bool
	 */
	private function maybe_login_dummy_user() {

		if ( empty( $_GET['ctpass'] ) ) {
			return false;
		}

		if ( is_user_logged_in() ) {
			return true;
		}

		add_filter( 'authenticate', array( $this, 'dummy_user_allow_programmatic_login' ), 100, 3 );
		$user = wp_signon( array( 'user_login' => $this->dummy_email_address ) );
		remove_filter( 'authenticate', array( $this, 'dummy_user_allow_programmatic_login' ), 100 );

		if ( is_a( $user, 'WP_User' ) ) {
			wp_set_current_user( $user->ID, $user->user_login );

			if ( is_user_logged_in() ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * An 'authenticate' filter callback that authenticates the user using only     the username.
	 *
	 * To avoid potential security vulnerabilities, this should only be used in     the context of a programmatic login,
	 * and unhooked immediately after it fires.
	 *
	 * @param WP_User $user
	 * @param string $username
	 * @param string $password
	 *
	 * @return bool|WP_User a WP_User object if the username matched an existing user, or false if it didn't
	 */
	public function dummy_user_allow_programmatic_login( $user, $username, $password ) {
		return get_user_by( 'login', $username );
	}

	/**
	 * @param $response
	 *
	 * @return stdClass
	 */
	private function get_validated_scanner_response( $response ) {

		if ( $response && ! empty( $response->result ) ) {
			$result = $response->result;
		} else {
			$result          = new stdClass();
			$result->message = esc_html__( 'There was a problem with connecting to the cookie scanner. Skipped this url.', 'ct-ultimate-gdpr' );
		}

		if ( ! isset( $result->cookies ) ) {
		    $result->cookies = array();
		    $result->error   = true;
        }

		return $result;

	}

    /**
     * @param string $site_url
     *
     * @param string $license
     * @return string
     */
	public function get_cookie_check_api_url( $site_url, $license ) {
		$api_url = "https://api.gdpr.createit.com/site-scan-wp?license=$license&url=$site_url/?ctpass=1";

		return $api_url;
	}

	/** This is run by the cookie scanner
	 *
	 * @param $cookie
	 * @param $registered_cookies
	 * @param $site_url
	 * @param array $services
	 *
	 * @return bool
	 */
	private
	function insert_post(
		$cookie, $registered_cookies, $site_url, $services
	) {

		if ( ! empty( $cookie->name ) && ! in_array( $cookie->name, $registered_cookies ) ) {

			/** @var CT_Ultimate_GDPR_Service_Abstract $service */
			foreach ( $services as $service ) {

				$service_cookies      = $service->cookies_to_block_filter( CT_Ultimate_GDPR_Update_Legacy_Options::get_empty_cookie_array(), true );
				$service_cookies_flat = array();

				foreach ( $service_cookies as $service_cookie_group => $service_cookie_array ) {

					foreach ( $service_cookie_array as $service_cookie_name ) {
						$service_cookies_flat[ $service_cookie_name ] = $service_cookie_name;
					}

				}

				$found = isset( $service_cookies_flat[ $cookie->name ] );

				// look for wildcards
				if ( ! $found ) {

					foreach ( $service_cookies_flat as $service_cookie ) {

						if ( false === strpos( $service_cookie, '*' ) ) {
							break;
						}

						$needle = str_replace( '*', '', $service_cookie );

						if ( false !== strpos( $cookie->name, $needle ) ) {
							$found = true;
							break;
						}

					}
				}

				// if this cookie exist in predefined service, then add whole service
				if ( $found ) {

					$service_scripts = $service->script_blacklist_filter( CT_Ultimate_GDPR_Update_Legacy_Options::get_empty_cookie_array(), true );
					$service_name    = $service->get_name();
					$service_id      = $service->get_id();

					$updater            = new CT_Ultimate_GDPR_Update_Legacy_Options();
					$registered_cookies = $updater->get_registered_cookies_names( false );
					$registered_scripts = $updater->get_registered_scripts_names();

					$updater->create_cookie_posts( $service_cookies, $registered_cookies, $service_scripts, $registered_scripts, $service_name, $service_id );

					return;

				}

			}

			// if this cookie is unknown, add it as a single post
			$post_id = wp_insert_post(
				array(
					'post_title'  => $cookie->name,
					'post_status' => 'publish',
					'post_type'   => 'ct_ugdpr_service'
				)
			);
			update_field( 'gdpr_id', time(), $post_id );
			update_field( 'cookie_name', $cookie->name, $post_id );
			if ( empty( $cookie->domain ) || strstr( $site_url, $cookie->domain ) ) {
				update_field( 'first_party', 'first_party', $post_id );
				update_field( 'can_be_blocked', true, $post_id );
			} else {
				update_field( 'first_party', 'third_party', $post_id );
				update_field( 'can_be_blocked', false, $post_id );
			}
			if ( isset( $cookie->expires ) && $cookie->expires > 0 ) {
                update_field( 'session_or_persistent', 'persistent', $post_id );
                update_field( 'expiry_time', $cookie->expires, $post_id );
            } else {
				update_field( 'session_or_persistent', 'session', $post_id );
			}
			$default_cookie_level = $this->get_option( 'cookie_default_level_assigned_for_inserted_cookies' );
			update_field( 'type_of_cookie', $default_cookie_level, $post_id );
			update_field( 'is_active', true, $post_id );

			return ( ! ( $post_id instanceof WP_Error ) ) ? true : false;
		}
	}

	/**
	 * Check the last scan cookies
	 */
	public function check_last_cookies_scan(){

		$date = date("d.m.Y");

		update_option( 'ct_gdpr_check_last_cookies_scan', $date );

    }

	// public function checkField() {
	// 	var_dump(get_option('cookie_single_popup'));
		// if( empty($this->ctOptimization['cookie_single_popup'])) {
		// 	return 'opt_disable';
		// }
	// }
 
}