<?php

namespace WPForms\Lite\Admin\Education\Builder;

use \WPForms\Admin\Education\EducationInterface;

/**
 * Builder/FormTemplates Education feature.
 *
 * @since 1.6.6
 */
class FormTemplates implements EducationInterface {

	/**
	 * Indicate if current Education feature is allowed to load.
	 *
	 * @since 1.6.6
	 *
	 * @return bool
	 */
	public function allow_load() {

		return wpforms_is_admin_page( 'builder' );
	}

	/**
	 * Init.
	 *
	 * @since 1.6.6
	 */
	public function init() {

		if ( ! $this->allow_load() ) {
			return;
		}

		// Define hooks.
		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.6.6
	 */
	public function hooks() {

		add_action( 'wpforms_setup_panel_after', [ $this, 'display' ] );
	}

	/**
	 * Templates.
	 *
	 * @since 1.6.6
	 */
	public function get_templates() {

		return [
			[
				'name'        => esc_html__( 'Request A Quote Form', 'wpforms-lite' ),
				'slug'        => 'request-quote',
				'description' => esc_html__( 'Start collecting leads with this pre-made Request a quote form. You can add and remove fields as needed.', 'wpforms-lite' ),
			],
			[
				'name'        => esc_html__( 'Donation Form', 'wpforms-lite' ),
				'slug'        => 'donation',
				'description' => esc_html__( 'Start collecting donation payments on your website with this ready-made Donation form. You can add and remove fields as needed.', 'wpforms-lite' ),
			],
			[
				'name'        => esc_html__( 'Billing / Order Form', 'wpforms-lite' ),
				'slug'        => 'order',
				'description' => esc_html__( 'Collect payments for product and service orders with this ready-made form template. You can add and remove fields as needed.', 'wpforms-lite' ),
			],
		];
	}

	/**
	 * Display templates.
	 *
	 * @since 1.6.6
	 */
	public function display() {

		$empty_template = [
			'name'        => '',
			'slug'        => '',
			'description' => '',
		];

		echo wpforms_render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'education/builder/form-templates',
			[
				'templates'      => $this->get_templates(),
				'empty_template' => $empty_template,
			],
			true
		);
	}
}
