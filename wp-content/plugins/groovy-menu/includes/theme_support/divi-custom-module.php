<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

if ( ! class_exists( 'GroovyMenu_Custom_Module_For_Builder' ) && class_exists('ET_Builder_Module') ) {

	class GroovyMenu_Custom_Module_For_Builder extends ET_Builder_Module {
		function init() {
			$this->name = esc_html__( 'Groovy Menu', 'groovy-menu' );
			$this->slug = 'grooni_groovymenu';

			$this->whitelisted_fields = array(
				'admin_label',
				'module_id',
				'module_class',
			);
		}

		function get_fields() {
			return array();
		}

		function shortcode_callback( $atts, $content = null, $function_name ) {
			$module_id    = $this->shortcode_atts['module_id'];
			$module_class = $this->shortcode_atts['module_class'];

			$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

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

	new GroovyMenu_Custom_Module_For_Builder();

}
