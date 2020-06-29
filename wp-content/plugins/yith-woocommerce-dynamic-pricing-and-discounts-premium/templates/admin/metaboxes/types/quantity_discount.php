<?php
/**
 * Quantity discount field.
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 *
 * @var array  $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

extract( $args );
$db_value = get_post_meta( $post->ID, $id, true );

$limit                 = empty( $db_value ) ? 1 : count( $db_value );
$pricing_rules_options = YITH_WC_Dynamic_Pricing()->pricing_rules_options;

?>

<?php if ( function_exists( 'yith_field_deps_data' ) ) : ?>
<div id="<?php echo esc_attr( $id ); ?>-container" <?php echo yith_field_deps_data( $args ); //phpcs:ignore ?>
	class="yith-plugin-fw-metabox-field-row">
	<?php else : ?>
	<div id="<?php echo esc_attr( $id ); ?>-container"
		<?php
		if ( isset( $deps ) ) :
			?>
			data-field="<?php echo esc_attr( $id ); ?>" data-dep="<?php echo esc_attr( $deps['ids'] ); ?>" data-value="<?php echo esc_attr( $deps['values'] ); ?>" <?php endif ?>>
		<?php endif; ?>
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>

		<div class="discount-table-rules-wrapper">
			<table class="discount-rules">
				<tr>
					<th><?php esc_html_e( 'Minimum Quantity', 'ywdpd' ); ?></th>
					<th><?php esc_html_e( 'Maximum Quantity', 'ywdpd' ); ?></th>
					<th><?php esc_html_e( 'Type of Discount', 'ywdpd' ); ?></th>
					<th><?php esc_html_e( 'Discount Amount', 'ywdpd' ); ?></th>
					<th></th>
				</tr>
				<?php

				for ( $i = 1; $i <= $limit; $i++ ) :
					$hide_first_remove = ( 1 === $i ) ? ' hide-remove' : '';
					if ( isset( $db_value[ $i ] ) ) :
						?>
						<tr data-index="<?php echo esc_attr( $i ); ?>">
							<td>
								<input type="text"
									name="<?php echo esc_attr( $name ) . '[' . esc_attr( $i ) . '][min_quantity]'; ?>"
									id="<?php echo esc_attr( $id ) . '[' . esc_attr( $i ) . '][min_quantity]'; ?>"
									value="<?php echo isset( $db_value[ $i ]['min_quantity'] ) ? esc_attr( $db_value[ $i ]['min_quantity'] ) : ''; ?>"
									placeholder="<?php esc_attr_e( 'e.g. 5', 'ywdpd' ); ?>">
							</td>
							<td>
								<input type="text"
									name="<?php echo esc_attr( $name ) . '[' . esc_attr( $i ) . '][max_quantity]'; ?>"
									id="<?php echo esc_attr( $id ) . '[' . esc_attr( $i ) . '][max_quantity]'; ?>"
									value="<?php echo isset( $db_value[ $i ]['max_quantity'] ) ? esc_attr( $db_value[ $i ]['max_quantity'] ) : ''; ?>"
									placeholder="<?php esc_attr_e( 'e.g. 10 - * for unlimited items', 'ywdpd' ); ?>">
							</td>
							<td>
								<select
									name="<?php echo esc_attr( $name ) . '[' . esc_attr( $i ) . '][type_discount]'; ?>"
									id="<?php echo esc_attr( $id ) . '[' . esc_attr( $i ) . '][type_discount]'; ?>"
									class="wc-enhanced-select"
								>
									<?php
									foreach ( $pricing_rules_options['type_of_discount'] as $key_type => $type ) :
										$type_discount_value = isset( $db_value[ $i ]['type_discount'] ) ? $db_value[ $i ]['type_discount'] : '';
										?>
										<option
											value="<?php echo esc_attr( $key_type ); ?>" <?php selected( $type_discount_value, $key_type ); ?>><?php echo esc_html( $type ); ?></option>
									<?php endforeach ?>
								</select>
							</td>
							<td>
								<input type="text"
									name="<?php echo esc_attr( $name ) . '[' . esc_attr( $i ) . '][discount_amount]'; ?>"
									id="<?php echo esc_attr( $id ) . '[' . esc_attr( $i ) . '][discount_amount]'; ?>"
									value="<?php echo isset( $db_value[ $i ]['discount_amount'] ) ? esc_attr( $db_value[ $i ]['discount_amount'] ) : ''; ?>"
									placeholder="<?php esc_attr_e( 'e.g. 50', 'ywdpd' ); ?>">
							</td>
							<td>
								<span class="add-row yith-icon-plus"></span><span
									class="remove-row yith-icon-trash <?php echo esc_attr( $hide_first_remove ); ?>"></span>
							</td>
						</tr>
					<?php
					else :
						?>
						<tr data-index="1">
							<td>
								<input type="text" name="<?php echo esc_attr( $name ) . '[1][min_quantity]'; ?>"
									id="<?php echo esc_attr( $id ) . '[1][min_quantity]'; ?>" value=""
									placeholder="<?php esc_attr_e( 'e.g. 5', 'ywdpd' ); ?>">
							</td>
							<td>
								<input type="text" name="<?php echo esc_attr( $name ) . '[1][max_quantity]'; ?>"
									id="<?php echo esc_attr( $id ) . '[1][max_quantity]'; ?>" value=""
									placeholder="<?php esc_attr_e( 'e.g. 10 - * for unlimited items', 'ywdpd' ); ?>">
							</td>
							<td>
								<select name="<?php echo esc_attr( $name ) . '[1][type_discount]'; ?>"
									id="<?php echo esc_attr( $id ) . '[1][type_discount]'; ?>"
									class="wc-enhanced-select">
									<?php foreach ( $pricing_rules_options['type_of_discount'] as $key => $type ) : ?>
										<option
											value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $type ); ?></option>
									<?php endforeach ?>
								</select>
							</td>
							<td>
								<input type="text" name="<?php echo esc_attr( $name ) . '[1][discount_amount]'; ?>"
									id="<?php echo esc_attr( $id ) . '[1][discount_amount]'; ?>" value=""
									placeholder="<?php esc_attr_e( 'e.g. 50', 'ywdpd' ); ?>">
							</td>
							<td><span class="add-row yith-icon-plus"></span><span
									class="remove-row hide-remove yith-icon-trash"></span></td>
						</tr>
					<?php
					endif;
				endfor;
				?>
			</table>

		</div>

	</div>
