<?php
/**
 * UAEL Helper.
 *
 * @package UAEL
 */

namespace UltimateElementor\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Widget_Base;
use UltimateElementor\Classes\UAEL_Config;

/**
 * Class UAEL_Helper.
 */
class UAEL_Helper {

	/**
	 * A list of safe tage for `validate_html_tag` method.
	 */
	const ALLOWED_HTML_WRAPPER_TAGS = array( 'article', 'aside', 'div', 'footer', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'main', 'nav', 'p', 'section', 'span' );

	/**
	 * CSS files folder
	 *
	 * @var script_debug
	 */
	private static $script_debug = null;

	/**
	 * CSS files folder
	 *
	 * @var uael_debug
	 */
	private static $uae_debug = null;

	/**
	 * CSS files folder
	 *
	 * @var css_folder
	 */
	private static $css_folder = null;

	/**
	 * CSS Suffix
	 *
	 * @var css_suffix
	 */
	private static $css_suffix = null;

	/**
	 * RTL CSS Suffix
	 *
	 * @var rtl_css_suffix
	 */
	private static $rtl_css_suffix = null;

	/**
	 * JS files folder
	 *
	 * @var js_folder
	 */
	private static $js_folder = null;

	/**
	 * JS Suffix
	 *
	 * @var js_suffix
	 */
	private static $js_suffix = null;

	/**
	 * Widget Options
	 *
	 * @var widget_options
	 */
	private static $widget_options = null;

	/**
	 * Skins Options
	 *
	 * @var skins_options
	 */
	private static $skins_options = null;

	/**
	 * Widget List
	 *
	 * @var widget_list
	 */
	private static $widget_list = null;

	/**
	 * Google Map Language List
	 *
	 * @var google_map_languages
	 */
	private static $google_map_languages = null;

	/**
	 * WHite label data
	 *
	 * @var branding
	 */
	private static $branding = null;

	/**
	 * Post Skins List
	 *
	 * @var post_skins_list
	 */
	private static $post_skins_list = null;

	/**
	 * Elementor Saved page templates list
	 *
	 * @var page_templates
	 */
	private static $page_templates = null;

	/**
	 * Elementor saved section templates list
	 *
	 * @var section_templates
	 */
	private static $section_templates = null;

	/**
	 * Elementor saved container templates list
	 *
	 * @var container_templates
	 */
	private static $container_templates = null;

	/**
	 * Elementor saved widget templates list
	 *
	 * @var widget_templates
	 */
	private static $widget_templates = null;

	/**
	 * Provide General settings array().
	 *
	 * @return array()
	 * @since 0.0.1
	 */
	public static function get_widget_list() {

		if ( ! isset( self::$widget_list ) ) {
			self::$widget_list = UAEL_Config::get_widget_list();
		}

		return apply_filters( 'uael_widget_list', self::$widget_list );
	}

	/**
	 * Provide post skins array.
	 *
	 * @return array()
	 * @since 1.21.0
	 */
	public static function get_post_skin_list() {

		self::$post_skins_list = UAEL_Config::get_post_skin_list();

		return apply_filters( 'uael_post_skin_list', self::$post_skins_list );
	}

	/**
	 * Check is script debug enabled.
	 *
	 * @since 0.0.1
	 *
	 * @return string The CSS suffix.
	 */
	public static function is_script_debug() {

		if ( null === self::$script_debug ) {

			self::$script_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
		}

		return self::$script_debug;
	}

	/**
	 * Check is uae debug enabled.
	 *
	 * @since 1.27.0
	 *
	 * @return string The CSS suffix.
	 */
	public static function is_uae_debug() {

		if ( null === self::$uae_debug ) {

			self::$uae_debug = defined( 'UAE_DEBUG' ) && UAE_DEBUG;
		}

		return self::$uae_debug;
	}

	/**
	 * Get CSS Folder.
	 *
	 * @since 0.0.1
	 *
	 * @return string The CSS folder.
	 */
	public static function get_css_folder() {

		if ( null === self::$css_folder ) {

			self::$css_folder = self::is_uae_debug() ? 'css' : 'min-css';
		}

		return self::$css_folder;
	}

	/**
	 * Get CSS suffix.
	 *
	 * @since 0.0.1
	 *
	 * @return string The CSS suffix.
	 */
	public static function get_css_suffix() {

		if ( null === self::$css_suffix ) {

			self::$css_suffix = self::is_uae_debug() ? '' : '.min';
		}

		return self::$css_suffix;
	}

	/**
	 * Get JS Folder.
	 *
	 * @since 0.0.1
	 *
	 * @return string The JS folder.
	 */
	public static function get_js_folder() {

		if ( null === self::$js_folder ) {

			self::$js_folder = self::is_script_debug() ? 'js' : 'min-js';
		}

		return self::$js_folder;
	}

	/**
	 * Get JS Suffix.
	 *
	 * @since 0.0.1
	 *
	 * @return string The JS suffix.
	 */
	public static function get_js_suffix() {

		if ( null === self::$js_suffix ) {

			self::$js_suffix = self::is_script_debug() ? '' : '.min';
		}

		return self::$js_suffix;
	}

	/**
	 *  Get link rel attribute
	 *
	 *  @param string $target Target attribute to the link.
	 *  @param int    $is_nofollow No follow yes/no.
	 *  @param int    $echo Return or echo the output.
	 *  @since 0.0.1
	 *  @return string
	 */
	public static function get_link_rel( $target, $is_nofollow = 0, $echo = 0 ) {

		$attr = '';
		if ( '_blank' === $target ) {
			$attr .= 'noopener';
		}

		if ( 1 === $is_nofollow ) {
			$attr .= ' nofollow';
		}

		if ( '' === $attr ) {
			return;
		}

		$attr = trim( $attr );
		if ( ! $echo ) {
			return 'rel="' . $attr . '"';
		}
		echo 'rel="' . esc_attr( $attr ) . '"';
	}

