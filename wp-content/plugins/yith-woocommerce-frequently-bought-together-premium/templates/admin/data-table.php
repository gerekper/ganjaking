<?php
/**
 * Admin View: Data Table Settings
 */

if ( ! defined( 'YITH_WFBT' ) ) {
	exit; // Exit if accessed directly
}

$mess = isset( $_GET['wfbt_mess'] ) ? $_GET['wfbt_mess'] : '';

switch ( $mess ) {
	case 1:
		$notice = __( 'Select at least one product that has to be removed.', 'yith-woocommerce-frequently-bought-together' );
		break;
	case 2:
		$message = __( 'Selected products have been removed.', 'yith-woocommerce-frequently-bought-together' );
		break;
	default:
		break;
}

$list_query_args = array(
	'page' => $_GET['page'],
	'tab'  => $_GET['tab'],
);

$list_url = add_query_arg( $list_query_args, admin_url( 'admin.php' ) );

?>
<div class="wrap yith-wfbt">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br/></div>
	<h2>
		<?php
		if ( isset( $_GET['view'] ) && 'linked' === $_GET['view'] && isset( $_GET['post_id'] ) ) {

			echo sprintf( esc_html__( 'Linked products for %s', 'yith-woocommerce-frequently-bought-together' ), get_the_title( $_GET['post_id'] ) );

			$query_args = array(
				'page' => $_GET['page'],
				'tab'  => $_GET['tab'],
			);

			$add_form_url = add_query_arg( $query_args, admin_url( 'admin.php' ) );

			echo '<a class="add-new-h2" href="' . esc_url( $add_form_url ) . '">' . esc_html__( 'Go back to main list of products', 'yith-woocommerce-frequently-bought-together' ) . '</a>';
		} else {
			esc_html_e( 'List of products', 'yith-woocommerce-frequently-bought-together' );
		}
		?>
	</h2>

	<?php if ( ! empty( $notice ) ) : ?>
		<div id="notice" class="error below-h2"><p><?php echo esc_html( $notice ); ?></p></div>
	<?php endif;

	if ( ! empty( $message ) ) : ?>
		<div id="message" class="updated below-h2"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif;

	?>

	<form id="yith-wfbt-data-table" class="yith-wfbt-table" method="GET" action="<?php echo esc_url( $list_url ); ?>">
		<input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page'] ); ?>"/>
		<input type="hidden" name="tab" value="<?php echo esc_attr( $_GET['tab'] ); ?>"/>
		<?php if ( isset( $_GET['post_id'] ) && isset( $_GET['view'] ) ) : ?>
			<input type="hidden" name="post_id" value="<?php echo esc_attr( $_GET['post_id'] ); ?>"/>
			<input type="hidden" name="view" value="<?php echo esc_attr( $_GET['view'] ); ?>"/>
		<?php endif; ?>
		<?php $table->add_search_box( esc_html__( 'Search Product', 'yith-woocommerce-frequently-bought-together' ), 's' ); ?>
		<?php $table->display(); ?>
	</form>

</div>