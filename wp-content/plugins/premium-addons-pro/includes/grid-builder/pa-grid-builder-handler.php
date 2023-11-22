<?php
/**
 * PA Grid Builder Handler File.
 * Used to Initialise the Grid Builder and
 * add its dependencies.
 */

namespace PremiumAddonsPro\Includes\GridBuilder;

use PremiumAddons\Admin\Includes\Admin_Helper;
use Elementor\Plugin;
use ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager;
use Elementor\Core\Documents_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Pa_Grid_Builder_Handler' ) ) {

	/**
	 * Class Pa_Grid_Builder_Handler.
	 */
	class Pa_Grid_Builder_Handler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$enabled_elements = Admin_Helper::get_enabled_elements();

			$is_spl_active = $enabled_elements['premium-smart-post-listing'];

			if ( $is_spl_active ) {

				$this->init_path_var();

				add_action( 'plugins_loaded', array( $this, 'init_grid_builder_hooks' ) );

			}
		}

		/**
		 * Define the grid builder folder path.
		 *
		 * @access private
		 * @since 2.8.20
		 */
		private function init_path_var() {

			if ( ! defined( 'PREMIUM_GRID_BUILDER_PATH' ) ) {
				define( 'PREMIUM_GRID_BUILDER_PATH', PREMIUM_PRO_ADDONS_PATH . 'includes/grid-builder/' );
			}
		}

		/**
		 * Add the grid builder hooks.
		 *
		 * @access public
		 * @since 2.8.20
		 */
		public function init_grid_builder_hooks() {

			add_action( 'elementor_pro/init', array( $this, 'add_grid_builder_docs' ) );

			add_action( 'elementor/theme/register_locations', array( $this, 'register_locations' ) );

			add_action( 'elementor/documents/register', array( $this, 'register_documents' ) );

			if ( defined( 'ELEMENTOR_VERSION' ) ) {

				add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

			}
		}

		/**
		 * Add the necessary grid builder docs/files.
		 *
		 * @access public
		 * @since 2.8.20
		 */
		public function add_grid_builder_docs() {
			require_once PREMIUM_GRID_BUILDER_PATH . 'conditions/premium-grid.php';
			require_once PREMIUM_GRID_BUILDER_PATH . 'documents/premium-grid.php';
		}

		/**
		 * Registers the Premium Grid Loaction.
		 *
		 * @access public
		 * @since 2.8.20
		 *
		 * @param Locations_Manager $location_manager  Elementor's location manager.
		 *
		 * @see https://developers.elementor.com/docs/themes/registering-locations/
		 */
		public function register_locations( Locations_Manager $location_manager ) {

			$location_manager->register_location(
				'premium_grid',
				array(
					'label'           => __( 'Premium Grid', 'premium-addons-pro' ),
					'multiple'        => true,
					'edit_in_content' => true,
				)
			);

		}

		/**
		 * Registers the Premium Grid Document.
		 *
		 * @access public
		 * @since 2.8.20
		 *
		 * @param Documents_Manager $documents_manager Elementor's document manager.
		 */
		public function register_documents( Documents_Manager $documents_manager ) {

			/* TODO: Look for a different hook to initialize documents - elementor_pro/init hook is not being called for maintenance tasks */
			if ( ! class_exists( '\PremiumAddonsPro\Includes\GridBuilder\Premium_Grid' ) ) {

				$this->add_grid_builder_docs();
			}

			$documents_manager->register_document_type( 'premium-grid', Premium_Grid::get_class_full_name() );
		}

		/**
		 * Registers the Premium Grid Widgets.
		 *
		 * @access public
		 * @since 2.8.20
		 */
		public function register_widgets() {

			require_once PREMIUM_GRID_BUILDER_PATH . 'widgets/premium-grid-item.php';

			Plugin::instance()->widgets_manager->register( new Premium_Grid_Item() );

		}
	}

}

new Pa_Grid_Builder_Handler();
