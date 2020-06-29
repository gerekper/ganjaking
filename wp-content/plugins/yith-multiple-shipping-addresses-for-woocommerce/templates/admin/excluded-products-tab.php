<?php
/**
 * Admin View: Exclusion Table Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$products_table = YITH_WCMAS_Excluded_Products_Table_Options()->prepare_table();
$products_table->prepare_items();

$mess = isset( $_GET['wcmas_mess'] ) ? $_GET['wcmas_mess'] : '';

switch( $mess ) {
	case 1:
		$notice = esc_html__( 'Select at least one product to remove.', 'yith-multiple-shipping-addresses-for-woocommerce' );
		break;
	case 2:
		$message = esc_html__( 'Products removed successfully.', 'yith-multiple-shipping-addresses-for-woocommerce' );
		break;
	case 3:
		$message = esc_html__( 'Products added successfully.', 'yith-multiple-shipping-addresses-for-woocommerce' );
		break;
	case 4:
		$notice = esc_html__( 'You must select at least one product to add', 'yith-multiple-shipping-addresses-for-woocommerce' );
		break;
	default:
		break;
}

$list_query_args = array(
	'page' => $_GET['page'],
	'tab'  => $_GET['tab']
);

$list_url = add_query_arg( $list_query_args, admin_url( 'admin.php' ) );

?>

<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br /></div>
    <h2><?php esc_html_e( 'Exclude products for Multi Shipping', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></h2>

	<?php if ( ! empty( $notice ) ) : ?>
        <div id="notice" class="error below-h2"><p><?php echo $notice; ?></p></div>
	<?php endif;

	if ( ! empty( $message ) ) : ?>
        <div id="message" class="updated below-h2"><p><?php echo $message; ?></p></div>
	<?php endif;

	?>
    <form id="ywcmas-add-exclusion" method="POST">
        <h4><?php echo esc_html__( 'Add products to list', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></h4>
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'yith_wcmas_add_exclusions' ); ?>" />
        <input type="hidden" name="action" value="insert" />
		<?php yit_add_select2_fields( array(
			'class' 		=> 'wc-product-search',
			'data-multiple' => true,
			'id'			=> 'ywcmas_products_for_exclude',
			'name'			=> 'ywcmas_products_for_exclude',
			'placeholder'   => esc_html__( 'Search for products...', 'yith-multiple-shipping-addresses-for-woocommerce' ),
			'data-action'   => 'yes' == get_option( 'ywcmas_search_for_variations', 'no' ) ? 'woocommerce_json_search_products_and_variations' : 'woocommerce_json_search_products'
		) ); ?>
        <input type="submit" value="<?php esc_html_e( 'Add products', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?>" id="insert" class="button" name="insert">
    </form>
    <form id="ywcmas-exclusion-table" class="yith-wocc-table" method="GET" action="<?php echo esc_url( $list_url ); ?>">
        <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
        <input type="hidden" name="tab" value="<?php echo $_GET['tab']; ?>" />
		<?php $products_table->display(); ?>
    </form>
</div>