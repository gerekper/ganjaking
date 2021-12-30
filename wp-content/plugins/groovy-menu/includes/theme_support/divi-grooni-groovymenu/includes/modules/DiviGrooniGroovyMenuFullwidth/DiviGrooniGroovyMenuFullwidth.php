<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

if ( ! class_exists( 'DiviGrooniGroovyMenuFullwidth' ) && class_exists( 'ET_Builder_Module' ) ) {

	class DiviGrooniGroovyMenuFullwidth extends ET_Builder_Module {

		public $slug       = 'grooni_groovymenu_fullwidth';
		public $vb_support = 'on';
		public $fullwidth  = true;

		protected $module_credits = array(
			'module_uri' => 'https://groovymenu.grooni.com',
			'author'     => 'grooni.com',
			'author_uri' => 'https://grooni.com',
		);

		public function init() {
			$this->name = esc_html__( 'Groovy Menu Fullwidth', 'groovy-menu' );

			$this->help_videos = array(
				array(
					'id'   => esc_html( 'ZiGtqayLllk' ),
					'name' => esc_html__( 'How To Create A DIVI Mega Menu with Groovy Menu', 'groovy-menu' ),
				),
			);
		}

		public function get_fields() {
			return array();
		}

		public function render( $attrs, $content, $render_slug ) {
			$module_id    = $this->props['module_id'];
			$module_class = $this->props['module_class'];

			$module_class = ET_Builder_Element::add_module_order_class( $module_class, $render_slug );

			$groovy_menu = '';
			if ( function_exists( 'groovy_menu' ) ) {
				$groovy_menu = groovy_menu( array( 'gm_echo' => false ) );
			}

			$output = sprintf(
				'<div%1$s class="%2$s">%3$s</div>',
				( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
				( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
				$groovy_menu
			);

			return $output;
		}
	}

	new DiviGrooniGroovyMenuFullwidth();

}