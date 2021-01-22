<?php
/**
 * WooCommerce Checkout Add-Ons
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Admin\Meta_Boxes;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_Factory;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_With_Options;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Display_Rules\Display_Rule;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Display_Rules\Display_Rule_Factory;

defined( 'ABSPATH' ) or exit;

/**
 * Add-On Data Meta Box Class
 *
 * @since 2.0.0
 */
class Add_On_Data extends Add_On_Meta_Box {


	/** @var array fields to display in this meta box */
	protected $fields;


	/**
	 * Constructs the meta box class.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On|null $add_on the add-on to provide data for the meta box
	 */
	public function __construct( $add_on = null ) {

		parent::__construct( $add_on );

		$this->fields = $this->get_fields();
	}


	/**
	 * Renders the meta box.
	 *
	 * @since 2.0.0
	 */
	public function render() {

		$panels = $this->get_panels();

		echo '<div class="panel-wrap data">';

		$this->render_tabs( $panels );
		$this->render_panels( $panels );

		echo '</div>';
	}


	/**
	 * Gets the fields to display in this meta box.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_fields() {

		$fields = [
			'enabled'                => [
				'id'          => 'enabled',
				'type'        => 'checkbox',
				'label'       => __( 'Enabled', 'woocommerce-checkout-add-ons' ),
				'description' => __( 'Enables or disables this add-on', 'woocommerce-checkout-add-ons' ),
				'value'       => $this->add_on && ! $this->add_on->get_enabled( 'edit' ) ? 'no' : 'yes',
			],
			'label'                  => [
				'id'          => 'label',
				'type'        => 'text',
				'label'       => __( 'Checkout Label', 'woocommerce-checkout-add-ons' ),
				'placeholder' => __( 'Label at Checkout', 'woocommerce-checkout-add-ons' ),
				'description' => __( 'Optional descriptive label shown on checkout, e.g., "Add a Gift Message?". This will default to Name if blank.', 'woocommerce-checkout-add-ons' ),
				'desc_tip'    => true,
				'value'       => $this->add_on ? $this->add_on->get_label( 'edit' ) : '',
			],
			'description'            => [
				'id'          => 'description',
				'type'        => 'textarea',
				'label'       => __( 'Description', 'woocommerce-checkout-add-ons' ),
				'placeholder' => __( 'Enter a description (optional)', 'woocommerce-checkout-add-ons' ),
				'description' => __( 'Optional description to display at checkout.', 'woocommerce-checkout-add-ons' ),
				'desc_tip'    => true,
				'value'       => $this->add_on ? $this->add_on->get_description( 'edit' ) : '',
			],
			'type'                   => [
				'id'      => 'type',
				'type'    => 'select',
				'style'   => 'width: 250px;',
				'label'   => __( 'Add-on Type', 'woocommerce-checkout-add-ons' ),
				'options' => Add_On_Factory::get_add_on_types(),
				'value'   => $this->add_on ? $this->add_on->get_type() : '',
			],
			'default_value'          => [
				'id'            => 'default_value',
				'type'          => 'text',
				'label'         => __( 'Default value', 'woocommerce-checkout-add-ons' ),
				'options_note'  => __( 'The default value for this field can be changed in the \'Options\' tab to the left.', 'woocommerce-checkout-add-ons' ),
				'description'   => __( 'Optional default value to pre-populate this field with.', 'woocommerce-checkout-add-ons' ),
				'placeholder'   => __( 'Enter a default value (optional)', 'woocommerce-checkout-add-ons' ),
				'desc_tip'      => true,
				'value'         => $this->add_on ? $this->add_on->get_default_value() : '',
				'exclude_types' => [ 'file', 'checkbox' ],
			],
			'default_checkbox_value' => [
				'id'           => 'default_checkbox_value',
				'type'         => 'checkbox',
				'label'        => __( 'Default value', 'woocommerce-checkout-add-ons' ),
				'description'  => __( 'Optional default value to pre-populate this field with.', 'woocommerce-checkout-add-ons' ),
				'value'        => $this->add_on ? $this->add_on->get_default_value() : false,
				'exclusive_to' => 'checkbox',
			],
			'price_adjustment'       => [
				'id'           => 'adjustment',
				'type'         => 'price_adjustment',
				'label'        => __( 'Price adjustment', 'woocommerce-checkout-add-ons' ),
				'options_note' => __( 'Price adjustments for this field can be made in the \'Options\' tab to the left.', 'woocommerce-checkout-add-ons' ),
				'description'  => __( 'Optional price adjustment for this add-on - can be fixed or percentage-based, and either positive (fee) or negative (discount)', 'woocommerce-checkout-add-ons' ),
				'desc_tip'     => true,
				'value'        => [
					'adjustment'      => $this->add_on ? $this->add_on->get_adjustment( 'edit' ) : 0.0,
					'adjustment_type' => $this->add_on ? $this->add_on->get_adjustment_type( 'edit' ) : 'fixed',
				],
			],
			'taxable'                => [
				'id'          => 'is_taxable',
				'type'        => 'checkbox',
				'label'       => __( 'Taxable?', 'woocommerce-checkout-add-ons' ),
				'description' => __( 'Enable to apply taxes to the add-on cost', 'woocommerce-checkout-add-ons' ),
				'value'       => $this->add_on && $this->add_on->get_is_taxable( 'edit' ) ? 'yes' : 'no',
			],
			'tax_class'              => [
				'id'      => 'tax_class',
				'type'    => 'select',
				'label'   => __( 'Tax Rate', 'woocommerce-checkout-add-ons' ),
				'options' => $this->get_tax_class_options(),
				'value'   => $this->add_on ? $this->add_on->get_tax_class( 'edit' ) : '',
			],
			'attributes'             => [
				'id'      => 'attributes',
				'type'    => 'multiselect',
				'label'   => __( 'Attributes', 'woocommerce-checkout-add-ons' ),
				'options' => Add_On::get_attribute_options(),
				'value'   => $this->add_on ? $this->add_on->get_attributes( 'edit' ) : [],
				'style'   => 'width: 250px;',
			],
			'options'                => [
				'id'                     => 'options',
				'type'                   => 'options_repeater',
				'label'                  => 'Options',
				'field_only'             => true,
				'value'                  => $this->add_on instanceof Add_On_With_Options ? $this->add_on->get_options( 'edit' ) : [],
				'adjustment_description' => __( 'Price adjustment for this option - can be fixed or percentage-based, and either positive (fee) or negative (discount)', 'woocommerce-checkout-add-ons' ),
			],
			'rules'                  => [
				'id'         => 'rules',
				'type'       => 'ruleset',
				'field_only' => true,
				'value'      => $this->add_on ? $this->add_on->get_ruleset() : self::get_empty_ruleset(),
			]
		];

		/**
		 * Filters the fields for the add-on data meta box.
		 *
		 * @since 2.0.0
		 *
		 * @param array set of fields to use for this meta box
		 * @param Add_On|null the add-on, if there is one
		 * @param Add_On_Data instance of this meta box
		 */
		return apply_filters( 'wc_checkout_add_ons_meta_box_add_on_fields', $fields, $this->add_on, $this );
	}


	/**
	 * Gets the default tabs for this meta box.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_panels() {

		$panels = [
			'general' => [
				'label'  => __( 'General', 'woocommerce-checkout-add-ons' ),
				'fields' => [
					'enabled',
					'label',
					'description',
					'type',
					'default_value',
					'default_checkbox_value',
					'price_adjustment',
					'taxable',
					'tax_class',
					'attributes'
				],
			],
			'options' => [
				'label'  => __( 'Options', 'woocommerce-checkout-add-ons' ),
				'fields' => [ 'options' ],
			],
			'rules'   => [
				'label'  => __( 'Display rules', 'woocommerce-checkout-add-ons' ),
				'fields' => [ 'rules' ],
			],
		];

		// don't show tax fields if taxes are disabled for the shop
		if ( 'yes' !== get_option( 'woocommerce_calc_taxes' ) ) {

			$panels['general']['fields'] = array_diff( $panels['general']['fields'], array( 'taxable', 'tax_class' ) );
		}

		/**
		 * Filters the panels for an add-on meta box.
		 *
		 * @since 2.0.0
		 *
		 * @param array panels to display in this meta box
		 * @param Add_On|null the current checkout add-on, if there is one
		 * @param Add_On_Data instance of this class
		 */
		return apply_filters( 'wc_checkout_add_ons_meta_box_add_on_panels', $panels, $this->add_on, $this );
	}


	/**
	 * Renders the tabs for the meta box.
	 *
	 * @since 2.0.0
	 *
	 * @param array $panels
	 */
	protected function render_tabs( $panels = array() ) {

		?>
		<ul class="checkout_add_on_data_tabs wc-tabs">

			<?php foreach ( $panels as $panel_key => $panel ) : ?>

				<li class="wc-tab <?php echo sanitize_html_class( $panel_key . '_tab' ); ?>">
					<a href="<?php echo esc_attr( '#wc-checkout-add-on-panel-' . $panel_key ); ?>">
						<span><?php echo esc_html( $panel['label'] ); ?></span>
					</a>
				</li>

			<?php endforeach; ?>

		</ul>
		<?php
	}


	/**
	 * Renders the panels for the meta box.
	 *
	 * @since 2.0.0
	 *
	 * @param array $panels
	 */
	protected function render_panels( $panels = array() ) {

		foreach ( $panels as $panel_key => $panel ) : ?>

			<div id="<?php echo sanitize_html_class( 'wc-checkout-add-on-panel-' . $panel_key ); ?>" class="panel woocommerce_options_panel">
				<?php array_map( array( $this, 'output_field' ), $panel['fields'] ); ?>
			</div>

		<?php endforeach;
	}


	/**
	 * Outputs a field.
	 *
	 * @since 2.0.0
	 *
	 * @param string|array|null $field field key or field data
	 */
	public function output_field( $field = null ) {

		$field = ! is_array( $field ) && isset( $this->fields[ $field ] ) ? $this->fields[ $field ] : null;

		if ( ! is_array( $field ) || ! isset( $field['type'] ) ) {
			return;
		}

		/**
		 * Filters the rendering function for a field.
		 *
		 * @since 2.0.0
		 *
		 * @param callable rendering function
		 * @param string field type
		 * @param array field data
		 * @param Add_On_Data instance of this class
		 */
		$render_function = apply_filters( 'wc_checkout_add_ons_get_field_renderer',
			array( $this, 'render_' . $field['type'] . '_field' ),
			$field['type'],
			$field,
			$this
		);

		$field_data = $this->get_field_data( $field );

		if ( is_callable( $render_function ) ) {

			if ( $field_data['field_only'] ) {

				$render_function( $field_data );

			} else {

				$this->render_field_wrapper( $field_data, $render_function );
			}
		}
	}


	/**
	 * Renders a field wrapper (containing field output).
	 *
	 * @since 2.0.0
	 *
	 * @param array $field_data field data, already initialized
	 * @param callable $field_renderer the function to render the field
	 */
	protected function render_field_wrapper( $field_data, callable $field_renderer ) {

		$exclude_classes = [];

		if ( isset( $field_data['exclusive_to'] ) && '' !== $field_data['exclusive_to'] ) {

			$exclude_classes[] = 'exclusive';
			$exclude_classes[] = 'exclusive_to_' . $field_data['exclusive_to'];

		} elseif ( isset( $field_data['exclude_types'] ) && is_array( $field_data['exclude_types'] ) ) {

			foreach ( $field_data['exclude_types'] as $type ) {

				$exclude_classes[] = 'exclude_' . $type;
			}
		}

		$wrapper_classes = array_merge(
			[
				'form-field',
				$field_data['id'] . '_field',
				$field_data['wrapper_class'],
			],
			$exclude_classes
		);

		?>

		<p class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">

			<label for="<?php echo esc_attr( $field_data['id'] ); ?>">
				<?php echo wp_kses_post( $field_data['label'] ); ?>
			</label>

			<?php if ( ! empty( $field_data['description'] ) && false !== $field_data['desc_tip'] ) : ?>

				<?php echo wc_help_tip( $field_data['description'] ); ?>

			<?php endif; ?>

			<?php if ( isset( $field_data['options_note'] ) && '' !== $field_data['options_note'] ) : ?>

				<span class="<?php echo esc_attr( $field_data['id'] ); ?>-options-note" style="display: none; font-style: italic;">
					<?php echo esc_html( $field_data['options_note'] ); ?>
				</span>

			<?php endif; ?>

			<span class="<?php echo esc_attr( $field_data['id'] ) . '-outer'; ?>">
				<?php $field_renderer( $field_data ); ?>
			</span>

			<?php if ( ! empty( $field_data['description'] ) && false === $field_data['desc_tip'] ) : ?>

				<span class="description">
					<?php echo wp_kses_post( $field_data['description'] ); ?>
				</span>

			<?php endif; ?>

		</p>

		<?php
	}


	/**
	 * Renders a checkbox field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field_data initialized field data
	 */
	public function render_checkbox_field( $field_data ) {

		$custom_attributes = $this->get_field_custom_attributes( $field_data );
		?>

		<input type="checkbox"
		       id="<?php echo esc_attr( $field_data['id'] ); ?>"
		       name="<?php echo esc_attr( $field_data['name'] ); ?>"
		       name="<?php echo esc_attr( $field_data['name'] ); ?>"
		       class="<?php echo esc_attr( $field_data['class'] ); ?>"
		       style="<?php echo esc_attr( $field_data['style'] ); ?>"
		       value="yes"
		       <?php checked( $field_data['value'], 'yes' ); ?>
		       <?php implode( ' ', $custom_attributes ); ?>
		/>

		<?php
	}


	/**
	 * Renders a select field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field_data initialized field data
	 */
	public function render_select_field( $field_data ) {

		$custom_attributes = $this->get_field_custom_attributes( $field_data );
		?>

		<select id="<?php echo esc_attr( $field_data['id'] ); ?>"
		        class="<?php echo esc_attr( $field_data['class'] ); ?> wc-enhanced-select"
		        name="<?php echo esc_attr( $field_data['name'] ); ?>"
		        style="<?php echo esc_attr( $field_data['style'] ); ?>"
		        <?php implode( ' ', $custom_attributes ); ?>
        >

			<?php foreach ( $field_data['options'] as $option_value => $option_label ) : ?>

				<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $option_value, $field_data['value'] ); ?>>
					<?php echo esc_html( $option_label ); ?>
				</option>

			<?php endforeach; ?>

		</select>

		<?php
	}


	/**
	 * Renders a text field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field_data initialized field data
	 */
	public function render_text_field( $field_data ) {

		$custom_attributes = $this->get_field_custom_attributes( $field_data );
		?>

		<input type="text"
		       id="<?php echo esc_attr( $field_data['id'] ); ?>"
		       class="<?php echo esc_attr( $field_data['class'] ); ?>"
		       name="<?php echo esc_attr( $field_data['name'] ); ?>"
		       style="<?php echo esc_attr( $field_data['style'] ); ?>"
		       value="<?php echo esc_attr( $field_data['value'] ); ?>"
		       placeholder="<?php echo esc_attr( $field_data['placeholder'] ); ?>"
		       <?php implode( ' ', $custom_attributes ); ?>
		/>

		<?php
	}


	/**
	 * Renders a textarea field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field_data initialized field data
	 */
	public function render_textarea_field( $field_data ) {

		$custom_attributes = $this->get_field_custom_attributes( $field_data );
		?>

		<textarea id="<?php echo esc_attr( $field_data['id'] ); ?>"
		          class="<?php echo esc_attr( $field_data['class'] ); ?>"
		          name="<?php echo esc_attr( $field_data['name'] ); ?>"
		          placeholder="<?php echo esc_attr( $field_data['placeholder'] ); ?>"
		          style="<?php echo esc_attr( $field_data['style'] ); ?>"
		          <?php implode( ' ', $custom_attributes ); ?>
		><?php echo wp_kses_post( $field_data['value'] ); ?></textarea>

		<?php
	}


	/**
	 * Renders a hidden field.
	 *
	 * @since 2.1.0
	 *
	 * @param array $field_data initialized field data
	 */
	public function render_hidden_field( $field_data ) {
		?>

		<input id="<?php echo esc_attr( $field_data['id'] ); ?>"
		       name="<?php echo esc_attr( $field_data['name'] ); ?>"
		       value="<?php echo esc_attr( $field_data['value'] ); ?>"
		       type="hidden"/>
		<?php
	}


	/**
	 * Renders a product search field.
	 *
	 * @since 2.1.0
	 *
	 * @param array $field_data initialized field data
	 */
	private function render_product_search_field( $field_data ) {

		$selected_options = [];
		if ( is_array( $field_data['value'] ) ) {

			foreach ( $field_data['value'] as $value ) {

				$product = wc_get_product( $value );
				if ( ! empty( $product ) ) {

					$title = $product->get_title();

					$selected_options[ esc_attr( $value ) ] = esc_html( $title );
				}
			}
		}

		?>

		<div id="product_ids_wrapper" style="display: inline-block;">

			<select
				name="<?php echo $field_data['name']; ?>"
				class="<?php echo $field_data['class']; ?>"
				style="width: 25em; <?php echo $field_data['style']; ?>"
				multiple="multiple"
				data-multiple="<?php echo $field_data['custom_attributes']['data-multiple']; ?>"
				data-placeholder="<?php echo $field_data['custom_attributes']['data-placeholder']; ?>"
				data-action="<?php echo $field_data['custom_attributes']['data-action']; ?>">
				<?php foreach ( $selected_options as $value => $title ) : ?>
					<option value="<?php echo $value; ?>" selected="selected"><?php echo $title; ?></option>
				<?php endforeach; ?>
			</select>

		</div>
		<?php
	}


	/**
	 * Renders price adjustment fields.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field_data initialized field data
	 */
	public function render_price_adjustment_field( $field_data ) {

		$adjustment_name         = isset( $field_data['name'] ) ? $field_data['name'] : $field_data['id'];
		$adjustment_class        = isset( $field_data['class'] ) ? $field_data['class'] : $adjustment_name . '_field';
		$adjustment_step         = isset( $field_data['step'] ) ? $field_data['step'] : pow( 10, -1 * wc_get_price_decimals() );
		$adjustment_value        = isset( $field_data['value']['adjustment'] ) ? (float) $field_data['value']['adjustment'] : 0.0;
		$adjustment_type_class   = isset( $field_data['type_class'] ) ? $field_data['type_class'] : $adjustment_class;
		$adjustment_type_name    = isset( $field_data['type_name'] ) ? $field_data['type_name'] : $adjustment_name . '_type';
		$adjustment_type_value   = isset( $field_data['value']['adjustment_type'] ) && 'percent' === $field_data['value']['adjustment_type'] ? 'percent' : 'fixed';
		$adjustment_type_options = '';
		$adjustment_types        = array(
			'fixed'   => get_woocommerce_currency_symbol(),
			'percent' => '%'
		);

		foreach ( $adjustment_types as $value => $label ) {

			$adjustment_type_options .= sprintf(
				'<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( $value ),
				selected( $adjustment_type_value === $value, true, false ),
				esc_html( $label )
			);
		}

		printf(
			'<input type="number" class="price-adjustment %1$s" name="%2$s" step="%3$s" value="%4$s"/><select class="price-adjustment-type %5$s" name="%6$s">%7$s</select>',
			esc_attr( $adjustment_class ),
			esc_attr( $adjustment_name ),
			esc_attr( $adjustment_step ),
			esc_attr( $adjustment_value ),
			esc_attr( $adjustment_type_class ),
			esc_attr( $adjustment_type_name ),
			$adjustment_type_options
		);
	}


	/**
	 * Gets the custom attributes value for a field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field field data
	 * @return array
	 */
	protected function get_field_custom_attributes( $field ) {

		$custom_attributes = array();

		if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

			foreach ( $field['custom_attributes'] as $attribute => $value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
			}
		}

		return $custom_attributes;
	}


	/**
	 * Renders a multiselect field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field_data initialized field data
	 */
	public function render_multiselect_field( $field_data ) {

		$field_data['class'] = '' === $field_data['class'] ? 'select short' : $field_data['class'];
		$field_data['name']  = '' === $field_data['name'] ? $field_data['id'] : $field_data['name'];
		$field_data['value'] = '' === $field_data['value'] ? array() : $field_data['value'];

		$custom_attributes = $this->get_field_custom_attributes( $field_data );

		?>
		<select id="<?php echo esc_attr( $field_data['id'] ); ?>"
		        name="<?php echo esc_attr( $field_data['name'] . '[]' ); ?>"
		        class="js-wc-checkout-add-on-attributes wc-enhanced-select <?php echo sanitize_html_class( $field_data['class'] ); ?>"
		        multiple="multiple"
		        style="<?php echo esc_attr( $field_data['style'] ); ?>"
		        <?php echo implode( ' ', $custom_attributes ); ?>
		>
			<?php foreach ( $field_data['options'] as $key => $value ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>"
				        <?php selected( in_array( $key, $field_data['value'] ) ); ?>
				>
					<?php echo esc_html( $value ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}


	/**
	 * Renders a repeater field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field field data
	 */
	public function render_options_repeater_field( $field ) {

		$field['value']                  = isset( $field['value'] ) ? $field['value'] : array();
		$field['adjustment_description'] = isset( $field['adjustment_description'] ) ? $field['adjustment_description'] : '';
		?>

		<div class="hide checkout-add-ons-no-options-allowed">
			<?php esc_html_e( 'This add-on type does not use an input that accepts options.', 'woocommerce-checkout-add-ons' ); ?>
		</div>

		<div class="table-wrap checkout-add-ons-options">
			<table class="widefat rules checkout-add-on-options-repeater striped js-rules">
				<thead>
					<tr>
						<td class="check-column" style="width: 5%;">
							<label class="screen-reader-text" for="checkout-add-on-option-select-all"> <?php esc_html_e( 'Select all', 'woocommerce-checkout-add-ons' ); ?></label>
							<input type="checkbox" id="checkout-add-on-option-select-all" />
						</td>
						<th scope="col" class="checkout-add-on-option-label" style="width: 50%;">
							<?php esc_html_e( 'Label', 'woocommerce-checkout-add-ons' ); ?>
						</th>
						<th scope="col" class="checkout-add-on-option-price-adjustment" style="width: 30%;">
							<?php esc_html_e( 'Price Adjustment', 'woocommerce-checkout-add-ons' ); ?>
							<?php echo wc_help_tip( $field['adjustment_description'] ); ?>
						</th>
						<th scope="col" class="checkout-add-on-option-default" style="width: 10%;">
							<?php esc_html_e( 'Default', 'woocommerce-checkout-add-ons' ); ?>
						</th>
						<th scope="col" class="checkout-add-on-option-reorder" style="width: 5%;">
							&nbsp;
						</th>
					</tr>
				</thead>
				<tbody id="checkout-add-ons-options">

					<tr class="hide checkout-add-ons-no-options">
						<td colspan="5">
							<?php esc_html_e( 'You don\'t have any options yet! Click the \'Add new option\' button below to get started.', 'woocommerce-checkout-add-ons' ); ?>
						</td>
					</tr>

					<?php

						// template row
						$this->render_option_row();

						foreach ( $field['value'] as $index => $option_value ) {

							$this->render_option_row( $index, $option_value );
						}
					?>

				</tbody>
				<tfoot>
					<tr>
						<th colspan="5">
							<button
								type="button"
								class="button button-primary add-option js-add-option">
								<?php esc_html_e( 'Add new option', 'woocommerce-checkout-add-ons' ); ?>
							</button>
							<button
								type="button"
								class="button button-secondary remove-options js-remove-options
							        <?php if ( count( $field['value'] ) < 2 ) : ?>hide<?php endif; ?>">
								<?php esc_html_e( 'Delete selected', 'woocommerce-checkout-add-ons' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>

		<?php
	}


	/**
	 * Renders an option repeater row.
	 *
	 * @since 2.0.0
	 *
	 * @param int|string $index (optional) the row identifier
	 * @param array $data (optional) the option data
	 *     @type bool $template whether this row is the template row or not
	 *     @type string $label the option label
	 *     @type float $adjustment the price adjustment
	 *     @type string $adjustment_type the adjustment type - `fixed` or `percent`
	 *     @type bool $default whether this option is set to default or not
	 */
	protected function render_option_row( $index = 'template', $data = array() ) {

		$label           = isset( $data['label'] ) ? $data['label'] : '';
		$default         = isset( $data['default'] ) ? (bool) $data['default'] : false;
		$adjustment_data = array(
			'class'     => 'checkout-add-on-option-field',
			'name'      => 'options[' . $index . '][adjustment]',
			'type_name' => 'options[' . $index . '][adjustment_type]',
			'value'     => array(
				'adjustment'      => isset( $data['adjustment'] ) ? $data['adjustment'] : '',
				'adjustment_type' => isset( $data['adjustment_type'] ) ? $data['adjustment_type'] : '',
			)
		)

		?>

		<tr class="checkout-add-on-option-row" id="<?php echo esc_attr( 'checkout-add-on-option--' . $index ); ?>">

			<th class="check-column">
				<input type="checkbox" />
			</th>

			<td class="checkout-add-on-option-label">
				<input type="text"
				       class="checkout-add-on-option-field"
				       name="<?php echo esc_attr( 'options[' . $index . '][label]' ); ?>"
				       value="<?php echo esc_attr( stripslashes( $label ) ); ?>"
				/>
			</td>

			<td class="checkout-add-on-option-price-adjustment">

				<?php $this->render_price_adjustment_field( $adjustment_data ); ?>

			</td>

			<td class="checkout-add-on-option-default">
				<input type="checkbox"
				       class="checkout-add-on-option-field multi-default-field"
				       name="<?php echo esc_attr( 'options[' . $index . '][multi_default]' ); ?>"
				       value="1"
				       <?php checked( $default ); ?>
				>

				<input type="radio"
				       class="checkout-add-on-option-field default-field"
				       name="default_option"
				       value="<?php echo esc_attr( $index ); ?>"
				       <?php checked( $default ); ?>
				/>
			</td>

			<td class="checkout-add-on-option-reorder">
				<img class="js-checkout-add-on-option-sort-handle"
				     src="<?php echo esc_url( wc_checkout_add_ons()->get_plugin_url() . '/assets/images/draggable-handle.png' ); ?>"
				     alt="draggable sorting handle"
				/>
			</td>

		</tr>

		<?php
	}


	/**
	 * Gets field data with filled-in defaults.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field raw field data
	 * @return array
	 */
	protected function get_field_data( $field ) {

		$field_data = wp_parse_args( $field, [
			'class'         => '',
			'desc_tip'      => false,
			'description'   => '',
			'field_only'    => false,
			'id'            => '',
			'label'         => '',
			'name'          => '',
			'options'       => [],
			'placeholder'   => '',
			'style'         => '',
			'value'         => '',
			'wrapper_class' => '',
		] );

		$field_data['name'] = '' === $field_data['name'] ? $field_data['id'] : $field_data['name'];

		return $field_data;
	}


	/**
	 * Gets an array of possible tax class options for a <select>.
	 *
	 * @since 2.0.0
	 *
	 * @return array tax_class_slug => tax_class_name
	 */
	protected function get_tax_class_options() {

		$options = array( 'standard' => __( 'Standard Rate', 'woocommerce-checkout-add-ons' ) );
		$keys    = \WC_Tax::get_tax_class_slugs();
		$values  = \WC_Tax::get_tax_classes();

		return array_merge( $options, array_combine( $keys, $values ) );
	}


	/**
	 * Renders a form field label.
	 *
	 * @since 2.1.0
	 *
	 * @param string $id the form field id
	 * @param string $label the label text
	 */
	protected function render_field_label( $id = '', $label = '' ) {

		if ( $id && $label ) {
			?>
			<label for="<?php echo esc_attr( $id ); ?>">
				<?php echo wp_kses_post( $label ); ?>
			</label>
			<?php
		}
	}


	/**
	 * Gets rule fields with filled-in defaults.
	 *
	 * @since 2.1.0
	 *
	 * @param array $field raw field data
	 * @return array
	 */
	protected function get_rule_field_data( $field ) {

		return wp_parse_args( $field, [
			'id'          => '',
			'name'        => '',
			'type'        => '',
			'value'       => '',
			'class'       => '',
			'label'       => '',
			'placeholder' => '',
			'style'       => '',
			'text_before' => '',
			'text_after'  => '',
			'tooltip'     => '',
		] );
	}


	/**
	 * Renders ruleset field.
	 *
	 * @since 2.1.0
	 *
	 * @param array $field field data
	 */
	public function render_ruleset_field( $field ) {

		$ruleset = isset( $field['value'] ) ? $field['value'] : [];
		?>

		<p class="checkout-add-ons-rules-instructions">
			<?php
				printf(
					/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag */
					__( 'Add values below to restrict the display of this add-on unless it meets %1$sall rules%2$s. Leave this section blank to %1$salways show%2$s this add-on.', 'woocommerce-checkout-add-ons' ),
					'<strong>',
					'</strong>'
				);
			?>
		</p>

		<div class="table-wrap checkout-add-ons-rules">
			<table class="widefat rules checkout-add-on-rules-sets striped js-rules">
				<thead>
					<tr>
						<th scope="col" class="checkout-rules-property" style="width: 33%;">
							<?php esc_html_e( 'Property', 'woocommerce-checkout-add-ons' ); ?>
						</th>
						<th scope="col" class="checkout-rules-evaluate">
							<?php esc_html_e( 'Value', 'woocommerce-checkout-add-ons' ); ?>
						</th>
						<th scope="col" class="checkout-rules-tooltip">
							&nbsp;
						</th>
					</tr>
				</thead>
				<tbody id="checkout-add-ons-rules">

					<?php
					foreach ( $ruleset as $rule ) {

						$this->render_rule_row( $rule );
					}
					?>

				</tbody>
			</table>
		</div>

		<?php
	}


	/**
	 * Renders a rule set table row.
	 *
	 * @since 2.1.0
	 *
	 * @param Display_Rule $rule the rule
	 */
	protected function render_rule_row( $rule ) {

		$type = $rule->get_type();
		$id   = 'checkout-add-on-rule-' . str_replace( '_', '-', $type );

		$tooltip = $rule->get_tooltip();

		?>
		<tr class="checkout-add-on-rule-row" id="<?php echo esc_attr( $id ); ?>">
			<td class="checkout-add-on-rule-property">
				<?php $this->render_rule_property( $rule ); ?>
			</td>
			<td class="checkout-add-on-rule-evaluate checkout-add-on-rule-<?php echo $rule->get_type() ?>">
				<?php $this->render_rule_logic( $rule ); ?>
			</td>
			<td class="checkout-add-on-rule-tooltip">
				<?php echo $tooltip ? wc_help_tip( $tooltip ) : '&nbsp;'; ?>
			</td>
		</tr>

		<?php
	}


	/**
	 * Renders contents of table cell for rule property.
	 *
	 * @since 2.1.0
	 *
	 * @param Display_Rule $rule the rule
	 */
	protected function render_rule_property( $rule ) {

		$property_field = $rule->get_property_field();

		if ( empty( $property_field ) ) {

			echo '<span>' . wp_kses_post( $rule->get_property() ) . '</span>';

		} else {

			$property_field = $this->get_rule_field_data( $property_field );
			$this->render_rule_field( $property_field );
		}
	}


	/**
	 * Renders contents of table cell for rule logic.
	 *
	 * @since 2.1.0
	 *
	 * @param Display_Rule $rule the rule
	 */
	public function render_rule_logic( $rule ) {

		$rule_fields = $rule->get_fields();

		foreach ( $rule_fields as $key => $rule_field ) {

			$rule_field = $this->get_rule_field_data( $rule_field );
			$this->render_rule_field( $rule_field );
		}
	}


	/**
	 * Renders a single field for rule logic table cell.
	 *
	 * @since 2.1.0
	 *
	 * @param array $rule_field the field data
	 */
	protected function render_rule_field( $rule_field = [] ) {

		if ( $rule_field['text_before'] ) {

			echo '<span>' . wp_kses_post( $rule_field['text_before'] ) . '</span>';
		}

		switch ( $rule_field['type'] ) {

			case 'text':
				$this->render_field_label( $rule_field['id'], $rule_field['label'] );
				$this->render_text_field( $rule_field );
			break;

			case 'select':
				$this->render_field_label( $rule_field['id'], $rule_field['label'] );
				$this->render_select_field( $rule_field );
			break;

			case 'multiselect':
				echo '<div id="' . $rule_field['id'] . '_wrapper">';
				$this->render_field_label( $rule_field['id'], $rule_field['label'] );
				$this->render_multiselect_field( $rule_field );
				echo '</div>';
			break;

			case 'hidden':
				$this->render_hidden_field( $rule_field );
			break;

			case 'product_search':
				$this->render_product_search_field( $rule_field );
			break;
		}

		if ( $rule_field['text_after'] ) {

			echo '<span>' . wp_kses_post( $rule_field['text_after'] ) . '</span>';
		}
	}


	/**
	 * Gets an empty ruleset for brand new add-ons.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	private static function get_empty_ruleset() {

		$ruleset = [];
		foreach ( array_keys( Display_Rule_Factory::get_display_rule_classnames() ) as $rule_type ) {

			$ruleset[ $rule_type ] = Display_Rule_Factory::create_display_rule( $rule_type );
		}

		return $ruleset;
	}


}
