<?php
/**
 * Storefront Powerpack Frontend Header Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Customizer_Header' ) ) :

	/**
	 * The Frontend class.
	 */
	class SP_Customizer_Header extends SP_Customizer {
		/**
		 * The id of this section.
		 *
		 * @const string
		 */
		const POWERPACK_HEADER_SECTION = 'sp_header_section';

		/**
		 * The id of the setting.
		 *
		 * @const string
		 */
		const POWERPACK_HEADER_SETTING = 'sp_header_setting';

		/**
		 * The name of the hook on which we will be working our magic.
		 * @var     string
		 * @access  public
		 * @since   1.0.0
		 */
		public $hook;

		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			$this->hook = (string) apply_filters( 'sp_header_hook', 'storefront_header' );

			add_action( 'customize_controls_enqueue_scripts', array( $this, 'scripts' ) );
			add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_configurator' ) );
		}

		/**
		 * Returns an array of the Storefront Powerpack setting defaults.
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function setting_defaults() {
			return $args = array(
				'sp_header_sticky' => false,
			);
		}

		/**
		 * Enqueue styles and scripts.
		 *
		 * @since   1.0.0
		 * @return  void
		 */
		public function scripts() {
			wp_enqueue_style( 'sp-header-css', SP_PLUGIN_URL . 'includes/customizer/header/assets/css/sp-header.css', array(), storefront_powerpack()->version, 'all' );

			// Gridstack
			wp_enqueue_style( 'sp-gridstack', SP_PLUGIN_URL . 'includes/customizer/header/assets/js/vendor/gridstack.min.css', array(), storefront_powerpack()->version, 'all' );
			wp_enqueue_script( 'sp-gridstack', SP_PLUGIN_URL . 'includes/customizer/header/assets/js/vendor/gridstack.min.js', array( 'jquery', 'jquery-ui-draggable', 'jquery-ui-resizable', 'jquery-ui-widget', 'jquery-ui-mouse', 'underscore' ), storefront_powerpack()->version );

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_script( 'sp-header', SP_PLUGIN_URL . 'includes/customizer/header/assets/js/sp-header' . $suffix . '.js', array( 'jquery', 'wp-backbone', 'customize-controls' ), storefront_powerpack()->version, true );
		}

		/**
		 * Customizer Controls and Settings
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @since 1.0.0
		 */
		public function customize_register( $wp_customize ) {
			require_once dirname( __FILE__ ) . '/controls/class-sp-header-setting.php';
			require_once dirname( __FILE__ ) . '/controls/class-sp-header-action-control.php';

			/**
			* Header Section
			*/
			$wp_customize->add_section( self::POWERPACK_HEADER_SECTION, array(
				'title'    => __( 'Header', 'storefront-powerpack' ),
				'panel'    => self::POWERPACK_PANEL,
				'priority' => 40,
			) );

			/**
			* Header Setting
			*/
			$wp_customize->add_setting( new SP_Header_Setting( $wp_customize, self::POWERPACK_HEADER_SETTING, array() ) );

			/**
			 * Action control
			 */
			if ( class_exists( 'SP_Header_Action_Control' ) ) {
				$wp_customize->add_control( new SP_Header_Action_Control( $wp_customize, 'sp_header_action', array(
					'section'  => self::POWERPACK_HEADER_SECTION,
					'priority' => 10,
					'settings' => self::POWERPACK_HEADER_SETTING
				) ) );
			}

			/**
			 * Storefront Divider
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_header_divider', array(
					'section'  => self::POWERPACK_HEADER_SECTION,
					'type'     => 'divider',
					'priority' => 20,
				) ) );
			}

			/**
			 * Sticky Header Heading
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_header_sticky_heading', array(
					'section'  => self::POWERPACK_HEADER_SECTION,
					'type'     => 'heading',
					'label'    => __( 'Sticky Header', 'storefront-powerpack' ),
					'priority' => 30,
				) ) );
			}

			/**
			 * Sticky header
			 */
			$wp_customize->add_setting( 'sp_header_sticky', array(
				'default' => false,
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_header_sticky', array(
				'label'    => __( 'Stick the site header to the top of the browser window.', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_HEADER_SECTION,
				'settings' => 'sp_header_sticky',
				'type'     => 'checkbox',
				'priority' => 40,
			) ) );
		}

		/**
		 * Container for configurator panel.
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function print_configurator() {
		?>
			<div id="sp-header-configurator">
				<div class="sp-header-section-title">
					<h1 class="sp-header-item-title"><?php _e( 'Header Customizer', 'storefront-powerpack' ); ?></h1>
				</div>

				<div class="sp-header-gridstack-wrapper sp-header-grid-empty">
					<div class="sp-header-gridstack-inner">
						<p class="sp-header-grid-empty-notice"><?php esc_attr_e( 'Add a component to start. Drag and drop to re-arrange their display order. Adjust the width of each component by dragging its edges.', 'storefront-powerpack' ); ?></p>
						<div class="grid-stack sp-header-gridstack">
						</div>
					</div>
				</div>

				<div class="sp-header-components-shelf">
					<?php foreach ( self::components() as $id => $component ) { ?>
						<a data-component-id="<?php echo esc_attr( $id ); ?>"><?php esc_attr_e( $component['title'], 'storefront-powerpack' ); ?></a>
					<?php } ?>
				</div>

				<?php if ( is_child_theme() ) { ?>
				<div style="margin: 12px 12px 0; padding: 10px; background-color: #fff; border: 1px solid #ccc;">
					<span class="dashicons dashicons-info" style="color: #007cb2; float: right; margin-left: 1em;"></span>
					<p style="margin: 0;"><?php esc_attr_e( 'Storefront child themes sometimes apply unique styles to header components based on the layout. Therefore changing the layout can impact the design. Remember you can edit the appearance of components in the header using the Designer feature if this is the case with your child theme.', 'storefront-powerpack' ); ?></p>
				</div>
				<?php } ?>
			</div>
		<?php
		}

		/**
		 * A list of Storefront header components.
		 * @access  private
		 * @since   1.0.0
		 * @return  array Array of components.
		 */
		public static function components() {
			$components = array(
				'logo' => array(
					'title' => __( 'Logo', 'storefront-powerpack' ),
					'hook'  => 'storefront_site_branding'
				),
				'primary_navigation' => array(
					'title' => __( 'Primary Navigation', 'storefront-powerpack' ),
					'hook'  => 'storefront_primary_navigation'
				),
				'secondary_navigation' => array(
					'title' => __( 'Secondary Navigation', 'storefront-powerpack' ),
					'hook'  => 'storefront_secondary_navigation'
				),
			);

			if ( class_exists( 'WooCommerce' ) ) {
				$components['search'] = array(
					'title' => __( 'Search', 'storefront-powerpack' ),
					'hook'  => 'storefront_product_search'
				);

				$components['cart'] = array(
					'title' => __( 'Cart', 'storefront-powerpack' ),
					'hook'  => 'storefront_header_cart'
				);
			}

			return apply_filters( 'sp_header_components', $components );
		}
	}

endif;

return new SP_Customizer_Header();