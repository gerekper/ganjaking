<?php
/**
 * Preset edit page - Admin view
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Admin
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $preset bool|YITH_WCAN_Preset
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php
$preset_id = $preset ? $preset->get_id() : false;
$fields    = YITH_WCAN_Preset::get_fields();
?>

<div id="yith_wcan_panel_filter-preset-edit" class="yith-plugin-fw yit-admin-panel-container">
	<div class="yit-admin-panel-content-wrap">
		<form id="plugin-fw-wc" method="post" action="admin.php?action=yith_wcan_save_preset">
			<span class="view-all-presets">
				<a href="<?php echo esc_url( $this->get_panel_url( 'filter-preset' ) ); ?>">
					<?php echo esc_html_x( '< back to preset list', '[Admin] Back link in new preset page', 'yith-woocommerce-ajax-navigation' ); ?>
				</a>
			</span>

			<h2>
				<?php
				if ( $preset ) {
					echo esc_html_x( 'Edit filter preset', '[ADMIN] Title for new preset page', 'yith-woocommerce-ajax-navigation' );
				} else {
					echo esc_html_x( 'Add new filter preset', '[ADMIN] Title for new preset page', 'yith-woocommerce-ajax-navigation' );
				}
				?>
			</h2>

			<?php if ( isset( $_GET['status'] ) && 'success' === $_GET['status'] ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<div class="preset-saved">
					<p><?php echo esc_html_x( 'Preset saved correctly', '[ADMIN] Preset save message', 'yith-woocommerce-ajax-navigation' ); ?></p>
				</div>
			<?php endif; ?>

			<?php do_action( 'yith_wcan_preset_edit_before_title', $preset_id, $preset ); ?>

			<?php if ( ! empty( $fields ) ) : ?>
				<table class="form-table">
					<tbody>
					<?php foreach ( $fields as $field_slug => $field ) : ?>
					<tr valign="top" class="yith-plugin-fw-panel-wc-row <?php echo esc_attr( $field['type'] ); ?>">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $field_slug ); ?>>"><?php echo esc_html( $field['label'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( $field['type'] ); ?>">
							<?php
							$field_name      = str_replace( 'preset_', '', $field_slug );
							$is_valid_preset = is_string( $preset ) || is_object( $preset );
							$field_args      = array_merge(
								$field,
								array(
									'id'     => $field_slug,
									'name'   => $field_slug,
									'preset' => $preset,
									'value'  => $is_valid_preset && method_exists( $preset, "get_{$field_name}" ) ? $preset->{"get_{$field_name}"}() : '',
								)
							);
								yith_plugin_fw_get_field( $field_args, true );
							?>
							<span class="description">
								<?php echo wp_kses_post( $field['desc'] ); ?>
							</span>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php endif; ?>

			<?php do_action( 'yith_wcan_preset_edit_before_filters', $preset_id, $preset ); ?>

			<?php require YITH_WCAN_DIR . 'templates/admin/preset-filters.php'; ?>

			<?php do_action( 'yith_wcan_preset_edit_after_filters', $preset_id, $preset ); ?>

			<p class="submit">
				<input type="submit" id="submit" class="button button-primary" value="<?php echo esc_attr_x( 'Save preset', '[Admin] Preset save button, in new/edit preset page', 'yith-woocommerce-ajax-navigation' ); ?>"/>
				<input type="hidden" name="id" id="preset_id" value="<?php echo $preset ? esc_attr( $preset->get_id() ) : ''; ?>"/>
				<input type="hidden" name="post_ID" id="post_ID" value="<?php echo $preset ? esc_attr( $preset->get_id() ) : ''; ?>"/>
				<input type="hidden" name="paged" id="paged" value="<?php echo $preset && $preset->needs_pagination() ? 1 : 0; ?>"/>
				<?php wp_nonce_field( 'save_preset' ); ?>
			</p>
		</form>
	</div>
</div>
