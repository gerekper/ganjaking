<?php if( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="hmwp_ms_diff" class="wrap">
	<?php screen_icon( 'plugins' ); ?>
	<h2><?php _e( 'Update Mute Screamer', 'mute-screamer' ); ?></h2>
	<p><?php _e( 'Showing changes to be updated.', 'mute-screamer' ); ?></p>

	<form action="update.php?action=hmwp_ms_upgrade" method="post">
		<?php wp_nonce_field( 'hmwp_ms-upgrade-diff' ); ?>
		<input type="hidden" name="url" value="<?php echo esc_url( $url ); ?>" />
		<p><input class="button-secondary" type="submit" value="<?php esc_attr_e( 'Continue', 'mute-screamer' ); ?>" /></p>
	</form>

	<?php foreach( $diff_files as $file ) : ?>
	<div class="hmwp_ms_diff_file" style="">
		<div class="meta">
			<?php echo str_replace( ABSPATH, '', HMWP_MS_PATH.'/libraries/IDS/' ).esc_html( $file->name ); ?>
		</div>

		<div class="data">
			<table class="form-table ie-fixed">

			<?php if( ! $file->diff ) : ?>

			<tr><td colspan="2"><div class="hmwp_ms-message"><p><?php _e( 'These revisions are identical.', 'mute-screamer' ); ?></p></div></td></tr>

			<?php else : ?>

			<tr id="revision-field-content">
				<td><div class="pre"><?php echo $file->diff; ?></div></td>
			</tr>

			<?php endif; ?>

			</table>
		</div>
	</div>

	<?php endforeach; ?>

</div>
