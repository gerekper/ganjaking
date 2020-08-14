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


if ( ! empty( $sections ) ) :
	?>
	<div id="ywraq-submenu">
		<ul class="subsubsub">
			<?php foreach ( $sections as $key => $value ) : ?>
				<li>
					<a href="<?php echo esc_url( add_query_arg( 'section', $value, $base_page_url ) ); ?>" <?php echo ( $value === $current ) ? 'class="current"' : ''; ?> ><?php echo esc_attr( ucwords( $value ) ) . ' ' . esc_html__( ' Exclusion List', 'yith-woocommerce-request-a-quote' ); ?></a>
					<?php
					if ( end( $sections ) !== $value ) {
						echo ' | ';
					}
					?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

<?php endif; ?>

<?php

$list_query_args = array(
	'page' => sanitize_text_field( wp_unslash( $_GET['page'] ) ), //phpcs:ignore
	'tab'  => sanitize_text_field( wp_unslash( $_GET['tab'] ) ), //phpcs:ignore
);

$list_url = add_query_arg( $list_query_args, admin_url( 'admin.php' ) );

if ( 'category' === $current ) {
	include_once YITH_YWRAQ_TEMPLATE_PATH . '/admin/ywraq-exclusions-cat-table.php';
}
if ( 'product' === $current ) {
	include_once YITH_YWRAQ_TEMPLATE_PATH . '/admin/ywraq-exclusions-prod-table.php';
}
if ( 'tag' === $current ) {
	include_once YITH_YWRAQ_TEMPLATE_PATH . '/admin/ywraq-exclusions-tag-table.php';
}
