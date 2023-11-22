<?php
/**
 * Class: Module
 * Name: Section Particles
 * Slug: premium-particles
 */

namespace PremiumAddonsPro\Modules\PremiumSectionParticles;

use Elementor\Controls_Manager;

use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddonsPro\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Module For Premium Particles section addon.
 */
class Module extends Module_Base {

	/**
	 * Load Script
	 *
	 * @var $load_assets
	 */
	private $load_assets = null;

	/**
	 * Class Constructor Funcion.
	 */
	public function __construct() {

		parent::__construct();

		$modules = Admin_Helper::get_enabled_elements();

		// Checks if Section Particles is enabled.
		$particles = $modules['premium-particles'];

		if ( ! $particles ) {
			return;
		}

		// Enqueue the required CSS/JS file.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_styles' ) );

		// Register Controls inside Section/Column Layout tab.
		add_action( 'elementor/element/section/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );

		add_action( 'elementor/section/print_template', array( $this, '_print_template' ), 10, 2 );
		add_action( 'elementor/column/print_template', array( $this, '_print_template' ), 10, 2 );

		// Insert data before Section/Column rendering.
		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render' ), 10, 1 );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'before_render' ), 10, 1 );

		add_action( 'elementor/frontend/column/before_render', array( $this, 'check_script_enqueue' ) );
		add_action( 'elementor/frontend/section/before_render', array( $this, 'check_script_enqueue' ) );

		if ( Helper_Functions::check_elementor_experiment( 'container' ) ) {
			add_action( 'elementor/element/container/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
			add_action( 'elementor/container/print_template', array( $this, '_print_template' ), 10, 2 );
			add_action( 'elementor/frontend/container/before_render', array( $this, 'before_render' ), 10, 1 );
			add_action( 'elementor/frontend/container/before_render', array( $this, 'check_script_enqueue' ) );
		}

	}

	/**
	 * Enqueue scripts.
	 *
	 * Registers required dependencies for the extension and enqueues them.
	 *
	 * @since 1.6.5
	 * @access public
	 */
	public function enqueue_scripts() {

		if ( ! wp_script_is( 'elementor-waypoints', 'enqueued' ) ) {
			wp_enqueue_script( 'elementor-waypoints' );
		}

		if ( ! wp_script_is( 'pa-particles', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-particles' );
		}

	}

	/**
	 * Enqueue styles.
	 *
	 * Registers required dependencies for the extension and enqueues them.
	 *
	 * @since 2.6.5
	 * @access public
	 */
	public function enqueue_styles() {

		if ( ! wp_style_is( 'pa-global', 'enqueued' ) ) {
			wp_enqueue_style( 'pa-global' );
		}
	}

	/**
	 * Register Particles controls.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$element->start_controls_section(
			'section_premium_particles',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Particles', 'premium-addons-pro' ) ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			)
		);

		$element->add_control(
			'premium_particles_switcher',
			array(
				'label'        => __( 'Enable Particles', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'premium-particles-',
				'render_type'  => 'template',
			)
		);

		$element->add_control(
			'premium_particles_zindex',
			array(
				'label'   => __( 'Z-index', 'premium-addons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
			)
		);

		$element->add_control(
			'premium_particles_custom_style',
			array(
				'label'       => __( 'Custom Style', 'premium-addons-pro' ),
				'type'        => Controls_Manager::CODE,
				'description' => __( 'Particles has been updated with many new features. You can now generate the JSON config from <a href="https://premiumaddons.com/docs/how-to-use-tsparticles-in-elementor-particles-section-addon/?utm_source=pa-dashboard&utm_medium=pa-editor&utm_campaign=pa-plugin" target="_blank">here</a> or <a href="http://vincentgarreau.com/particles.js/#default" target="_blank">here</a>', 'premium-addons-pro' ),
				'render_type' => 'template',
			)
		);

		$element->add_control(
			'particles_background_notice',
			array(
				'raw'             => __( 'Kindly, be noted that you will need to add a background as particles JSON code doesn\'t include a background color', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$element->add_control(
			'premium_particles_responsive',
			array(
				'label'       => __( 'Apply Particles On', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => array(
					'desktop' => __( 'Desktop', 'premium-addons-pro' ),
					'tablet'  => __( 'Tablet', 'premium-addons-pro' ),
					'mobile'  => __( 'Mobile', 'premium-addons-pro' ),
				),
				'default'     => array( 'desktop', 'tablet', 'mobile' ),
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$element->end_controls_section();

	}

	/**
	 * Render Particles output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.2.8
	 * @access public
	 *
	 * @param object $template current template.
	 * @param object $widget current widget.
	 */
	public function _print_template( $template, $widget ) {

		if ( $widget->get_name() === 'widget' ) {
			return $template;
		}

		$old_template = $template;
		ob_start();
		?>
		<# if( 'yes' === settings.premium_particles_switcher ) {

			view.addRenderAttribute( 'particles_data', {
				'id': 'premium-particles-' + view.getID(),
				'class': 'premium-particles-wrapper',
				'data-particles-style': settings.premium_particles_custom_style,
				'data-particles-zindex':  settings.premium_particles_zindex,
				'data-particles-devices': settings.premium_particles_responsive
			});

		#>
			<div {{{ view.getRenderAttributeString( 'particles_data' ) }}}></div>
		<# } #>
		<?php
			$slider_content = ob_get_contents();
			ob_end_clean();
			$template = $slider_content . $old_template;
			return $template;
	}

	/**
	 * Render Particles output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function before_render( $element ) {

		$type = $element->get_name();

		$settings = $element->get_settings_for_display();

		$zindex = ! empty( $settings['premium_particles_zindex'] ) ? $settings['premium_particles_zindex'] : 0;

		if ( 'yes' === $settings['premium_particles_switcher'] ) {

			if ( ! empty( $settings['premium_particles_custom_style'] ) ) {

				$particles_settings = array(
					'zindex'     => $zindex,
					'style'      => $settings['premium_particles_custom_style'],
					'responsive' => $settings['premium_particles_responsive'],
				);

				$element->add_render_attribute(
					'_wrapper',
					array(
						'data-particles-style'   => $particles_settings['style'],
						'data-particles-zindex'  => $particles_settings['zindex'],
						'data-particles-devices' => $particles_settings['responsive'],
					)
				);

			}
		}
	}

	/**
	 * Check Script Enqueue
	 *
	 * Check if the script files should be loaded.
	 *
	 * @since 2.6.3
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function check_script_enqueue( $element ) {

		if ( $this->load_assets ) {
			return;
		}

		if ( 'yes' == $element->get_settings_for_display( 'premium_particles_switcher' ) ) {

			$this->enqueue_styles();

			$this->enqueue_scripts();

			$this->load_assets = true;

			remove_action( 'elementor/frontend/section/before_render', array( $this, 'check_script_enqueue' ) );
			remove_action( 'elementor/frontend/column/before_render', array( $this, 'check_script_enqueue' ) );
			remove_action( 'elementor/frontend/container/before_render', array( $this, 'check_script_enqueue' ) );
		}

	}
}
