<?php
	function pafe_acf_repeater_meta_box() {
		add_meta_box( 'pafe-acf-repeater-meta-box', 'PAFE ACF Repeater', 'pafe_acf_repeater_meta_box_output', 'elementor_library' );
	}
	add_action( 'add_meta_boxes', 'pafe_acf_repeater_meta_box' );

	function pafe_acf_repeater_meta_box_output( $post ) {
		$pafe_acf_repeater_name = get_post_meta( $post->ID, '_pafe_acf_repeater_name', true );
		$pafe_acf_repeater_preview_post_id = get_post_meta( $post->ID, '_pafe_acf_repeater_preview_post_id', true );
		?>
			<table class="form-table">
		        <tr valign="top">
		        <th scope="row"><?php _e('Repeater Name:','pafe'); ?></th>
		        <td><input type="text" class="regular-text" id="pafe_acf_repeater_name" name="pafe_acf_repeater_name" value="<?php echo esc_attr( $pafe_acf_repeater_name ); ?>" /></td>
		        </tr>
		        <tr valign="top">
		        <th scope="row"><?php _e('Preview Post ID:','pafe'); ?></th>
		        <td><input type="number" class="regular-text" id="pafe_acf_repeater_preview_post_id" name="pafe_acf_repeater_preview_post_id" value="<?php echo esc_attr( $pafe_acf_repeater_preview_post_id ); ?>" /></td>
		        </tr>
		    </table>
		<?php
	}

	function pafe_acf_repeater_meta_box_save( $post_id ) {
		if (isset($_POST['pafe_acf_repeater_name']) && isset($_POST['pafe_acf_repeater_preview_post_id'])) {
			$pafe_acf_repeater_name = sanitize_text_field( $_POST['pafe_acf_repeater_name'] );
			update_post_meta( $post_id, '_pafe_acf_repeater_name', $pafe_acf_repeater_name );

			$pafe_acf_repeater_preview_post_id = sanitize_text_field( $_POST['pafe_acf_repeater_preview_post_id'] );
			update_post_meta( $post_id, '_pafe_acf_repeater_preview_post_id', $pafe_acf_repeater_preview_post_id );
		}
	}  
	add_action( 'save_post', 'pafe_acf_repeater_meta_box_save' );
?>