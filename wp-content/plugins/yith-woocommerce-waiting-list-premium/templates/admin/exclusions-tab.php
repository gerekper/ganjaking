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
		$notice = __( 'Select at least one product to remove.', 'yith-woocommerce-waiting-list' );
		break;
	case 2:
		$message = __( 'Products removed successfully.', 'yith-woocommerce-waiting-list' );
		break;
	case 3:
		$message = __( 'Products added successfully.', 'yith-woocommerce-waiting-list' );
		break;
	case 4:
		$notice = __( 'You must select at least one product to add', 'yith-woocommerce-waiting-list' );
		break;
	default:
		break;
}

?>
<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br/></div>
	<h2><?php esc_html_e( 'Exclusion list', 'yith-woocommerce-waiting-list' ); ?></h2>

	<?php if ( ! empty( $notice ) ) : ?>
		<div id="notice" class="error below-h2">
			<p><?php echo esc_html( $notice ); ?></p>
		</div>
	<?php endif;

	if ( ! empty( $message ) ) : ?>
		<div id="message" class="updated below-h2">
			<p><?php echo esc_html( $message ); ?></p>
		</div>
	<?php endif;

	?>
	<form id="yith-add-exclusion" method="POST">
		<input type="hidden" name="action" value="exclude_products"/>
		<label for="products"><?php esc_html_e( 'Products to exclude', 'yith-woocommerce-waiting-list' ); ?></label>
		<?php yit_add_select2_fields( array(
			'class'         => 'wc-product-search',
			'data-multiple' => true,
			'id'            => 'products',
			'name'          => 'products',
		) ); ?>
		<input type="submit" value="<?php esc_html_e( 'Add product exclusion', 'yith-woocommerce-waiting-list' ); ?>"
			id="insert" class="button button-primary button-large" name="insert">
	</form>

	<form id="yith-exclusion-table" class="yith-wcwtl-table" method="GET">
		<input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page'] ); ?>"/>
		<input type="hidden" name="tab" value="<?php echo esc_attr( $_GET['tab'] ); ?>"/>
		<?php $table->add_search_box( __( 'Search Product', 'yith-woocommerce-waiting-list' ), 's' ); ?>
		<?php $table->display(); ?>
	</form>

</div>