	/**
	 * Returns an option from the database for
	 * the admin settings page.
	 *
	 * @param  string  $key     The option key.
	 * @param  mixed   $default Option default value if option is not available.
	 * @param  boolean $network_override Whether to allow the network admin setting to be overridden on subsites.
	 * @return string           Return the option value
	 */
	public static function get_admin_settings_option( $key, $default = false, $network_override = false ) {

		// Get the site-wide option if we're in the network admin.
		if ( $network_override && is_multisite() ) {
			$value = get_site_option( $key, $default );
		} else {
			$value = get_option( $key, $default );
		}

		return $value;
	}

	/**
	 * Updates an option from the admin settings page.
	 *
	 * @param string $key       The option key.
	 * @param mixed  $value     The value to update.
	 * @param bool   $network   Whether to allow the network admin setting to be overridden on subsites.
	 * @return mixed
	 */
	public static function update_admin_settings_option( $key, $value, $network = false ) {

		// Update the site-wide option since we're in the network admin.
		if ( $network && is_multisite() ) {
			update_site_option( $key, $value );
		} else {
			update_option( $key, $value );
		}

	}

	/**
	 * Provide White Label array().
	 *
	 * @return array()
	 * @since 0.0.1
	 */
	public static function get_white_labels() {

		if ( null === self::$branding ) {
			$branding_default = apply_filters(
				'uael_branding_options',
				array(
					'agency'                => array(
						'author'        => '',
						'author_url'    => '',
						'hide_branding' => false,
					),
					'plugin'                => array(
						'name'        => '',
						'short_name'  => '',
						'description' => '',
					),
					'replace_logo'          => 'disable',
					'enable_knowledgebase'  => 'enable',
					'knowledgebase_url'     => '',
					'enable_support'        => 'enable',
					'support_url'           => '',
					'enable_beta_box'       => 'enable',
					'enable_custom_tagline' => 'disable',
					'internal_help_links'   => 'enable',
				)
			);

			$branding       = self::get_admin_settings_option( '_uael_white_label', array(), true );
			self::$branding = wp_parse_args( $branding, $branding_default );

			if ( defined( 'UAEL_WL_AUTHOR' ) ) {
				self::$branding['agency']['author'] = UAEL_WL_AUTHOR;
			}

			if ( defined( 'UAEL_WL_AUTHOR_URL' ) ) {
				self::$branding['agency']['author_url'] = UAEL_WL_AUTHOR_URL;
			}

			if ( defined( 'UAEL_WL_PLUGIN_NAME' ) ) {
				self::$branding['plugin']['name'] = UAEL_WL_PLUGIN_NAME;
			}

			if ( defined( 'UAEL_WL_PLUGIN_SHORT_NAME' ) ) {
				self::$branding['plugin']['short_name'] = UAEL_WL_PLUGIN_SHORT_NAME;
			}

			if ( defined( 'UAEL_WL_PLUGIN_DESCRIPTION' ) ) {
				self::$branding['plugin']['description'] = UAEL_WL_PLUGIN_DESCRIPTION;
			}

			if ( defined( 'UAEL_WL_REPLACE_LOGO' ) ) {
				self::$branding['replace_logo'] = UAEL_WL_REPLACE_LOGO;
			}

			if ( defined( 'UAEL_WL_KNOWLEDGEBASE' ) ) {
				self::$branding['enable_knowledgebase'] = UAEL_WL_KNOWLEDGEBASE;
			}

			if ( defined( 'UAEL_WL_KNOWLEDGEBASE_URL' ) ) {
				self::$branding['knowledgebase_url'] = UAEL_WL_KNOWLEDGEBASE_URL;
			}

			if ( defined( 'UAEL_WL_SUPPORT' ) ) {
				self::$branding['enable_support'] = UAEL_WL_SUPPORT;
			}

			if ( defined( 'UAEL_WL_SUPPORT_URL' ) ) {
				self::$branding['support_url'] = UAEL_WL_SUPPORT_URL;
			}

			if ( defined( 'UAEL_WL_BETA_UPDATE_BOX' ) ) {
				self::$branding['enable_beta_box'] = UAEL_WL_BETA_UPDATE_BOX;
			}

			if ( defined( 'UAEL_WL_INTERNAL_HELP_LINKS' ) ) {
				self::$branding['internal_help_links'] = UAEL_WL_INTERNAL_HELP_LINKS;
			}

			if ( defined( 'UAEL_WL_CUSTOM_TAGLINE' ) ) {
				self::$branding['enable_custom_tagline'] = UAEL_WL_CUSTOM_TAGLINE;
			}
		}

		return self::$branding;
	}

	/**
	 * Is White Label.
	 *
	 * @return string
	 * @since 0.0.1
	 */
	public static function is_hide_branding() {

		$branding = self::get_white_labels();

		$hide = false;

		if ( defined( 'WP_UAEL_WL' ) && WP_UAEL_WL ) {

			$hide = true;
		} else {

			if ( isset( $branding['agency']['hide_branding'] ) && false === $branding['agency']['hide_branding'] ) {

				$hide = false;
			} else {
				$hide = true;
			}
		}

		return $hide;
	}

	/**
	 * Is replace_logo.
	 *
	 * @return string
	 * @since 0.0.1
	 */
	public static function is_replace_logo() {

		$branding = self::get_white_labels();

		if ( isset( $branding['replace_logo'] ) && 'disable' === $branding['replace_logo'] ) {

			return false;
		}

		return true;
	}

	/**
	 * Is hide_tagline.
	 *
	 * @return string
	 * @since 1.21.1
	 */
	public static function is_hide_tagline() {

		$branding = self::get_white_labels();

		if ( isset( $branding['enable_custom_tagline'] ) && 'disable' === $branding['enable_custom_tagline'] ) {

			return false;
		}

		return true;
	}


