<?php

/**
 * XPorter class
 *
 * Processes template export impoart
 */

namespace Happy_Addons_Pro;

use Elementor\Controls_Stack;
use Elementor\DB;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Plugin;
use Elementor\Utils;

defined('ABSPATH') || exit;

class IXPorter {

	public function prepare_template_export($template_id) {
		$template_data = $this->get_data([
			'template_id' => $template_id,
		]);

		if (empty($template_data['content'])) {
			return new \WP_Error('empty_template', 'The template is empty');
		}

		$template_data['content'] = $this->process_export_import_content($template_data['content'], 'on_export');

		if (get_post_meta($template_id, '_elementor_page_settings', true)) {
			$page = SettingsManager::get_settings_managers('page')->get_model($template_id);

			$page_settings_data = $this->process_element_export_import_content($page, 'on_export');

			if (!empty($page_settings_data['settings'])) {
				$template_data['page_settings'] = $page_settings_data['settings'];
			}
		}

		$export_data = [
			'version' => DB::DB_VERSION,
			'title' => get_the_title($template_id),
		];

		$export_data += $template_data;

		return $export_data;
	}

	/**
	 * Get template data.
	 *
	 * Retrieve the data of a single local template saved by the user on his site.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $args Custom template arguments.
	 *
	 * @return array Local template data.
	 */
	public function get_data(array $args) {

		$template_id = $args['template_id'];

		// TODO: Validate the data (in JS too!).
		if (!empty($args['display'])) {
			$content = Plugin::$instance->documents->get_doc_or_auto_save( $template_id )->get_elements_raw_data( null, true );
		} else {
			$document = Plugin::$instance->documents->get($template_id);
			$content = $document ? $document->get_elements_data() : [];
		}

		if (!empty($content)) {
			$content = $this->replace_elements_ids($content);
		}

		$data = [
			'content' => $content,
		];

		if (!empty($args['with_page_settings'])) {
			$page = SettingsManager::get_settings_managers('page')->get_model($args['template_id']);

			$data['page_settings'] = $page->get_data('settings');
		}

		return $data;
	}

	/**
	 * Process content for export/import.
	 *
	 * Process the content and all the inner elements, and prepare all the
	 * elements data for export/import.
	 *
	 * @since 1.5.0
	 * @access protected
	 *
	 * @param array  $content A set of elements.
	 * @param string $method  Accepts either `on_export` to export data or
	 *                        `on_import` to import data.
	 *
	 * @return mixed Processed content data.
	 */
	public function process_export_import_content($content, $method) {
		return Plugin::$instance->db->iterate_data(
			$content,
			function ($element_data) use ($method) {
				$element = Plugin::$instance->elements_manager->create_element_instance($element_data);

				// If the widget/element isn't exist, like a plugin that creates a widget but deactivated
				if (!$element) {
					return null;
				}

				return $this->process_element_export_import_content($element, $method);
			}
		);
	}

	/**
	 * Process single element content for export/import.
	 *
	 * Process any given element and prepare the element data for export/import.
	 *
	 * @since 1.5.0
	 * @access protected
	 *
	 * @param Controls_Stack $element
	 * @param string         $method
	 *
	 * @return array Processed element data.
	 */
	public function process_element_export_import_content(Controls_Stack $element, $method) {
		$element_data = $element->get_data();

		if (method_exists($element, $method)) {
			// TODO: Use the internal element data without parameters.
			$element_data = $element->{$method}($element_data);
		}

		foreach ($element->get_controls() as $control) {
			$control_class = Plugin::$instance->controls_manager->get_control($control['type']);

			// If the control isn't exist, like a plugin that creates the control but deactivated.
			if (!$control_class) {
				return $element_data;
			}

			if ('on_import' === $method) {
				if (method_exists($control_class, $method)) {

					if ($control['type'] == 'media') {

						$element_data['settings'][$control['name']] = $this->handleMediaImport($element->get_settings($control['name']), $control);
					} elseif ($control['type'] == 'repeater') {

						$element_data['settings'][$control['name']] = $this->handleRepeaterImport($element->get_settings($control['name']), $control);
					} else {
						$element_data['settings'][$control['name']] = $control_class->{$method}($element->get_settings($control['name']), $control);
					}
				}
			} else {
				if (method_exists($control_class, $method)) {
					$element_data['settings'][$control['name']] = $control_class->{$method}($element->get_settings($control['name']), $control);
				}
			}

			// if ( method_exists( $control_class, $method ) ) {
			// 	$element_data['settings'][ $control['name'] ] = $control_class->{$method}( $element->get_settings( $control['name'] ), $control );
			// }

			// On Export, check if the control has an argument 'export' => false.
			if ('on_export' === $method && isset($control['export']) && false === $control['export']) {
				unset($element_data['settings'][$control['name']]);
			}
		}

		return $element_data;
	}

	/**
	 * Replace elements IDs.
	 *
	 * For any given Elementor content/data, replace the IDs with new randomly
	 * generated IDs.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array $content Any type of Elementor data.
	 *
	 * @return mixed Iterated data.
	 */
	public function replace_elements_ids($content) {
		return Plugin::$instance->db->iterate_data($content, function ($element) {
			$element['id'] = Utils::generate_random_string();

			return $element;
		});
	}


	public function handleMediaImport($settings) {
		if (empty($settings['url'])) {
			return $settings;
		}

		if (strpos($settings['url'], 'images/placeholder.png') !== false) {
			$settings['url'] = Utils::get_placeholder_image_src();
			// return $settings;
		} else {
			Plugin::$instance->uploads_manager->enable_unfiltered_files_upload();
			$settings = Plugin::$instance->templates_manager->get_import_images_instance()->import($settings);
		}

		if (!$settings) {
			$settings = [
				'id' => '',
				'url' => Utils::get_placeholder_image_src(),
			];
		}

		return $settings;
	}

	public function handleRepeaterImport($settings, $control_data = []) {
		if (empty($settings) || empty($control_data['fields'])) {
			return $settings;
		}

		$method = 'on_import';

		foreach ($settings as &$item) {
			foreach ($control_data['fields'] as $field) {
				if (empty($field['name']) || empty($item[$field['name']])) {
					continue;
				}

				$control_obj = Plugin::$instance->controls_manager->get_control($field['type']);

				if (!$control_obj) {
					continue;
				}
				if ($field['type'] == 'media') {

					$item[$field['name']] = $this->handleMediaImport($item[$field['name']], $field);
				} else {
					if (method_exists($control_obj, $method)) {
						$item[$field['name']] = $control_obj->{$method}($item[$field['name']], $field);
					}
				}
			}
		}

		return $settings;
	}
}
