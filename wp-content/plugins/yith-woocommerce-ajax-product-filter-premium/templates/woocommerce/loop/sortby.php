<?php
/**
 * Show options for ordering
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates
 * @version 3.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $catalog_orderby_options array
 * @var $orderby                 string
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
global $wp_query;

$shop_page_uri     = yit_get_woocommerce_layered_nav_link();
$queried_object    = $wp_query instanceof WP_Query ? $wp_query->get_queried_object() : false;
$filter_value_args = array(
	'queried_object' => $queried_object,
);
$filter_value      = yit_get_filter_args( $filter_value_args );
$rel_nofollow      = yith_wcan_add_rel_nofollow_to_url( true );
?>
<ul class="orderby">
	<?php foreach ( $catalog_orderby_options as $option_id => $name ) : ?>

		<?php if ( $orderby === $option_id ) : ?>
			<?php $a_class = 'orderby-item active'; ?>
			<?php unset( $filter_value['orderby'] ); ?>
		<?php else : ?>
			<?php $a_class = 'orderby-item'; ?>
			<?php $filter_value['orderby'] = $option_id; ?>
		<?php endif; ?>

		<li class="orderby-wrapper">
			<a
				<?php echo $rel_nofollow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				data-id="<?php echo esc_attr( $option_id ); ?>"
				class="<?php echo esc_attr( $a_class ); ?>"
				href="<?php echo esc_url( add_query_arg( $filter_value, $shop_page_uri ) ); ?>"
			>
				<?php echo esc_html( $name ); ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