	/**
	 * Is Knowledgebase.
	 *
	 * @return string
	 * @since 0.0.1
	 */
	public static function knowledgebase_data() {

		$branding = self::get_white_labels();

		$knowledgebase = array(
			'enable_knowledgebase' => true,
			'knowledgebase_url'    => UAEL_DOMAIN . 'docs/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
		);

		if ( isset( $branding['enable_knowledgebase'] ) && 'disable' === $branding['enable_knowledgebase'] ) {

			$knowledgebase['enable_knowledgebase'] = false;
		}

		if ( isset( $branding['knowledgebase_url'] ) && '' !== $branding['knowledgebase_url'] ) {
			$knowledgebase['knowledgebase_url'] = $branding['knowledgebase_url'];
		}

		return $knowledgebase;
	}

	/**
	 * Is Knowledgebase.
	 *
	 * @return string
	 * @since 0.0.1
	 */
	public static function support_data() {

		$branding = self::get_white_labels();

		$support = array(
			'enable_support' => true,
			'support_url'    => UAEL_DOMAIN . 'support/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
		);

		if ( isset( $branding['enable_support'] ) && 'disable' === $branding['enable_support'] ) {

			$support['enable_support'] = false;
		}

		if ( isset( $branding['support_url'] ) && '' !== $branding['support_url'] ) {
			$support['support_url'] = $branding['support_url'];
		}

		return $support;
	}

	/**
	 * Is internal links enable.
	 *
	 * @return string
	 * @since 0.0.1
	 */
	public static function is_internal_links() {

		$branding = self::get_white_labels();

		if ( isset( $branding['internal_help_links'] ) && 'disable' === $branding['internal_help_links'] ) {

			return false;
		}

		return true;
	}

	/**
	 * Provide Widget Name
	 *
	 * @param string $slug Module slug.
	 * @return string
	 * @since 0.0.1
	 */
	public static function get_widget_slug( $slug = '' ) {

		if ( ! isset( self::$widget_list ) ) {
			self::$widget_list = self::get_widget_list();
		}

		$widget_slug = '';

		if ( isset( self::$widget_list[ $slug ] ) ) {
			$widget_slug = self::$widget_list[ $slug ]['slug'];
		}

		return apply_filters( 'uael_widget_slug', $widget_slug );
	}

	/**
	 * Provide Widget Name
	 *
	 * @param string $slug Module slug.
	 * @return string
	 * @since 0.0.1
	 */
	public static function get_widget_title( $slug = '' ) {

		if ( ! isset( self::$widget_list ) ) {
			self::$widget_list = self::get_widget_list();
		}

		$widget_name = '';

		if ( isset( self::$widget_list[ $slug ] ) ) {
			$widget_name = self::$widget_list[ $slug ]['title'];
		}

		return apply_filters( 'uael_widget_name', $widget_name );
	}

	/**
	 * Provide Widget Name
	 *
	 * @param string $slug Module slug.
	 * @return string
	 * @since 0.0.1
	 */
	public static function get_widget_icon( $slug = '' ) {

		if ( ! isset( self::$widget_list ) ) {
			self::$widget_list = self::get_widget_list();
		}

		$widget_icon = '';

		if ( isset( self::$widget_list[ $slug ] ) ) {
			$widget_icon = self::$widget_list[ $slug ]['icon'];
		}

		return apply_filters( 'uael_widget_icon', $widget_icon );
	}

	/**
	 * Provide Widget Keywords
	 *
	 * @param string $slug Module slug.
	 * @return string
	 * @since 1.5.1
	 */
	public static function get_widget_keywords( $slug = '' ) {

		if ( ! isset( self::$widget_list ) ) {
			self::$widget_list = self::get_widget_list();
		}

		$widget_keywords = '';

		if ( isset( self::$widget_list[ $slug ] ) && isset( self::$widget_list[ $slug ]['keywords'] ) ) {
			$widget_keywords = self::$widget_list[ $slug ]['keywords'];
		}

		return apply_filters( 'uael_widget_keywords', $widget_keywords );
	}

	/**
	 * Provide Integrations settings array().
	 *
	 * @param string $name Module slug.
	 * @return array()
	 * @since 0.0.1
	 */
	public static function get_integrations_options( $name = '' ) {

		$integrations_default = array(
			'google_api'                           => '',
			'developer_mode'                       => false,
			'language'                             => '',
			'google_places_api'                    => '',
			'yelp_api'                             => '',
			'recaptcha_v3_key'                     => '',
			'recaptcha_v3_secretkey'               => '',
			'recaptcha_v3_score'                   => '0.5',
			'google_client_id'                     => '',
			'facebook_app_id'                      => '',
			'facebook_app_secret'                  => '',
			'uael_share_button'                    => '',
			'uael_maxmind_geolocation_license_key' => '',
			'uael_maxmind_geolocation_db_path'     => '',
			'uael_twitter_feed_consumer_key'       => '',
			'uael_twitter_feed_consumer_secret'    => '',
			'instagram_app_id'                     => '',
			'instagram_app_secret'                 => '',
			'instagram_app_token'                  => '',
		);

		$integrations = self::get_admin_settings_option( '_uael_integration', array(), true );
		$integrations = wp_parse_args( $integrations, $integrations_default );
		$integrations = apply_filters( 'uael_integration_options', $integrations );

		if ( '' !== $name && isset( $integrations[ $name ] ) && '' !== $integrations[ $name ] ) {
			return $integrations[ $name ];
		} else {
			return $integrations;
		}
	}

