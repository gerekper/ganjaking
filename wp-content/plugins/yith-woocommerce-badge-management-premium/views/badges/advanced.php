<?php
/**
 * Advanced Badge Template
 *
 * @var WC_Product      $product Product.
 * @var YITH_WCBM_Badge $badge   The Badge Object.
 *
 * @package YITH\BadgeManagementPremium\Views\Badges
 */

$advanced_display = $badge->get_advanced_display();

$advanced_style = isset( $advanced_style ) ? $advanced_style : 'advanced';
$is_preview     = isset( $is_preview ) && $is_preview;
$is_template    = isset( $is_template ) && $is_template;
$saved_display  = true;

$saved_money           = 0;
$saved_money_float     = 0;
$sale_percentage       = 0;
$sale_percentage_float = 0;

if ( $is_preview || $is_template ) {
	$sale_percentage       = 50;
	$sale_percentage_float = 50;
	$saved_money           = 15;
	$saved_money_float     = 15;
} else {
	if ( $product->is_type( 'variable' ) ) {

		$saved_money_array     = array();
		$sale_percentage_array = array();

		foreach ( $product->get_children() as $child_id ) {
			$child = wc_get_product( $child_id );
			if ( $child ) {
				$price   = floatval( $child->get_price() );
				$price   = apply_filters( 'yith_wcbm_advanced_badge_product_price', $price, $child );
				$price   = apply_filters( 'yith_wcbm_advanced_badge_variation_price', $price, $child, $product );
				$regular = floatval( $child->get_regular_price() );
				$regular = apply_filters( 'yith_wcbm_advanced_badge_product_regular_price', $regular, $child );
				$regular = apply_filters( 'yith_wcbm_advanced_badge_variation_regular_price', $regular, $child, $product );

				$current_saved_money     = 0;
				$current_sale_percentage = 0;

				if ( $price > 0 && $regular > 0 ) {
					$current_saved_money     = $regular - $price;
					$current_sale_percentage = ( $regular - $price ) / $regular * 100;
				}

				$saved_money_array[]     = $current_saved_money;
				$sale_percentage_array[] = $current_sale_percentage;
			}
		}

		$unique_saved_money     = array_unique( $saved_money_array );
		$unique_sale_percentage = array_unique( $sale_percentage_array );

		$show_in_variables = get_option( 'yith-wcbm-show-advanced-badge-in-variable-products', 'same' );

		if ( 1 === count( $unique_sale_percentage ) ) {
			$sale_percentage_float = current( $sale_percentage_array );
			$saved_money_float     = current( $unique_saved_money );
		} else {
			switch ( $show_in_variables ) {
				case 'min':
					$sale_percentage_float = min( $sale_percentage_array );
					$saved_money_float     = min( $saved_money_array );
					break;
				case 'max':
					$sale_percentage_float = max( $sale_percentage_array );
					$saved_money_float     = max( $saved_money_array );
					break;
				default:
					$sale_percentage_float = 0;
					$saved_money_float     = 0;
					break;
			}
		}

		$saved_money   = absint( $saved_money_float );
		$saved_display = $saved_money > 0;

	} else {
		$price         = apply_filters( 'yith_wcbm_advanced_badge_product_price', floatval( $product->get_price() ), $product );
		$regular_price = apply_filters( 'yith_wcbm_advanced_badge_product_regular_price', floatval( $product->get_regular_price() ), $product );

		$sale_percentage_float = 0;
		if ( 0 !== $regular_price ) {
			$sale_percentage_float = ( $regular_price - $price ) / $regular_price * 100;
		}

		$saved_money_float = $regular_price - $price;
		$saved_money       = absint( round( $saved_money_float ) );
	}
	$saved_money = $saved_money ? yit_get_display_price( $product, $saved_money ) : 0;
}

$sale_percentage = absint( round( $sale_percentage_float ) );

$saved = wp_strip_all_tags( wc_price( $saved_money, array( 'decimals' => 0 ) ) );

$translate_badge_strings = apply_filters( 'yith_wcbm_translate_badge_strings', true, $badge );

$labels = array(
	'on_sale'                 => $translate_badge_strings ? _x( 'On Sale', 'Text in badge: preserve length', 'yith-woocommerce-badges-management' ) : 'On Sale',
	'off'                     => $translate_badge_strings ? _x( 'Off', 'Text in badge: preserve length', 'yith-woocommerce-badges-management' ) : 'Off',
	'percentage'              => '%',
	'save'                    => $translate_badge_strings ? _x( 'Save', 'Text in badge: preserve length', 'yith-woocommerce-badges-management' ) : 'Save',
	// translators: 1. the 'Save' text; 2. the saved amount; example: 'Save $9'.
	'save_format'             => _x( '%1$s %2$s', 'Text in badge(preserve length): Save $9', 'yith-woocommerce-badges-management' ),
	'sale'                    => $translate_badge_strings ? _x( 'Sale!', 'Text in badge: preserve length', 'yith-woocommerce-badges-management' ) : 'Sale!',
	// translators: 1. the saved amount; 2. the currency symbol; example: '9 $'.
	'amount_saved_format'     => _x( '%1$s%2$s', 'Text in badge (preserve length): 9 $', 'yith-woocommerce-badges-management' ), // phpcs:ignore WordPress.WP.I18n.NoEmptyStrings
	// translators: 1. the saved amount; 2. the percentage symbol; example: '9 %'.
	'percentage_saved_format' => _x( '%1$s%2$s', 'Text in badge (preserve length): 9 %', 'yith-woocommerce-badges-management' ), // phpcs:ignore WordPress.WP.I18n.NoEmptyStrings
);

