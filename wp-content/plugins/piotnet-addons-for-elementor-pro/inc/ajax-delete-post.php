<?php
	add_action( 'wp_ajax_pafe_delete_post', 'pafe_delete_post' );
	add_action( 'wp_ajax_nopriv_pafe_delete_post', 'pafe_delete_post' );
	function pafe_delete_post() {
		global $wpdb;
			if ( !empty($_POST['id']) ) {
				$id = intval($_POST['id']);
				$force_delete = intval($_POST['force_delete']);

				if ($force_delete == 0) {
					$force_delete = false;
				}

				if ($force_delete == 1) {
					$force_delete = true;
				}

				$delete_post = wp_delete_post( $id, $force_delete );
				if ($delete_post != false) {
					echo 1;
				}
			}
		wp_die();
	}
?>