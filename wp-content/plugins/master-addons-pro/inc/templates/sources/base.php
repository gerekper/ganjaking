<?php

namespace MasterAddons\Inc\Templates\Sources;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Master_Addons_Templates_Source_Base {


	abstract public function get_slug();


	abstract public function get_version();


	abstract public function get_items();


	abstract public function get_categories();


	abstract public function get_keywords();


	abstract public function get_item( $template_id );


	abstract public function transient_lifetime();


	public function templates_key() {
		return 'master_addons_templates_' . $this->get_slug() . '_' . $this->get_version();
	}


	public function categories_key() {
		return 'master_addons_categories_' . $this->get_slug() . '_' . $this->get_version();
	}


	public function keywords_key() {
		return 'master_addons_keywords_' . $this->get_slug() . '_' . $this->get_version();
	}


	public function set_templates_cache( $value ) {
		set_transient( $this->templates_key(), $value, $this->transient_lifetime() );
	}


	public function get_templates_cache() {

		if ( $this->is_debug_active() ) {
			return false;
		}

		return get_transient( $this->templates_key() );
	}


	public function delete_templates_cache() {
		delete_transient( $this->templates_key() );
	}


	public function set_categories_cache( $value ) {
		set_transient( $this->categories_key(), $value, $this->transient_lifetime() );
	}


	public function get_categories_cache() {

		if ( $this->is_debug_active() ) {
			return false;
		}

		return get_transient( $this->categories_key() );
	}


	public function delete_categories_cache() {
		delete_transient( $this->categories_key() );
	}


	public function set_keywords_cache( $value ) {
		set_transient( $this->keywords_key(), $value, $this->transient_lifetime() );
	}


	public function get_keywords_cache() {

		if ( $this->is_debug_active() ) {
			return false;
		}

		return get_transient( $this->keywords_key() );
	}


	public function delete_keywords_cache() {
		delete_transient( $this->keywords_key() );
	}


	public function is_debug_active() {

		if ( defined( 'MA_EL_API_DEBUG' ) && true === MA_EL_API_DEBUG ) {
			return true;
		} else {
			return false;
		}

	}


	public function id_prefix() {
		return 'ma_el_';
	}


	protected function replace_elements_ids( $content ) {
		return \Elementor\Plugin::$instance->db->iterate_data( $content, function( $element ) {
			$element['id'] = \Elementor\Utils::generate_random_string();
			return $element;
		} );
	}


	protected function process_export_import_content( $content, $method ) {
		return \Elementor\Plugin::$instance->db->iterate_data(
			$content, function( $element_data ) use ( $method ) {
				$element = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );

				// If the widget/element isn't exist, like a plugin that creates a widget but deactivated
				if ( ! $element ) {
					return null;
				}

				return $this->process_element_export_import_content( $element, $method );
			}
		);
	}


	protected function process_element_export_import_content( $element, $method ) {

		$element_data = $element->get_data();

		if ( method_exists( $element, $method ) ) {
			// TODO: Use the internal element data without parameters.
			$element_data = $element->{$method}( $element_data );
		}

		foreach ( $element->get_controls() as $control ) {
			$control_class = \Elementor\Plugin::$instance->controls_manager->get_control( $control['type'] );

			// If the control isn't exist, like a plugin that creates the control but deactivated.
			if ( ! $control_class ) {
				return $element_data;
			}

			if ( method_exists( $control_class, $method ) ) {
				$element_data['settings'][ $control['name'] ] = $control_class->{$method}( $element->get_settings( $control['name'] ), $control );
			}
		}

		return $element_data;
	}
}