$currency = get_woocommerce_currency_symbol();
$show     = ( 'percentage' === $advanced_display && absint( $sale_percentage ) > 0 ) || ( 'amount' === $advanced_display && absint( $saved_money ) > 0 );

$badge_info = array(
	'id'                => $is_preview || $is_template ? 1 : $badge->get_id(),
	'advanced_badge_id' => $badge->get_advanced_id(),
	'advanced_style'    => $badge->get_advanced_id(),
	'display_class'     => 'yith-wcbm-advanced-display-' . $badge->get_advanced_display(),
);
$badge_info = array_merge( $badge_info, compact( 'sale_percentage', 'sale_percentage_float', 'saved_display', 'saved_money', 'saved_money_float', 'saved', 'labels', 'currency', 'show' ) );
$badge_info = apply_filters( 'yith_wcbm_advanced_badge_info', $badge_info, $product ?? false );

$badge_meta = ! empty( $id_badge ) ? yith_wcbm_get_badge_meta_premium( $id_badge ) : false;

if ( $badge_info['show'] || $is_preview || $is_template ) : ?>
	<div class="yith-wcbm-badge yith-wcbm-badge-advanced <?php echo esc_attr( $badge_info['display_class'] ); ?> <?php echo esc_attr( $is_template ? '{{data.classes}}' : $badge->get_classes( $product ) ); ?> yith-wcbm-badge-advanced-<?php echo esc_attr( $is_template ? '{{data.style}}' : $badge_info['advanced_style'] ); ?>" data-transform="<?php echo esc_attr( $badge->get_transform_css() ); ?>">
		<div class='yith-wcbm-badge__wrap'>
			<div class="yith-wcbm-badge-shape">
				<?php
				if ( ! $is_template ) {
					$args = array(
						'badge' => $badge,
						'style' => $badge->get_advanced(),
						'type'  => 'advanced',
					);
					echo yith_wcbm_get_badge_svg( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo '{{{data.badgeSvg}}}';
				}
				?>
			</div>
			<div class="yith-wcbm-badge-text-advanced">
				<div class="yith-wcbm yith-wcbm-symbol-sale"><?php echo wp_kses_post( $badge_info['labels']['on_sale'] ); ?></div>
				<div class="yith-wcbm yith-wcbm-symbol-sale-exclamation"><?php echo wp_kses_post( $badge_info['labels']['sale'] ); ?></div>
				<div class="yith-wcbm yith-wcbm-symbol-percent"><?php echo wp_kses_post( $badge_info['labels']['percentage'] ); ?></div>
				<div class="yith-wcbm yith-wcbm-symbol-off"><?php echo wp_kses_post( $badge_info['labels']['off'] ); ?></div>
				<div class="yith-wcbm yith-wcbm-sale-percent"><?php echo wp_kses_post( $badge_info['sale_percentage'] ); ?></div>
				<?php if ( $badge_info['saved_display'] && $badge_info['saved_money'] > 0 ) : ?>
					<div class="yith-wcbm yith-wcbm-save"><?php echo wp_kses_post( sprintf( $badge_info['labels']['save_format'], $badge_info['labels']['save'], $badge_info['saved'] ) ); ?></div>
				<?php endif; ?>
				<div class="yith-wcbm yith-wcbm-saved-money"><?php echo wp_kses_post( sprintf( $badge_info['labels']['amount_saved_format'], $badge_info['saved_money'], $badge_info['currency'] ) ); ?></div>
				<div class="yith-wcbm yith-wcbm-saved-percentage"><?php echo wp_kses_post( sprintf( $badge_info['labels']['percentage_saved_format'], $badge_info['sale_percentage'], $badge_info['labels']['percentage'] ) ); ?></div>
				<div class="yith-wcbm yith-wcbm-saved-money-value"><?php echo wp_kses_post( $badge_info['saved_money'] ); ?></div>
				<div class="yith-wcbm yith-wcbm-saved-money-currency"><?php echo wp_kses_post( $badge_info['currency'] ); ?></div>
			</div>
		</div><!--yith-wcbm-badge__wrap-->
	</div>
<?php endif; ?>
