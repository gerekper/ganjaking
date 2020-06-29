<?php
/**
 * Pre-built websites
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 * @version 2.2
 */

if ( ! defined( 'ABSPATH' ) ){
	exit;
}

class Mfn_Importer {

	private $error	= array();
	private $failed	= array();

	private $demos 	= array();

	private $categories = array(
		'bus'	=> 'Business',
		'ent'	=> 'Entertainment',
		'cre'	=> 'Creative',
		'blo'	=> 'Blog',
		'por'	=> 'Portfolio',
		'one'	=> 'One Page',
		'sho'	=> 'Shop',
		'ele'	=> 'Elementor',
		'oth'	=> 'Other',
	);

	private $plugins = array(
		'bud'	=> array(
			'name' 	=> 'BuddyPress',
			'class' => 'BuddyPress',
			's'	=> 'BuddyPress',
		),
		'cf7'	=> array(
			'name' => 'Contact Form 7',
			'class' => 'WPCF7',
		),
		'ele'	=> array(
			'name' => 'Elementor',
			'class' => 'Elementor\Plugin',
		),
		'mch'	=> array(
			'name' => 'MailChimp',
			'class' => 'MC4WP_MailChimp',
			's' => 'Mailchimp+for+WordPress',
		),
		'rev'	=> array(
			'name' => 'Revolution Slider',
			'class' => 'RevSlider',
		),
		'woo'	=> array(
			'name' => 'WooCommerce',
			'class'	=> 'WooCommerce',
			's'	=> 'WooCommerce',
		),
	);

	/**
	 * Constructor
	 */

	function __construct() {

		// Set demos list

		require_once(get_theme_file_path('/functions/importer/demos.php'));
		$this->demos = $demos;

		// It runs after the basic admin panel menu structure is in place

		add_action( 'admin_menu', array( $this, 'init' ), 12 );

		// Removes Elementor filter in case it will be deprecated some day and add our own code
		// https://github.com/elementor/elementor/issues/10774

		remove_filter( 'wp_import_post_meta', array( 'Elementor\Compatibility', 'on_wp_import_post_meta') );
		add_filter( 'wp_import_post_meta', array( $this, 'on_wp_import_post_meta') );

		// Ajax | database reset

		add_action('wp_ajax_mfn_db_reset', array( $this, '_db_reset' ));

	}

	/**
	 *
	 */

	public function _db_reset()
	{
		global $wpdb;

		check_ajax_referer( 'mfn-importer-nonce', 'mfn-importer-nonce' );

		$wpdb->query( "TRUNCATE TABLE $wpdb->posts" );
		$wpdb->query( "TRUNCATE TABLE $wpdb->postmeta" );
		$wpdb->query( "TRUNCATE TABLE $wpdb->comments" );
		$wpdb->query( "TRUNCATE TABLE $wpdb->commentmeta" );
		$wpdb->query( "TRUNCATE TABLE $wpdb->terms" );
		$wpdb->query( "TRUNCATE TABLE $wpdb->termmeta" );
		$wpdb->query( "TRUNCATE TABLE $wpdb->term_taxonomy" );
		$wpdb->query( "TRUNCATE TABLE $wpdb->term_relationships" );
		$wpdb->query( "TRUNCATE TABLE $wpdb->links" );

		esc_html_e('Database was reset', 'mfn-opts');

		exit;
	}

	/**
	 * Add theme page & enqueue styles
	 */

	function init() {

		$this->page = add_submenu_page(
			'betheme',
			__( 'Install pre-built website', 'mfn-opts' ),
			__( 'Pre-built websites', 'mfn-opts' ),
			'edit_theme_options',
			'be-websites',
			array( $this, 'import' )
		);

		add_action( 'admin_print_styles-'.$this->page, array( $this, '_enqueue' ) );

	}

	/**
	 * Enqueue
	 */

