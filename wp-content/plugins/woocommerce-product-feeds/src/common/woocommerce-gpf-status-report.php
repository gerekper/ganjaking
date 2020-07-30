<?php

class WoocommerceGpfStatusReport {

	/**
	 * The template loader used for rendering the output.
	 * @var WoocommerceGpfTemplateLoader.
	 */
	private $template_loader;

	/**
	 * The plugin settings, as retrieved from the database.
	 * @var array
	 */
	private $settings = array();

	/**
	 * Constructor.
	 *
	 * Store dependencies.
	 *
	 * @param WoocommerceGpfTemplateLoader $template_loader An instance of the template loader.
	 * @param WoocommerceGpfCommon $common
	 */
	public function __construct(
		WoocommerceGpfTemplateLoader $template_loader,
		WoocommerceGpfCommon $common
	) {
		$this->template_loader = $template_loader;
		$this->common          = $common;
	}

	/**
	 * Actually run the class.
	 *
	 * Attaches to the relevant hooks.
	 */
	public function initialise() {
		add_action( 'woocommerce_system_status_report', array( $this, 'render' ) );
	}

	/**
	 * Render the system status output for this plugin.
	 */
	public function render() {
		$this->settings = get_option( 'woocommerce_gpf_config', array() );
		$this->render_options();
		$this->render_field_config();
	}

	private function render_field_config() {
		$this->template_loader->output_template_with_variables(
			'woo-gpf-status-report',
			'header',
			array(
				'title'      => esc_html( __( 'WooCommerce Google Product Feed fields', 'woocommerce_gpf' ) ),
				'attr_title' => esc_attr( __( 'WooCommerce Google Product Feed fields', 'woocommerce_gpf' ) ),
			)
		);
		foreach ( $this->settings['product_fields'] as $key => $value ) {
			if ( 'on' !== $value ) {
				continue;
			}
			$field_name = ! empty( $this->common->product_fields[ $key ]['desc'] ) ? $this->common->product_fields[ $key ]['desc'] : $key;
			$status     = '';
			if ( ! empty( $this->settings['product_defaults'][ $key ] ) ) {
				$status .= sprintf(
				// Translators: Placeholder is the "default value" for this field
					__( 'Defaults to &quot;%s&quot;. ', 'woocommerce_gpf' ),
					esc_html( $this->settings['product_defaults'][ $key ] )
				);
			}
			if ( ! empty( $this->settings['product_prepopulate'][ $key ] ) ) {
				if ( stripos( $this->settings['product_prepopulate'][ $key ], 'description:' ) === 0 ) {
					$description_options = $this->common->get_description_prepopulate_options();
					$prepop_value        = $this->settings['product_prepopulate'][ $key ];
					$status             .= isset( $description_options[ $prepop_value ] ) ?
						$description_options[ $prepop_value ] :
						$prepop_value;
				} else {
					$prepopulate = $this->generate_prepopulate_for_field( $key );
					// Translators: Placeholder is a description of the pre-population rule for this field.
					$status .= sprintf( __( 'Pre-populates from %s.', 'woocommerce_gpf' ), $prepopulate );
				}
			}
			$this->template_loader->output_template_with_variables(
				'woo-gpf-status-report',
				'item',
				array(
					'attr_name' => esc_attr( $field_name ),
					'name'      => esc_html( $field_name ),
					'status'    => $status,
				)
			);
		}
		$this->template_loader->output_template_with_variables(
			'woo-gpf-status-report',
			'footer',
			array()
		);
	}

