<?php
/**
 * Image Badge Template
 *
 * @var string          $image_alt Image Alt.
 * @var YITH_WCBM_Badge $badge     Badge Object.
 * @var WC_Product      $product   The product.
 *
 * @package YITH\BadgeManagementPremium\Views\Badges
 */

?>
<div class='<?php echo esc_attr( $badge->get_classes( $product ) ); ?>' data-position='<?php echo esc_attr( wp_json_encode( $badge->get_positions() ) ); ?>' data-transform='<?php echo esc_attr( $badge->get_transform_css() ); ?>'>
	<div class='yith-wcbm-badge__wrap'>
		<img src="<?php echo esc_url( $badge->get_image_url() ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>"/>
	</div>
</div>
<!--yith-wcbm-badge-->
