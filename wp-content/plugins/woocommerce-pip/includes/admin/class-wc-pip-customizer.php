<?php
/**
 * WooCommerce Print Invoices/Packing Lists
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Print
 * Invoices/Packing Lists to newer versions in the future. If you wish to
 * customize WooCommerce Print Invoices/Packing Lists for your needs please refer
 * to http://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * PIP Customizer class
 *
 * Handles WYSIWYG customization of the document templates
 *
 * @since 3.0.0
 */
class WC_PIP_Customizer {


	/** @var string the customizer trigger */
	private $customizer_trigger;

	/** @var string the customizer URL address */
	private $customizer_url;


	/**
	 * PIP Document Customizer Constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		$this->customizer_trigger = $this->get_customizer_trigger();
		$this->customizer_url     = $this->get_customizer_url();

		// register a new setting field type
		add_action( 'woocommerce_admin_field_wc_pip_customizer_button', array( $this, 'render_customize_button_html' ) );

		// add a Customize button to access the customizer from PIP Settings
		add_filter( 'wc_pip_general_settings', array( $this, 'add_customize_button' ) );

		// register PIP customizer settings
		add_filter( 'customize_register', array( $this, 'add_customizer_settings' ) );

		// load PIP customizer content when invoked
		if ( isset( $_GET[ $this->customizer_trigger ] ) ) {

			// first remove stuff we don't need on our views...
			remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
			add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_scripts' ), 999999 );
			add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_styles'  ), 999999 );
			add_filter( 'customize_register', array( $this, 'remove_sections' ), 150 );
			// ...then add our stuff
			add_filter( 'customize_register', array( $this, 'add_customizer_sections' ), 200 );
			add_filter( 'customize_register', array( $this, 'add_customizer_controls' ), 200 );

			// enqueue Customizer scripts and styles
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'customizer_controls_scripts' ), 999 );
			add_action( 'customize_preview_init',             array( $this, 'customizer_preview_scripts' ), 999 );
			add_action( 'customize_controls_print_styles',    array( $this, 'customizer_styles' ), 999 );
		}

		// Load the PIP Customizer template
		add_action( 'template_redirect', array( $this, 'load_template' ), 1 );
	}


	/**
	 * Get the customizer action trigger
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_customizer_trigger() {
		return 'wc-pip-customizer';
	}


	/**
	 * Get the customizer url
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_customizer_url() {

		// Build URL
		$url = admin_url( 'customize.php' );
		$url = add_query_arg( $this->customizer_trigger, 'true', $url );
		$url = add_query_arg( 'url', wp_nonce_url( site_url() . '/?' . $this->customizer_trigger .'=true', 'preview-document' ), $url );
		$url = add_query_arg( 'return', urlencode( add_query_arg( array( 'page' => 'wc-settings', 'tab' => 'pip' ), admin_url( 'admin.php' ) ) ), $url );

		return esc_url_raw( $url );
	}


	/**
	 * Add a custom setting to display a customizer button
	 *
	 * @since 3.0.0
	 * @param array $settings
	 */
	public function render_customize_button_html( $settings ) {
		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php echo esc_html( $settings['desc'] ); ?></th>
			<td class="forminp forminp-<?php echo sanitize_html_class( $settings['type'] ) ?>">
				<a href="<?php echo $settings['link']; ?>">
					<button
						name="<?php echo esc_attr( $settings['id'] ); ?>"
						id="<?php echo esc_attr( $settings['id'] ); ?>"
						style="<?php echo esc_attr( $settings['css'] ); ?>"
						class="button-primary <?php echo esc_attr( $settings['class'] ); ?>"
						type="button">
						<?php echo $settings['title']; ?>
					</button>
				</a>
			</td>
		</tr>
		<?php
	}


	/**
	 * Add Customize Button to PIP Settings
	 *
	 * @since 3.0.0
	 * @param array $template_settings Template Settings
	 * @return array
	 */
	public function add_customize_button( $template_settings ) {

		$customizer_settings = array(
			// start section
			array(
				'title' => __( 'Template Customizer', 'woocommerce-pip' ),
				'desc'  => __( 'Customize the template using a visual editor.', 'woocommerce-pip' ),
				'type'  => 'title',
			),
			// add the button
			array(
				'title' => __( 'Customize', 'woocommerce-pip'),
				'desc'  => __( 'Customize Template', 'woocommerce-pip' ),
				'type'  => 'wc_pip_customizer_button',
				'id'    => 'wc_pip_template_customizer_button',
				'link'  => $this->customizer_url,
			),
			// end section
			array(
				'type' => 'sectionend',
				'id'   => 'email_customizer_sectionend',
			),
		);

		return array_merge( $customizer_settings, $template_settings );
	}