	private function render_options() {
		// Grab the output for the various settings.
		if ( isset( $this->settings['include_variations'] ) && 'on' === $this->settings['include_variations'] ) {
			$include_variations = __( 'Enabled', 'woocommerce_gpf' );
		} else {
			$include_variations = __( 'No', 'woocommerce_gpf' );
		}
		if ( isset( $this->settings['send_item_group_id'] ) && 'on' === $this->settings['send_item_group_id'] ) {
			$send_item_group_id = __( 'Enabled', 'woocommerce_gpf' );
		} else {
			$send_item_group_id = __( 'No', 'woocommerce_gpf' );
		}
		$debug_key = get_option( 'woocommerce_gpf_debug_key', __( 'Not set', 'woocommerce_gpf' ) );

		// Output them.
		$this->template_loader->output_template_with_variables(
			'woo-gpf-status-report',
			'header',
			array(
				'attr_title' => esc_attr( __( 'WooCommerce Google Product Feed options', 'woocommerce_gpf' ) ),
				'title'      => esc_html( __( 'WooCommerce Google Product Feed options', 'woocommerce_gpf' ) ),
			)
		);
		$this->render_enabled_feeds();
		$this->template_loader->output_template_with_variables(
			'woo-gpf-status-report',
			'item',
			array(
				'name'      => esc_html( __( 'Include variations in feed', 'woocommerce_gpf' ) ),
				'attr_name' => esc_attr( __( 'Include variations in feed', 'woocommerce_gpf' ) ),
				'status'    => esc_html( $include_variations ),
			)
		);
		$this->template_loader->output_template_with_variables(
			'woo-gpf-status-report',
			'item',
			array(
				'name'      => esc_html( __( 'Send &quot;item group ID&quot;', 'woocommerce_gpf' ) ),
				'attr_name' => esc_attr( __( 'Send &quot;item group ID&quot;', 'woocommerce_gpf' ) ),
				'status'    => esc_html( $send_item_group_id ),
			)
		);

		$this->template_loader->output_template_with_variables(
			'woo-gpf-status-report',
			'item',
			array(
				'name'      => esc_html( __( 'Debug key', 'woocommerce_gpf' ) ),
				'attr_name' => esc_attr( __( 'Debug key', 'woocommerce_gpf' ) ),
				'status'    => esc_html( $debug_key ),
			)
		);
		$this->template_loader->output_template_with_variables(
			'woo-gpf-status-report',
			'footer',
			array()
		);
	}

	/**
	 * Render the list showing which feed types are enabled.
	 */
	private function render_enabled_feeds() {
		$feed_types = $this->common->get_feed_types();
		foreach ( $feed_types as $feed_type => $feed_type_info ) {
			$enabled_or_not = __( '-', 'woocommerce_gpf' );
			if ( isset( $this->settings['gpf_enabled_feeds'][ $feed_type ] ) &&
				 'on' === $this->settings['gpf_enabled_feeds'][ $feed_type ] ) {
				$enabled_or_not = __( 'Enabled', 'woocommerce_gpf' );
			}
			$name      = $feed_type_info['name'];
			$attr_name = $feed_type_info['name'];
			$this->template_loader->output_template_with_variables(
				'woo-gpf-status-report',
				'item',
				array(
					'name'      => esc_html( $name ),
					'attr_name' => esc_attr( $attr_name ),
					'status'    => esc_html( $enabled_or_not ),
				)
			);
		}

	}

	/**
	 * @param $key
	 *
	 * @return mixed|string
	 */
	private function generate_prepopulate_for_field( $key ) {
		if ( stripos( $this->settings['product_prepopulate'][ $key ], 'tax:' ) === 0 ) {
			// Translators: Placeholder is the name of the taxonomy
			$prepopulate = sprintf( __( '%s taxonomy', 'woocommerce_gpf' ), str_replace( 'tax:', '', esc_html( $this->settings['product_prepopulate'][ $key ] ) ) );
		} elseif ( stripos( $this->settings['product_prepopulate'][ $key ], 'field:' ) === 0 ) {
			// Translators: Placeholder is the name of the product field
			$prepopulate = sprintf( __( 'product %s', 'woocommerce_gpf' ), str_replace( 'field:', '', esc_html( $this->settings['product_prepopulate'][ $key ] ) ) );
		} elseif ( stripos( $this->settings['product_prepopulate'][ $key ], 'meta:' ) === 0 ) {
			// Translators: Placeholder is the key of the meta field
			$prepopulate = sprintf( __( '%s meta field', 'woocommerce_gpf' ), str_replace( 'meta:', '', esc_html( $this->settings['product_prepopulate'][ $key ] ) ) );
		} else {
			$description = apply_filters(
				'woocommerce_gpf_prepopulation_description',
				$this->settings['product_prepopulate'][ $key ],
				$this->settings['product_prepopulate'][ $key ]
			);
			$prepopulate = esc_html( $description );
		}

		return $prepopulate;
	}
}
