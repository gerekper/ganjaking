<?php
/**
 * Plugin Name: Storefront Parallax Hero
 * Plugin URI: http://woocommerce.com/products/storefront-parallax-hero/
 * Description: Adds a hero component to the Storefront homepage template. Customise the component in the customizer and include it on additional pages using the included shortcode.
 * Version: 1.5.7
 * Author: WooCommerce
 * Author URI: http://woocommerce.com/
 * Requires at least: 4.0
 * Tested up to: 4.9
 * Woo: 518370:9be6247229e653685a9d1c4accf5de99
 *
 * Text Domain: storefront-parallax-hero
 * Domain Path: /languages/
 *
 * @package Storefront_Parallax_Hero
 * @category Core
 * @author James Koster
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '9be6247229e653685a9d1c4accf5de99', '518370' );

/**
 * Returns the main instance of Storefront_Parallax_Hero to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Storefront_Parallax_Hero
 */
function Storefront_Parallax_Hero() {
	return Storefront_Parallax_Hero::instance();
} // End Storefront_Parallax_Hero()

Storefront_Parallax_Hero();

/**
 * Main Storefront_Parallax_Hero Class
 *
 * @class Storefront_Parallax_Hero
 * @version	1.0.0
 * @since 1.0.0
 * @package	Storefront_Parallax_Hero
 * @author Matty
 */
