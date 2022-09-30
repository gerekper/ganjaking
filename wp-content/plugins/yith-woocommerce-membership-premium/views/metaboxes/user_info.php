<?php
/*
 * Template for Metabox User Info
 */
?>

<table class="yith-wcmbs-message-user-info">
	<tr>
		<th><?php esc_html_e( 'Username' ) ?></th>
		<td><?php echo esc_html( $username ); ?></td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'First Name' ) ?></th>
		<td><?php echo esc_html( $firstname ); ?></td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'Last Name' ) ?></th>
		<td><?php echo esc_html( $lastname ); ?></td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'E-mail' ) ?></th>
		<td><?php echo esc_html( $email ); ?></td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'Membership', 'yith-woocommerce-membership' ) ?></th>
		<td><?php echo wp_kses_post( $plans ); ?></td>
	</tr>
</table>
