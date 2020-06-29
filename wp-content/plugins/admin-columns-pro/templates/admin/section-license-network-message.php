<p class="description">
	<?php
	$page = __( 'network settings page', 'codepress-admin-columns' );

	if ( current_user_can( 'manage_network_options' ) ) {
		$page = ac_helper()->html->link( network_admin_url( 'settings.php?page=codepress-admin-columns&tab=settings' ), $page );
	}

	printf( __( 'The license can be managed on the %s.', 'codepress-admin-columns' ), $page );
	?>
</p>