<?php
/**
 * Text Badge Template
 *
 * @var WC_Product      $product    The Product.
 * @var YITH_WCBM_Badge $badge      Badge Object.
 * @var bool            $is_preview Is preview.
 *
 * @package YITH\BadgeManagementPremium\Views\Badges
 */

?>

<div class='<?php echo esc_attr( $badge->get_classes( $product ) . ' yith-wcbm-badge-' . $badge->get_type() ); ?>' data-position='<?php echo esc_attr( wp_json_encode( $badge->get_positions() ) ); ?>' data-transform='<?php echo esc_attr( $badge->get_transform_css() ); ?>'>
	<div class='yith-wcbm-badge__wrap'>
		<div class="yith-wcbm-badge-text"><?php echo wp_kses_post( $badge->get_text( 'view', $is_preview ? false : $product ) ); ?></div>
	</div>
</div>
<!--yith-wcbm-badge-->
