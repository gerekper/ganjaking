<?php
/**
 * Admin View:  General Rules Tab
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="wrap" class="plugin-option yit-admin-panel-container">
	<div class="yit-admin-panel-content-wrap yit-admin-panel-content-wrap-full yit-admin-panel-content-wrap-full ywdpd_table_list" data-type="<?php echo $type ?>">
		<table class="form-table">
			<tbody>
			<tr>
				<td>
					<p>
						<input id="yith-ywdpd-add-section" type="text" class="ywdpd-section-title" value="" />
						<a href="" id="yith-ywdpd-add-section-button" class="button-secondary" data-section_id="yit_ywdpd_options_<?php echo $type ?>-rules" data-action="add_section" data-type="<?php echo $type ?>" data-section_name="yit_ywdpd_options[<?php echo $type ?>-rules]"><?php _e( 'Add new rule', 'ywdpd' ) ?></a>
						<span class="ywdpd-error-input-section"></span>
					</p>
					<?php do_action( 'ywdpd_print_rules', $type ); ?>
				</td>
			</tr>
			</tbody>
		</table>

	</div>
</div>