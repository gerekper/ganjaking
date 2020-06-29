<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

if ( ! class_exists( 'DiviGrooniGroovyMenu_Init' ) ) {

	class DiviGrooniGroovyMenu_Init extends DiviExtension {

		/**
		 * The gettext domain for the extension's translations.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $gettext_domain = 'groovy-menu';

		/**
		 * The extension's WP Plugin name.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $name = 'grooni_groovymenu';

		/**
		 * The extension's version
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $version = '1.0.3';

		/**
		 * DiviGrooniGroovyMenu_Init constructor.
		 *
		 * @param string $name
		 * @param array  $args
		 */
		public function __construct( $name = 'grooni_groovymenu', $args = array() ) {
			$this->plugin_dir     = plugin_dir_path( __FILE__ );
			$this->plugin_dir_url = plugin_dir_url( $this->plugin_dir );

			parent::__construct( $name, $args );
		}
	}

	new DiviGrooniGroovyMenu_Init;

}