	/**
	 * Provide Widget settings.
	 *
	 * @return array()
	 * @since 0.0.1
	 */
	public static function get_widget_options() {

		if ( null === self::$widget_options ) {

			if ( ! isset( self::$widget_list ) ) {
				$widgets = self::get_widget_list();
			} else {
				$widgets = self::$widget_list;
			}

			$saved_widgets = self::get_admin_settings_option( '_uael_widgets' );

			if ( is_array( $widgets ) ) {

				foreach ( $widgets as $slug => $data ) {

					if ( isset( $saved_widgets[ $slug ] ) ) {

						if ( 'disabled' === $saved_widgets[ $slug ] ) {
							$widgets[ $slug ]['is_activate'] = false;
						} else {
							$widgets[ $slug ]['is_activate'] = true;
						}
					} else {
						$widgets[ $slug ]['is_activate'] = ( isset( $data['default'] ) ) ? $data['default'] : false;
					}
				}
			}

			if ( false === self::is_hide_branding() ) {
				$options_url  = admin_url( 'options-general.php' );
				$branding_url = add_query_arg(
					array(
						'page'   => UAEL_SLUG,
						'action' => 'branding',
					),
					$options_url
				);

				$widgets['White_Label'] = array(
					'slug'         => 'uael-white-label',
					'title'        => __( 'White Label', 'uael' ),
					'icon'         => '',
					'title_url'    => '#',
					'is_activate'  => true,
					'setting_text' => __( 'Settings', 'uael' ),
					'setting_url'  => $branding_url,
					'category'     => 'feature',
				);
			}

			self::$widget_options = $widgets;
		}
		return apply_filters( 'uael_enabled_widgets', self::$widget_options );
	}

	/**
	 * Widget Active.
	 *
	 * @param string $slug Module slug.
	 * @return string
	 * @since 0.0.1
	 */
	public static function is_widget_active( $slug = '' ) {

		$widgets     = self::get_widget_options();
		$is_activate = false;

		if ( isset( $widgets[ $slug ] ) ) {
			$is_activate = $widgets[ $slug ]['is_activate'];
		}

		return $is_activate;
	}

	/**
	 * Provide Post skin settings.
	 *
	 * @return array()
	 * @since 1.21.0
	 */
	public static function get_post_skin_options() {
		if ( null === self::$post_skins_list ) {

			$post_skins_list = self::get_post_skin_list();
			$saved_widgets   = self::get_admin_settings_option( '_uael_widgets' );

			if ( is_array( $post_skins_list ) ) {

				foreach ( $post_skins_list as $slug => $data ) {
					if ( isset( $saved_widgets[ $slug ] ) ) {

						if ( 'disabled' === $saved_widgets[ $slug ] ) {
							$post_skins_list[ $slug ]['is_activate'] = false;
						} else {
							$post_skins_list[ $slug ]['is_activate'] = true;
						}
					} else {
						$post_skins_list[ $slug ]['is_activate'] = ( isset( $data['default'] ) ) ? $data['default'] : false;
					}
				}
			}

			self::$skins_options = $post_skins_list;

		}

		return apply_filters( 'uael_enabled_skins', self::$skins_options );
	}

	/**
	 * Post skin Active.
	 *
	 * @param string $slug Module slug.
	 * @return string
	 * @since 1.21.0
	 */
	public static function is_post_skin_active( $slug = '' ) {

		$post_skins_list = self::get_post_skin_options();
		$is_activate     = false;

		if ( isset( $post_skins_list[ $slug ] ) ) {
			$is_activate = $post_skins_list[ $slug ]['is_activate'];
		}

		return $is_activate;
	}

	/**
	 * Condition compare.
	 *
	 * @param string $left_value left value to be compare.
	 * @param string $right_value right value to be compare.
	 * @param string $operator operator.
	 * @return string
	 * @since 1.32.0
	 */
	public static function display_conditions_compare( $left_value, $right_value, $operator ) {
		switch ( $operator ) {
			case 'is':
			case 'less':
			case 'greater':
			case 'less_than_equal':
			case 'greater_than_equal':
				return $left_value === $right_value;
			case 'not':
				return $left_value !== $right_value;
			default:
				return $left_value === $right_value;
		}
	}

	/**
	 * Get Client Site Time
	 *
	 * @param string $format Time format.
	 * @return string
	 * @since 1.32.0
	 */
	public static function get_local_time( $format = 'Y-m-d h:i:s A' ) {
		$timezone_name   = isset( $_COOKIE['GetLocalTimeZone'] ) ? timezone_name_from_abbr( '', (int) $_COOKIE['GetLocalTimeZone'] * 60, false ) : ''; // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___COOKIE
		$local_time_zone = isset( $timezone_name ) && ! empty( $timezone_name ) ? str_replace( 'GMT ', 'GMT+', $timezone_name ) : date_default_timezone_get();
		$now_date        = new \DateTime( 'now', new \DateTimeZone( $local_time_zone ) );
		$today           = $now_date->format( $format );
		return $today;
	}

