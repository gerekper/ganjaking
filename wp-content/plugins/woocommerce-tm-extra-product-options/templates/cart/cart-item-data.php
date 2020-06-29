<?php
/**
 * Cart item data (when outputting non-flat)
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/cart/cart-item-data.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see           https://docs.woocommerce.com/document/template-structure/
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       2.4.0
 *
 * Modified for Extra Product Options
 */

defined( 'ABSPATH' ) || exit;

$separator = THEMECOMPLETE_EPO()->tm_epo_separator_cart_text;
?>
<dl class="tc-epo-metadata variation">
	<?php foreach ( $item_data as $data ) :
		
		$is_epo = FALSE;
		$show_dt = TRUE;
		$show_dd = TRUE;
		$class_name = '';
		$class_value = '';
		if ( isset( $data['tm_label'] ) ) {
			$is_epo      = TRUE;
			$class_name  = 'tc-name ';
			$class_value = 'tc-value ';
		}

		if ( ! isset( $data['display'] ) && isset( $data['value'] ) ) {
			$data['display'] = $data['value'];
		}
		if ( $is_epo && $data['key'] === '' ) {
			$show_dt = FALSE;
		}
		if ( $is_epo && $data['display'] === '' ) {
			$show_dd = FALSE;
		}
		if ( (THEMECOMPLETE_EPO()->tm_epo_cart_field_display == "link" && isset($data['popuplink'])) || ! $show_dd ) {
			$separator = '';
		}

		// $data['key'] and $data['display'] contians HTML code
		// thus the use of wp_kses_post and not esc_html		
		?>
		<?php if ( $show_dt ): ?>
        <dt class="<?php echo esc_attr( sanitize_html_class( $class_name ) ); ?> variation-<?php echo esc_attr( sanitize_html_class( $data['key'] ) ); ?>"><?php echo apply_filters( 'wc_epo_kses', wp_kses_post( $data['key'] ), $data['key'], FALSE ); ?><?php echo esc_html( $separator ); ?></dt>
	<?php else: ?>
        <dt class="<?php echo esc_attr( sanitize_html_class( $class_name ) ); ?> tc-hidden-variation">&nbsp;</dt>
	<?php endif; ?>
		<?php if ( $show_dd ): ?>
        <dd class="<?php echo esc_attr( sanitize_html_class( $class_value ) ); ?> variation-<?php echo esc_attr( sanitize_html_class( $data['key'] ) ); ?>"><?php echo apply_filters( 'wc_epo_kses', wp_kses_post( wpautop( $data['display'] ) ), wpautop( $data['display'] ), FALSE ); ?></dd>
	<?php else: ?>
        <dd class="<?php echo esc_attr( sanitize_html_class( $class_value ) ); ?> tc-hidden-variation">&nbsp;</dd>
	<?php endif; ?>
	<?php endforeach; ?>
</dl>
