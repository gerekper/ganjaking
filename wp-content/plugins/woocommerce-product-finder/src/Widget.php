<?php
/**
 * Product Finder Widget.
 *
 * @since 1.4.0
 */

namespace Themesquad\WC_Product_Finder;

defined( 'ABSPATH' ) || exit;

/**
 * Product finder widget class.
 */
class Widget extends \WC_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {
		$this->widget_id          = 'product_finder';
		$this->widget_name        = __( 'WooCommerce Product Finder', 'woocommerce-product-finder' );
		$this->widget_cssclass    = 'widget_product_finder';
		$this->widget_description = __( 'Customisable Product Finder widget.', 'woocommerce-product-finder' );

		parent::__construct();

		$this->control_options = array_merge(
			$this->control_options,
			array(
				'width'  => 250,
				'height' => 350,
			)
		);

		add_action( 'woocommerce_widget_field_heading', array( $this, 'output_heading_field' ), 10, 4 );
	}

	/**
	 * Initializes the widget settings.
	 *
	 * @since 1.4.0
	 */
	public function init_settings() {
		if ( empty( $this->settings ) ) {
			$this->settings = $this->get_setting_fields();
		}
	}

	/**
	 * Gets the widget settings.
	 *
	 * @since 1.4.0
	 *
	 * @return array An array with the settings data.
	 */
	protected function get_setting_fields() {
		$settings = array(
			'title'           => array(
				'type'  => 'text',
				'label' => __( 'Title', 'woocommerce' ), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
				'std'   => __( 'Product Finder', 'woocommerce-product-finder' ),
			),
			'filters_heading' => array(
				'type'  => 'heading',
				'label' => __( 'Select which criteria to include:', 'woocommerce-product-finder' ),
				'std'   => '',
			),
			'use_category'    => array(
				'type'  => 'checkbox',
				'label' => __( 'Product Category', 'woocommerce-product-finder' ),
				'std'   => 1,
			),
		);

		$attributes = wc_get_attribute_taxonomy_labels();

		foreach ( $attributes as $name => $label ) {
			$settings[ 'use_att_pa_' . $name ] = array(
				'type'  => 'checkbox',
				'label' => $label,
				'std'   => 1,
			);
		}

		return $settings;
	}

	/**
	 * Adds a heading when the widget is edited.
	 *
	 * @since 1.4.0
	 *
	 * @param string $key     Setting key.
	 * @param string $value   Setting value.
	 * @param array  $setting An array with the setting data.
	 */
	public function output_heading_field( $key, $value, $setting ) {
		if ( ! empty( $setting['label'] ) ) {
			printf( '<h4 class="%1$s">%2$s</h4>', esc_attr( $key ), esc_html( $setting['label'] ) );
		}
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @since 1.4.0
	 *
	 * @param array $instance Instance.
	 */
	public function form( $instance ) {
		$this->init_settings();

		parent::form( $instance );
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @since 1.4.0
	 *
	 * @see WC_Widget->update
	 *
	 * @param array $new_instance New instance.
	 * @param array $old_instance Old instance.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$this->init_settings();

		return parent::update( $new_instance, $old_instance );
	}

	/**
	 * Display the widget on the frontend.
	 *
	 * @since 1.4.0
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Widget settings for this instance.
	 */
	public function widget( $args, $instance ) {
		$this->init_settings();

		$this->widget_start( $args, $instance );

		/**
		 * Fires on top of the product finder widget.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->widget_cssclass . '_top' );

		// Get selected attributes.
		$used_atts  = array();
		$attributes = wc_get_attribute_taxonomy_labels();

		foreach ( $attributes as $name => $label ) {
			if ( ! in_array( $name, array( 'title', 'use_category' ), true ) ) {
				$used_atts[] = $name;
			}
		}

		$use_category = ( isset( $instance['use_category'] ) ? $instance['use_category'] : $this->settings['use_category']['std'] );

		// Outputs the search form.
		woocommerce_product_finder(
			array(
				'use_category'      => $use_category,
				'search_attributes' => $used_atts,
			)
		);

		/**
		 * Fires at the bottom of the product finder widget.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->widget_cssclass . '_bottom' );

		$this->widget_end( $args );
	}
}
