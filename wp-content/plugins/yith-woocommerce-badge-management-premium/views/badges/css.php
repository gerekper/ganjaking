<?php
/**
 * Css Badge Template
 *
 * @var int             $css_badge_style The CSS badge style.
 * @var YITH_WCBM_Badge $badge           The badge object.
 * @var WC_Product      $product         The product.
 * @var bool            $is_preview      Is preview.
 * @var bool            $is_template     Is template.
 *
 * @package YITH\BadgeManagementPremium\Views\Badges
 */

$is_preview  = isset( $is_preview ) && $is_preview;
$is_template = isset( $is_template ) && $is_template;

$classes = array(
	$is_template ? '{{data.classes}}' : esc_attr( $badge->get_classes( $product ) ),
	'yith-wcbm-badge-css-' . ( $is_template ? '{{data.style}}' : $badge->get_css_id() ),
	'yith-wcbm-css-badge-' . ( $is_template ? '{{data.id}}' : $badge->get_id() ),
	'yith-wcbm-badge-' . ( $is_template ? '{{data.id}}' : $badge->get_id() ),
);
$text    = $is_template ? '{{{data.text}}}' : $badge->get_text( 'view', $is_preview || $is_template ? 'template' : $product );

$has_unsolved_placeholders = count(
	array_filter(
		array_map(
			function ( $placeholder ) use ( $text ) {
				return strpos( $text, '{{' . $placeholder . '}}' ) !== false;
			},
			array_keys( yith_wcbm_get_badges_placeholders( 'template' ) )
		)
	)
);

if ( $has_unsolved_placeholders ) {
	return;
}

?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-transform='<?php echo $is_template ? '' : esc_attr( $badge->get_transform_css() ); ?>'>
	<div class='yith-wcbm-badge__wrap'>
		<div class="yith-wcbm-css-s1"></div>
		<div class="yith-wcbm-css-s2"></div>
		<div class="yith-wcbm-css-text">
			<div class="yith-wcbm-badge-text"><?php echo $is_template ? '{{{data.text}}}' : wp_kses_post( $text ); ?></div>
		</div>
	</div><!--yith-wcbm-badge__wrap-->
	<?php

	if ( ! $is_template ) {
		$args = array(
			'badge' => $badge,
			'style' => $badge->get_css(),
			'type'  => 'css',
		);
		echo yith_wcbm_get_badge_svg( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
		echo '{{{data.badgeSvg}}}';
	}
	?>
</div>