	/**
	 * Get Server Time
	 *
	 * @param string $format time format.
	 * @return string
	 * @since 1.32.0
	 */
	public static function get_server_time( $format = 'Y-m-d h:i:s A' ) {
		$today = gmdate( $format, strtotime( 'now' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
		return $today;
	}

	/**
	 * Get User Browser name
	 *
	 * @param string $user_agent Browser names.
	 * @return string
	 * @since 1.32.0
	 */
	public static function get_browser_name( $user_agent ) {

		if ( strpos( $user_agent, 'Opera' ) || strpos( $user_agent, 'OPR/' ) ) {
			return 'opera';
		} elseif ( strpos( $user_agent, 'Edg' ) || strpos( $user_agent, 'Edge' ) ) {
			return 'edge';
		} elseif ( strpos( $user_agent, 'Chrome' ) ) {
			return 'chrome';
		} elseif ( strpos( $user_agent, 'Safari' ) ) {
			return 'safari';
		} elseif ( strpos( $user_agent, 'Firefox' ) ) {
			return 'firefox';
		} elseif ( strpos( $user_agent, 'MSIE' ) || strpos( $user_agent, 'Trident/7' ) ) {
			return 'ie';
		}
	}

	/**
	 * Returns Script array.
	 *
	 * @return array()
	 * @since 0.0.1
	 */
	public static function get_widget_script() {

		return UAEL_Config::get_widget_script();
	}

	/**
	 * Returns Style array.
	 *
	 * @return array()
	 * @since 0.0.1
	 */
	public static function get_widget_style() {

		return UAEL_Config::get_widget_style();
	}

	/**
	 * Returns Google Map languages List.
	 *
	 * @since 0.0.1
	 *
	 * @return array Google Map languages List.
	 */
	public static function get_google_map_languages() {

		if ( null === self::$google_map_languages ) {

			self::$google_map_languages = array(
				'ar'    => __( 'ARABIC', 'uael' ),
				'eu'    => __( 'BASQUE', 'uael' ),
				'bg'    => __( 'BULGARIAN', 'uael' ),
				'bn'    => __( 'BENGALI', 'uael' ),
				'ca'    => __( 'CATALAN', 'uael' ),
				'cs'    => __( 'CZECH', 'uael' ),
				'da'    => __( 'DANISH', 'uael' ),
				'de'    => __( 'GERMAN', 'uael' ),
				'el'    => __( 'GREEK', 'uael' ),
				'en'    => __( 'ENGLISH', 'uael' ),
				'en-AU' => __( 'ENGLISH (AUSTRALIAN)', 'uael' ),
				'en-GB' => __( 'ENGLISH (GREAT BRITAIN)', 'uael' ),
				'es'    => __( 'SPANISH', 'uael' ),
				'fa'    => __( 'FARSI', 'uael' ),
				'fi'    => __( 'FINNISH', 'uael' ),
				'fil'   => __( 'FILIPINO', 'uael' ),
				'fr'    => __( 'FRENCH', 'uael' ),
				'gl'    => __( 'GALICIAN', 'uael' ),
				'gu'    => __( 'GUJARATI', 'uael' ),
				'hi'    => __( 'HINDI', 'uael' ),
				'hr'    => __( 'CROATIAN', 'uael' ),
				'hu'    => __( 'HUNGARIAN', 'uael' ),
				'id'    => __( 'INDONESIAN', 'uael' ),
				'it'    => __( 'ITALIAN', 'uael' ),
				'iw'    => __( 'HEBREW', 'uael' ),
				'ja'    => __( 'JAPANESE', 'uael' ),
				'kn'    => __( 'KANNADA', 'uael' ),
				'ko'    => __( 'KOREAN', 'uael' ),
				'lt'    => __( 'LITHUANIAN', 'uael' ),
				'lv'    => __( 'LATVIAN', 'uael' ),
				'ml'    => __( 'MALAYALAM', 'uael' ),
				'mr'    => __( 'MARATHI', 'uael' ),
				'nl'    => __( 'DUTCH', 'uael' ),
				'no'    => __( 'NORWEGIAN', 'uael' ),
				'pl'    => __( 'POLISH', 'uael' ),
				'pt'    => __( 'PORTUGUESE', 'uael' ),
				'pt-BR' => __( 'PORTUGUESE (BRAZIL)', 'uael' ),
				'pt-PT' => __( 'PORTUGUESE (PORTUGAL)', 'uael' ),
				'ro'    => __( 'ROMANIAN', 'uael' ),
				'ru'    => __( 'RUSSIAN', 'uael' ),
				'sk'    => __( 'SLOVAK', 'uael' ),
				'sl'    => __( 'SLOVENIAN', 'uael' ),
				'sr'    => __( 'SERBIAN', 'uael' ),
				'sv'    => __( 'SWEDISH', 'uael' ),
				'tl'    => __( 'TAGALOG', 'uael' ),
				'ta'    => __( 'TAMIL', 'uael' ),
				'te'    => __( 'TELUGU', 'uael' ),
				'th'    => __( 'THAI', 'uael' ),
				'tr'    => __( 'TURKISH', 'uael' ),
				'uk'    => __( 'UKRAINIAN', 'uael' ),
				'vi'    => __( 'VIETNAMESE', 'uael' ),
				'zh-CN' => __( 'CHINESE (SIMPLIFIED)', 'uael' ),
				'zh-TW' => __( 'CHINESE (TRADITIONAL)', 'uael' ),
			);
		}

		return self::$google_map_languages;
	}

	/**
	 * Provide Image data based on id.
	 *
	 * @return array()
	 * @param int    $image_id Image ID.
	 * @param string $image_url Image URL.
	 * @param array  $image_size Image sizes array.
	 * @since 0.0.1
	 */
	public static function get_image_data( $image_id, $image_url, $image_size ) {

		if ( ! $image_id && ! $image_url ) {
			return false;
		}

		$data = array();

		$image_url = esc_url_raw( $image_url );

		if ( ! empty( $image_id ) ) { // Existing attachment.

			$attachment = get_post( $image_id );
			if ( is_object( $attachment ) ) {
				$data['id']          = $image_id;
				$data['url']         = $image_url;
				$data['image']       = wp_get_attachment_image( $attachment->ID, $image_size, true );
				$data['caption']     = wp_get_attachment_caption( $image_id );
				$data['title']       = $attachment->post_title;
				$data['description'] = $attachment->post_content;

			}
		} else { // Placeholder image, most likely.

			if ( empty( $image_url ) ) {
				return;
			}

			$data['id']          = false;
			$data['url']         = $image_url;
			$data['image']       = '<img src="' . $image_url . '" alt="" title="" />';
			$data['caption']     = '';
			$data['title']       = '';
			$data['description'] = '';
		}

		return $data;
	}

	/**
	 * Authenticate Google & Yelp API keys
	 *
	 * @since 1.13.0
	 */
	public static function get_api_authentication() {

		$integration_settings = self::get_integrations_options();

		if ( '' !== $integration_settings['google_places_api'] ) {

			$api_key = $integration_settings['google_places_api'];

			$place_id = 'ChIJq6qqat2_wjsR4Rri4i22ap4';

			$parameters = "key=$api_key&placeid=$place_id";

			$url = "https://maps.googleapis.com/maps/api/place/details/json?$parameters";

			$result = wp_remote_post(
				$url,
				array(
					'method'      => 'POST',
					'timeout'     => 60,
					'httpversion' => '1.0',
				)
			);

			if ( ! is_wp_error( $result ) || wp_remote_retrieve_response_code( $result ) === 200 ) {
				$final_result  = json_decode( wp_remote_retrieve_body( $result ) );
				$result_status = $final_result->status;

				switch ( $result_status ) {
					case 'OVER_QUERY_LIMIT':
						update_option( 'uael_google_api_status', 'exceeded' );
						break;
					case 'OK':
						update_option( 'uael_google_api_status', 'yes' );
						break;
					case 'REQUEST_DENIED':
						update_option( 'uael_google_api_status', 'no' );
						break;
					default:
						update_option( 'uael_google_api_status', '' );
						break;
				}
			} else {
				update_option( 'uael_google_api_status', 'no' );
			}
		} else {
			delete_option( 'uael_google_api_status' );
		}

		if ( '' !== $integration_settings['yelp_api'] ) {
			$url = 'https://api.yelp.com/v3/businesses/search?term=pizza&location=boston';

			$result = wp_remote_get(
				$url,
				array(
					'method'      => 'GET',
					'timeout'     => 60,
					'httpversion' => '1.0',
					'user-agent'  => '',
					'headers'     => array(
						'Authorization' => 'Bearer ' . $integration_settings['yelp_api'],
					),
				)
			);

			if ( is_wp_error( $result ) ) {
				update_option( 'uael_yelp_api_status', 'no' );
				return;
			} else {
				$reviews = json_decode( $result['body'] );

				$response_code = wp_remote_retrieve_response_code( $result );

				if ( 200 !== $response_code ) {
					$error_message = $reviews->error->code;
					if ( 'VALIDATION_ERROR' === $error_message ) {
						update_option( 'uael_yelp_api_status', 'no' );
					}
				} else {
					update_option( 'uael_yelp_api_status', 'yes' );
				}
			}
		} else {
			delete_option( 'uael_yelp_api_status' );
		}

		global $wpdb;

		$param1     = '%\_transient\_%';
		$param2     = '%_uael_reviews_%';
		$param3     = '%\_transient\_timeout%';
		$transients = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->options} WHERE option_name LIKE %s AND option_name LIKE %s AND option_name NOT LIKE %s", $param1, $param2, $param3 ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		foreach ( $transients as $transient ) {
			$transient_name = $transient->option_name;
			$transient_name = str_replace( '_transient_', '', $transient_name );
			delete_transient( $transient_name );
		}
	}

