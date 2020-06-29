<?php
/**
 * Admin View: Exclusion Table Settings
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit; // Exit if accessed directly
}

$mess = isset( $_GET['wcwtl_mess'] ) ? $_GET['wcwtl_mess'] : '';

switch ( $mess ) {
	case 1:
		$notice = __( 'Select at last one waitlist to remove.', 'yith-woocommerce-waiting-list' );
		break;
	case 2:
		$message = __( 'Waiting lists removed successfully.', 'yith-woocommerce-waiting-list' );
		break;
	case 3:
		$notice = __( 'You must select at least one user.', 'yith-woocommerce-waiting-list' );
		break;
	case 4:
		$notice = __( 'The plugin is currently inactive. Activate it from the admin settings to send emails.', 'yith-woocommerce-waiting-list' );
		break;
	case 5:
		$message = __( 'Email sent correctly!', 'yith-woocommerce-waiting-list' );
		break;
	case 6:
		$notice = __( 'An error occurred. Please try again.', 'yith-woocommerce-waiting-list' );
		break;
	case 7:
		$message = __( 'All users have been added to this waiting list', 'yith-woocommerce-waiting-list' );
		break;
	case 8:
		$message = sprintf( _n( '%s user removed successfully', '%s users removed successfully', $_GET['wcwtl_count'], 'yith-woocommerce-waiting-list' ), $_GET['wcwtl_count'] );
		break;
	default:
		break;
}

?>
<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br/></div>
	<h2>
		<?php esc_html_e( 'Users in the Waiting list', 'yith-woocommerce-waiting-list' );
		$query_args   = array(
			'page' => $_GET['page'],
			'tab'  => $_GET['tab'],
		);
		$add_form_url = add_query_arg( $query_args, admin_url( 'admin.php' ) );
		?>
		<a class="add-new-h2" href="<?php echo esc_url( $add_form_url ) ?>">
			<?php esc_html_e( 'Return to waiting lists checklist', 'yith-woocommerce-waiting-list' ) ?>
		</a>
	</h2>


	<?php if ( ! empty( $notice ) ) : ?>
		<div id="notice" class="error below-h2">
			<p><?php echo esc_html( $notice ); ?></p>
		</div>
	<?php endif;

	if ( ! empty( $message ) ) : ?>
		<div id="message" class="updated below-h2">
			<p><?php echo esc_html( $message ); ?></p>
		</div>
	<?php endif; ?>

	<form id="yith-add-user" method="POST">
		<input type="hidden" name="action" value="insert_users"/>
		<input type="hidden" name="view" value="users"/>
		<label for="users_email"><?php esc_html_e( 'Add user to the Waiting list', 'yith-woocommerce-waiting-list' ); ?></label>
		<?php
		yit_add_select2_fields( array(
			'name'             => 'users_id',
			'id'               => 'users_id',
			'class'            => 'wc-customer-search',
			'data-multiple'    => true,
			'data-placeholder' => __( 'Search for an user', 'yith-woocommerce-waiting-list' ),
		) );
		?>
		<input type="submit" value="<?php esc_html_e( 'Add new user', 'yith-woocommerce-waiting-list' ); ?>" id="insert"
			class="button button-primary button-large" name="insert">
	</form>
	<form id="yith-waitlistdata-table" class="yith-wcwtl-table" method="get">
		<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>">
		<input type="hidden" name="tab" value="<?php echo esc_attr( $_REQUEST['tab'] ); ?>">
		<input type="hidden" name="view" value="users"/>
		<input type="hidden" name="id" value="<?php echo intval( $_GET['id'] ); ?>">
		<?php $table->add_search_box( __( 'Search User', 'yith-woocommerce-waiting-list' ), 's' ); ?>
		<?php $table->display(); ?>
	</form>
</div>