<?php
$sp_post_id = isset( $_GET['id'] ) ? absint( wp_unslash( $_GET['id'] ) ) : null;

if ( ! empty( $_POST['sp_post_json'] ) && wp_verify_nonce( 'importexport-' . $sp_post_id ) ) {
	// update
	global $wpdb;
	$json = json_decode( wp_unslash( $_POST['sp_post_json'] ) );
	if ( json_last_error() !== JSON_ERROR_NONE ) {
		wp_die( 'JSON is NOT valid' );
	}
	$json      = wp_json_encode( $json );
	$tablename = $wpdb->prefix . 'posts';
	$r         = $wpdb->update(
		$tablename,
		array(
			'post_content_filtered' => $json,   // string
		),
		array( 'ID' => $sp_post_id ),
		array(
			'%s',   // value1
		),
		array( '%d' )
	);
	if ( false === $r ) {
		echo 'Update error' . PHP_EOL;
	} else {
		echo 'Updated' . PHP_EOL;
	}
}

		global $wpdb;
		$tablename = $wpdb->prefix . 'posts';
		$sql       = "SELECT * FROM $tablename";
		$sql      .= ' WHERE ID = %s';
		$safe_sql  = $wpdb->prepare( $sql, $sp_post_id ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$result    = $wpdb->get_row( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared


		$js = json_decode( $result->post_content_filtered );
if ( json_last_error() === JSON_ERROR_NONE ) {
	echo 'JSON is valid' . PHP_EOL;
} else {
	echo 'JSON is NOT valid' . PHP_EOL;
}


?>
<form method="post">
<?php wp_nonce_field( 'importexport-' . $sp_post_id ); ?>
<h1>Post JSON</h1>
<textarea name="sp_post_json" style="width:100%; height: 500px;"><?php echo esc_textarea( $result->post_content_filtered ); ?></textarea>
<input type="submit">
</form>
