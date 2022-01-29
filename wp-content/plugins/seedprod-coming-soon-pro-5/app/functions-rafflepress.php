<?php
/**
 * Get RafflePress.
 *
 * @return void
 */
function seedprod_pro_get_rafflepress() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$giveaways  = array();
		$rp_version = 'lite';
		if ( function_exists( 'rafflepress_pro_load_textdomain' ) ) {
			$rp_version = 'pro';
		}
		if ( function_exists( 'rafflepress_' . $rp_version . '_activation' ) || function_exists( 'rafflepress_' . $rp_version . '' ) ) {
			global $wpdb;
			$tablename = $wpdb->prefix . 'rafflepress_giveaways';
			$sql       = "SELECT id,name FROM $tablename WHERE deleted_at IS NULL"; // Unnecessary to prepare a query which doesn't user variable replacement.
			$giveaways = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		wp_send_json( $giveaways );
	}
}

/**
 * Get RafflePress code.
 *
 * @return void
 */
function seedprod_pro_get_rafflepress_code() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$id = absint( filter_input( INPUT_GET, 'form_id', FILTER_SANITIZE_NUMBER_INT ) );
		ob_start();
		?>

		<div class="sp-relative">
			<div class="rafflepress-giveaway-iframe-wrapper rpoverlay">
				<iframe id="rafflepress-<?php echo esc_attr( $id ); ?>" src="<?php echo esc_attr( home_url() ) . '?rpid=' . esc_attr( $id ) . '?iframe=1&giframe=' . esc_attr( $a['giframe'] ) . '&rpr=' . esc_attr( $ref ) . '&parent_url=' . rawurlencode( $parent_url ); ?>&<?php echo esc_attr( wp_rand( 1, 99999 ) ); ?>" frameborder="0" scrolling="no" allowtransparency="true" style="width:100%; height:400px"></iframe>
			</div>
		</div>

		<?php
		$code = ob_get_clean();
		wp_send_json( $code );
	}
}
