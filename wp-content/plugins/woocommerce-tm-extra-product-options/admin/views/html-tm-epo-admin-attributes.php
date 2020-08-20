<?php
/**
 * View for displaying the attributes select box
 *
 * Variables used:
 * @required   $parent_data
 * @required   $loop
 *
 * @package Extra Product Options/Admin/Views
 * @version 4.8.5
 */

defined( 'ABSPATH' ) || exit;

if ( isset( $parent_data ) && isset( $tmcp_attribute_selected_value ) && isset( $loop ) ) {
	foreach ( $parent_data['attributes'] as $attribute ) {
		// Get only attributes that are not variations
		if ( $attribute['is_variation'] || sanitize_title( $attribute['name'] ) != $tmcp_attribute_selected_value ) {
			continue;
		} ?>
        <select data-tm-attr="<?php echo esc_attr( sanitize_title( $attribute['name'] ) ); ?>" class="tmcp_att tmcp_attribute_<?php echo esc_attr( sanitize_title( $attribute['name'] ) ); ?>" name="attribute_<?php echo esc_attr( sanitize_title( $attribute['name'] ) ); ?>[<?php echo esc_attr( $loop ); ?>]">
            <option value="0"><?php esc_html_e( 'Any', 'woocommerce-tm-extra-product-options' ); ?>
				<?php echo esc_html( wc_attribute_label( $attribute['name'] ) ); ?>&hellip;
            </option>
			<?php
			// Get terms for attribute taxonomy or value if its a custom attribute
			if ( $attribute['is_taxonomy'] ) {
				$all_terms = get_terms( $attribute['name'], 'orderby=name&hide_empty=0' );
				if ( $all_terms ) {
					foreach ( $all_terms as $term ) {
						$has_term = has_term( (int) $term->term_id, $attribute['name'], $parent_data['id'] ) ? 1 : 0;
						if ( $has_term ) { ?>
                            <option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( apply_filters( 'woocommerce_tm_epo_option_name', $term->name, NULL, NULL ) ); ?></option>
							<?php
						}
					}
				}
			} else {
				$options = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
				foreach ( $options as $option ) { ?>
                    <option value="<?php echo esc_attr( sanitize_title( $option ) ); ?>"><?php echo esc_html( apply_filters( 'woocommerce_tm_epo_option_name', $option, NULL, NULL ) ); ?></option>
					<?php
				}
			} ?>
        </select>
		<?php
	}
}