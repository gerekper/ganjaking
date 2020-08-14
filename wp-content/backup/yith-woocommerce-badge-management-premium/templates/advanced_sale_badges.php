<?php
/**
 * @var WC_Product $product
 */

$advanced_badge = isset( $advanced_badge ) ? $advanced_badge : 'advanced';

$saved_display     = true;
$saved_money       = 0;
$saved_money_float = 0;
$sale_percentage   = 0;
$sale_percentage_float   = 0;
if ( 'preview' === $product_id ) {
	$sale_percentage   = 50;
	$sale_percentage_float   = 50;
	$saved_money       = 15;
	$saved_money_float = 15;
} else {
	if ( $product->is_type( 'variable' ) ) {

		$children = $product->get_children();

		$saved_money_array     = array();
		$sale_percentage_array = array();

		foreach ( $children as $child_id ) {
			$child = wc_get_product( $child_id );
			if ( ! $child ) {
				continue;
			}

			$price   = floatval( $child->get_price() );
			$regular = floatval( $child->get_regular_price() );
			if ( $price > 0 && $regular > 0 ) {
				$current_saved_money     = $regular - $price;
				$current_sale_percentage = ( $regular - $price ) / $regular * 100;
			} else {
				$current_saved_money     = 0;
				$current_sale_percentage = 0;
			}

			$saved_money_array[]     = $current_saved_money;
			$sale_percentage_array[] = $current_sale_percentage;
		}

		$unique_saved_money     = array_unique( $saved_money_array );
		$unique_sale_percentage = array_unique( $sale_percentage_array );

		$show_in_variables = get_option( 'yith-wcbm-show-advanced-badge-in-variable-products', 'same' );

		if ( count( $unique_sale_percentage ) == 1 ) {
			$sale_percentage_float = $sale_percentage_array[0];
		} else {
			switch ( $show_in_variables ) {
				case 'min':
					$sale_percentage_float = min( $sale_percentage_array );
					break;
				case 'max':
					$sale_percentage_float = max( $sale_percentage_array );

					break;
				default:
					// the badge will be shown only if the discount percentage will be the same for all variations
					$sale_percentage_float = 0;
					break;
			}
		}

		if ( count( $unique_saved_money ) == 1 ) {
			$saved_money_float = $unique_saved_money[0];
		} else {
			switch ( $show_in_variables ) {
				case 'min':
					$saved_money_float = min( $saved_money_array );
					break;
				case 'max':
					$saved_money_float = max( $saved_money_array );
					break;
				default:
					$saved_money_float = 0;
					break;
			}
		}
		$saved_money   = absint( $saved_money_float );
		$saved_display = $saved_money > 0;

	} else {
		$price         = floatval( $product->get_price() );
		$regular_price = floatval( $product->get_regular_price() );
		if ( $regular_price != 0 ) {
			$sale_percentage_float = ( $regular_price - $price ) / $regular_price * 100;
		} else {
			$sale_percentage_float = 0;
		}
		$saved_money_float = $regular_price - $price;
		$saved_money       = absint( round( $saved_money_float ) );
	}
	$saved_money = $saved_money ? yit_get_display_price( $product, $saved_money ) : 0;
}

$sale_percentage = intval( round( $sale_percentage_float ) );

$args              = array( 'decimals' => 0 );
$saved             = strip_tags( wc_price( $saved_money, $args ) );
$id_advanced_badge = ( isset( $id_advanced_badge ) ) ? '-' . $id_advanced_badge : '-advanced';

$display_class      = 'yith-wcbm-advanced-display-' . $advanced_display;
$badge_info         = (object) apply_filters( 'yith_wcbm_advanced_badge_info', array(
	'id'                => $id_badge,
	'advanced_badge_id' => $id_advanced_badge,
	'advanced_badge'    => $advanced_badge,
	'display_class'     => $display_class,
	'sale_percentage'   => $sale_percentage,
	'sale_percentage_float'   => $sale_percentage_float,
	'saved_display'     => $saved_display,
	'saved_money'       => $saved_money,
	'saved_money_float' => $saved_money_float,
	'saved'             => $saved,
	'currency'          => get_woocommerce_currency_symbol(),
	'labels'            => array(
		'on_sale'             => _x( 'On Sale', 'Text in badge: preserve lenght', 'yith-woocommerce-badges-management' ),
		'off'                 => _x( 'Off', 'Text in badge: preserve lenght', 'yith-woocommerce-badges-management' ),
		'percentage'          => '%',
		'save'                => _x( 'Save', 'Text in badge: preserve lenght', 'yith-woocommerce-badges-management' ),
		'save_format'         => _x( '%1$s %2$s', 'Text in badge(preserve lenght): Save $9', 'yith-woocommerce-badges-management' ),
		'sale'                => _x( 'Sale!', 'Text in badge: preserve lenght', 'yith-woocommerce-badges-management' ),
		'amount_saved_format' => _x( '%1$s%2$s', 'Text in badge(preserve lenght): 9 $', 'yith-woocommerce-badges-management' ),
	),
	'show'              => ( 'percentage' === $advanced_display && absint( $sale_percentage ) > 0 ) || ( 'amount' === $advanced_display && absint( $saved_money ) > 0 ),
), $product );
$deprecated_classes = "yith-wcbm-badge{$badge_info->advanced_badge_id} yith-wcbm-on-sale-badge{$badge_info->advanced_badge_id}";
if ( $badge_info->show ): ?>
    <div class="<?php echo $badge_classes ?> yith-wcbm-badge-advanced yith-wcbm-badge-advanced-<?php echo $badge_info->advanced_badge; ?> <?php echo $badge_info->display_class ?> <?php echo $deprecated_classes ?>" <?php echo $position_data_html ?>>
        <div class='yith-wcbm-badge__wrap'>
            <div class="yith-wcbm yith-wcbm-shape1"></div>
            <div class="yith-wcbm yith-wcbm-shape2"></div>
            <div class="yith-wcbm-badge-text-advanced">
                <div class="yith-wcbm yith-wcbm-simbol-sale"><?php echo $badge_info->labels['on_sale'] ?></div>
                <div class="yith-wcbm yith-wcbm-simbol-sale-exclamation"><?php echo $badge_info->labels['sale'] ?></div>
                <div class="yith-wcbm yith-wcbm-simbol-percent"><?php echo $badge_info->labels['percentage'] ?></div>
                <div class="yith-wcbm yith-wcbm-simbol-off"><?php echo $badge_info->labels['off'] ?></div>
                <div class="yith-wcbm yith-wcbm-sale-percent"><?php echo $badge_info->sale_percentage ?></div>
				<?php if ( $badge_info->saved_display && $badge_info->saved_money > 0 ): ?>
                    <div class="yith-wcbm yith-wcbm-save"><?php echo sprintf( $badge_info->labels['save_format'], $badge_info->labels['save'], $badge_info->saved ); ?></div>
				<?php endif; ?>
                <div class="yith-wcbm yith-wcbm-saved-money"><?php echo sprintf( $badge_info->labels['amount_saved_format'], $badge_info->saved_money, $badge_info->currency ); ?></div>
                <div class="yith-wcbm yith-wcbm-saved-money-value"><?php echo $badge_info->saved_money; ?></div>
                <div class="yith-wcbm yith-wcbm-saved-money-currency"><?php echo $badge_info->currency; ?></div>
            </div>
        </div><!--yith-wcbm-badge__wrap-->
    </div>
<?php endif; ?>