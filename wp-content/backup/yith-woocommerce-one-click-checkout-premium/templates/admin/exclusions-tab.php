<?php
/**
 * Admin View: Exclusion Table Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$table = YITH_WOCC_Exclusions_Table()->prepare_table();
$table->prepare_items();

$mess = isset( $_GET['wocc_mess'] ) ? $_GET['wocc_mess'] : '';

switch( $mess ) {
	case 1:
		$notice = __( 'Select at least one product to remove.', 'yith-woocommerce-one-click-checkout' );
		break;
	case 2:
		$message = __( 'Products removed successfully.', 'yith-woocommerce-one-click-checkout' );
		break;
	case 3:
		$message = __( 'Products added successfully.', 'yith-woocommerce-one-click-checkout' );
		break;
	case 4:
		$notice = __( 'You must select at least one product to add', 'yith-woocommerce-one-click-checkout' );
		break;
	default:
		break;
}

$list_query_args = array(
	'page' => $_GET['page'],
	'tab'  => $_GET['tab']
);


$list_url = add_query_arg( $list_query_args, admin_url( 'admin.php' ) );
$list_title = get_option( 'yith-wocc-exclusion-inverted' ) == 'yes' ? __( 'Active product list', 'yith-woocommerce-one-click-checkout' ) : __( 'Product exclusion list', 'yith-woocommerce-one-click-checkout' );

?>
<div class="wrap"a>
	<div class="icon32 icon32-posts-post" id="icon-edit"><br /></div>
	<h2><?php echo esc_html( $list_title ); ?></h2>

	<?php if ( ! empty( $notice ) ) : ?>
		<div id="notice" class="error below-h2"><p><?php echo wp_kses_post( $notice ); ?></p></div>
	<?php endif;

	if ( ! empty( $message ) ) : ?>
		<div id="message" class="updated below-h2"><p><?php echo wp_kses_post( $message ); ?></p></div>
	<?php endif;

	?>
	<form id="yith-add-exclusion" method="POST">
		<h4><?php esc_html_e( 'Add products to list', 'yith-woocommerce-one-click-checkout' ); ?></h4>
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'yith_wocc_add_exclusions' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" />
		<input type="hidden" name="action" value="insert" />
        <?php yit_add_select2_fields( array(
            'class' 		=> 'wc-product-search',
            'data-multiple' => true,
            'id'			=> 'products',
            'name'			=> 'products',
            'placeholder'   => __( 'Search for products...', 'yith-woocommerce-one-click-checkout' ),
            'data-action'   => "woocommerce_json_search_products"
        ) ); ?>

        <input type="submit" value="<?php esc_html_e( 'Add products', 'yith-woocommerce-one-click-checkout' ); ?>" id="insert" class="button" name="insert">
	</form>

	<form id="yith-exclusion-table" class="yith-wocc-table" method="GET" action="<?php echo esc_url( $list_url ); ?>">
		<input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page'] ); ?>" />
		<input type="hidden" name="tab" value="<?php echo esc_attr( $_GET['tab'] ); ?>" />
		<?php $table->display(); ?>
	</form>

</div>