final class Storefront_Parallax_Hero {
	/**
	 * Storefront_Parallax_Hero The single instance of Storefront_Parallax_Hero.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct () {
		$this->token 			= 'storefront-parallax-hero';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.5.7';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );

		add_action( 'init', array( $this, 'sph_setup' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'sph_plugin_links' ) );
	} // End __construct()

	/**
	 * Main Storefront_Parallax_Hero Instance
	 *
	 * Ensures only one instance of Storefront_Parallax_Hero is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Storefront_Parallax_Hero()
	 * @return Main Storefront_Parallax_Hero instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'storefront-parallax-hero', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Plugin page links
	 *
	 * @since  1.1.2
	 */
	public function sph_plugin_links( $links ) {
		$plugin_links = array(
			'<a href="http://support.woothemes.com/">' . __( 'Support', 'storefront-parallax-hero' ) . '</a>',
			'<a href="http://docs.woothemes.com/document/storefront-parallax-hero/">' . __( 'Docs', 'storefront-parallax-hero' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Regiser Assets for later use
	 * @access  public
	 * @since   1.1.0
	 * @return  void
	 */
	public function assets() {
		wp_register_script( 'sph-stellar-init', plugins_url( '/assets/js/stellar-init.js', __FILE__ ), array( 'jquery' ) );
		wp_register_script( 'sph-full-height', plugins_url( '/assets/js/full-height.js', __FILE__ ), array( 'jquery' ) );
		wp_register_script( 'sph-script', plugins_url( '/assets/js/general.js', __FILE__ ), array( 'jquery' ) );
		wp_register_script( 'stellar', plugins_url( '/assets/js/jquery.stellar.min.js', __FILE__ ), array( 'jquery' ), '0.6.2' );
	}

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();

		// get theme customizer url
        $url = admin_url() . 'customize.php?';
        $url .= 'url=' . urlencode( site_url() . '?storefront-customizer=true' ) ;
        $url .= '&return=' . urlencode( admin_url() . 'plugins.php' );
        $url .= '&storefront-customizer=true';

		$notices 		= get_option( 'sph_activation_notice', array() );
		$notices[]		= sprintf( __( '%sThanks for installing the Storefront Parallax Hero extension. To get started, visit the %sCustomizer%s.%s %sOpen the Customizer%s', 'storefront-woocommerce-customiser' ), '<p>', '<a href="' . $url . '">', '</a>', '</p>', '<p><a href="' . $url . '" class="button button-primary">', '</a></p>' );

		update_option( 'sph_activation_notice', $notices );
	} // End install()

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()

	/**
	 * Setup all the things, if Storefront or a child theme using Storefront that has not disabled the Customizer settings is active
	 * @return void
	 */
	public function sph_setup() {
		$theme = wp_get_theme();

		if ( 'Storefront' == $theme->name || 'storefront' == $theme->template && apply_filters( 'storefront_parallax_hero_enabled', true ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'sph_script' ) );
			add_action( 'customize_register', array( $this, 'sph_customize_register' ) );
			add_action( 'customize_controls_print_styles', array( $this, 'customizer_custom_control_css' ) );
			add_action( 'homepage', array( $this, 'homepage_parallax_hero' ), 10 );
			add_action( 'body_class', array( $this, 'body_classes' ) );

			add_action( 'customize_preview_init', array( $this, 'sph_customize_preview_js' ) );

			add_action( 'admin_notices', array( $this, 'customizer_notice' ) );

			// Hide the 'More' section in the customizer
			add_filter( 'storefront_customizer_more', '__return_false' );
		}
	}

	/**
	 * Display a notice linking to the Customizer
	 * @since   1.0.0
	 * @return  void
	 */
	public function customizer_notice() {
		$notices = get_option( 'sph_activation_notice' );

		if ( $notices = get_option( 'sph_activation_notice' ) ) {

			foreach ( $notices as $notice ) {
				echo '<div class="updated">' . $notice . '</div>';
			}

			delete_option( 'sph_activation_notice' );
		}
	}

	/**
	 * Add custom body classes.
	 * @since   1.5.0
	 * @return  void
	 */
	public function body_classes( $classes ) {
		if ( apply_filters( 'sph_do_mobile', false ) ) {
			$classes[] = 'sph-do-mobile';
		}

		$background_video_image_fallback = sanitize_text_field( get_theme_mod( 'sph_hero_background_video_image_fallback', '' ) );

		if ( '' !== $background_video_image_fallback ) {
			$classes[] = 'sph-video-image-fallback';
		}

		return $classes;
	}

	/**
	 * Enqueue CSS.
	 * @since   1.0.0
	 * @return  void
	 */
	public function sph_script() {
		global $post, $storefront_version;

		wp_enqueue_style( 'sph-styles', plugins_url( '/assets/css/style.css', __FILE__ ) );

		$link_color 		= get_theme_mod( 'sph_hero_link_color', '#96588a' );
		$sph_heading_color 	= get_theme_mod( 'sph_heading_color', '#ffffff' );
		$accent_color 		= get_theme_mod( 'storefront_accent_color', '#96588a' );

		$sph_style = '
		.sph-hero a:not(.button) {
			color: ' . $link_color . ';
		}

		.overlay.animated h1:after {
			color: ' . $sph_heading_color . ';
		}

		.overlay.animated span:before {
			background-color: ' . $accent_color . ';
		}';

		if ( version_compare( $storefront_version, '2.2.0', '<' ) ) {
			$sph_style .= '
			.page-template-template-homepage .site-main .sph-hero:first-child {
				margin-top: -4.236em;
			}
			';
		}

		// Custom CSS for shortcodes
		if ( $post && true === has_shortcode( $post->post_content, 'parallax_hero' ) ) {
			preg_match_all( '/' . get_shortcode_regex() . '/sx', $post->post_content, $shortcode_matches, PREG_SET_ORDER );

			foreach ( $shortcode_matches as $shortcode ) {
				if ( 'parallax_hero' !== $shortcode[2] ) {
					continue;
				}

				if ( empty( $shortcode[3] ) ) {
					continue;
				}

				$atts = shortcode_parse_atts( $shortcode[3] );

				if ( ! isset( $atts['heading_text_color'] ) ) {
					continue;
				}

				$hash = md5( json_encode( $atts ) );

				$sph_style .= '
				#sph-' . $hash . ' .overlay.animated h1:after {
					color: ' . $atts['heading_text_color'] . ';
				}';
			}
		}

		wp_add_inline_style( 'sph-styles', $sph_style );
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 *
	 * @since  1.0.0
	 */
	function sph_customize_preview_js() {
		wp_enqueue_script( 'sph-customizer', plugins_url( '/assets/js/customizer.js', __FILE__ ), array( 'customize-preview' ), '1.0', true );
	}

	/**
	 * Customizer Controls and settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function sph_customize_register( $wp_customize ) {
		/**
		 * Include custom controls.
		 */
		require_once dirname( __FILE__ ) . '/includes/class-sph-buttonset-control.php';

		/**
		 * Add the panel
		 */
		$wp_customize->add_panel( 'sph_panel', array(
		    'priority'       	=> 60,
		    'capability'     	=> 'edit_theme_options',
		    'theme_supports' 	=> '',
		    'title'				=> __( 'Parallax Hero', 'storefront-parallax-hero' ),
		    'description'    	=> __( 'Customise the appearance and content of the hero component that is displayed on your homepage.', 'storefront-parallax-hero' ),
		    'active_callback'	=> array( $this, 'storefront_homepage_template_callback' ),
		) );

	    /**
	     * Add the sections
	     */
	    $wp_customize->add_section( 'sph_section_content' , array(
		    'title'		=> __( 'Content', 'storefront-parallax-hero' ),
		    'priority'	=> 10,
		    'panel'		=> 'sph_panel',
		) );

		$wp_customize->add_section( 'sph_section_background' , array(
		    'title'		=> __( 'Background', 'storefront-parallax-hero' ),
		    'priority'	=> 20,
		    'panel'		=> 'sph_panel',
		) );

		$wp_customize->add_section( 'sph_section_layout' , array(
		    'title'		=> __( 'Layout', 'storefront-parallax-hero' ),
		    'priority'	=> 30,
		    'panel'		=> 'sph_panel',
		) );

		/**
		 * Heading Text
		 */
	    $wp_customize->add_setting( 'sph_hero_heading_text', array(
	        'default'			=> __( 'Heading Text', 'storefront-parallax-hero' ),
	        'sanitize_callback'	=> 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sph_hero_heading_text', array(
            'label'			=> __( 'Heading text', 'storefront-parallax-hero' ),
            'section'		=> 'sph_section_content',
            'settings'		=> 'sph_hero_heading_text',
            'type'			=> 'text',
            'priority'		=> 10,
        ) ) );

