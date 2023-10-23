<?php
/**
 * Edit rule page
 *
 * @var YITH_WCBM_Badge_Rule $rule Rule.
 *
 * @package YITH\BadgeManagementPremium\Views
 */

$rules_types = yith_wcbm_get_badge_rules_types();
$fields      = array();
$rule_type   = sanitize_text_field( wp_unslash( ( $rule ? $rule->get_type() : null ) ?? $_GET['badge_rule_type'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( array_key_exists( $rule_type, $rules_types ) ) {
	$fields = $rules_types[ $rule_type ]['fields'];
} else {
	$fields = current( $rules_types )['fields'];
}

?>

<div id="yith_wcbm_panel_rules" class="yith-plugin-fw  yit-admin-panel-container">
	<div class="yit-admin-panel-content-wrap">
		<form id="plugin-fw-wc" method="post" action="admin.php?action=yith_wcbm_save_rule<?php echo $rule ? '&rule_id=' . absint( $rule->get_id() ) : ''; ?><?php echo '&yith_wcbm_security=' . esc_attr( wp_create_nonce( 'yith_wcbm_save_badge_rule' ) ); ?>">
			<span class="yith-plugin-fw__back-to-wp-list">
				<a href="<?php echo esc_url_raw( yith_wcbm_get_panel_url( 'badge-rules' ) ); ?>">
					<?php echo esc_html_x( '< back to rules list', '[Admin] Back link in edit rule page', 'yith-woocommerce-badges-management' ); ?>
				</a>
			</span>
			<h2>
				<?php
				if ( $rule ) {
					echo esc_html_x( 'Edit rule', '[ADMIN] Title for rule editing page', 'yith-woocommerce-badges-management' );
				} else {
					echo esc_html_x( 'Add rule', '[ADMIN] Title for new rule page', 'yith-woocommerce-badges-management' );
				}
				?>
			</h2>

			<table class="form-table">
				<tbody>
				<?php foreach ( $fields as $field ) : ?>
					<tr class="yith-plugin-fw-panel-wc-row onoff ">
						<th scope="row" class="titledesc"><label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label></th>
						<td id="<?php echo esc_attr( $field['id'] ) . '-container'; ?>" class="forminp" <?php echo esc_html( yith_field_deps_data( $field ) ); ?>>
							<?php yith_plugin_fw_get_field( $field, true ); ?>
							<span class="description">
								<?php echo( $field['desc'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>


			<p class="submit">
				<input type="submit" class="button button-primary" value="<?php echo esc_attr_x( 'Save rule', '[Admin] Rule save button, in new/edit rule page', 'yith-woocommerce-badges-management' ); ?>"/>
				<input type="hidden" name="id" id="preset_id" value="<?php echo $rule ? esc_attr( $rule->get_id() ) : ''; ?>"/>
				<?php wp_nonce_field( 'yith_wcbm_save_rule' ); ?>
			</p>
		</form>
	</div>
</div>