	/**
	 * Remove Customizer Sections
	 *
	 * @since 3.0.0
	 * @param WP_Customize_Manager $wp_customize Customizer instance
	 */
	public function remove_sections( $wp_customize ) {
		global $wp_customize;

		// remove all sections
		if ( $wp_customize instanceof \WP_Customize_Manager && ( $sections = $wp_customize->sections() ) ) {

			foreach ( $sections as $section_id => $section ) {
				$wp_customize->remove_section( $section_id );
			}
		}
	}


	/**
	 * Add Customizer Sections
	 *
	 * @since 3.0.0
	 * @param WP_Customize_Manager $wp_customize Customizer instance
	 */
	public function add_customizer_sections( $wp_customize ) {
		global $wp_customize;

		// company
		$wp_customize->add_section( 'wc_pip_company_information', array (
			'title'      => __( 'Company Information', 'woocommerce-pip' ),
			'capability' => 'manage_woocommerce',
			'priority'   => 10,
		) );

		// typography
		$wp_customize->add_section( 'wc_pip_typography', array (
			'title'      => __( 'Typography', 'woocommerce-pip' ),
			'capability' => 'manage_woocommerce',
			'priority'   => 15,
		) );

		// colors
		$wp_customize->add_section( 'wc_pip_colors', array (
			'title'      => __( 'Colors', 'woocommerce-pip' ),
			'capability' => 'manage_woocommerce',
			'priority'   => 20,
		) );

		// content
		$wp_customize->add_section( 'wc_pip_content', array (
			'title'      => __( 'Content', 'woocommerce-pip' ),
			'capability' => 'manage_woocommerce',
			'priority'   => 25,
		) );

		// misc & advanced
		$wp_customize->add_section( 'wc_pip_misc', array (
			'title'      => __( 'Advanced', 'woocommerce-pip' ),
			'capability' => 'manage_woocommerce',
			'priority'   => 30,
		) );

	}


	/**
	 * Add Customizer Settings
	 *
	 * @since 3.0.0
	 * @param WP_Customize_Manager $wp_customize Customizer instance
	 */
	public function add_customizer_settings( $wp_customize ) {
		global $wp_customize;


		// ===================
		// Company Information
		// ===================

		// company logo
		$wp_customize->add_setting( 'wc_pip_company_logo', array(
			'type'      => 'option',
			'transport' => 'postMessage',
		) );

		// logo max size
		$wp_customize->add_setting( 'wc_pip_company_logo_max_width', array(
			'type'      => 'option',
			'default'   => '300',
			'transport' => 'postMessage',
		) );

		// company name
		$wp_customize->add_setting( 'wc_pip_company_name', array(
			'type'      => 'option',
			'default'   => get_bloginfo( 'name' ),
			'transport' => 'postMessage',
		) );

		// company URL
		$wp_customize->add_setting( 'wc_pip_company_url', array(
			'type'      => 'option',
			'default'   => get_bloginfo( 'url' ),
			'transport' => 'postMessage',
		) );

		// company extra
		$wp_customize->add_setting( 'wc_pip_company_extra', array(
			'type'      => 'option',
			'transport' => 'postMessage',
		) );

		// company VAT number
		$wp_customize->add_setting( 'wc_pip_company_vat_number', array(
			'type'      => 'option',
			'transport' => 'postMessage',
		) );

		// company title alignment
		$wp_customize->add_setting( 'wc_pip_company_title_align', array(
			'type'      => 'option',
			'transport' => 'postMessage',
		) );

		// company address
		$wp_customize->add_setting( 'wc_pip_company_address', array(
			'type'      => 'option',
			'transport' => 'postMessage',
		) );

		// company address alignment
		$wp_customize->add_setting( 'wc_pip_company_address_align', array(
			'type'      => 'option',
			'transport' => 'postMessage',
		) );


		// ==========
		// Typography
		// ==========

		// body font size
		$wp_customize->add_setting( 'wc_pip_body_font_size', array(
			'type'      => 'option',
			'default'   => '14',
			'transport' => 'postMessage',
		) );

		// heading font size
		$wp_customize->add_setting( 'wc_pip_heading_font_size', array(
			'type'      => 'option',
			'default'   => '32',
			'transport' => 'postMessage',
		) );


		// ======
		// Colors
		// ======

		// link color
		$wp_customize->add_setting( 'wc_pip_link_color', array(
			'type'      => 'option',
			'default'   => '#000000',
			'transport' => 'postMessage',
		) );

		// headings color
		$wp_customize->add_setting( 'wc_pip_headings_color', array(
			'type'      => 'option',
			'default'   => '#000000',
			'transport' => 'postMessage',
		) );

		// table head background color
		$wp_customize->add_setting( 'wc_pip_table_head_bg_color', array(
			'type'      => 'option',
			'default'   => '#333333',
			'transport' => 'postMessage',
		) );

		// table head background color
		$wp_customize->add_setting( 'wc_pip_table_head_color', array(
			'type'      => 'option',
			'default'   => '#FFFFFF',
			'transport' => 'postMessage',
		) );


		// =======
		// Content
		// =======

		// header
		$wp_customize->add_setting( 'wc_pip_header', array(
			'type'      => 'option',
			'transport' => 'postMessage',
		) );

		// terms & conditions
		$wp_customize->add_setting( 'wc_pip_return_policy', array(
			'type'      => 'option',
			'transport' => 'postMessage',
		) );

		// terms & conditions fine print
		$wp_customize->add_setting( 'wc_pip_return_policy_fine_print', array(
			'type'      => 'option',
			'transport' => 'postMessage',
		) );

		// footer
		$wp_customize->add_setting( 'wc_pip_footer', array(
			'type'      => 'option',
			'transport' => 'postMessage',
		) );


		// ========
		// ADVANCED
		// ========

		// custom CSS
		$wp_customize->add_setting( 'wc_pip_custom_styles', array(
			'type'      => 'option',
			'transport' => 'postMessage',
		) );

	}


