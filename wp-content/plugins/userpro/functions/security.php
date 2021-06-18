<?php

	/* Nonce field before submitting */
	add_action('userpro_before_fields', 'userpro_nonce');
	function userpro_nonce($args){
		wp_nonce_field( '_myuserpro_nonce_'.$args['template'].'_'.$args['unique_id'] , '_myuserpro_nonce' );
		?>
		<input type="hidden" name="unique_id" id="unique_id" value="<?php echo $args['unique_id']; ?>" />
		<?php
	}