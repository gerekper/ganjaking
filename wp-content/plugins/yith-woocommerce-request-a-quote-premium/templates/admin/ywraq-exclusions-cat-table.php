<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Request A Quote Premium
 */

/**
 * Admin View: Exclusions List Table
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$list_query_args = array(
	'page' => sanitize_text_field( wp_unslash( $_GET['page'] ) ), //phpcs:ignore WordPress.Security.NonceVerification.Recommended
	'tab'  => sanitize_text_field( wp_unslash( $_GET['tab'] ) ), //phpcs:ignore WordPress.Security.NonceVerification.Recommended
);

$list_url = add_query_arg( $list_query_args, admin_url( 'admin.php' ) );

?>
<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br/></div>
	<h2><?php esc_html_e( 'Category exclusion list', 'yith-woocommerce-request-a-quote' ); ?></h2>

	<?php if ( ! empty( $notice ) ) : ?>
		<div id="notice" class="error below-h2"><p><?php echo wp_kses_post( $notice ); ?></p></div>
		<?php
			endif;

	if ( ! empty( $message ) ) :
		?>
		<div id="message" class="updated below-h2"><p><?php echo wp_kses_post( $message ); ?></p></div>
		<?php
	endif;

	?>
	<form id="yith-add-exclusion-cat" method="POST">
		<input type="hidden" name="_nonce" value="<?php echo sanitize_key( wp_create_nonce( 'yith_ywraq_add_exclusions_cat' ) ); ?>"/>
		<label for="add_categories"><?php esc_html_e( 'Select categories to exclude', 'yith-woocommerce-request-a-quote' ); ?></label>
		<?php
			yit_add_select2_fields(
				array(
					'style'            => 'width: 300px;display: inline-block;',
					'class'            => 'wc-product-search',
					'id'               => 'add_categories',
					'name'             => 'add_categories',
					'data-placeholder' => __( 'Search category...', 'yith-woocommerce-request-a-quote' ),
					'data-multiple'    => true,
					'data-action'      => 'yith_ywraq_search_categories',
				)
			);
			?>

		<input type="submit" value="<?php esc_html_e( 'Exclude', 'yith-woocommerce-request-a-quote' ); ?>" id="add" class="button" name="add">
	</form>

	<?php $table->display(); ?>

</div>
