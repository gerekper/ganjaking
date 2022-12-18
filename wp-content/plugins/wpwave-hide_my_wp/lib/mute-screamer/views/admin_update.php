<?php if( ! defined( 'ABSPATH' ) ) exit; ?>

<h3><?php _e( 'Mute Screamer', 'mute-screamer' ); ?></h3>
<p><?php _e( 'The following files have new versions available. Check the ones you want to update and then click &#8220;Update Mute Screamer&#8221;.', 'mute-screamer' ); ?></p>
<form method="post" action="update.php?action=hmwp_ms_upgrade_diff" name="upgrade-mute-screamer" class="upgrade">
<?php wp_nonce_field( 'upgrade-core' ); ?>
<p><input id="upgrade-mute-screamer" class="button" type="submit" value="<?php esc_attr_e( 'Update Mute Screamer', 'mute-screamer' ); ?>" name="upgrade" /></p>
<table class="widefat" cellspacing="0" id="update-mute-screamer-table">
	<thead>
	<tr>
		<th scope="col" class="manage-column check-column"><input type="checkbox" id="mute-screamer-select-all" /></th>
		<th scope="col" class="manage-column"><label for="mute-screamer-select-all"><?php _e( 'Select All', 'mute-screamer' ); ?></label></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column check-column"><input type="checkbox" id="mute-screamer-select-all-2" /></th>
		<th scope="col" class="manage-column"><label for="mute-screamer-select-all-2"><?php _e( 'Select All', 'mute-screamer' ); ?></label></th>
	</tr>
	</tfoot>
	<tbody class="plugins">
<?php
	foreach ( $files as $file => $file_data ) {
		echo "
	<tr class='active'>
		<th scope='row' class='check-column'><input type='checkbox' name='checked[]' value='" . esc_attr( $file ) . "' /></th>
		<td class='plugin-title'><strong>".esc_html( $file )."</strong>" . sprintf( __( 'Update to revision %1$s. <a href="%2$s">Review changeset</a>.', 'mute-screamer' ), esc_html( $file_data->revision ), esc_url( $file_data->revision_url ) ) . "</td>
	</tr>";
	}
?>
	</tbody>
</table>
<p><input id="upgrade-mute-screamer-2" class="button" type="submit" value="<?php esc_attr_e( 'Update Mute Screamer', 'mute-screamer' ); ?>" name="upgrade" /></p>
</form>
