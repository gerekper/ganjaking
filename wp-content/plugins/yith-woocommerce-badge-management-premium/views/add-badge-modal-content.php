<?php
/**
 * Add Badge Modal Content
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagement\Views
 */

$types = array(
	'text'  => array(
		'title'       => __( 'Text badge', 'yith-woocommerce-badges-management' ),
		'description' => __( 'A plain text without background or inside a rectangle or circle shape', 'yith-woocommerce-badges-management' ),
		'icon'        => 'text',
	),
	'image' => array(
		'title'       => __( 'Image badge', 'yith-woocommerce-badges-management' ),
		'description' => __( 'Choose one of the badges in our library or upload a custom image', 'yith-woocommerce-badges-management' ),
		'icon'        => 'image',
	),
);

if ( defined( 'YITH_WCBM_PREMIUM' ) ) {
	$premium_types = array(
		'css'      => array(
			'title'       => __( 'CSS badge', 'yith-woocommerce-badges-management' ),
			'description' => __( 'Beautiful badges fully customizable by CSS', 'yith-woocommerce-badges-management' ),
			'icon'        => 'svg',
		),
		'advanced' => array(
			'title'       => __( 'Advanced badge for on sales products', 'yith-woocommerce-badges-management' ),
			'description' => __( 'A badge to show the discount percentage using the values in regular price and sale price for each product.', 'yith-woocommerce-badges-management' ),
			'icon'        => 'advanced',
		),
	);
	$types         = array_merge( $types, $premium_types );
}

$add_badge_link = add_query_arg(
	array(
		'post_type' => YITH_WCBM_Post_Types::$badge,
		'security'  => wp_create_nonce( 'yith_wcbm_create_badge' ),
	),
	admin_url( 'post-new.php' )
);
?>

<div class="yith-wcbm-badge-types">
	<?php foreach ( $types as $badge_type => $type_args ) : ?>
		<a href="<?php echo esc_url_raw( add_query_arg( array( 'badge-type' => $badge_type ), $add_badge_link ) ); ?>" class="yith-wcbm-badge-type yith-wcbm-badge-type__<?php echo esc_attr( $badge_type ); ?>">
			<div class="yith-wcbm-badge-type__icon <?php echo ! empty( $type_args['icon'] ) ? 'yith-wcbm-icon-' . esc_attr( $type_args['icon'] ) . '-badge' : ''; ?>"></div>
			<div class="yith-wcbm-badge-type__info">
				<div class="yith-wcbm-badge-type__title">
					<?php echo esc_html( $type_args['title'] ); ?>
				</div>
				<div class="yith-wcbm-badge-type__description">
					<?php echo esc_html( $type_args['description'] ); ?>
				</div>

			</div>
		</a>
	<?php endforeach; ?>
</div>
