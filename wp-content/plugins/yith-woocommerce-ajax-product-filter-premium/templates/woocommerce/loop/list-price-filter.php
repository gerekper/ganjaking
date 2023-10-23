<?php
/**
 * Price list filter
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates
 * @version 3.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $instance      array
 * @var $rel_nofollow  string
 * @var $shop_page_uri string
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
global $wp_query;

$queried_object    = $wp_query instanceof WP_Query ? $wp_query->get_queried_object() : false;
$filter_value_args = array(
	'check_price_filter' => true,
	'instance'           => $instance,
	'queried_object'     => $queried_object,
);

$filter_value = yit_get_filter_args( $filter_value_args );

if ( ! empty( $prices ) ) : ?>
	<ul class="yith-wcan-list-price-filter">
		<?php foreach ( $prices as $price ) : ?>
			<li class="price-item">
				<?php $is_active = yit_check_active_price_filter( $price['min'], $price['max'] ); ?>
				<?php
				if ( $is_active ) {
					$filter_value = yit_remove_price_filter_query_args( $filter_value );
				} else {
					$filter_value = array_merge(
						$filter_value,
						array(
							'min_price' => $price['min'],
							'max_price' => $price['max'],
						)
					);
				}
				?>
				<?php $link_class = $is_active ? 'yith-wcan-price-link active' : 'yith-wcan-price-link'; ?>
				<a
					<?php echo $rel_nofollow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					class="<?php echo esc_attr( $link_class ); ?> yith-wcan-price-filter-list-link"
					href="<?php echo esc_url( add_query_arg( $filter_value, $shop_page_uri ) ); ?>"
				>
					<?php echo esc_html_x( 'From', 'Price filter option: price starts from', 'yith-woocommerce-ajax-navigation' ) . ': ' . wc_price( $price['min'] ) . ' ' . esc_html_x( 'To', 'Price filter option: price ends to', 'yith-woocommerce-ajax-navigation' ) . ': ' . wc_price( $price['max'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