	/**
	 * Authenticate Facebook Access Token.
	 *
	 * @since 1.30.0
	 */
	public static function facebook_token_authentication() {
		$integration_settings = self::get_integrations_options();

		if ( '' !== $integration_settings['uael_share_button'] ) {
			$access_token_validation = UAEL_FACEBOOK_GRAPH_API_ENDPOINT . '?id=https://facebook.com&access_token=' . $integration_settings['uael_share_button'];

			$response = wp_remote_get( $access_token_validation );

			if ( is_wp_error( $response ) ) {

				return false;

			}

			return $response['response']['code'];
		}

		return false;
	}

	/**
	 * Social account share count.
	 *
	 * @since 1.30.0
	 *
	 * @return response.
	 * @param string $response response.
	 * @param string $args arguments.
	 */
	public static function get_social_share_count( $response, $args ) {

		$response = wp_remote_get( $response, $args );

		if ( is_wp_error( $response ) ) {

			return false;
		}

		$response = wp_remote_retrieve_body( $response );

		return $response;

	}

	/**
	 * Check if the Elementor is updated.
	 *
	 * @since 1.16.1
	 *
	 * @return boolean if Elementor updated.
	 */
	public static function is_elementor_updated() {
		if ( class_exists( 'Elementor\Icons_Manager' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Return the new icon name.
	 *
	 * @since 1.16.1
	 *
	 * @param string $control_name name of the control.
	 * @return string of the updated control name.
	 */
	public static function get_new_icon_name( $control_name ) {
		if ( class_exists( 'Elementor\Icons_Manager' ) ) {
			return 'new_' . $control_name . '[value]';
		} else {
			return $control_name;
		}
	}

	/**
	 * Return the current client IP.
	 *
	 * @since 1.18.0
	 *
	 * @return string of the current IP address.
	 */
	public static function get_client_ip() {
		$server_ip_keys = array(
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		);

		foreach ( $server_ip_keys as $key ) {
			if ( isset( $_SERVER[ $key ] ) && filter_var( $_SERVER[ $key ], FILTER_VALIDATE_IP ) ) {
				return sanitize_text_field( $_SERVER[ $key ] );
			}
		}

		// Fallback local ip.
		return '127.0.0.1';
	}

	/**
	 *  Get Saved templates
	 *
	 *  @param string $type Type.
	 *  @since 1.22.0
	 *  @return array of templates
	 */
	public static function get_saved_data( $type = 'page' ) {

		$template_type = $type . '_templates';

		$templates_list = array();

		if ( ( null === self::$page_templates && 'page' === $type ) || ( null === self::$section_templates && 'section' === $type ) || ( null === self::$container_templates && 'container' === $type ) || ( null === self::$widget_templates && 'widget' === $type ) ) {

			$posts = get_posts(
				array(
					'post_type'      => 'elementor_library',
					'orderby'        => 'title',
					'order'          => 'ASC',
					'posts_per_page' => '-1',
					'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						array(
							'taxonomy' => 'elementor_library_type',
							'field'    => 'slug',
							'terms'    => $type,
						),
					),
				)
			);

			foreach ( $posts as $post ) {

				$templates_list[] = array(
					'id'   => $post->ID,
					'name' => $post->post_title,
				);
			}

			self::${$template_type}[-1] = __( 'Select', 'uael' );

			if ( count( $templates_list ) ) {
				foreach ( $templates_list as $saved_row ) {

					$content_id                            = $saved_row['id'];
					$content_id                            = apply_filters( 'wpml_object_id', $content_id );
					self::${$template_type}[ $content_id ] = $saved_row['name'];

				}
			} else {
				self::${$template_type}['no_template'] = __( 'It seems that, you have not saved any template yet.', 'uael' );
			}
		}

		return self::${$template_type};
	}

	/**
	 * Get Skin Specific Stylesheet
	 *
	 * @param string $saved_blocks widget name.
	 * @param array  $combined array of stylesheets.
	 * @since 1.27.0
	 */
	public static function get_active_skins_stylesheet( $saved_blocks, $combined ) {

		$is_already_event        = false;
		$is_already_posts_common = false;
		$is_already_carousel     = false;
		$folder                  = self::get_css_folder();
		$suffix                  = self::get_css_suffix();

		foreach ( UAEL_Config::get_post_skin_list() as $key => $skin ) {

			$block_name = str_replace( 'uael/', '', $key );

			if ( isset( $saved_blocks[ $block_name ] ) && 'disabled' === $saved_blocks[ $block_name ] ) {
				continue;
			}

			$skin_css = substr( $skin['slug'], 5 );

			$combined[ $skin['slug'] ] = array(
				'path'     => 'assets/' . $folder . '/modules/' . $skin_css . $suffix . '.css',
				'path-rtl' => 'assets/min-css/modules/' . $skin_css . '-rtl.min.css',
				'dep'      => array(),
			);
		}

		if ( ! $is_already_event ) {
			$combined['uael-skin-event'] = array(
				'path'     => 'assets/' . $folder . '/modules/skin-event' . $suffix . '.css',
				'path-rtl' => 'assets/min-css/modules/skin-event-rtl.min.css',
				'dep'      => array(),
			);

			$is_already_event = true;
		}

		if ( ! $is_already_posts_common ) {
			$combined['uael-posts'] = array(
				'path'     => 'assets/' . $folder . '/modules/post' . $suffix . '.css',
				'path-rtl' => 'assets/min-css/modules/post-rtl.min.css',
				'dep'      => array(),
			);

			$is_already_posts_common = true;
		}

		if ( ! $is_already_carousel ) {
			$combined['uael-posts-carousel'] = array(
				'path'     => 'assets/' . $folder . '/modules/post-carousel' . $suffix . '.css',
				'path-rtl' => 'assets/min-css/modules/post-carousel-rtl.min.css',
				'dep'      => array(),
			);

			$is_already_carousel = true;
		}

		return $combined;
	}

	/**
	 * Get Specific Stylesheet
	 *
	 * @since 1.27.0
	 */
	public static function get_active_widget_stylesheet() {

		$saved_blocks                     = self::get_admin_settings_option( '_uael_widgets' );
		$combined                         = array();
		$is_already_heading               = false;
		$is_already_buttons               = false;
		$is_already_wc                    = false;
		$is_already_widget_common         = false;
		$is_already_fancybox              = false;
		$is_already_party_propz_extension = false;
		$is_already_welcome_music         = false;
		$folder                           = self::get_css_folder();
		$suffix                           = self::get_css_suffix();

		foreach ( UAEL_Config::$widget_list as $key => $block ) {

			$block_name = str_replace( 'uael/', '', $key );

			if ( isset( $saved_blocks[ $block_name ] ) && ( 'disabled' === $saved_blocks[ $block_name ] && ! is_multisite() ) || 'DisplayConditions' === $block_name || 'Presets' === $block_name || 'SectionDivider' === $block_name ) {
				continue;
			}

			if ( 'uael-cross-domain-copy-paste' !== $block['slug'] && 'uael-retina-image' !== $block['slug'] ) {

				if ( ! $is_already_widget_common ) {
					$combined['uael-common'] = array(
						'path'     => 'assets/' . $folder . '/modules/common' . $suffix . '.css',
						'path-rtl' => 'assets/min-css/modules/common-rtl.min.css',
						'dep'      => array(),
					);

					$is_already_widget_common = true;
				}

				if ( ! $is_already_fancybox ) {
					$combined['uael-fancybox'] = array(
						'path'     => 'assets/min-css/modules/jquery-fancybox.min.css',
						'path-rtl' => 'assets/min-css/modules/jquery-fancybox-rtl.min.css',
						'dep'      => array(),
					);

					$is_already_fancybox = true;
				}
			}

			switch ( $block_name ) {
				case 'Advanced_Heading':
				case 'Dual_Heading':
				case 'Fancy_Heading':
					if ( ! $is_already_heading ) {
						$combined['uael-heading'] = array(
							'path'     => 'assets/' . $folder . '/modules/heading' . $suffix . '.css',
							'path-rtl' => 'assets/min-css/modules/heading-rtl.min.css',
							'dep'      => array(),
						);

						$is_already_heading = true;
					}
					break;
				case 'Buttons':
				case 'Marketing_Button':
					if ( ! $is_already_buttons ) {
						$combined['uael-buttons'] = array(
							'path'     => 'assets/' . $folder . '/modules/buttons' . $suffix . '.css',
							'path-rtl' => 'assets/min-css/modules/buttons-rtl.min.css',
							'dep'      => array(),
						);

						$is_already_buttons = true;
					}
					break;
				case 'Woo_Add_To_Cart':
				case 'Woo_Categories':
				case 'Woo_Products':
				case 'Woo_Mini_Cart':
				case 'Woo_Checkout':
					if ( ! $is_already_wc ) {
						$combined['uael-woocommerce'] = array(
							'path'     => 'assets/' . $folder . '/modules/uael-woocommerce' . $suffix . '.css',
							'path-rtl' => 'assets/min-css/modules/uael-woocommerce-rtl.min.css',
							'dep'      => array(),
						);

						$is_already_wc = true;
					}
					break;
				case 'Posts':
					$combined = self::get_active_skins_stylesheet( $saved_blocks, $combined );

					break;

				case 'PartyPropzExtension':
					if ( ! $is_already_party_propz_extension ) {
						$combined['uael-party-propz-extension'] = array(
							'path'     => 'assets/' . $folder . '/modules/party-propz-extension' . $suffix . '.css',
							'path-rtl' => 'assets/min-css/modules/party-propz-extension-rtl.min.css',
							'dep'      => array(),
						);
						$is_already_party_propz_extension       = true;

					}
					break;

				case 'Welcome_Music':
					if ( ! $is_already_welcome_music ) {
						$combined['uael-welcome-music'] = array(
							'path'     => 'assets/' . $folder . '/modules/welcome-music' . $suffix . '.css',
							'path-rtl' => 'assets/min-css/modules/welcome-music-rtl.min.css',
							'dep'      => array(),
						);
						$is_already_welcome_music       = true;

					}
					break;

				default:
					if ( 'uael-cross-domain-copy-paste' !== $block['slug'] && 'uael-retina-image' !== $block['slug'] ) {

						$block_css = substr( $block['slug'], 5 );

						$combined[ $block['slug'] ] = array(
							'path'     => 'assets/' . $folder . '/modules/' . $block_css . $suffix . '.css',
							'path-rtl' => 'assets/min-css/modules/' . $block_css . '-rtl.min.css',
							'dep'      => array(),
						);
					}

					break;
			}
		}

		return $combined;
	}

	/**
	 * Generate dynamic combined min.css
	 *
	 * @since 1.27.0
	 */
	public static function create_specific_stylesheet() {

		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$combined = self::get_active_widget_stylesheet();

		$combined_path     = plugin_dir_path( UAEL_FILE ) . 'assets/min-css/uael-frontend.min.css';
		$combined_rtl_path = plugin_dir_path( UAEL_FILE ) . 'assets/min-css/uael-frontend-rtl.min.css';

		wp_delete_file( $combined_path );
		wp_delete_file( $combined_rtl_path );

		$style     = '';
		$rtl_style = '';

		foreach ( $combined as $key => $c_block ) {

			$path     = plugin_dir_path( UAEL_FILE ) . $c_block['path'];
			$rtl_path = plugin_dir_path( UAEL_FILE ) . $c_block['path-rtl'];

			$style     .= $wp_filesystem->get_contents( $path );
			$rtl_style .= $wp_filesystem->get_contents( $rtl_path );

		}
		$wp_filesystem->put_contents( $combined_path, $style, FS_CHMOD_FILE );
		$wp_filesystem->put_contents( $combined_rtl_path, $rtl_style, FS_CHMOD_FILE );
	}

	/**
	 * Provide Widget Name
	 *
	 * @param string $slug Module slug.
	 * @return string
	 * @since 1.33.0
	 */
	public static function get_widget_presets( $slug = '' ) {
		if ( ! isset( self::$widget_list ) ) {
			self::$widget_list = self::get_widget_list();
		}

		$widget_preset = '';

		if ( isset( self::$widget_list[ $slug ] ) ) {
			$widget_preset = self::$widget_list[ $slug ]['preset'];
		}

		return apply_filters( 'uael_widget_preset', $widget_preset );
	}

	/**
	 * Validate an HTML tag against a safe allowed list.
	 *
	 * @since 1.30.0
	 * @param string $tag specifies the HTML Tag.
	 * @access public
	 */
	public static function validate_html_tag( $tag ) {

		// Check if Elementor method exists, else we will run custom validation code.
		if ( method_exists( 'Elementor\Utils', 'validate_html_tag' ) ) {
			return Utils::validate_html_tag( $tag );
		} else {
			return in_array( strtolower( $tag ), self::ALLOWED_HTML_WRAPPER_TAGS, true ) ? $tag : 'div';
		}
	}

	/**
	 * Output the ld+json schema markup.
	 *
	 * @since  1.33.1
	 *
	 * @param  array $schema_data Array to be converted to json markup.
	 */
	public static function print_json_schema( $schema_data ) {
		$schema_output = '';
		if ( ! empty( $schema_data ) && is_array( $schema_data ) ) {
			$encoded_data   = wp_json_encode( $schema_data, self::is_script_debug() ? JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES : JSON_UNESCAPED_SLASHES );
			$schema_output .= '<script type="application/ld+json">' . $encoded_data . '</script>';
		}
		echo $schema_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Returns the type of elementor element.
	 *
	 * @since  1.36.5
	 *
	 * @param array $element The Element.
	 *
	 * @return Widget_Base|Widget_Base[]|mixed|string|null
	 */
	public static function get_widget_type( $element ) {
		$type = '';
		if ( empty( $element['widgetType'] ) ) {
			$type = $element['elType'];
		} else {
			$type = $element['widgetType'];
		}

		if ( 'global' === $type && ! empty( $element['templateID'] ) ) {
			$type = self::get_global_widget_type( $element['templateID'] );
		}

		return $type;
	}

	/**
	 * Returns the type of elementor element if global.
	 *
	 * @since  1.36.5
	 *
	 * @param int|string $template_id Template ID.
	 * @param bool       $return_type Return type.
	 *
	 * @return Widget_Base|Widget_Base[]|mixed|string|null
	 */
	public static function get_global_widget_type( $template_id, $return_type = false ) {
		$template_data = Plugin::$instance->templates_manager->get_template_data(
			array(
				'source'      => 'local',
				'template_id' => $template_id,
			)
		);

		if ( is_wp_error( $template_data ) ) {
			return '';
		}

		if ( empty( $template_data['content'] ) ) {
			return '';
		}

		$original_widget_type = Plugin::$instance->widgets_manager->get_widget_types( $template_data['content'][0]['widgetType'] );

		if ( $return_type ) {
			return $original_widget_type;
		}

		return $original_widget_type ? $template_data['content'][0]['widgetType'] : '';
	}
}
