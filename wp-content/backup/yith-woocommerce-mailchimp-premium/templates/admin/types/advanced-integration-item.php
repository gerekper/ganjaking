<?php
/**
 * Advanced integration item
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly
?>

<div class="advanced-panel-item opened" id="item_<?php echo esc_attr( $item_id ); ?>" data-id="<?php echo esc_attr( $item_id ); ?>">
	<div class="panel-item-handle">
		<a href="#" class="collapse-button"><?php esc_html_e( 'toggle', 'yith-woocommerce-mailchimp' ); ?></a>
		<a href="#" class="remove-button"><?php esc_html_e( 'remove', 'yith-woocommerce-mailchimp' ); ?></a>
		<h3>
			<?php
			// translators: 1. Item id.
			echo esc_html( sprintf( __( 'Options Set #%d', 'yith-woocommerce-mailchimp' ), $item_id ) );
			?>
		</h3>
	</div>

	<div class="panel-item-content">
		<div class="section">
			<h4><?php esc_html_e( 'Lists & Groups', 'yith-woocommerce-mailchimp' ); ?></h4>
			<table class="form-table" >
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="yith_wcmc_advanced_integration[<?php echo esc_attr( $item_id ); ?>][list]"><?php esc_html_e( 'MailChimp list', 'yith-woocommerce-mailchimp' ); ?></label>
					</th>
					<td>
						<select name="yith_wcmc_advanced_integration[<?php echo esc_attr( $item_id ); ?>][list]" id="yith_wcmc_advanced_integration_<?php echo esc_attr( $item_id ); ?>_list" class="list-select" style="min-width: 300px;">
							<?php if ( ! empty( $lists ) ) : ?>
								<?php foreach ( $lists as $list_id => $list_name ) : ?>
									<option value="<?php echo esc_attr( $list_id ); ?>" <?php selected( $list_id, $selected_list ); ?>><?php echo esc_html( $list_name ); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
						<a href="#" class="button button-secondary ajax-mailchimp-updater-list"><?php esc_html_e( 'Update Lists', 'yith-woocommerce-mailchimp' ); ?></a>
						<span class="description"><?php esc_html_e( 'Select a list for the new user', 'yith-woocommerce-mailchimp' ); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="yith_wcmc_advanced_integration_<?php esc_attr( $item_id ); ?>_groups"><?php esc_html_e( 'Interest groups', 'yith-woocommerce-mailchimp' ); ?></label>
					</th>
					<td>
						<select multiple="multiple" name="yith_wcmc_advanced_integration[<?php echo esc_attr( $item_id ); ?>][groups][]" id="yith_wcmc_advanced_integration_<?php echo esc_attr( $item_id ); ?>_groups" class="chosen_select" style="width: 300px;">
							<?php if ( ! empty( $groups ) ) : ?>
								<?php foreach ( $groups as $group_id => $group_name ) : ?>
									<option value="<?php echo esc_attr( $group_id ); ?>" <?php selected( in_array( $group_id, $selected_groups ) ); ?>><?php echo esc_html( $group_name ); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
						<a href="#" class="button button-secondary ajax-mailchimp-updater-group"><?php esc_html_e( 'Update Groups', 'yith-woocommerce-mailchimp' ); ?></a>
						<span class="description"><?php esc_html_e( 'Select an interest group for the user', 'yith-woocommerce-mailchimp' ); ?></span>
					</td>
				</tr>
			</table>
		</div>

		<div class="section">
			<h4><?php esc_html_e( 'Fields', 'yith-woocommerce-mailchimp' ); ?></h4>
			<div class="fields-header">
				<a href="#" class="button button-secondary add-field"><?php esc_html_e( '+ Add New Field', 'yith-woocommerce-mailchimp' ); ?></a>
			</div>

			<p class="description"><?php esc_html_e( 'Select the checkout field to connect with the MailChimp list merge var', 'yith-woocommerce-mailchimp' ); ?></p>

			<div class="fields-content">
				<?php
				if ( ! empty( $fields ) ) {
					$counter = 1;
					foreach ( $fields as $field ) {
						$args = array(
							'item_id' => $item_id,
							'field_id' => $counter,
							'selected_list' => isset( $selected_list ) ? $selected_list : '',
							'selected_checkout' => isset( $field['checkout'] ) ? $field['checkout'] : '',
							'selected_merge_var' => isset( $field['merge_var'] ) ? $field['merge_var'] : '',
						);
						YITH_WCMC_Admin_Premium()->print_advanced_integration_field( $args );
						$counter ++;
					}
				}
				?>
			</div>
		</div>

		<div class="section">
			<h4><?php esc_html_e( 'Conditions', 'yith-woocommerce-mailchimp' ); ?></h4>
			<div class="conditions-header">
				<a href="#" class="button button-secondary add-condition"><?php esc_html_e( '+ Add New Condition', 'yith-woocommerce-mailchimp' ); ?></a>
			</div>
			<p class="description"><?php esc_html_e( 'Select order matching conditions for user\'s subscription; all conditions selected must be matched in order to complete subscription', 'yith-woocommerce-mailchimp' ); ?></p>
			<div class="conditions-content">
				<?php
				if ( ! empty( $conditions ) ) {
					$counter = 1;
					foreach ( $conditions as $condition ) {
						$args = array(
							'item_id' => $item_id,
							'condition_id' => $counter,
							'condition' => $condition['condition'],
							'op_set' => $condition['op_set'],
							'op_number' => $condition['op_number'],
							'products' => isset( $condition['products'] ) ? $condition['products'] : array(),
							'prod_cats' => isset( $condition['prod_cats'] ) ? $condition['prod_cats'] : array(),
							'order_total' => $condition['order_total'],
							'custom_key' => $condition['custom_key'],
							'op_mixed' => $condition['op_mixed'],
							'custom_value' => $condition['custom_value'],
						);
						YITH_WCMC_Admin_Premium()->print_advanced_integration_condition( $args );
						$counter ++;
					}
				}
				?>
			</div>
		</div>
	</div>
</div>
