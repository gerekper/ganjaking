<?php
/**
 * Porto Customizer Header Builder
 *
 * @author     Porto Themes
 * @category   Admin Functions
 * @since      4.8.0
 */

defined( 'ABSPATH' ) || exit;

class Porto_Header_Builder {

	public $transport = 'postMessage';

	public $elements;

	protected $container_elements = array( 'row' );

	protected $infinite_elements = array( 'row', 'porto_block', 'html', 'divider' );

	protected $desktop_elements = array( 'main-menu', 'secondary-menu' );

	protected $mobile_elements = array();

	/**
	 * Constructor.
	 */
	public function __construct() {

		global $wp_customize;
		if ( ! isset( $wp_customize->selective_refresh ) ) {
			$this->transport = 'refresh';
		}
		$this->elements = array(
			'logo'              => __( 'Logo', 'porto' ),
			'mini-cart'         => __( 'Mini Cart', 'porto' ),
			'menu-icon'         => __( '☰ Mobile Menu Icon', 'porto' ),
			'main-menu'         => __( 'Main Menu', 'porto' ),
			'main-toggle-menu'  => __( 'Main Toggle Menu', 'porto' ),
			'secondary-menu'    => __( 'Secondary Menu', 'porto' ),
			'nav-top'           => __( 'Top Menu', 'porto' ),
			'menu-block'        => __( 'Custom Menu', 'porto' ),
			'search-form'       => __( 'Search Form', 'porto' ),
			'social'            => __( 'Social Icons', 'porto' ),
			'contact'           => __( 'Contact', 'porto' ),
			'currency-switcher' => __( 'Currency Switcher', 'porto' ),
			'language-switcher' => __( 'Language Switcher', 'porto' ),
			'myaccount'         => __( 'My Account', 'porto' ),
			'wishlist'          => __( 'Wishlist', 'porto' ),
			'compare'           => __( 'Compare', 'porto' ),
			'porto_block'       => __( 'Porto Block', 'porto' ),
			'html'              => __( 'HTML', 'porto' ),
			'row'               => __( 'Row', 'porto' ),
			'divider'           => __( ' | ', 'porto' ),
		);
		$this->elements = apply_filters( 'porto_header_elements', $this->elements );

		$this->header_elements = array(
			'top_left'             => __( 'Header Top Left', 'porto' ),
			'top_center'           => __( 'Header Top Center', 'porto' ),
			'top_right'            => __( 'Header Top Right', 'porto' ),
			'main_left'            => __( 'Header Main Left', 'porto' ),
			'main_center'          => __( 'Header Main Center', 'porto' ),
			'main_right'           => __( 'Header Main Right', 'porto' ),
			'bottom_left'          => __( 'Header Bottom Left', 'porto' ),
			'bottom_center'        => __( 'Header Bottom Center', 'porto' ),
			'bottom_right'         => __( 'Header Bottom Right', 'porto' ),
			'mobile_top_left'      => __( 'Mobile Header Top Left', 'porto' ),
			'mobile_top_center'    => __( 'Mobile Header Top Center', 'porto' ),
			'mobile_top_right'     => __( 'Mobile Header Top Right', 'porto' ),
			'mobile_main_left'     => __( 'Mobile Header Main Left', 'porto' ),
			'mobile_main_center'   => __( 'Mobile Header Main Center', 'porto' ),
			'mobile_main_right'    => __( 'Mobile Header Main Right', 'porto' ),
			'mobile_bottom_left'   => __( 'Mobile Header Bottom Left', 'porto' ),
			'mobile_bottom_center' => __( 'Mobile Header Bottom Center', 'porto' ),
			'mobile_bottom_right'  => __( 'Mobile Header Bottom Right', 'porto' ),
		);
		add_action( 'customize_register', array( $this, 'add_section' ) );
		add_action( 'customize_controls_print_scripts', array( $this, 'add_scripts' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'add_js' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'add_header_builder_content' ) );

		add_action( 'customize_save_after', array( $this, 'save_header_layout_values' ) );

		add_action( 'wp_ajax_porto_add_new_header_layout', array( $this, 'add_new_header_layout' ) );
		add_action( 'wp_ajax_nopriv_porto_add_new_header_layout', array( $this, 'add_new_header_layout' ) );
		add_action( 'wp_ajax_porto_delete_header_layout', array( $this, 'delete_header_layout' ) );
		add_action( 'wp_ajax_nopriv_porto_delete_header_layout', array( $this, 'delete_header_layout' ) );
		add_action( 'wp_ajax_porto_load_header_elements', array( $this, 'load_header_elements' ) );
		add_action( 'wp_ajax_nopriv_porto_load_header_elements', array( $this, 'load_header_elements' ) );
	}