        /**
	     * Heading text color
	     */
	    $wp_customize->add_setting( 'sph_heading_color', array(
	        'default'			=> '#ffffff',
	        'sanitize_callback'	=> 'sanitize_hex_color',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sph_heading_color', array(
	        'label'		=> 'Heading text color',
	        'section'	=> 'sph_section_content',
	        'settings'	=> 'sph_heading_color',
	        'priority'	=> 20,
	    ) ) );

	    if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_parallax_hero_heading_text_divider', array(
				'section'  	=> 'sph_section_content',
				'type'		=> 'divider',
				'priority' 	=> 25,
			) ) );
	    }

        /**
		 * Text
		 */
	    $wp_customize->add_setting( 'sph_hero_text', array(
	        'default'			=> __( 'Description Text', 'storefront-parallax-hero' ),
	        'sanitize_callback'	=> 'wp_kses_post'
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sph_hero_text', array(
            'label'			=> __( 'Description text', 'storefront-parallax-hero' ),
            'section'		=> 'sph_section_content',
            'settings'		=> 'sph_hero_text',
            'type'			=> 'textarea',
            'priority'		=> 30,
        ) ) );

        /**
	     * Text color
	     */
	    $wp_customize->add_setting( 'sph_hero_text_color', array(
	        'default'			=> '#5a6567',
	        'sanitize_callback'	=> 'sanitize_hex_color',
	        'transport'			=> 'postMessage',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sph_hero_text_color', array(
	        'label'		=> 'Description text color',
	        'section'	=> 'sph_section_content',
	        'settings'	=> 'sph_hero_text_color',
	        'priority'	=> 40,
	    ) ) );

	    if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_parallax_hero_text_divider', array(
				'section'  	=> 'sph_section_content',
				'type'		=> 'divider',
				'priority' 	=> 44,
			) ) );
	    }

	    /**
	     * Link color
	     */
	    $wp_customize->add_setting( 'sph_hero_link_color', array(
	        'default'			=> '#96588a',
	        'sanitize_callback'	=> 'sanitize_hex_color',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sph_hero_link_color', array(
	        'label'		=> 'Link color',
	        'section'	=> 'sph_section_content',
	        'settings'	=> 'sph_hero_link_color',
	        'priority'	=> 45,
	    ) ) );

	    if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_parallax_hero_link_divider', array(
				'section'  	=> 'sph_section_content',
				'type'		=> 'divider',
				'priority' 	=> 46,
			) ) );
	    }

        /**
	     * Media buttonset
	     */
		$wp_customize->add_setting( 'sph_hero_background_media', array(
			'default'           => 'none',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( new SPH_Buttonset_Control( $wp_customize, 'sph_hero_background_media', array(
			'label'    => __( 'Background media', 'storefront-powerpack' ),
			'section'  => 'sph_section_background',
			'settings' => 'sph_hero_background_media',
			'type'     => 'select',
			'priority' => 10,
			'choices'  => array(
				'none'  => __( 'None', 'storefront-powerpack' ),
				'image' => __( 'Image', 'storefront-powerpack' ),
				'video' => __( 'Video', 'storefront-powerpack' )
			),
		) ) );

		/**
		 * Background image
		 */
		$wp_customize->add_setting( 'sph_hero_background_image', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( new WP_Customize_Cropped_Image_Control( $wp_customize, 'sph_hero_background_image', array(
			'section'     => 'sph_section_background',
			'label'       => __( 'Background image', 'storefront-parallax-hero' ),
			'description' => __( 'Upload a video to be displayed as the hero background', 'storefront-parallax-hero' ),
			'settings'    => 'sph_hero_background_image',
			'flex_width'  => false,
			'flex_height' => false,
			'width'       => 1920,
			'height'      => 2560,
			'priority'    => 20,
		) ) );

		/**
		 * Background size
		 */
		$wp_customize->add_setting( 'sph_background_size', array(
			'default' => 'auto',
		) );

		$wp_customize->add_control( 'sph_background_size', array(
				'label'			=> __( 'Background image size', 'storefront-parallax-hero' ),
				'description'	=> __( 'When using a background image, specify which background size method to apply', 'storefront-parallax-hero' ),
				'section'		=> 'sph_section_background',
				'settings'		=> 'sph_background_size',
				'type'			=> 'select',
				'priority'		=> 30,
				'choices'		=> array(
					'auto'			=> 'Default',
					'cover'			=> 'Cover',
				),
			)
		);

        /**
	     * Background Video
	     */
		$wp_customize->add_setting( 'sph_hero_background_video', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

	    $wp_customize->add_control( new WP_Customize_Upload_Control( $wp_customize, 'sph_hero_background_video', array(
            'label'			=> __( 'Background video', 'storefront-parallax-hero' ),
            'description'	=> __( 'Upload a video to be displayed as the hero background', 'storefront-parallax-hero' ),
	        'section'		=> 'sph_section_background',
	        'settings'		=> 'sph_hero_background_video',
	        'priority'		=> 40,
	    ) ) );

	    if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_media_divider', array(
				'section'  	=> 'sph_section_background',
				'type'		=> 'divider',
				'priority' 	=> 45,
			) ) );
	    }

		/**
		 * Background video image fallback
		 */
		$wp_customize->add_setting( 'sph_hero_background_video_image_fallback', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( new WP_Customize_Cropped_Image_Control( $wp_customize, 'sph_hero_background_video_image_fallback', array(
			'section'     => 'sph_section_background',
			'label'       => __( 'Background image video fallback', 'storefront-parallax-hero' ),
			'description' => __( 'Autoplay of videos is not support by some mobile browsers. Use this option to display an image instead.', 'storefront-parallax-hero' ),
			'settings'    => 'sph_hero_background_video_image_fallback',
			'flex_width'  => false,
			'flex_height' => false,
			'width'       => 1920,
			'height'      => 2560,
			'priority'    => 46,
		) ) );


		/**
		 * Background Color
		 */
		$wp_customize->add_setting( 'sph_background_color', array(
			'default'           => '#2c2d33',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sph_background_color', array(
			'label'       => __( 'Background color', 'storefront-parallax-hero' ),
			'description' => __( 'Set the background color for the hero component (the background might not always be visible)' ),
			'section'     => 'sph_section_background',
			'settings'    => 'sph_background_color',
			'priority'    => 50,
		) ) );

	    if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_parallax_hero_divider', array(
				'section'  => 'sph_section_background',
				'type'     => 'divider',
				'priority' => 54,
			) ) );
	    }

	    /**
	     * Parallax
	     */
	    $wp_customize->add_setting( 'sph_hero_parallax', array(
	        'default'		=> true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sph_hero_parallax', array(
            'label'			=> __( 'Parallax', 'storefront-parallax-hero' ),
            'description'	=> __( 'Enable the parallax scrolling effect', 'storefront-parallax-hero' ),
            'section'		=> 'sph_section_background',
            'settings'		=> 'sph_hero_parallax',
            'type'			=> 'checkbox',
            'priority'		=> 55,
        ) ) );

        /**
	     * Parallax scroll speed
	     */
        $wp_customize->add_setting( 'sph_parallax_scroll_ratio', array(
            'default'			=> '0.5',
        ) );
        $wp_customize->add_control( 'sph_parallax_scroll_ratio', array(
				'label'			=> __( 'Parallax scroll speed', 'storefront-parallax-hero' ),
				'description'	=> __( 'The speed at which the parallax background scrolls relative to the window', 'storefront-parallax-hero' ),
				'section'		=> 'sph_section_background',
				'settings'		=> 'sph_parallax_scroll_ratio',
				'type'			=> 'select',
				'priority'		=> 56,
				'choices'		=> array(
					'0.25'			=> '25%',
					'0.5'			=> '50%',
					'0.75'			=> '75%',
				),
			)
		);

        /**
         * Parallax Offset
         */
        $wp_customize->add_setting( 'sph_parallax_offset', array(
            'default'			=> 0,
            'sanitize_callback'	=> 'esc_attr',
        ) );
		$wp_customize->add_control( 'sph_parallax_offset', array(
		    'type'        => 'range',
		    'priority'    => 57,
		    'section'     => 'sph_section_background',
		    'label'			=> __( 'Parallax offset', 'storefront-parallax-hero' ),
			'description'	=> __( 'Offset the starting position of your background image', 'storefront-parallax-hero' ),
		    'input_attrs' => array(
		        'min'   => -500,
		        'max'   => 500,
		        'step'  => 1,
		    ),
		) );

		if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
	        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_parallax_hero_offset_divider', array(
				'section'  	=> 'sph_section_background',
				'type'		=> 'divider',
				'priority' 	=> 57,
			) ) );
	    }

	    /**
	     * Overlay color
	     */
		$wp_customize->add_setting( 'sph_overlay_color', array(
	        'default'				=> '#000000',
	        'description'			=> __( 'Specify the overlay background color', 'storefront-parallax-hero' ),
	        'sanitize_callback'		=> 'sanitize_hex_color',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sph_overlay_color', array(
	        'label'		=> 'Overlay color',
	        'section'	=> 'sph_section_background',
	        'settings'	=> 'sph_overlay_color',
	        'priority'	=> 57,
	    ) ) );

	    /**
	     * Overlay opacity
	     */
        $wp_customize->add_setting( 'sph_overlay_opacity', array(
            'default'		=> '0.5',
        ) );
        $wp_customize->add_control( 'sph_overlay_opacity', array(
				'label'			=> __( 'Overlay opacity', 'storefront-parallax-hero' ),
				'section'		=> 'sph_section_background',
				'settings'		=> 'sph_overlay_opacity',
				'type'			=> 'select',
				'priority'		=> 58,
				'choices'		=> array(
					'0'			=> '0%',
					'0.1'		=> '10%',
					'0.2'		=> '20%',
					'0.3'		=> '30%',
					'0.4'		=> '40%',
					'0.5'		=> '50%',
					'0.6'		=> '60%',
					'0.7'		=> '70%',
					'0.8'		=> '80%',
					'0.9'		=> '90%',
				),
			)
		);

	    /**
		 * Button Text
		 */
	    $wp_customize->add_setting( 'sph_hero_button_text', array(
	        'default'			=> __( 'Go shopping', 'storefront-parallax-hero' ),
	        'sanitize_callback'	=> 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sph_hero_button_text', array(
            'label'			=> __( 'Button text', 'storefront-parallax-hero' ),
            'section'		=> 'sph_section_content',
            'settings'		=> 'sph_hero_button_text',
            'type'			=> 'text',
            'priority'		=> 70,
        ) ) );

        /**
		 * Button Text
		 */
	    $wp_customize->add_setting( 'sph_hero_button_url', array(
	        'default'			=> home_url(),
	        'sanitize_callback'	=> 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sph_hero_button_url', array(
            'label'			=> __( 'Button url', 'storefront-parallax-hero' ),
            'section'		=> 'sph_section_content',
            'settings'		=> 'sph_hero_button_url',
            'type'			=> 'text',
            'priority'		=> 80,
        ) ) );

        /**
	     * Alignment
	     */
	    $wp_customize->add_setting( 'sph_alignment', array(
            'default'		=> 'center',
        ) );
        $wp_customize->add_control( 'sph_alignment', array(
				'label'		=> __( 'Text alignment', 'storefront-parallax-hero' ),
				'section'	=> 'sph_section_layout',
				'settings'	=> 'sph_alignment',
				'type'		=> 'select',
				'priority'	=> 90,
				'choices'	=> array(
					'left'		=> 'Left',
					'center'	=> 'Center',
					'right'		=> 'Right',
				),
			)
		);

		/**
	     * Layout
	     */
	    $wp_customize->add_setting( 'sph_layout', array(
            'default'		=> 'full',
        ) );
        $wp_customize->add_control( 'sph_layout', array(
				'label'		=> __( 'Hero layout', 'storefront-parallax-hero' ),
				'section'	=> 'sph_section_layout',
				'settings'	=> 'sph_layout',
				'type'		=> 'select',
				'priority'	=> 100,
				'choices'	=> array(
					'full'	=> 'Full width',
					'fixed'	=> 'Fixed width',
				),
			)
		);

		/**
	     * Full height
	     */
	    $wp_customize->add_setting( 'sph_hero_full_height', array(
	        'default'		=> false,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sph_hero_full_height', array(
            'label'			=> __( 'Full height', 'storefront-parallax-hero' ),
            'description'	=> __( 'Set the hero component to full height. Works best when the Hero is the first element in your homepage content area.', 'storefront-parallax-hero' ),
            'section'		=> 'sph_section_layout',
            'settings'		=> 'sph_hero_full_height',
            'type'			=> 'checkbox',
            'priority'		=> 110,
        ) ) );

	}

	/**
	 * Display the hero section
	 * @see get_theme_mod()
	 */
	public static function display_parallax_hero( $atts ) {

		$atts = extract( shortcode_atts( array(
			'heading_text'                    => sanitize_text_field( get_theme_mod( 'sph_hero_heading_text', __( 'Heading Text', 'storefront-parallax-hero' ) ) ),
			'heading_text_color'              => get_theme_mod( 'sph_heading_color', '#ffffff' ),
			'description_text'                => wp_kses_post( get_theme_mod( 'sph_hero_text', __( 'Description Text', 'storefront-parallax-hero' ) ) ),
			'description_text_color'          => get_theme_mod( 'sph_hero_text_color', '#5a6567' ),
			'background_media'                => sanitize_text_field( get_theme_mod( 'sph_hero_background_media', 'none' ) ),
			'background_image'                => sanitize_text_field( get_theme_mod( 'sph_hero_background_image', '' ) ),
			'background_video'                => sanitize_text_field( get_theme_mod( 'sph_hero_background_video', '' ) ),
			'background_video_image_fallback' => sanitize_text_field( get_theme_mod( 'sph_hero_background_video_image_fallback', '' ) ),
			'background_color'                => sanitize_text_field( get_theme_mod( 'sph_background_color', '#2c2d33' ) ),
			'background_size'                 => get_theme_mod( 'sph_background_size', 'auto' ),
			'button_text'                     => sanitize_text_field( get_theme_mod( 'sph_hero_button_text', __( 'Go shopping', 'storefront-parallax-hero' ) ) ),
			'button_url'                      => sanitize_text_field( get_theme_mod( 'sph_hero_button_url', home_url() ) ),
			'alignment'                       => get_theme_mod( 'sph_alignment', 'center' ),
			'layout'                          => 'fixed',
			'parallax'                        => get_theme_mod( 'sph_hero_parallax', true ),
			'parallax_scroll'                 => get_theme_mod( 'sph_parallax_scroll_ratio', '0.5' ),
			'parallax_offset'                 => get_theme_mod( 'sph_parallax_offset', 0 ),
			'overlay_color'                   => get_theme_mod( 'sph_overlay_color', '#000000' ),
			'overlay_opacity'                 => get_theme_mod( 'sph_overlay_opacity', '0.5' ),
			'full_height'                     => get_theme_mod( 'sph_hero_full_height', false ),
			'style'                           => '',
			'overlay_style'                   => '',
			'background_img'                  => false,
			'shortcode_uid'                   => false,
		), $atts, 'parallax_hero' ) );

		// Get RGB color of overlay from HEX
		list( $r, $g, $b ) 			= sscanf( $overlay_color, "#%02x%02x%02x" );

		// Determine the file type of the background item
		$is_image 					= false;
		$is_video 					= false;

		// Image or video?
		if ( ! $background_img ) { // Support for shortcode
			if ( $background_media && 'none' !== $background_media ) {
				if ( 'image' === $background_media ) {
					if ( isset( $background_image ) && '' !== $background_image  ) {
						$background_img = wp_get_attachment_url( absint( $background_image ) );
					}
				}

				if ( 'video' === $background_media ) {
					$background_img = $background_video;
				}
			} elseif ( '' !== get_theme_mod( 'sph_hero_background_img', '' ) ) { // < 1.5.0
				$background_img = get_theme_mod( 'sph_hero_background_img', '' );
			}
		}

		if ( $background_img ) {
			$filetype 				= wp_check_filetype( $background_img );

			// Is it a video or an image?
			if ( $filetype['ext'] == 'jpg' || $filetype['ext'] == 'jpeg' || $filetype['ext'] == 'gif' || $filetype['ext'] == 'png' || $filetype['ext'] == 'bmp' || $filetype['ext'] == 'tif' || $filetype['ext'] == 'tiff' || 'ico' ) {
				$is_image = true;
				$is_video = false;
			}

			if ( $filetype['ext'] == 'mp4' || $filetype['ext'] == 'm4v' || $filetype['ext'] == 'mov' || $filetype['ext'] == 'wmv' || $filetype['ext'] == 'avi' || $filetype['ext'] == 'mpg' || $filetype['ext'] == 'ogv' || $filetype['ext'] == '3gp' || $filetype['ext'] == '3g2' ) {
				$is_video = true;
				$is_image = false;
			}
		}

		// Include the parallax script if required and set the scroll ratio variable
		$stellar = '';

		if ( true == $parallax ) {
			wp_enqueue_script( 'sph-stellar-init' );
			wp_enqueue_script( 'stellar' );

			$stellar = 'data-stellar-background-ratio="' . $parallax_scroll . '"';
		}

		$full_height_class 			= '';

		if ( true == $full_height ) {
			$full_height_class 		= 'sph-full-height';
			wp_enqueue_script( 'sph-full-height' );
		}

		// If shortcode, append id for custom CSS
		$section_id = '';
		if ( false !== $shortcode_uid ) {
			$section_id = 'id="sph-' . $shortcode_uid . '"';
		}

		/**
		 * If the background item is an image the parallax attributes need to be applied to the main wrapper
		 */
		if ( true == $is_image ) { ?>
			<section <?php echo $section_id; ?> data-stellar-vertical-offset="<?php echo intval( $parallax_offset ); ?>" <?php echo $stellar; ?> class="sph-hero <?php echo esc_attr( $alignment ) . ' ' . esc_attr( $layout ) . ' ' . $full_height_class; ?>" style="<?php echo esc_attr( $style ); ?>background-image: url(<?php echo esc_url( $background_img ); ?>); background-color: <?php echo esc_attr( $background_color ); ?>; color: <?php echo esc_attr( $description_text_color ); ?>; background-size: <?php echo esc_attr( $background_size ); ?>;">
		<?php } else { ?>
			<section <?php echo $section_id; ?> class="sph-hero <?php echo esc_attr( $alignment ) . ' ' . esc_attr( $layout ) . ' ' . $full_height_class; ?>" style="background-color: <?php echo esc_attr( $background_color ); ?>; color: <?php echo esc_attr( $description_text_color ); ?>;">
		<?php } ?>

			<?php
			/**
			 * If the background item is a video, let's load the html5 video player
			 */
			if ( true == $is_video ) { ?>
			<div class="video-wrapper" data-stellar-vertical-offset="<?php echo intval( $parallax_offset ); ?>" data-stellar-ratio="<?php echo esc_attr( $parallax_scroll ); ?>">
				<video src="<?php echo esc_url( $background_img ); ?>" <?php echo apply_filters( 'storefront_parallax_hero_video_attributes', $atts = 'autoplay loop preload muted' ); ?> class="sph-video" height="auto" width="auto"></video>

				<?php
					$fallback_image = false;
					if ( '' !== $background_video_image_fallback ) {
						$fallback_image = wp_get_attachment_url( absint( $background_video_image_fallback ) );
					}
				?>

				<?php if ( $fallback_image ) { ?>
				<div class="sph-video-image-fallback" style="background-image: url(<?php echo esc_url( $fallback_image ); ?>);"></div>
				<?php } ?>
			</div>
			<?php } ?>

			<div class="overlay animated" style="background-color: rgba(<?php echo $r . ', ' . $g . ', ' . $b . ', ' . $overlay_opacity; ?>);<?php echo $overlay_style; ?>">

				<div class="sph-inner-wrapper">

					<div class="col-full sph-inner">

						<?php do_action( 'sph_content_before' ); ?>

						<h1 style="color: <?php echo $heading_text_color; ?>;" data-content="<?php echo esc_attr( $heading_text ); ?>"><span><?php echo esc_attr( $heading_text ); ?></span></h1>

						<div class="sph-hero-content-wrapper">
							<div class="sph-hero-content">
								<?php echo wpautop( $description_text ); ?>

								<?php if ( $button_text && $button_url ) { ?>
									<p>
										<a href="<?php echo $button_url; ?>" class="button"><?php echo $button_text; ?></a>
									</p>
								<?php } ?>
							</div>
						</div>

						<?php do_action( 'sph_content_after' ); ?>
					</div>

				</div>

			</div>
		</section>
		<?php
		// Load the general sph scripts
		wp_enqueue_script( 'sph-script' );
	}

	/**
	 * Display the hero section via shortcode
	 * @see display_parallax_hero()
	 */
	public static function display_parallax_hero_shortcode( $atts ) {
		$atts = ( is_array( $atts ) ? $atts : array() );
		$hero = new Storefront_Parallax_Hero();

		// Generate unique id for this shortcode
		$hash                  = md5( json_encode( $atts ) );
		$atts['shortcode_uid'] = $hash;

		ob_start();
		$hero->display_parallax_hero( $atts );
		return ob_get_clean();
	}

	/**
	 * Display the hero section via homepage action
	 * @see display_parallax_hero()
	 */
	public static function homepage_parallax_hero( $atts ) {

		// Default just for homepage customizer one needs to be full, so set that there
		$atts = array( 'layout' => get_theme_mod( 'sph_layout', 'full' ) );

		Storefront_Parallax_Hero::display_parallax_hero( $atts );
	}

	/**
	 * Homepage callback
	 * @return bool
	 */
	public function storefront_homepage_template_callback() {
		return is_page_template( 'template-homepage.php' ) ? true : false;
	}

	/**
	 * Add CSS for custom controls
	 *
	 * @since  1.5.0
	 */
	public function customizer_custom_control_css() {
		?>
		<style>
		.sp-buttonset {
			background-color: #ddd;
			overflow: hidden;
			zoom: 1;
			display: inline-block;
			border-radius: 3px;
			box-shadow: inset 0 1px 2px rgba(0,0,0,0.15), 0 1px 1px rgba(255,255,255,.5);
		}

		.sp-buttonset label {
			display: inline-block;
			padding: 5px 10px;
			border-right: 1px solid #ccc;
			float: left;
		}

		.sp-buttonset label:first-child {
			border-radius: 3px 0 0 3px;
		}

		.sp-buttonset label:last-child {
			border-right: 0;
			border-radius: 0 3px 3px 0;
		}

		.sp-buttonset label.ui-state-active {
			background-color: #008ec2;
			color: #fff;
			text-shadow: 0 -1px 1px #006799, 1px 0 1px #006799, 0 1px 1px #006799, -1px 0 1px #006799;
			box-shadow: inset 0 0 0 1px rgba(0,0,0,0.2);
		}

		#accordion-section-storefront_homepage .customize-control-checkbox:not(#customize-control-sp_reviews_carousel):not(#customize-control-sp_reviews_gravatar) label {
			margin-left: 0;
			font-weight: 700;
			font-size: 14px;
		}

		#accordion-section-storefront_homepage .customize-control-checkbox:not(#customize-control-sp_reviews_carousel):not(#customize-control-sp_reviews_gravatar) input[type=checkbox] {
			width: 40px;
			height: 20px;
			position: relative;
			background-color: #fff;
			border-radius: 4em;
			border: 1px solid #ccc;
			box-sizing: content-box;
			float: right;
			margin-top: -1px;
			transition: all ease .2s;
		}

		#accordion-section-storefront_homepage .customize-control-checkbox:not(#customize-control-sp_reviews_carousel):not(#customize-control-sp_reviews_gravatar) input[type=checkbox]:before {
			content: "";
			display: block;
			height: 20px;
			width: 20px;
			background-color: #fff;
			position: absolute;
			top: 0px;
			left: 0px;
			margin: 0;
			border-radius: 100%;
			box-shadow: 0 1px 3px rgba(0,0,0,.5);
			transition: margin ease .2s;
		}

		#accordion-section-storefront_homepage .customize-control-checkbox:not(#customize-control-sp_reviews_carousel):not(#customize-control-sp_reviews_gravatar) input[type=checkbox]:checked {
			background-color: #0085ba;
			border-color: #0085ba;
		}

		#accordion-section-storefront_homepage .customize-control-checkbox:not(#customize-control-sp_reviews_carousel):not(#customize-control-sp_reviews_gravatar) input[type=checkbox]:focus {
			box-shadow: none;
		}

		#accordion-section-storefront_homepage .customize-control-checkbox:not(#customize-control-sp_reviews_carousel):not(#customize-control-sp_reviews_gravatar) input[type=checkbox]:checked:before {
			margin-left: calc(100% - 20px);
		}
		</style>
		<?php
	}

} // End Class

// Create a shortcode to display the hero
add_shortcode( 'parallax_hero', array( 'Storefront_Parallax_Hero', 'display_parallax_hero_shortcode' ) );