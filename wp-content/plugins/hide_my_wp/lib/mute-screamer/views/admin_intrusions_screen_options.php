<?php if( ! defined( 'ABSPATH' ) ) exit; ?>

<h5><?php _e( 'Show on screen', 'mute-screamer' ); ?></h5>
<div class="screen-options">
	<input type="text" value="<?php echo esc_attr( $per_page ); ?>" maxlength="3" id="hmwp_ms_intrusions_per_page" name="wp_screen_options[value]" class="screen-per-page" />
	<label for="hmwp_ms_intrusions_per_page"><?php _e( 'Intrusions', 'mute-screamer' ); ?></label>
	<input type="submit" value="<?php _e( 'Apply', 'mute-screamer' ); ?>" class="button" />
	<input type="hidden" value="hmwp_ms_intrusions_per_page" name="wp_screen_options[option]" />
</div>