	public function add_section( $wp_customize ) {
		$options_style = get_theme_mod( 'theme_options_use_new_style', false );

		// add section to select header types
		if ( $options_style ) {
			$wp_customize->add_section(
				'porto_header_layouts',
				array(
					'title'       => __( 'Header Builder', 'porto' ),
					'description' => '',
					'priority'    => -479,
					'panel'       => 'header-settings',
				)
			);
		} else {
			$wp_customize->add_section(
				'porto_header_layouts',
				array(
					'title'       => __( 'Header Builder', 'porto' ),
					'description' => '',
					'priority'    => 10,
				)
			);
		}

		$wp_customize->add_setting(
			'porto_header_builder[on_section]',
			array(
				'default'           => '',
				'capability'        => 'edit_theme_options',
				'type'              => 'option',
				'transport'         => $this->transport,
				'sanitize_callback' => array( $this, 'sanitize_boolean_value' ),
			)
		);
		$wp_customize->add_control(
			'porto_is_header_builder_section',
			array(
				'settings' => 'porto_header_builder[on_section]',
				'label'    => '',
				'section'  => 'porto_header_layouts',
				'type'     => 'text',
			)
		);

		$wp_customize->add_setting(
			'porto_header_builder[selected_layout]',
			array(
				'default'           => '',
				'capability'        => 'edit_theme_options',
				'type'              => 'option',
				'transport'         => $this->transport,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$header_layouts = get_option( 'porto_header_builder_layouts', array() );
		if ( ! empty( $header_layouts ) ) {
			foreach ( $header_layouts as $key => $layout ) {
				if ( isset( $layout['name'] ) ) {
					$header_layouts[ $key ] = $layout['name'];
				}
			}
		}

		$wp_customize->add_setting(
			'porto_header_layouts_create_text',
			array(
				'default'           => '',
				'transport'         => $this->transport,
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			)
		);
		include_once 'classes/simple-notice-control.php';
		$wp_customize->add_control(
			new Porto_Simple_Notice_Custom_Control(
				$wp_customize,
				'porto_header_layouts_create_text',
				array(
					'description' => '<a href="#" class="porto_create_new_header_layout_link button button-dark btn-block">' . esc_html__( 'New Header Layout', 'porto' ) . '</a>',
					'section'     => 'porto_header_layouts',
				)
			)
		);

		$wp_customize->add_control(
			'porto_header_layouts_title',
			array(
				'type'     => 'text',
				'label'    => __( 'Header Type Title', 'porto' ),
				'section'  => 'porto_header_layouts',
				'settings' => array(),
			)
		);

		$wp_customize->add_control(
			'porto_header_layouts_create_button',
			array(
				'type'        => 'button',
				'settings'    => array(),
				'priority'    => 10,
				'section'     => 'porto_header_layouts',
				'input_attrs' => array(
					'value' => __( 'Add Header Layout', 'porto' ),
					'class' => 'button button-primary porto_header_builder_new',
				),
			)
		);

		$header_layouts = array_merge( array( '' => __( 'Select Header Layout...', 'porto' ) ), $header_layouts );
		$wp_customize->add_control(
			'porto_header_layouts_select',
			array(
				'settings' => 'porto_header_builder[selected_layout]',
				'label'    => __( 'Select a header layout to edit:', 'porto' ),
				'section'  => 'porto_header_layouts',
				'type'     => 'select',
				'choices'  => $header_layouts,
			)
		);

		$wp_customize->add_setting(
			'porto_header_layouts_delete',
			array(
				'default'           => '',
				'transport'         => $this->transport,
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			)
		);
		$wp_customize->add_control(
			new Porto_Simple_Notice_Custom_Control(
				$wp_customize,
				'porto_header_layouts_delete',
				array(
					'description' => '<a href="#" class="porto_delete_header_layout_link button button-red"><i class="fas fa-trash-alt"></i></a>',
					'section'     => 'porto_header_layouts',
				)
			)
		);

		$preset_img_html  = '<div class="porto_header_presets">';
		$preset_img_html .= '<h3 class="preset-title">' . esc_html__( 'Header Presets', 'porto' ) . '</h3>';
		$header_presets   = array( '' => '' );
		foreach ( porto_header_builder_presets() as $key => $preset ) {
			$header_presets[ $key ] = $preset['title'];
			$img_class              = '';
			if ( strpos( $key, '_side_' ) !== false ) {
				$img_class = 'side';
			}
			$preset_img_html .= '<img src="' . esc_url( PORTO_OPTIONS_URI . '/header_builder_presets/' . $preset['img'] ) . '"' . ( $img_class ? ' class="' . $img_class . '"' : '' ) . ' alt="' . esc_attr( $preset['title'] ) . '" data-preset="' . esc_attr( $key ) . '" />';
		}
		$preset_img_html .= '</div>';

		$wp_customize->add_setting(
			'porto_header_builder[preset]',
			array(
				'default'           => '',
				'transport'         => $this->transport,
				'capability'        => 'edit_theme_options',
				'type'              => 'option',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'porto_header_layouts_preset',
			array(
				'type'     => 'select',
				'label'    => __( 'Preset', 'porto' ),
				'section'  => 'porto_header_layouts',
				'settings' => 'porto_header_builder[preset]',
				'choices'  => $header_presets,
			)
		);
		$wp_customize->add_setting(
			'porto_header_layouts_preset_img',
			array(
				'default'           => '',
				'transport'         => $this->transport,
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			)
		);
		$wp_customize->add_control(
			new Porto_Simple_Notice_Custom_Control(
				$wp_customize,
				'porto_header_layouts_preset_img',
				array(
					'description' => $preset_img_html,
					'section'     => 'porto_header_layouts',
				)
			)
		);

		$wp_customize->add_setting(
			'porto_header_builder[type]',
			array(
				'default'           => '',
				'transport'         => $this->transport,
				'capability'        => 'edit_theme_options',
				'type'              => 'option',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'porto_header_layouts_type',
			array(
				'type'     => 'select',
				'label'    => __( 'Type', 'porto' ),
				'section'  => 'porto_header_layouts',
				'settings' => 'porto_header_builder[type]',
				'choices'  => array(
					''     => __( 'Default', 'porto' ),
					'side' => __( 'Side Header', 'porto' ),
				),
			)
		);

		// side header options
		$wp_customize->add_setting(
			'porto_header_builder[side_header_toggle]',
			array(
				'default'           => '',
				'transport'         => $this->transport,
				'capability'        => 'edit_theme_options',
				'type'              => 'option',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'porto_header_layouts_side_header_toggle',
			array(
				'type'     => 'select',
				'label'    => __( 'Side Header Toggle', 'porto' ),
				'section'  => 'porto_header_layouts',
				'settings' => 'porto_header_builder[side_header_toggle]',
				'choices'  => array(
					''     => __( 'No Toggle', 'porto' ),
					'side' => __( 'In Side Bar', 'porto' ),
					'top'  => __( 'In Top Bar', 'porto' ),
				),
			)
		);
		$wp_customize->add_setting(
			'porto_header_builder[side_header_toggle_logo]',
			array(
				'default'           => '',
				'transport'         => $this->transport,
				'capability'        => 'edit_theme_options',
				'type'              => 'option',
				'sanitize_callback' => 'esc_url',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'porto_header_layouts_side_header_toggle_logo',
				array(
					'label'    => __( 'Upload a Logo in Toggle bar', 'porto' ),
					'section'  => 'porto_header_layouts',
					'settings' => 'porto_header_builder[side_header_toggle_logo]',
				)
			)
		);
		$wp_customize->add_setting(
			'porto_header_builder[side_header_disable_overlay]',
			array(
				'default'           => '',
				'transport'         => $this->transport,
				'capability'        => 'edit_theme_options',
				'type'              => 'option',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'porto_header_layouts_side_header_disable_overlay',
			array(
				'type'     => 'checkbox',
				'label'    => __( 'Disable Overlay', 'porto' ),
				'section'  => 'porto_header_layouts',
				'settings' => 'porto_header_builder[side_header_disable_overlay]',
			)
		);
		$wp_customize->add_setting(
			'porto_header_builder[side_header_toggle_desc]',
			array(
				'default'           => '',
				'transport'         => $this->transport,
				'capability'        => 'edit_theme_options',
				'type'              => 'option',
				'sanitize_callback' => 'porto_strip_script_tags',
			)
		);
		$wp_customize->add_control(
			'porto_header_layouts_side_header_toggle_desc',
			array(
				'type'     => 'textarea',
				'label'    => __( 'Description in Toggle bar', 'porto' ),
				'section'  => 'porto_header_layouts',
				'settings' => 'porto_header_builder[side_header_toggle_desc]',
			)
		);
		$wp_customize->add_setting(
			'porto_header_builder[side_header_width]',
			array(
				'default'           => '256',
				'transport'         => $this->transport,
				'capability'        => 'edit_theme_options',
				'type'              => 'option',
				'sanitize_callback' => array( $this, 'sanitize_number_value' ),
			)
		);
		$wp_customize->add_control(
			'porto_header_layouts_side_header_width',
			array(
				'type'     => 'number',
				'label'    => __( 'Side Header Width (px)', 'porto' ),
				'section'  => 'porto_header_layouts',
				'settings' => 'porto_header_builder[side_header_width]',
			)
		);

		// html and porto block
		$blocks = porto_get_post_type_items(
			'porto_builder',
			array(
				'meta_query' => array(
					array(
						'key'     => 'porto_builder_type',
						'value'   => 'block',
					),
				)
			),
			false
		);
		$wp_customize->add_control(
			'porto_header_layouts_block_element',
			array(
				'type'     => 'select',
				'label'    => __( 'Select a block', 'porto' ),
				'section'  => 'porto_header_layouts',
				'settings' => array(),
				'choices'  => $blocks,
			)
		);
		$wp_customize->add_control(
			'porto_header_layouts_html_element',
			array(
				'type'     => 'textarea',
				'label'    => __( 'HTML', 'porto' ),
				'section'  => 'porto_header_layouts',
				'settings' => array(),
			)
		);
		$wp_customize->add_control(
			'porto_header_layouts_el_class',
			array(
				'type'     => 'text',
				'label'    => __( 'Custom CSS Class', 'porto' ),
				'section'  => 'porto_header_layouts',
				'settings' => array(),
			)
		);
		$wp_customize->add_control(
			'porto_header_layouts_save_html_button',
			array(
				'type'        => 'button',
				'settings'    => array(),
				'priority'    => 10,
				'section'     => 'porto_header_layouts',
				'description' => __( 'Note: You need to click Save button below to make changes.', 'porto' ),
				'input_attrs' => array(
					'value' => __( 'Save', 'porto' ),
					'class' => 'button button-primary porto_header_builder_save_html porto_header_builder_popup',
				),
			)
		);

		// custom css
		$wp_customize->add_setting(
			'porto_header_builder[custom_css]',
			array(
				'default'           => '',
				'transport'         => $this->transport,
				'capability'        => 'edit_theme_options',
				'type'              => 'option',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Code_Editor_Control(
				$wp_customize,
				'porto_header_layouts_custom_css',
				array(
					'label'     => __( 'Additional CSS', 'porto' ),
					'section'   => 'porto_header_layouts',
					'settings'  => 'porto_header_builder[custom_css]',
					'code_type' => 'text/css',
				)
			)
		);

		// header builder section
		$wp_customize->add_section(
			'porto_header_builder',
			array(
				'title'    => __( 'Header Builder Elements', 'porto' ),
				'priority' => 125,
			)
		);
		foreach ( $this->header_elements as $key => $element ) {
			$wp_customize->add_setting(
				'porto_header_builder_elements[' . $key . ']',
				array(
					'default'           => '',
					'capability'        => 'edit_theme_options',
					'type'              => 'option',
					'transport'         => $this->transport,
					'sanitize_callback' => 'porto_strip_script_tags',
				)
			);
			$wp_customize->add_control(
				'porto_header_builder_elements_' . $key,
				array(
					'type'     => 'text',
					'label'    => esc_html( $element ),
					'section'  => 'porto_header_builder',
					'settings' => 'porto_header_builder_elements[' . $key . ']',
				)
			);
		}

		// selective refresh
		$settings = array( 'porto_header_builder[selected_layout]', 'porto_header_layouts_delete', 'porto_header_builder[type]', 'porto_header_builder[side_header_toggle]', 'porto_header_builder[side_header_toggle_logo]', 'porto_header_builder[side_header_disable_overlay]', 'porto_header_builder[side_header_toggle_desc]' );

		$header_elements = array(
			'top_left',
			'top_center',
			'top_right',
			'main_left',
			'main_center',
			'main_right',
			'bottom_left',
			'bottom_center',
			'bottom_right',
			'mobile_top_left',
			'mobile_top_center',
			'mobile_top_right',
			'mobile_main_left',
			'mobile_main_center',
			'mobile_main_right',
			'mobile_bottom_left',
			'mobile_bottom_center',
			'mobile_bottom_right',
		);
		foreach ( $header_elements as $key ) {
			$settings[] = 'porto_header_builder_elements[' . $key . ']';
		}
		$wp_customize->selective_refresh->add_partial(
			'header-wrapper',
			array(
				'selector'        => '.header-wrapper',
				'settings'        => $settings,
				'render_callback' => function() {
					global $porto_settings;
					$porto_settings['header-type-select'] = 'header_builder';
					return get_template_part( 'header/header_builder' );
				},
			)
		);

		$wp_customize->selective_refresh->add_partial(
			'refresh_css_header_builder',
			array(
				'selector'            => 'head > style#porto-style-inline-css',
				'container_inclusive' => false,
				'settings'            => array( 'porto_header_builder[type]', 'porto_header_builder[on_section]', 'porto_header_builder[side_header_toggle]', 'porto_header_builder[side_header_toggle]', 'porto_header_builder[side_header_toggle_desc]', 'porto_header_builder[side_header_width]' ),
				'render_callback'     => function() {
					global $porto_dynamic_style, $porto_settings;
					$porto_settings['header-type-select'] = 'header_builder';
					if ( $porto_dynamic_style ) {
						return $porto_dynamic_style->output_dynamic_styles( true );
					}
				},
			)
		);
	}

	public function add_new_header_layout() {
		if ( wp_verify_nonce( $_POST['nonce'], 'porto-header-builder' ) && isset( $_POST['header_layout_title'] ) ) {
			$header_layouts                                  = get_option( 'porto_header_builder_layouts', array() );
			$header_layouts[ $_POST['header_layout_title'] ] = array( 'name' => $_POST['header_layout_title'] );
			update_option( 'porto_header_builder_layouts', $header_layouts );
			echo json_encode( array( 'result' => 'success' ) );
			die();
		}
	}

	public function delete_header_layout() {
		if ( wp_verify_nonce( $_POST['nonce'], 'porto-header-builder' ) && isset( $_POST['header_layout'] ) && $_POST['header_layout'] ) {
			$header_layouts = get_option( 'porto_header_builder_layouts', array() );
			unset( $header_layouts[ $_POST['header_layout'] ] );
			update_option( 'porto_header_builder_layouts', $header_layouts );

			echo json_encode( array( 'result' => 'success' ) );
			die();
		}
	}

	public function load_header_elements() {
		if ( wp_verify_nonce( $_POST['nonce'], 'porto-customizer' ) && isset( $_POST['header_layout'] ) ) {
			$header_layouts = get_option( 'porto_header_builder_layouts', array() );
			if ( ! empty( $_POST['header_layout'] ) && isset( $header_layouts[ $_POST['header_layout'] ] ) && isset( $header_layouts[ $_POST['header_layout'] ]['elements'] ) ) {
				echo json_encode( $header_layouts[ $_POST['header_layout'] ] );
			}
		}
		die();
	}

	public function save_header_layout_values( $obj ) {
		$porto_header_builder_elements = get_option( 'porto_header_builder_elements', array() );
		$current_header                = get_option( 'porto_header_builder', array() );
		if ( isset( $current_header['selected_layout'] ) && $current_header['selected_layout'] ) {
			$header_layouts = get_option( 'porto_header_builder_layouts', array() );
			$header_layouts[ $current_header['selected_layout'] ]['elements'] = $porto_header_builder_elements;

			if ( isset( $current_header['custom_css'] ) ) {
				$header_layouts[ $current_header['selected_layout'] ]['custom_css'] = $current_header['custom_css'];
			}
			if ( isset( $current_header['type'] ) ) {
				$header_layouts[ $current_header['selected_layout'] ]['type'] = $current_header['type'];

				if ( 'side' == $current_header['type'] ) {
					if ( isset( $current_header['side_header_toggle'] ) ) {
						$header_layouts[ $current_header['selected_layout'] ]['side_header_toggle'] = $current_header['side_header_toggle'];
					}
					if ( isset( $current_header['side_header_toggle_logo'] ) ) {
						$header_layouts[ $current_header['selected_layout'] ]['side_header_toggle_logo'] = $current_header['side_header_toggle_logo'];
					}
					if ( isset( $current_header['side_header_disable_overlay'] ) ) {
						$header_layouts[ $current_header['selected_layout'] ]['side_header_disable_overlay'] = $current_header['side_header_disable_overlay'];
					}
					if ( isset( $current_header['side_header_toggle_desc'] ) ) {
						$header_layouts[ $current_header['selected_layout'] ]['side_header_toggle_desc'] = $current_header['side_header_toggle_desc'];
					}
					if ( isset( $current_header['side_header_width'] ) ) {
						$header_layouts[ $current_header['selected_layout'] ]['side_header_width'] = $current_header['side_header_width'];
					}
				}
			}

			update_option( 'porto_header_builder_layouts', $header_layouts );

			// save default theme options when using header preset
			if ( isset( $current_header['preset'] ) && $current_header['preset'] && ! get_theme_mod( 'theme_options_use_new_style', false ) ) {
				$header_presets = porto_header_builder_presets();
				if ( isset( $header_presets[ $current_header['preset'] ] ) && isset( $header_presets[ $current_header['preset'] ]['options'] ) ) {
					$defalt_options = $header_presets[ $current_header['preset'] ]['options'];

					global $reduxPortoSettings;
					if ( empty( $reduxPortoSettings->ReduxFramework->options ) ) {
						$reduxPortoSettings->ReduxFramework->get_options();
					}
					$orig_options = $reduxPortoSettings->ReduxFramework->options;
					$changed      = false;
					foreach ( $defalt_options as $key => $value ) {
						if ( ! isset( $orig_options[ $key ] ) || $orig_options[ $key ] != $value || ( isset( $orig_options[ $key ] ) && ! empty( $orig_options[ $key ] ) && empty( $value ) ) ) {
							$orig_options[ $key ] = $value;
							$changed              = true;
						}
					}

					if ( $changed ) {
						$reduxPortoSettings->ReduxFramework->set_options( $orig_options );
					}

					$current_header['preset'] = '';
					update_option( 'porto_header_builder', $current_header );
				}
			}

			porto_save_theme_settings();
		}
	}

	public function add_header_builder_content() {
		?>
		<div class="porto-header-builder">
			<div class="header-builder-header">
				<h3><?php esc_html_e( 'Header Builder', 'porto' ); ?></h3>
				<div class="devices-wrapper">
					<a href="#" class="preview-desktop active"></a>
					<a href="#" class="preview-mobile"></a>
				</div>
				<div class="actions">
					<a href="https://youtu.be/pk2W281QUa8" class="button" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Tutorial', 'porto' ); ?></a>
					<a href="#" class="button button-clear"><?php esc_html_e( 'Clear All', 'porto' ); ?></a>
					<a href="#" class="button button-close"><?php esc_html_e( 'Close', 'porto' ); ?></a>
				</div>
			</div>
			<div class="header-wrapper-desktop">
				<div class="header-builder porto-header-builder-items porto-lg-visible">
					<p class="description"><strong><?php esc_html_e( 'Elements', 'porto' ); ?></strong><br /><?php esc_html_e( 'Drag &amp; Drop', 'porto' ); ?></p>
					<div class="porto-header-builder-list porto-drop-item">
						<?php
						foreach ( $this->elements as $key => $value ) {
							if ( in_array( $key, $this->mobile_elements ) ) {
								continue;
							}
							$class = array();
							if ( in_array( $key, $this->container_elements ) ) {
								$class[] = 'element-cont';
							}
							if ( in_array( $key, $this->infinite_elements ) ) {
								$class[] = 'element-infinite';
							}
							if ( ! empty( $class ) ) {
								$class = 'class="' . implode( ' ', $class ) . '"';
							} else {
								$class = '';
							}
							if ( in_array( $key, $this->container_elements ) ) {
								echo '<span data-id="' . $key . '"' . $class . '>' . $value . '<b class="dashicons dashicons dashicons-plus"></b></span>';
							} else {
								echo '<span data-id="' . $key . '"' . $class . '>' . $value . '<i class="dashicons dashicons-admin-generic"></i></span>';
							}
						}
						?>
					</div>
				</div>
				<div class="header-builder-desktop header-builder-wrapper">
					<div class="header-builder porto-header-builder-top">
						<a class="porto-header-builder-tooltip" href="#" data-id="porto_settings[header-top-bg-color]" data-type="control"><?php esc_html_e( 'Header Top', 'porto' ); ?><i class="dashicons dashicons-admin-generic"></i></a>
						<div class="porto-header-builder-left porto-drop-item" data-id="porto_header_builder_elements[top_left]">
						</div>
						<div class="porto-header-builder-center porto-drop-item" data-id="porto_header_builder_elements[top_center]">
						</div>
						<div class="porto-header-builder-right porto-drop-item" data-id="porto_header_builder_elements[top_right]">
						</div>
					</div>
					<div class="header-builder porto-header-builder-main">
						<a class="porto-header-builder-tooltip" href="#" data-id="porto_settings[header-bg]" data-type="control"><?php esc_html_e( 'Header Main', 'porto' ); ?><i class="dashicons dashicons-admin-generic"></i></a>
						<div class="porto-header-builder-left porto-drop-item" data-id="porto_header_builder_elements[main_left]">
						</div>
						<div class="porto-header-builder-center porto-drop-item" data-id="porto_header_builder_elements[main_center]">
						</div>
						<div class="porto-header-builder-right porto-drop-item" data-id="porto_header_builder_elements[main_right]">
						</div>
					</div>
					<div class="header-builder porto-header-builder-bottom">
						<a class="porto-header-builder-tooltip" href="#" data-id="porto_settings[header-bottom-text-color]" data-type="control"><?php esc_html_e( 'Header Bottom', 'porto' ); ?><i class="dashicons dashicons-admin-generic"></i></a>
						<div class="porto-header-builder-left porto-drop-item" data-id="porto_header_builder_elements[bottom_left]">
						</div>
						<div class="porto-header-builder-center porto-drop-item" data-id="porto_header_builder_elements[bottom_center]">
						</div>
						<div class="porto-header-builder-right porto-drop-item" data-id="porto_header_builder_elements[bottom_right]">
						</div>
					</div>
				</div>
			</div>
			<div class="header-wrapper-mobile">
				<div class="header-builder porto-header-builder-items porto-sm-visible">
					<p class="description"><strong><?php esc_html_e( 'Elements', 'porto' ); ?></strong><br /><?php esc_html_e( 'Drag &amp; Drop', 'porto' ); ?></p>
					<div class="porto-header-builder-list porto-drop-item-mobile">
						<?php
						foreach ( $this->elements as $key => $value ) {
							if ( in_array( $key, $this->desktop_elements ) ) {
								continue;
							}
							$class = array();
							if ( in_array( $key, $this->container_elements ) ) {
								$class[] = 'element-cont';
							}
							if ( in_array( $key, $this->infinite_elements ) ) {
								$class[] = 'element-infinite';
							}
							if ( ! empty( $class ) ) {
								$class = 'class="' . implode( ' ', $class ) . '"';
							} else {
								$class = '';
							}
							if ( in_array( $key, $this->container_elements ) ) {
								echo '<span data-id="' . $key . '"' . $class . '>' . $value . '<b class="dashicons dashicons dashicons-plus"></b></span>';
							} else {
								echo '<span data-id="' . $key . '"' . $class . '>' . $value . '<i class="dashicons dashicons-admin-generic"></i></span>';
							}
						}
						?>
					</div>
				</div>
				<div class="header-builder-mobile header-builder-wrapper">
					<div class="header-builder porto-header-builder-top">
						<a class="porto-header-builder-tooltip" href="#" data-id="porto_settings[header-bg]" data-type="control"><?php esc_html_e( 'Header Main', 'porto' ); ?><i class="dashicons dashicons-admin-generic"></i></a>
						<div class="porto-header-builder-left porto-drop-item-mobile" data-id="porto_header_builder_elements[mobile_top_left]">
						</div>
						<div class="porto-header-builder-center porto-drop-item-mobile" data-id="porto_header_builder_elements[mobile_top_center]">
						</div>
						<div class="porto-header-builder-right porto-drop-item-mobile" data-id="porto_header_builder_elements[mobile_top_right]">
						</div>
					</div>
					<div class="header-builder porto-header-builder-main">
						<a class="porto-header-builder-tooltip" href="#" data-id="porto_settings[header-bg]" data-type="control"><?php esc_html_e( 'Header Main', 'porto' ); ?><i class="dashicons dashicons-admin-generic"></i></a>
						<div class="porto-header-builder-left porto-drop-item-mobile" data-id="porto_header_builder_elements[mobile_main_left]">
						</div>
						<div class="porto-header-builder-center porto-drop-item-mobile" data-id="porto_header_builder_elements[mobile_main_center]">
						</div>
						<div class="porto-header-builder-right porto-drop-item-mobile" data-id="porto_header_builder_elements[mobile_main_right]">
						</div>
					</div>
					<div class="header-builder porto-header-builder-bottom">
						<a class="porto-header-builder-tooltip" href="#" data-id="porto_settings[header-bottom-text-color]" data-type="control"><?php esc_html_e( 'Header Bottom', 'porto' ); ?><i class="dashicons dashicons-admin-generic"></i></a>
						<div class="porto-header-builder-left porto-drop-item-mobile" data-id="porto_header_builder_elements[mobile_bottom_left]">
						</div>
						<div class="porto-header-builder-center porto-drop-item-mobile" data-id="porto_header_builder_elements[mobile_bottom_center]">
						</div>
						<div class="porto-header-builder-right porto-drop-item-mobile" data-id="porto_header_builder_elements[mobile_bottom_right]">
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function add_scripts() {
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				wp.customize.bind( 'ready', function() { // Ready?
					var selectedLayout = wp.customize.instance('porto_header_builder[selected_layout]').get();
					if (!selectedLayout) {
						$('.porto_delete_header_layout_link, #customize-control-porto_header_layouts_custom_css').hide();
					}
					$('#customize-control-porto_header_layouts_title, #customize-control-porto_header_layouts_create_button').hide();
					$('.porto_create_new_header_layout_link').on('click', function() {
						$('#customize-control-porto_header_layouts_title, #customize-control-porto_header_layouts_create_button').slideDown();
						$('#customize-control-porto_header_layouts_title input[type=text]').focus();
					});
					$('#customize-control-porto_header_layouts_title input[type=text]').on('keyup', function(e) {
						if (typeof e.keyCode != 'undefined' && e.keyCode === 13) {
							$('.porto_header_builder_new').trigger('click');
						}
					});

					$('#customize-control-porto_header_layouts_block_element, #customize-control-porto_header_layouts_html_element, #customize-control-porto_header_layouts_el_class, #customize-control-porto_header_layouts_save_html_button').hide();

					var sideHeaderOptions = ['porto_header_layouts_side_header_toggle', 'porto_header_layouts_side_header_toggle_logo', 'porto_header_layouts_side_header_toggle_desc', 'porto_header_layouts_side_header_width'],
						sideHeaderToggleOptions = ['porto_header_layouts_side_header_toggle_logo', 'porto_header_layouts_side_header_toggle_desc'];
					if (wp.customize.instance('porto_header_builder[type]').get() != 'side') {
						for (var i in sideHeaderOptions) {
							wp.customize.control(sideHeaderOptions[i]).container.hide();
						}
					}
					if (wp.customize.instance('porto_header_builder[side_header_toggle]').get() == '') {
						for (var i in sideHeaderToggleOptions) {
							wp.customize.control(sideHeaderToggleOptions[i]).container.hide();
						}
					}
					wp.customize.control('porto_header_layouts_type').container.find('select').on('change', function() {
						if ('side' == $(this).val()) {
							for (var i in sideHeaderOptions) {
								wp.customize.control(sideHeaderOptions[i]).container.show();
							}
						} else {
							for (var i in sideHeaderOptions) {
								wp.customize.control(sideHeaderOptions[i]).container.hide();
							}
						}
						wp.customize.control('porto_header_layouts_side_header_toggle').container.find('select').trigger('change');
					});
					wp.customize.control('porto_header_layouts_side_header_toggle').container.find('select').on('change', function() {
						if ($(this).val()) {
							for (var i in sideHeaderToggleOptions) {
								wp.customize.control(sideHeaderToggleOptions[i]).container.show();
							}
						} else {
							for (var i in sideHeaderToggleOptions) {
								wp.customize.control(sideHeaderToggleOptions[i]).container.hide();
							}
						}
						if ('top' == $(this).val()) {
							wp.customize.control('porto_header_layouts_side_header_disable_overlay').container.show();
						} else {
							wp.customize.control('porto_header_layouts_side_header_disable_overlay').container.hide();
						}
					});
				});

				$(document.body).on('click', '.porto_header_builder_new', function(e) {
					var title = $('#customize-control-porto_header_layouts_title input[type="text"]').val().trim(),
						$this = $(this);
					if (!title) {
						return;
					}
					$this.attr('disabled', 'disabled');
					$.ajax({
						url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
						data:{ wp_customize: 'on', action: 'porto_add_new_header_layout', nonce: '<?php echo wp_create_nonce( 'porto-header-builder' ); ?>', header_layout_title: title},
						type: 'post',
						success: function(response) {
							$this.removeAttr('disabled');
							$('#customize-control-porto_header_layouts_title, #customize-control-porto_header_layouts_create_button').slideUp();
							if (!$('#customize-control-porto_header_layouts_select select option[val="' + title + '"]').length) {
								$('#customize-control-porto_header_layouts_select select').append('<option val="' + title + '">' + title + '</option>');
							}
							$('#customize-control-porto_header_layouts_select select option').removeAttr('selected');
							$('#customize-control-porto_header_layouts_select select option[val="' + title + '"]').attr('selected', 'selected');
							$('#customize-control-porto_header_layouts_select select').trigger('change');
						}
					})
				});
				$(document.body).on('click', '.porto_delete_header_layout_link', function(e) {
					if (confirm('You are about to permanently delete this header layout.\n\'Cancel\' to stop, \'OK\' to delete.')) {
						var $this = $(this);
						if ($this.hasClass('disabled')) {
							return false;
						}
						$this.addClass('disabled');
						$.ajax({
							url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
							data:{ wp_customize: 'on', action: 'porto_delete_header_layout', nonce: '<?php echo wp_create_nonce( 'porto-header-builder' ); ?>', header_layout: $('#customize-control-porto_header_layouts_select select').val()},
							type: 'post',
							success: function(response) {
								$this.removeClass('disabled');
								$('#customize-control-porto_header_layouts_select select option:selected').remove();
								$('#customize-control-porto_header_layouts_select select').trigger('change');
							}
						});
					}
				});
			});
		</script>
		<?php
	}

	public function add_js() {
		$admin_vars = array(
			'header_builder_presets' => json_encode( porto_header_builder_presets() ),
		);
		wp_localize_script( 'porto-admin', 'js_porto_hb_vars', $admin_vars );
	}

	public function sanitize_number_value( $value ) {
		if ( ! preg_match( '#[0-9]#', $value ) ) {
			return '';
		}
		return $value;
	}
	public function sanitize_boolean_value( $value ) {
		if ( $value ) {
			return '1';
		}
		return '';
	}
}

new Porto_Header_Builder();
