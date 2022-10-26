<?php
/**
 * Admin Functions
 *
 * Functions for the WooCommerce Mix and Match admin metaboxes.
 *
 * @package  WooCommerce Mix and Match Products/Admin/Functions
 * @since    2.2.0
 * @version  2.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Utilities\OrderUtil;

/*--------------------------------------------------------*/
/*  Mix and Match admin functions     */
/*--------------------------------------------------------*/

/**
 * Output a radio image input box.
 *
 * @param array   $field Field data.
 * @param WC_Data $data WC_Data object, will be preferred over post object when passed.
 */
function wc_mnm_wp_radio_images( $field, WC_Data $data = null ) {
	global $post;

	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = $field['value'] ?? OrderUtil::get_post_or_object_meta( $post, $data, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;

	echo '<fieldset class="form-field wc_mnm_radio_images ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><legend>' . wp_kses_post( $field['label'] ) . '</legend>';

	if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
		echo wc_help_tip( $field['description'] );
	}

	echo '<ul class="wc_mnm_radio_image_options">';

	foreach ( $field['options'] as $key => $option ) {

		$use_icon = isset( $option[ 'mb_display' ] ) && $option[ 'mb_display' ];
		$image    = $use_icon ? '<img src = "' . esc_url( $option['image'] ) .'" alt = "' . sprintf( esc_html__( 'Icon for %s', 'woocommerce-mix-and-match-products' ), ! empty( $option['label'] ) ? $option['label']: $option ) .'" />': '';
		$tip      = ! empty( $option[ 'description' ] ) ? wc_help_tip( $option[ 'description' ] ) : '';

		$radio_attributes = array( 
			'id'    => $field['id'] . '_' . $key,
			'name'  => $field['name'],
			'value' => $key,
			'type'  => 'radio',
			'class' => $field['class'] . " {$key}",
			'style' => $field['style'],
		);

		echo '<li class="wc_mnm_radio_image_option ' . esc_attr( $key ) . '" >
				<input ' . wc_implode_html_attributes( $radio_attributes ) . ' ' . checked( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '/>
				<label for="' . esc_attr( $radio_attributes['id'] ) . '" class="' . esc_attr( $use_icon ? 'has_svg_icon' : 'has_font_icon' ) . '">' . $image . '<span>' . esc_html( ! empty( $option['label'] ) ? $option['label'] : $option ) . '</span></label>' . $tip .
			'</li>';
	}

	echo '</ul>';

	if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	echo '</fieldset>';
}


/**
 * Output a toggle checkbox input box.
 * Wrapper for woocommerce_wp_checkbox()
 *
 * @param array   $field Field data.
 * @param WC_Data $data WC_Data object, will be preferred over post object when passed.
 */
function wc_mnm_wp_toggle( $field, WC_Data $data = null ) {

	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['wrapper_class'] .= ' wc_mnm_toggle';

	$field['description'] = '<label for="' . $field['id' ]. '"></label>';

	woocommerce_wp_checkbox( $field, $data );
}

/**
 * Output a enhanced select input box. Defaults to a product search, very specific to the plugin and not a generic function for any kind of enhanced search.
 * WC_Data object is not used here since the "value" param must be formatted a specific way.
 *
 * @param array   $field Field data.
 */
function wc_mnm_wp_enhanced_select( $field ) {
	global $post;

	$field = wp_parse_args(
		$field, array(
			'class'             => 'wc-product-search wc-mnm-enhanced-select',
			'style'             => '',
			'wrapper_class'     => 'form-field',
			'value'             => array(), // REQUIRED. Format is [ [ id=>label] ].
			'name'              => $field['id'],
			'desc_tip'          => false,
			'custom_attributes' => array(),
		)
	);

	$wrapper_attributes = array(
		'class' => $field['wrapper_class'] . " form-field {$field['id']}_field",
	);

	$label_attributes = array(
		'for' => $field['id'],
	);

	$field_attributes          = (array) $field['custom_attributes'];
	$field_attributes['style'] = $field['style'];
	$field_attributes['id']    = $field['id'];
	$field_attributes['name']  = $field['name'];
	$field_attributes['class'] = $field['class'];

	$tooltip     = ! empty( $field['description'] ) && false !== $field['desc_tip'] ? $field['description'] : '';
	$description = ! empty( $field['description'] ) && false === $field['desc_tip'] ? $field['description'] : '';
	?>
	<p <?php echo wc_implode_html_attributes( $wrapper_attributes ); // WPCS: XSS ok. ?>>
		<label <?php echo wc_implode_html_attributes( $label_attributes ); // WPCS: XSS ok. ?>><?php echo wp_kses_post( $field['label'] ); ?></label>
		<?php if ( $tooltip ) : ?>
			<?php echo wc_help_tip( $tooltip ); // WPCS: XSS ok. ?>
		<?php endif; ?>
		<select <?php echo wc_implode_html_attributes( $field_attributes ); // WPCS: XSS ok. ?>>
			<?php
			foreach ( $field['value'] as $key => $value ) {
				echo '<option value="' . esc_attr( $key ) . '"' . selected( true, true, false ) . '>' . esc_html( $value ) . '</option>';
			}
			?>
		</select>
		<?php if ( $description ) : ?>
			<span class="description"><?php echo wp_kses_post( $description ); ?></span>
		<?php endif; ?>
	</p>
	<?php
}