	function _enqueue(){

		wp_enqueue_style('mfn-opts-font', 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,400italic,600');
		wp_enqueue_style('mfn-import', get_theme_file_uri('/functions/importer/css/style.css'), false, MFN_THEME_VERSION, 'all');

		wp_enqueue_script('mfn-import', get_theme_file_uri('/functions/importer/js/scripts.js'), false, MFN_THEME_VERSION, true);

	}

	/**
	 * Process post meta before WP importer.
	 *
	 * Normalize Elementor post meta on import, We need the `wp_slash` in order
	 * to avoid the unslashing during the `add_post_meta`
	 *
	 * @todo: Required by WordPress Importer 0.6.4 - can be removed when migrate to 0.7
	 */

	function on_wp_import_post_meta( $post_meta ) {

		foreach ( $post_meta as &$meta ) {
			if ( '_elementor_data' === $meta['key'] ) {
				$meta['value'] = wp_slash( $meta['value'] );
				break;
			}
		}

		return $post_meta;
	}

	/**
	 * Demo URL
	 *
	 * Get demo url to replace
	 *
	 * @param string $demo
	 * @return string
	 */

	function get_demo_url( $demo ){

		if( $demo == 'theme' ){

			$url = 'http://themes.muffingroup.com/betheme/';

		} else {

			$url = array(
				'http://themes.muffingroup.com/be/'. $demo .'/',
				'https://themes.muffingroup.com/be/'. $demo .'/',
			);

		}

		return $url;
	}

	/**
	 * Get FILE data
	 *
	 * @param $file string
	 * @param $method string
	 * @return string
	 */

	function get_file_data( $path ){

		$data = false;
		$path = wp_normalize_path( $path );
		$wp_filesystem = Mfn_Helper::filesystem();

		if( $wp_filesystem->exists( $path ) ){

			if( ! $data = $wp_filesystem->get_contents( $path ) ){

				$fp = fopen( $path, 'r' );
				$data = fread( $fp, filesize( $path ) );
				fclose( $fp );

			}

		}

		return $data;
	}

	/**
	 * Elementor
	 */

	function elementor_settings( $demo ){

		$wrapper = '1140';

		if( isset( $this->demos[$demo]['wrapper'] ) ){
			$wrapper = $this->demos[$demo]['wrapper'];
		}

		$elementor_settings = [
			'elementor_disable_color_schemes' => 'yes',
			'elementor_disable_typography_schemes' => 'yes',
			'elementor_load_fa4_shim' => 'yes',
			'elementor_container_width' => $wrapper,
			'elementor_stretched_section_container' => '#Wrapper',
			'elementor_viewport_lg' => '960',
			'elementor_cpt_support' => [ 'post', 'page', 'portfolio' ],
		];

		foreach ( $elementor_settings as $key => $value ) {
			update_option( $key, $value );
		}

	}

	/**
	 * Import | Content
	 *
	 * @param string $file
	 * @param string $demo
	 */

	function import_content( $file, $demo ){

		$this->import_xml( $file );

		// Muffin Builder

		$this->replace_builder( $this->get_demo_url( $demo ) );

		// Elementor

		$this->replace_elementor( $this->get_demo_url( $demo ) );

		$this->elementor_settings( $demo );

		if ( class_exists( 'Elementor\Plugin' ) ){
			Elementor\Plugin::$instance->files_manager->clear_cache();
		}

	}

	/**
	 * Import | XML
	 *
	 * @param string $file
	 */

	function import_xml( $file ){

		$import = new WP_Import();

		if( $_POST[ 'attachments' ] && ( $_POST[ 'type' ] == 'complete' ) ){
			$import->fetch_attachments = true;
		} else {
			$import->fetch_attachments = false;
		}

		ob_start();
		$import->import( $file );
		ob_end_clean();
	}

	/**
	 * Import | Menu - Locations
	 *
	 * @param string $file
	 */

	function import_menu_location( $file ){

		$file_data = $this->get_file_data( $file );
		$data = unserialize( call_user_func( 'base'.'64_decode', $file_data ) );

		if( is_array( $data ) ){

			$menus = wp_get_nav_menus();

			foreach( $data as $key => $val ){
				foreach( $menus as $menu ){
					if( $val && $menu->slug == $val ){
						$data[$key] = absint( $menu->term_id );
					}
				}
			}

			set_theme_mod( 'nav_menu_locations', $data );

		} else {

			$this->failed['menu'] = true;

		}
	}

	/**
	 * Set homepage
	 */

	function set_homepage(){

		$home = get_page_by_title( 'Home' );

		if( isset( $home->ID ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $home->ID ); // Front Page
		}

	}

	/**
	 * Import | Theme Options
	 *
	 * @param string $file
	 * @param string $url
	 */

	function import_options( $file, $url = false ){

		$file_data 	= $this->get_file_data( $file );
		$data = unserialize( call_user_func( 'base'.'64_decode', $file_data ) );

		if( is_array( $data ) ){

			// images URL | replace exported URL with destination URL

			if( $url ){
				$replace = home_url('/');
				foreach( $data as $key => $option ){
					if( is_string( $option ) ){
						// variable type string only
						$option = $this->replace_multisite( $option );
						$data[$key] = str_replace( $url, $replace, $option );
					}
				}
			}

			ob_start();
			update_option( 'betheme', $data );
			ob_end_clean();

		} else {

			$this->failed['options'] = true;

		}
	}

	/**
	 * Import | Widgets
	 *
	 * @param string $file
	 */

	function import_widget( $file ){

		$file_data 	= $this->get_file_data( $file );

		if( $file_data ){

			$this->import_widget_data( $file_data );

		} else {

			$this->failed['widgets'] = true;

		}
	}

	/**
	 * Import | Revolution Slider
	 *
	 * @param string $demo
	 */

	function import_slider( $demo_path, $demo ){

		$sliders = array();
		$demo_args = $this->demos[ $demo ];

		if( ! isset( $demo_args['plugins'] ) ){
			return false;
		}

		if( false === array_search( 'rev', $demo_args['plugins'] ) ){
			return false;
		}

		if( ! class_exists( 'RevSliderSlider' ) ){
			return false;
		}

		if( isset( $demo_args['revslider'] ) ){

			// multiple sliders
			foreach( $demo_args['revslider'] as $slider ){
				$sliders[] = $slider;
			}

		} else {

			// single slider
			$sliders[] = $demo .'.zip';

		}

		if( is_callable( array( 'RevSliderSlider', 'importSliderFromPost' ) ) ){

			// RevSlider pre 6.0
			$revslider = new RevSliderSlider();

			foreach( $sliders as $slider ){

				ob_start();
					$file = wp_normalize_path( $demo_path .'/'. $slider );
					$revslider->importSliderFromPost( true, false, $file );
				ob_end_clean();

			}

		} elseif( is_callable( array( 'RevSliderSliderImport', 'import_slider' ) ) ){

			// RevSlider 6.0 +
			$revslider = new RevSliderSliderImport();

			foreach( $sliders as $slider ){

				ob_start();
					$file = wp_normalize_path( $demo_path .'/'. $slider );
					$revslider->import_slider( true, $file );
				ob_end_clean();

			}

		} else {

			return new WP_Error( 'rev_update', 'Revolution Slider is outdated. Please update plugin.' );

		}

		return true;
	}

	/**
	 * Replace Multisite URLs
	 *
	 * Multisite 'uploads' directory url
	 *
	 * @param string $field
	 * @return string
	 */

	function replace_multisite( $field ){

		if ( is_multisite() ){

			global $current_blog;

			if( $current_blog->blog_id > 1 ){
				$old_url = '/wp-content/uploads/';
				$new_url = '/wp-content/uploads/sites/'. $current_blog->blog_id .'/';
				$field = str_replace( $old_url, $new_url, $field );
			}

		}

		return $field;
	}

	/**
	 * Replace Elementor URLs
	 *
	 * @param string $old_url
	 */

	function replace_elementor( $old_url ){

		global $wpdb;

		if( is_array($old_url) ){
			$old_url = $old_url[1]; // https
		}

		$old_url = str_replace('/','\/',$old_url);
		$new_url = home_url('/');

		// FIX: importer new line characters in longtext

		$wpdb->query($wpdb->prepare("UPDATE $wpdb->postmeta
			SET `meta_value` =
			REPLACE( meta_value, %s, %s)
			WHERE `meta_key` = '_elementor_data'
		", "\n", ""));

		// replace urls

		$wpdb->query($wpdb->prepare("UPDATE $wpdb->postmeta
			SET `meta_value` =
			REPLACE( meta_value, %s, %s)
			WHERE `meta_key` = '_elementor_data'
		", $old_url, $new_url));

	}

	/**
	 * Replace Muffin Builder URLs
	 *
	 * @param array $old_url
	 */

	function replace_builder( $old_url ){

		global $wpdb;

		$uids = array();
		$new_url = home_url('/');

		$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->postmeta
			WHERE `meta_key` = %s
		", 'mfn-page-items'));

		// posts loop -----

		if( is_array( $results ) ){
			foreach( $results as $result_key => $result ){

				$meta_id = $result->meta_id;
				$meta_value = @unserialize( $result->meta_value );

				// builder 2.0 compatibility

				if( $meta_value === false ){
					$meta_value = unserialize(call_user_func('base'.'64_decode', $result->meta_value));
				}

				// SECTIONS

				if( is_array( $meta_value ) ){
					foreach( $meta_value as $sec_key => $sec ){

						// section uIDs

						if( empty( $sec['uid'] ) ){
							$uids[] = Mfn_Builder_Helper::unique_ID($uids);
							$meta_value[$sec_key]['uid'] = end($uids);
						} else {
							$uids[] = $sec['uid'];
						}

						// section attributes

						if( isset( $sec['attr'] ) && is_array( $sec['attr'] ) ){
							foreach( $sec['attr'] as $attr_key => $attr ){
								$attr = str_replace( $old_url, $new_url, $attr );
								$meta_value[$sec_key]['attr'][$attr_key] = $attr;
							}
						}

						// WRAPS

						if( isset( $sec['wraps'] ) && is_array( $sec['wraps'] ) ){
							foreach( $sec['wraps'] as $wrap_key => $wrap ){

								// wrap uIDs

								if( empty( $wrap['uid'] ) ){
									$uids[] = Mfn_Builder_Helper::unique_ID($uids);
									$meta_value[$sec_key]['wraps'][$wrap_key]['uid'] = end($uids);
								} else {
									$uids[] = $wrap['uid'];
								}

								// wrap attributes

								if( isset( $wrap['attr'] ) && is_array( $wrap['attr'] ) ){
									foreach( $wrap['attr'] as $attr_key => $attr ){

										$attr = str_replace( $old_url, $new_url, $attr );
										$meta_value[$sec_key]['wraps'][$wrap_key]['attr'][$attr_key] = $attr;

									}
								}

								// ITEMS

								if( isset( $wrap['items'] ) && is_array( $wrap['items'] ) ){
									foreach( $wrap['items'] as $item_key => $item ){

										// item uIDs

										if( empty( $item['uid'] ) ){
											$uids[] = Mfn_Builder_Helper::unique_ID($uids);
											$meta_value[$sec_key]['wraps'][$wrap_key]['items'][$item_key]['uid'] = end($uids);
										} else {
											$uids[] = $item['uid'];
										}

										// item fields

										if( isset( $item['fields'] ) && is_array( $item['fields'] ) ){
											foreach( $item['fields'] as $field_key => $field ) {

												if( $field_key == 'tabs' ) {

													// tabs

													if( isset( $field ) && is_array( $field ) ){
														foreach( $field as $tab_key => $tab ){
															$field = str_replace( $old_url, $new_url, $tab['content'] );
															$field = $this->replace_multisite( $field );
															$meta_value[$sec_key]['wraps'][$wrap_key]['items'][$item_key]['fields'][$field_key][$tab_key]['content'] = $field;
														}
													}

												} else {

													// default

													$field = str_replace( $old_url, $new_url, $field );
													$field = $this->replace_multisite( $field );
													$meta_value[$sec_key]['wraps'][$wrap_key]['items'][$item_key]['fields'][$field_key] = $field;

												}

											}
										}

									}
								}

							}
						}

						// builder 2.0 WITHOUT wraps

						if( isset( $sec['items'] ) && is_array( $sec['items'] ) ){
							foreach( $sec['items'] as $item_key => $item ){

								// item uIDs

								if( empty( $item['uid'] ) ){
									$uids[] = Mfn_Builder_Helper::unique_ID($uids);
									$meta_value[$sec_key]['items'][$item_key]['uid'] = end($uids);
								} else {
									$uids[] = $item['uid'];
								}

								// item fields

								if( isset( $item['fields'] ) && is_array( $item['fields'] ) ){
									foreach( $item['fields'] as $field_key => $field ) {

										if( $field_key == 'tabs' ) {

											// tabs

											if( is_array( $field ) ){
												foreach( $field as $tab_key => $tab ){
													$field = str_replace( $old_url, $new_url, $tab['content'] );
													$field = $this->replace_multisite( $field );
													$meta_value[$sec_key]['items'][$item_key]['fields'][$field_key][$tab_key]['content'] = $field;
												}
											}

										} else {

											// default

											$field = str_replace( $old_url, $new_url, $field );
											$field = $this->replace_multisite( $field );
											$meta_value[$sec_key]['items'][$item_key]['fields'][$field_key] = $field;

										}

									}
								}

							}
						}

					}
				}

				// builder 2.0 compatibility

				$meta_value = call_user_func('base'.'64_encode', serialize( $meta_value ));

				$wpdb->query($wpdb->prepare("UPDATE $wpdb->postmeta
					SET `meta_value` = %s
					WHERE `meta_key` = 'mfn-page-items'
					AND `meta_id`= %d
				", $meta_value, $meta_id));

			}
		}
	}

	/**
	 * Returns formated error messages
	 *
	 * @return string
	 */

	public function error_messages( $errors ){

		$output = '';

		if( ! is_array( $errors ) ){
			return false;
		}

		foreach( $errors as $error ){
			echo '<div class="mfn-message mfn-error">'. esc_html($error) .'</div>';
		}

	}

	/**
	 * Import
	 */

	function import(){

		if( WHITE_LABEL ){
			require_once(get_theme_file_path('/functions/admin/templates/parts/white-label.php'));
			return false;
		}

		$output 	= '';

		if( isset( $_POST['mfn-importer-nonce'] ) ){

			// AFTER IMPORT --------------------

			if ( wp_verify_nonce( $_POST['mfn-importer-nonce'], 'mfn-importer-nonce' ) ){

				// Importer classes

				if( ! defined( 'WP_LOAD_IMPORTERS' ) ){
					define( 'WP_LOAD_IMPORTERS', true );
				}

				if( ! class_exists( 'WP_Importer' ) ){
					require_once(ABSPATH .'wp-admin/includes/class-wp-importer.php');
				}

				if( ! class_exists( 'WP_Import' ) ){
					require_once(get_theme_file_path('/functions/importer/wordpress-importer.php'));
				}

				// Import START

				if( class_exists( 'WP_Importer' ) && class_exists( 'WP_Import' ) ){

					$demo = htmlspecialchars( stripslashes( $_POST['demo'] ) );

					// Importer remote API

					require_once(get_theme_file_path('/functions/importer/class-mfn-importer-api.php'));
					$importer_api = new Mfn_Importer_API( $demo );
					$demo_path = get_theme_file_path( '/functions/importer/demo/be/' . $demo );

					if( ! $demo_path ){

						$this->error[] = __( 'Remote API error.', 'mfn-opts' );

					} elseif( is_wp_error( $demo_path ) ){

						$this->error[] = $demo_path->get_error_message();

					} else {

						if( 'selected' == $_POST['type'] ){

							// Selected data only ---------------------------------

							if( 'content' == $_POST['data'] ){

								// WordPress XML importer

								$file = wp_normalize_path( $demo_path .'/content.xml.gz' );
								$this->import_content( $file, $demo );

							} elseif( 'options' == $_POST['data'] ) {

								// Theme Options

								$file = wp_normalize_path( $demo_path .'/options.txt' );
								$this->import_options( $file, $this->get_demo_url( $demo ) );

							} else {

								// Revolution Slider

								$result = $this->import_slider( $demo_path, $demo );
								if( is_wp_error( $result ) ){
									$this->error[] = $result->get_error_message();
								}

							}

						} else {

							// Complete pre-built website ---------------------------------

							// WordPress XML importer

							$file = wp_normalize_path( $demo_path .'/content.xml.gz' );
							$this->import_content( $file, $demo );

							// Menu locations

							$file = wp_normalize_path( $demo_path .'/menu.txt' );
							$this->import_menu_location( $file );

							// Theme Options

							$file = wp_normalize_path( $demo_path .'/options.txt' );
							$this->import_options( $file, $this->get_demo_url( $demo ) );

							// Widgets

							$file = wp_normalize_path( $demo_path .'/widget_data.json' );
							$this->import_widget( $file );

							// Revolution Slider
							
							if( $_POST['slider'] ){
								$demo_path = get_theme_file_path( '/functions/importer/slider-revolution-demo/demos' );
								$result = $this->import_slider( $demo_path, $demo );
								if( is_wp_error( $result ) ){
									$this->error[] = $result->get_error_message();
								}
							}
							
							// Set homepage

							$this->set_homepage();

						}

						// delete temp dir

						$importer_api->delete_temp_dir();

					}

				}

			}

			$this->import_html( 'after', $output );

		} else {

			// BEFORE IMPORT --------------------

			$this->import_html( 'before' );

		}

	}

	/**
	 * Import HTML
	 *
	 * @param string $status
	 * @param string|array $output
	 */

	function import_html( $status, $output = '' ){
		?>

		<div class="mfn-demo-data wrap">

			<div id="mfn-overlay" <?php if( isset( $_GET['demo'] ) ) echo 'style="display:block"'; ?>><!-- overlay --></div>

			<form id="form" action="" method="post">

				<input type="hidden" name="mfn-importer-nonce" value="<?php echo wp_create_nonce( 'mfn-importer-nonce' ); ?>" />
				<input type="hidden" name="demo" id="input-demo" value="" />

				<input type="hidden" name="type" id="input-type" value="complete" />
				<input type="hidden" name="data" id="input-data" value="content" />
				<input type="hidden" name="attachments" id="input-attachments" value="1" />
				<input type="hidden" name="slider" id="input-slider" value="1" />

				<div class="header">
					<div class="logo">

						<svg width="30" height="20" xmlns="http://www.w3.org/2000/svg">
							<path d="M0,19.8V0h7.3c1.4,0,2.5,0.1,3.5,0.4c1,0.3,1.7,0.6,2.3,1.1c0.6,0.5,1,1,1.3,1.7c0.3,0.7,0.4,1.4,0.4,2.2
							c0,0.4-0.1,0.9-0.2,1.3c-0.1,0.4-0.3,0.8-0.6,1.2c-0.3,0.4-0.6,0.7-1,1c-0.4,0.3-0.9,0.5-1.5,0.8c1.3,0.3,2.3,0.8,2.9,1.5
							c0.6,0.7,0.9,1.6,0.9,2.7c0,0.8-0.2,1.6-0.5,2.3c-0.3,0.7-0.8,1.4-1.4,1.9c-0.6,0.5-1.4,1-2.3,1.3c-0.9,0.3-2,0.5-3.2,0.5H0z
							 M4.6,8.3H7c0.5,0,1,0,1.4-0.1c0.4-0.1,0.8-0.2,1-0.4C9.7,7.7,9.9,7.4,10,7.1c0.1-0.3,0.2-0.7,0.2-1.2c0-0.5-0.1-0.9-0.2-1.2
							C10,4.4,9.8,4.2,9.5,4C9.3,3.8,9,3.6,8.6,3.6C8.2,3.5,7.8,3.4,7.3,3.4H4.6V8.3z M4.6,11.4v4.9h3.2c0.6,0,1.1-0.1,1.5-0.2
							c0.4-0.2,0.7-0.4,0.9-0.6c0.2-0.2,0.4-0.5,0.4-0.8c0.1-0.3,0.1-0.6,0.1-0.9c0-0.4,0-0.7-0.1-1c-0.1-0.3-0.3-0.5-0.5-0.7
							c-0.2-0.2-0.5-0.4-0.9-0.5c-0.4-0.1-0.9-0.2-1.4-0.2H4.6z"/>
							<path d="M22.8,5.5c0.9,0,1.8,0.1,2.6,0.4c0.8,0.3,1.4,0.7,2,1.3c0.6,0.6,1,1.2,1.3,2c0.3,0.8,0.5,1.7,0.5,2.7
							c0,0.3,0,0.6,0,0.8c0,0.2-0.1,0.4-0.1,0.5s-0.2,0.2-0.3,0.2c-0.1,0-0.3,0.1-0.5,0.1H20c0.1,1.2,0.5,2,1.1,2.6
							c0.6,0.5,1.3,0.8,2.2,0.8c0.5,0,0.9-0.1,1.3-0.2c0.4-0.1,0.7-0.2,0.9-0.4c0.3-0.1,0.5-0.3,0.8-0.4c0.2-0.1,0.5-0.2,0.7-0.2
							c0.3,0,0.6,0.1,0.8,0.4l1.2,1.5c-0.4,0.5-0.9,0.9-1.4,1.2c-0.5,0.3-1,0.6-1.5,0.7s-1.1,0.3-1.6,0.4c-0.5,0.1-1,0.1-1.5,0.1
							c-1,0-1.9-0.2-2.8-0.5c-0.9-0.3-1.6-0.8-2.3-1.4c-0.6-0.6-1.2-1.4-1.5-2.4c-0.4-0.9-0.6-2-0.6-3.3c0-0.9,0.2-1.8,0.5-2.7
							c0.3-0.8,0.8-1.6,1.4-2.2C18.3,6.9,19,6.4,19.9,6C20.7,5.7,21.7,5.5,22.8,5.5z M22.8,8.4c-0.8,0-1.4,0.2-1.9,0.7
							c-0.5,0.5-0.8,1.1-0.9,2h5.3c0-0.3,0-0.7-0.1-1c-0.1-0.3-0.2-0.6-0.4-0.8C24.6,9,24.3,8.8,24,8.6C23.7,8.5,23.3,8.4,22.8,8.4z"/>
						</svg>

					</div>

					<div class="title"><?php echo esc_html(get_admin_page_title()) ?></div>
				</div>

				<?php if( 'after' == $status ): ?>

					<?php if( ! $this->error ): ?>

						<?php
							$demo = htmlspecialchars( stripslashes( $_POST['demo'] ) );
							$demo_args = $this->demos[ $demo ];

							// data | name

							if( isset( $demo_args['name'] ) ){
								$demo_name = $demo_args['name'];
							} else {
								$demo_name = ucfirst( $demo );
							}

							$slider = false;

							if( isset( $demo_args['plugins'] ) ){
								if( false !== array_search( 'rev', $demo_args['plugins'] ) ){
									$slider = true;
								}
							}

						?>

						<div class="import-all-done item" data-id="<?php echo esc_attr($demo); ?>">

							<div class="done-image">
								<div class="item-image"></div>
							</div>

							<div class="done-header">
								Be <?php echo esc_html($demo_name); ?> has been successfully installed<br />
								You have a new website now
							</div>

							<div class="done-subheader">
								What would you like to do next?
							</div>

							<div class="done-buttons">

								<a target="_blank" href="admin.php?page=be-options" class="mfn-button mfn-button-secondary">Go to Muffin Options</a>
								<a target="_blank" href="<?php echo esc_url(get_home_url()); ?>" class="mfn-button mfn-button-primary">Preview website</a>

							</div>

							<div class="done-learn">
								<span>or</span>
								<div class="learn-header">Learn more about BeTheme</div>
								Remember, it is a good practise to read the manual first
							</div>

							<div class="done-help">
								<a target="_blank" href="https://themes.muffingroup.com/betheme/documentation/">
									<span class="dashicons dashicons-info"></span>
									Learn how to use BeTheme from our manual
								</a>
							</div>

						</div>

					<?php
						else:

							// show errors

							$this->error_messages($this->error);

						endif;
					?>

				<?php else: ?>

					<div class="subheader">

						<div class="filters">
							<ul class="filter-categories">
								<li data-filter="*" class="active"><a href="javascript:void(0);">All</a></li>
								<?php
									foreach( $this->categories as $key_cat => $cat ){
										echo '<li data-filter="'. esc_attr($key_cat) .'"><a href="javascript:void(0);">'. esc_html($cat) .'</a></li>';
									}
								?>
							</ul>
						</div>

						<div class="demo-search">
							<span class="dashicons dashicons-search"></span>
							<input class="input-search" placeholder="Search website..." onkeypress="return event.keyCode != 13;" />
						</div>

					</div>

					<ul class="demos">
						<?php
							foreach( $this->demos as $key => $demo ){

								$categories = array_intersect_key( $this->categories, array_flip( $demo['categories'] ));
								$categories = implode( ', ', $categories );

								// class | categories

								$class = '';
								if( is_array( $demo['categories'] ) ){
									foreach( $demo['categories'] as $cat ){
										$class .= ' category-' .$cat;
									}
								}

								// pre-selected demo

								if( isset( $_GET['demo'] ) && ( $_GET['demo'] == $key ) ){
									$class .= ' active';
								}

								if( isset( $demo['new'] ) ){
									$class .= ' new';
								}

								// data | name

								if( isset( $demo['name'] ) ){
									$demo_name = $demo['name'];
								} else {
									$demo_name = ucfirst( $key );
								}

								echo '<li class="item'. esc_attr($class) .'" data-id="'. esc_attr($key) .'" data-name="'. esc_attr($demo_name) .'">';

									echo '<div class="icons"></div>';

									echo '<div class="border"></div>'; // border for hover effect

									echo '<div class="item-inner">';

										echo '<div class="item-header">';

											echo '<a href="javascript:void(0);" class="close"><i class="dashicons dashicons-no-alt"></i></a>';

											echo '<div class="item-image"></div>'; // sprite image

											echo '<div class="item-title">'. esc_html($demo_name) .'</div>';

											if( $categories ){
												echo '<div class="item-category">';
													echo '<span class="label">Category:</span>';
													echo '<span class="list">'. esc_html($categories) .'</span>';
												echo '</div>';
											}

										echo '</div>';

										echo '<div class="item-content">';
											echo '<div class="item-content-wrapper">';

												if( isset( $demo['plugins'] ) ){

													echo '<p>';
														echo '<b>Install the following plugins before website installation</b>';
													echo '</p>';

													echo '<ul class="plugins-used">';

														if( ( $plugins_key = array_search( 'rev', $demo['plugins'] ) ) !== false ){

															echo '<li class="plugin-rev">';
																echo '<b>'. esc_html($this->plugins['rev']['name']) .'</b><br />';

																	if( class_exists( $this->plugins['rev']['class'] ) ){
																		echo '<span class="install is-active">Active</span>';
																	} else {
																		echo '<span class="install"><a href="admin.php?page=be-plugins">Install</a></span>';
																		echo 'Slider demo <u>will not</u> be installed if Revolution Slider is not active';
																	}

															echo '</li>';

															unset( $demo['plugins'][$plugins_key] );
														}

														foreach( $demo['plugins'] as $plugin ){

															if( isset( $this->plugins[ $plugin ]['s'] ) ){
																$install_url = 'plugin-install.php?s='. $this->plugins[ $plugin ]['s'] .'&amp;tab=search&amp;type=term';
															} else {
																$install_url = 'admin.php?page=be-plugins';
															}

															echo '<li class="plugin-'. esc_attr($plugin) .'">';

																echo '<b>'. esc_html($this->plugins[$plugin]['name']) .'</b><br />';

																if( class_exists( $this->plugins[ $plugin ]['class'] ) ){
																	echo '<span class="install">Active</span>';
																} else {
																	echo '<span class="install"><a href="'. esc_url($install_url) .'">Install</a></span>';
																}

															echo '</li>';
														}

													echo '</ul>';

												}

												if( mfn_is_registered() ){
													echo '<a href="javascript:void(0);" class="mfn-button mfn-button-primary mfn-button-import">Install</a>';
												} else {
													echo '<a href="admin.php?page=betheme" class="mfn-button mfn-button-secondary">Please register the theme</a>';
												}

												if( isset( $demo['url'] ) ){
													$demo_url = $demo['url'];
												} else {
													$demo_url = 'https://themes.muffingroup.com/be/'. $key .'/';
												}

												echo '<p class="align-center"><a target="_blank" href="'. esc_url($demo_url) .'">Live preview</a></p>';

											echo '</div>';
										echo '</div>';

									echo '</div>';

								echo '</li>'."\n";
							}
						?>
					</ul>

					<div id="mfn-demo-popup">
						<div class="popup-inner">

							<div class="popup-header">
								<div class="item-image"></div>
							</div>

							<div class="popup-content">

								<div class="popup-step step-1">

									<h3 class="item-title-wrapper"><b>Database Reset</b></h3>

									<p class="align-center">Before installing a new pre-built website, it is recommended to <b>clean up your WordPress database</b>.</p>

									<div class="db-reset">

										<div class="reset-step reset-1">

											<p class="align-center important">Important: This tool DOES NOT create backups.</p>

											<ul class="reset-list">
												<li class="delete"><b>Deletes:</b> Posts, custom posts, pages, menus, categories, comments, etc.</li>
												<li class="keep"><b>Remains:</b> Users and passwords, wp_options, files on your server.</li>
											</ul>

											<a href="javascript:void(0);" class="mfn-button mfn-button-reset">Reset now</a>

										</div>

										<div class="reset-step reset-2">

											<p class="align-center"><strong>Are you sure you want to reset the database?</strong></p>
											<p class="align-center"><label><input type="checkbox" class="checkbox-reset" value="1" /> I understand that there is NO UNDO.</label></p>

											<a href="javascript:void(0);" class="mfn-button mfn-button-reset-confirm disabled" data-ajax="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">Reset now</a>

										</div>

									</div>

									<div class="popup-buttons">
										<a href="javascript:void(0);" class="mfn-button mfn-button-secondary mfn-button-cancel">Cancel</a>
										<a href="javascript:void(0);" class="mfn-button mfn-button-primary mfn-button-next">Skip <span class="dashicons dashicons-arrow-right"></span></a>
									</div>

								</div>

								<div class="popup-step step-2">

									<h3 class="item-title-wrapper">Install Be<span class="item-title"></span></h3>

									<div class="import-options active">
										<label><input type="radio" name="radio_import" class="radio-type checked" value="complete" /> <b>Complete pre-built website</b></label>
										<ul>
											<li>
												<label><input type="checkbox" class="checkbox-attachments checked" value="1" /> Import media (images, videos, etc.)<br />
												Media download may take a while</label>
											</li>
											<li class="slider-active">
												<label><input type="checkbox" class="checkbox-slider checked" value="1" /> Import Revolution Slider demo</label>
											</li>
										</ul>
									</div>

									<div class="import-options">
										<label><input type="radio" name="radio_import" class="radio-type" value="selected" /> <b>Selected data only</b></label>
										<ul>
											<li><label><input type="radio" name="radio_type" class="radio-data checked" value="content" /> Content</label></li>
											<li><label><input type="radio" name="radio_type" class="radio-data" value="options" /> Theme Options</label></li>
											<li class="slider-active"><label><input type="radio" name="radio_type" class="radio-data" value="slider" /> Revolution Slider demo</label></li>
										</ul>
									</div>

									<div class="popup-buttons">
										<a href="javascript:void(0);" class="mfn-button mfn-button-secondary mfn-button-cancel">Cancel</a>
										<a href="javascript:void(0);" class="mfn-button mfn-button-primary mfn-button-submit">Install</a>
									</div>

								</div>

								<div class="popup-step step-3">

									<h3 class="item-title-wrapper">Installing Be<span class="item-title"></span></h3>

									<div class="import-progress-bar"></div>

									<p class="align-center"><b>Please wait</b> for the whole demo data import before doing anything. <b>It may take a while</b>...</p>

								</div>

							</div>

						</div>
					</div>

					<input id="form-submit" type="submit" name="submit" value="import" style="display:none" />

				<?php endif; ?>

			</form>

		</div>

		<?php
	}

	/**
	 * Parse JSON import file
	 *
	 * http://wordpress.org/plugins/widget-settings-importexport/
	 *
	 * @param string $json_data
	 */

	function import_widget_data( $json_data ) {

		$json_data = json_decode( $json_data, true );
		$sidebar_data = $json_data[0];
		$widget_data = $json_data[1];

		// prepare widgets table

		$widgets = array();
		foreach( $widget_data as $k_w => $widget_type ){
			if( $k_w ){
				$widgets[ $k_w ] = array();
				foreach( $widget_type as $k_wt => $widget ){
					if( is_int( $k_wt ) ) $widgets[$k_w][$k_wt] = 1;
				}
			}
		}

		// sidebars

		foreach ( $sidebar_data as $title => $sidebar ) {
			$count = count( $sidebar );
			for ( $i = 0; $i < $count; $i++ ) {
				$widget = array( );
				$widget['type'] = trim( substr( $sidebar[$i], 0, strrpos( $sidebar[$i], '-' ) ) );
				$widget['type-index'] = trim( substr( $sidebar[$i], strrpos( $sidebar[$i], '-' ) + 1 ) );
				if ( !isset( $widgets[$widget['type']][$widget['type-index']] ) ) {
					unset( $sidebar_data[$title][$i] );
				}
			}
			$sidebar_data[$title] = array_values( $sidebar_data[$title] );
		}

		// widgets

		foreach ( $widgets as $widget_title => $widget_value ) {
			foreach ( $widget_value as $widget_key => $widget_value ) {
				$widgets[$widget_title][$widget_key] = $widget_data[$widget_title][$widget_key];
			}
		}

		$sidebar_data = array( array_filter( $sidebar_data ), $widgets );
		$this->parse_import_data( $sidebar_data );
	}

	/**
	 * Import widgets
	 *
	 * http://wordpress.org/plugins/widget-settings-importexport/
	 *
	 * @param array $import_array
	 * @return boolean
	 */

	function parse_import_data( $import_array ) {
		$sidebars_data = $import_array[0];
		$widget_data = $import_array[1];

		mfn_register_sidebars(); // fix for sidebars added in Theme Options

		$current_sidebars 	= array( );
		$new_widgets = array( );

		foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :

			foreach ( $import_widgets as $import_widget ) :

				// if NOT the sidebar exists

				if ( ! isset( $current_sidebars[$import_sidebar] ) ){
					$current_sidebars[$import_sidebar] = array();
				}

				$title = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
				$index = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
				$current_widget_data = get_option( 'widget_' . $title );
				$new_widget_name = $this->get_new_widget_name( $title, $index );
				$new_index = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );

				if ( !empty( $new_widgets[ $title ] ) && is_array( $new_widgets[$title] ) ) {
					while ( array_key_exists( $new_index, $new_widgets[$title] ) ) {
						$new_index++;
					}
				}
				$current_sidebars[$import_sidebar][] = $title . '-' . $new_index;
				if ( array_key_exists( $title, $new_widgets ) ) {
					$new_widgets[$title][$new_index] = $widget_data[$title][$index];

					// notice fix

					if( ! key_exists('_multiwidget',$new_widgets[$title]) ) $new_widgets[$title]['_multiwidget'] = '';

					$multiwidget = $new_widgets[$title]['_multiwidget'];
					unset( $new_widgets[$title]['_multiwidget'] );
					$new_widgets[$title]['_multiwidget'] = $multiwidget;
				} else {
					$current_widget_data[$new_index] = $widget_data[$title][$index];

					// notice fix

					if( ! key_exists('_multiwidget',$current_widget_data) ) $current_widget_data['_multiwidget'] = '';

					$current_multiwidget = $current_widget_data['_multiwidget'];
					$new_multiwidget = isset($widget_data[$title]['_multiwidget']) ? $widget_data[$title]['_multiwidget'] : false;
					$multiwidget = ($current_multiwidget != $new_multiwidget) ? $current_multiwidget : 1;
					unset( $current_widget_data['_multiwidget'] );
					$current_widget_data['_multiwidget'] = $multiwidget;
					$new_widgets[$title] = $current_widget_data;
				}

			endforeach;
		endforeach;

		// remove old widgets

		delete_option( 'sidebars_widgets' );

		if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
			update_option( 'sidebars_widgets', $current_sidebars );

			foreach ( $new_widgets as $title => $content )
				update_option( 'widget_' . $title, $content );

			return true;
		}

		return false;
	}

	/**
	 * Get new widget name
	 *
	 * http://wordpress.org/plugins/widget-settings-importexport/
	 *
	 * @param string $widget_name
	 * @param int $widget_index
	 * @return string
	 */

	function get_new_widget_name( $widget_name, $widget_index ) {
		$current_sidebars = get_option( 'sidebars_widgets' );
		$all_widget_array = array( );
		foreach ( $current_sidebars as $sidebar => $widgets ) {
			if ( !empty( $widgets ) && is_array( $widgets ) && $sidebar != 'wp_inactive_widgets' ) {
				foreach ( $widgets as $widget ) {
					$all_widget_array[] = $widget;
				}
			}
		}
		while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
			$widget_index++;
		}
		$new_widget_name = $widget_name . '-' . $widget_index;
		return $new_widget_name;
	}

}

$Mfn_Importer = new Mfn_Importer;
