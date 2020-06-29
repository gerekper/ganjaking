<?php
/**
 * Special offer discount field.
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 *
 * @var array $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
extract( $args );
$db_value              = get_post_meta( $post->ID, $id, true );
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
			<table class="special-offers-rules">
				<tr>
					<th width="18%"><?php esc_html_e( 'Purchase', 'ywdpd' ); ?></th>
					<th width="18%"><?php esc_html_e( 'Receive', 'ywdpd' ); ?></th>
					<th width="26%"><?php esc_html_e( 'Type of Discount', 'ywdpd' ); ?></th>
					<th width="18%"><?php esc_html_e( 'Discount Amount', 'ywdpd' ); ?></th>
					<th width="10%"><?php esc_html_e( 'Repeat', 'ywdpd' ); ?></th>
				</tr>
				<tr>
					<td>
						<input type="text"
							name="<?php echo esc_attr( $name ) . '[purchase]'; ?>"
							id="<?php echo esc_attr( $id ) . '[purchase]'; ?>"
							value="<?php echo isset( $db_value['purchase'] ) ? esc_attr( $db_value['purchase'] ) : ''; ?>"
							placeholder="<?php esc_attr_e( 'e.g. 5', 'ywdpd' ); ?>">
					</td>
					<td>
						<input type="text"
							name="<?php echo esc_attr( $name ) . '[receive]'; ?>"
							id="<?php echo esc_attr( $id ) . '[receive]'; ?>"
							value="<?php echo isset( $db_value['receive'] ) ? esc_attr( $db_value['receive'] ) : ''; ?>"
							placeholder="<?php esc_attr_e( 'e.g. 10 - * for unlimited items', 'ywdpd' ); ?>">
					</td>
					<td>
						<select name="<?php echo esc_attr( $name ) . '[type_discount]'; ?>"
							id="<?php echo esc_attr( $id ) . '[type_discount]'; ?>" class="wc-enhanced-select">
							<?php foreach ( $pricing_rules_options['type_of_discount'] as $key_type => $type ) : ?>
								<option
									value="<?php echo esc_attr( $key_type ); ?>"
									<?php
									if ( isset( $db_value['type_discount'] ) ) {
										selected( $db_value['type_discount'], $key_type );
									}
									?>
								><?php echo esc_html( $type ); ?></option>
							<?php endforeach ?>
						</select>
					</td>
					<td>
						<input type="text"
							name="<?php echo esc_attr( $name ) . '[discount_amount]'; ?>"
							id="<?php echo esc_attr( $id ) . '[discount_amount]'; ?>"
							value="<?php echo isset( $db_value['discount_amount'] ) ? esc_attr( $db_value['discount_amount'] ) : ''; ?>"
							placeholder="<?php esc_attr_e( 'e.g. 50', 'ywdpd' ); ?>">
					</td>
					<td>
						<input type="checkbox"
							name="<?php echo esc_attr( $name ) . '[repeat]'; ?>"
							id="<?php echo esc_attr( $id ) . '[repeat]'; ?>"
							value="1" <?php echo ( isset( $db_value['repeat'] ) && $db_value['repeat'] == 1 ) ? 'checked' : ''; //phpcs:ignore ?> />
					</td>
				</tr>
			</table>

		</div>

	</div>