	/**
	 * Add Customizer Controls
	 *
	 * @since 3.0.0
	 * @param WP_Customize_Manager $wp_customize Customizer instance
	 */
	public function add_customizer_controls( $wp_customize ) {
		global $wp_customize;


		// ===================
		// Company Information
		// ===================

		// company logo
		$wp_customize->add_control( new \WP_Customize_Image_Control( $wp_customize, 'wc_pip_shop_logo', array(
			'label'       => __( 'Shop Logo', 'woocommerce-pip' ),
			'description' => __( 'Upload a logo representing your shop or business.', 'woocommerce-pip' ),
			'priority'    => 10,
			'section'     => 'wc_pip_company_information',
			'settings'    => 'wc_pip_company_logo',
			'context'     => 'woocommerce_pip_customizer',
		) ) );

		// logo max width
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'wc_pip_shop_logo_max_width_control', array(
			'type'        => 'range',
			'priority'    => 15,
			'section'     => 'wc_pip_company_information',
			'label'       => __( 'Logo Max Width', 'woocommerce-pip' ),
			'description' => __( 'The maximum width of the logo.', 'woocommerce-pip' ),
			'settings'    => 'wc_pip_company_logo_max_width',
			'input_attrs' => array(
				'min'   => 0,
				'max'   => 800,
				'step'  => 10,
			),
		) ) );

		// company name
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'wc_pip_shop_name_control', array(
			'label'       => __( 'Shop Name', 'woocommerce-pip' ),
			'description' => __( 'Enter your shop or business name to be displayed when not using a logo. Leave blank to use the default site title.', 'woocommerce-pip' ),
			'priority'    => 20,
			'section'     => 'wc_pip_company_information',
			'settings'    => 'wc_pip_company_name',
			'type'        => 'text',
		) ) );

		// company URL
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'wc_pip_shop_url_control', array(
			'label'       => __( 'Shop URL', 'woocommerce-pip' ),
			'description' => __( 'Enter the URL to be used in the template for your shop or business website URL. Leave blank to use the default site url.', 'woocommerce-pip' ),
			'priority'    => 25,
			'section'     => 'wc_pip_company_information',
			'settings'    => 'wc_pip_company_url',
			'type'        => 'text',
		) ) );

		// extra information
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'wc_pip_shop_extra_control', array(
			'label'       => __( 'Extra information', 'woocommerce-pip' ),
			'description' => __( 'Enter additional information or subheading to be displayed below the title or logo.', 'woocommerce-pip' ),
			'priority'    => 30,
			'section'     => 'wc_pip_company_information',
			'settings'    => 'wc_pip_company_extra',
			'type'        => 'textarea',
		) ) );

		// company VAT number
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'wc_pip_company_vat_number_control', array(
			'label'       => __( 'VAT Number', 'woocommerce-pip' ),
			'description' => __( 'Enter your business VAT number.', 'woocommerce-pip' ),
			'priority'    => 35,
			'section'     => 'wc_pip_company_information',
			'settings'    => 'wc_pip_company_vat_number',
			'type'        => 'text',
		) ) );

		// company title alignment
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'wc_pip_shop_title_align_control', array(
			'label'       => __( 'Shop name or logo alignment', 'woocommerce-pip' ),
			'description' => __( 'Set the alignment of the shop title or logo in template.', 'woocommerce-pip' ),
			'priority'    => 40,
			'section'     => 'wc_pip_company_information',
			'settings'    => 'wc_pip_company_title_align',
			'type'        => 'select',
			'default'     => 'left',
			'choices'     => array(
				'left'   => __( 'Left', 'woocommerce-pip' ),
				'right'  => __( 'Right', 'woocommerce-pip' ),
				'center' => __( 'Center', 'woocommerce-pip' ),
			),
		) ) );

		// company address
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'wc_pip_shop_address_control', array(
			'label'       => __( 'Shop Address', 'woocommerce-pip' ),
			'description' => __( 'Enter your shop or business address to be displayed next to the logo or shop name.', 'woocommerce-pip' ),
			'priority'    => 45,
			'section'     => 'wc_pip_company_information',
			'settings'    => 'wc_pip_company_address',
			'type'        => 'textarea',
		) ) );

		// company address align
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'wc_pip_shop_address_align_control', array(
			'label'       => __( 'Shop Address alignment', 'woocommerce-pip' ),
			'description' => __( 'Set the alignment of the shop address in template.', 'woocommerce-pip' ),
			'priority'    => 50,
			'section'     => 'wc_pip_company_information',
			'settings'    => 'wc_pip_company_address_align',
			'type'        => 'select',
			'default'     => 'right',
			'choices'     => array(
				'left'   => __( 'Left', 'woocommerce-pip' ),
				'right'  => __( 'Right', 'woocommerce-pip' ),
				'center' => __( 'Center', 'woocommerce-pip' ),
			),
		) ) );


		// ==========
		// Typography
		// ==========

		// body font
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'wc_pip_body_font_size_control', array(
			'type'        => 'range',
			'priority'    => 15,
			'section'     => 'wc_pip_typography',
			'label'       => __( 'Body font size', 'woocommerce-pip' ),
			'description' => __( 'The template default body font size.', 'woocommerce-pip' ),
			'settings'    => 'wc_pip_body_font_size',
			'default'     => 12,
			'input_attrs' => array(
				'min'   => 1,
				'max'   => 36,
				'step'  => 1,
			),
		) ) );

		// heading font
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'wc_pip_heading_font_size_control', array(
			'type'        => 'range',
			'priority'    => 25,
			'section'     => 'wc_pip_typography',
			'label'       => __( 'Heading font size', 'woocommerce-pip' ),
			'description' => __( 'The template default heading font size.', 'woocommerce-pip' ),
			'settings'    => 'wc_pip_heading_font_size',
			'default'     => 28,
			'input_attrs' => array(
				'min'   => 1,
				'max'   => 96,
				'step'  => 1,
			),
		) ) );


		// ======
		// Colors
		// ======

		// link color
		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'wc_pip_link_color_control', array(
			'label'       => __( 'Link Color', 'woocommerce-pip' ),
			'description' => __( 'Choose the default color of links in template.', 'woocommerce-pip' ),
			'priority'    => 15,
			'section'     => 'wc_pip_colors',
			'settings'    => 'wc_pip_link_color',
		) ) );

		// heading color
		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'wc_pip_headings_color_control', array(
			'label'       => __( 'Headings Color', 'woocommerce-pip' ),
			'description' => __( 'Choose the default color for heading text.', 'woocommerce-pip' ),
			'priority'    => 20,
			'section'     => 'wc_pip_colors',
			'settings'    => 'wc_pip_headings_color',
		) ) );

		// table head background color
		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'wc_pip_table_head_bg_color_control', array(
			'label'       => __( 'Table Head Background Color', 'woocommerce-pip' ),
			'description' => __( 'Choose the default color for the table head background.', 'woocommerce-pip' ),
			'priority'    => 25,
			'section'     => 'wc_pip_colors',
			'settings'    => 'wc_pip_table_head_bg_color',
		) ) );

		// table head color
		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'wc_pip_table_head_color_control', array(
			'label'       => __( 'Table Head Color', 'woocommerce-pip' ),
			'description' => __( 'Choose the default color for the table head text.', 'woocommerce-pip' ),
			'priority'    => 25,
			'section'     => 'wc_pip_colors',
			'settings'    => 'wc_pip_table_head_color',
		) ) );


		// =======
		// Content
		// =======

		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'wc_pip_header_control', array(
			'label'       => __( 'Header', 'woocommerce-pip' ),
			'description' => __( "Enter additional information to be displayed below the document's header.", 'woocommerce-pip' ),
			'priority'    => 10,
			'section'     => 'wc_pip_content',
			'settings'    => 'wc_pip_header',
			'type'        => 'textarea',
		) ) );

		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'woocommerce_pip_return_policy_control', array(
			'label'       => __( 'Returns Policy, Terms and Conditions, etc.', 'woocommerce-pip' ),
			'description' => __( "Enter your shop's policies to be displayed at the bottom of the document.", 'woocommerce-pip' ),
			'priority'    => 20,
			'section'     => 'wc_pip_content',
			'settings'    => 'wc_pip_return_policy',
			'type'        => 'textarea',
		) ) );

		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'woocommerce_pip_return_policy_fine_print_control', array(
			'label'       => __( 'Fine print', 'woocommerce-pip' ),
			'description' => __( 'Display the terms and conditions text in fine print.', 'woocommerce-pip' ),
			'priority'    => 25,
			'section'     => 'wc_pip_content',
			'settings'    => 'wc_pip_return_policy_fine_print',
			'type'        => 'checkbox',
		) ) );

		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'wc_pip_footer_control', array(
			'label'       => __( 'Footer', 'woocommerce-pip' ),
			'description' => __( "Enter additional information to be displayed at the document's footer.", 'woocommerce-pip' ),
			'priority'    => 30,
			'section'     => 'wc_pip_content',
			'settings'    => 'wc_pip_footer',
			'type'        => 'textarea',
		) ) );


		// ========
		// Advanced
		// ========

		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'wc_pip_custom_styles_control', array(
			'label'       => __( 'Custom Styles', 'woocommerce-pip' ),
			'description' => __( 'For advanced users: you may enter additional CSS styles that will be used in the document template.', 'woocommerce-pip' ),
			'priority'    => 20,
			'section'     => 'wc_pip_misc',
			'settings'    => 'wc_pip_custom_styles',
			'type'        => 'textarea',
		) ) );
	}


	/**
	 * Add customizer scripts for customizing controls
	 *
	 * @since 3.0.0
	 */
	public function customizer_controls_scripts() {

		// Prevent script caching in development environments
		$version = defined( 'WP_DEBUG' ) && WP_DEBUG ? \WC_PIP::VERSION . '.' . time() : \WC_PIP::VERSION;

		wp_enqueue_script( 'wc-pip-customizer-controls-scripts',  wc_pip()->get_plugin_url() . '/assets/js/admin/wc-pip-customizer-controls.min.js', array( 'jquery' ), $version, true );

		wp_localize_script( 'wc-pip-customizer-controls-scripts', 'wc_pip_customizer', array(
			'i18n' => array(
				'preview_changes' => __( 'Save and Preview Changes', 'woocommerce-pip' ),
			),
		) );
	}


	/**
	 * Add customizer scripts for previewing changes
	 *
	 * @since 3.0.0
	 */
	public function customizer_preview_scripts() {

		// Prevent script caching in development environments
		$version = defined( 'WP_DEBUG' ) && WP_DEBUG ? \WC_PIP::VERSION . '.' . time() : \WC_PIP::VERSION;

		wp_enqueue_script( 'wc-pip-customizer-preview-scripts',  wc_pip()->get_plugin_url() . '/assets/js/admin/wc-pip-customizer-preview.min.js', array( 'jquery', 'customize-preview' ), $version, true );

		wp_localize_script( 'wc-pip-customizer-preview-scripts', 'wc_pip_customizer_preview', array(
			'is_rtl'         => is_rtl(),
			'i18n'           => array(
				'vat_number' => __( 'VAT Number', 'woocommerce-pip' ),
			),
		) );
	}


	/**
	 * Add Customizer styles
	 *
	 * @since 3.0.0
	 */
	public function customizer_styles() {
		?>
		<style type="text/css">
			#accordion-panel-widgets,
			#accordion-panel-nav_menus {
				display: none !important;
				height: 0 !important;
				visibility: hidden !important;
			}
			span.wc-pip-range-index {
				color: #999999;
				display: block;
				font-style: italic;
				margin-top: 4px;
			}
		</style>
		<?php
	}


	/**
	 * Dequeue theme styles from showing in the Customizer preview.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 */
	public function dequeue_styles() {
		global $wp_styles;

		if ( is_object( $wp_styles ) && isset( $_GET[ $this->customizer_trigger ] ) ) {

			if ( ! empty( $wp_styles->registered ) ) {

				foreach ( $wp_styles->registered as $registered ) {

					if ( ! $this->is_dependency_allowed( $registered ) ) {
						wp_deregister_style( $registered->handle );
					}
				}
			}

			if ( ! empty( $wp_styles->queue ) ) {

				foreach ( $wp_styles->queue as $enqueued ) {

					if ( ! $this->is_dependency_allowed( $enqueued ) ) {
						wp_dequeue_style( $enqueued );
					}
				}
			}
		}
	}


	/**
	 * Dequeue scripts from showing in the Customizer preview.
	 *
	 * @internal
	 *
	 * @since 3.2.0
	 */
	public function dequeue_scripts() {
		global $wp_scripts;

		if ( is_object( $wp_scripts ) && isset( $_GET[ $this->customizer_trigger ] ) ) {

			if ( ! empty( $wp_scripts->registered ) ) {

				foreach ( $wp_scripts->registered as $registered ) {

					if ( ! $this->is_dependency_allowed( $registered ) ) {
						wp_deregister_script( $registered->handle );
					}
				}
			}

			if ( ! empty( $wp_scripts->queue ) ) {

				foreach ( $wp_scripts->queue as $enqueued ) {

					if ( ! $this->is_dependency_allowed( $enqueued ) ) {
						wp_dequeue_script( $enqueued );
					}
				}
			}
		}
	}


	/**
	 * Checks whether a script or style should be allowed in the PIP customizer screen.
	 *
	 * @since 3.4.1-dev.1
	 *
	 * @param \stdClass|\WP_Dependency|string $dep a script or style as object or handle name
	 * @return bool
	 */
	private function is_dependency_allowed( $dep ) {

		$is_allowed = false;
		$haystack   = '';

		if ( is_string( $dep ) ) {
			$haystack = $dep;
		} elseif ( is_object( $dep ) ) {
			if ( ! empty( $dep->src ) ) {
				$haystack = $dep->src;
			} elseif ( ! empty( $dep->handle ) ) {
				$haystack = $dep->handle;
			}
		}

		if ( '' !== $haystack ) {

			$whitelist = array(
				// common WP admin script origins
				'wp-admin',
				'wp-includes',
				'googleapis',
				// WooCommerce core
				'/woocommerce/',
				// PIP own scripts
				'wc-pip',
				'woocommerce-pip',
				// WP Customizer scripts
				'customize-preview',
				'customize-selective-refresh',
				// others
				'jquery',
				'plupload',
				'mediaelement',
				'colors',
			);

			foreach ( $whitelist as $needle ) {

				if ( false !== strpos( $haystack, $needle ) ) {
					$is_allowed = true;
					break;
				}
			}
		}

		return $is_allowed;
	}


	/**
	 * Load template preview
	 *
	 * @since 3.0.0
	 * @param WP_Query $wp_query
	 * @return void|WP_Query Maybe redirect
	 */
	public function load_template( $wp_query ) {

		if ( isset( $_GET[ $this->customizer_trigger ] ) ) {

			wp_head();

			ob_start();

			include( wc_pip()->get_plugin_path() . '/includes/admin/views/html-pip-template-preview.php' );

			$template = ob_get_clean();

			wp_footer();

			echo $template;
			exit;
		}

		return $wp_query;
	}


}
