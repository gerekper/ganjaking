<?php
/**
 * Badges Select on Product bulk editing
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Views
 * @since   2.0
 */

$field = array(
	'type'     => 'ajax-posts',
	'data'     => array(
		'placeholder' => __( 'Search Badge...', 'yith-woocommerce-badges-management' ),
		'post_type'   => YITH_WCBM_Post_Types::$badge,
	),
	'name'     => 'yith_wcbm_bulk_badge_ids',
	'multiple' => defined( 'YITH_WCBM_PREMIUM' ) && YITH_WCBM_PREMIUM,
);

?>

<label>
	<span class="title"><?php esc_html_e( 'Badge', 'yith-woocommerce-badges-management' ); ?></span>
	<?php yith_plugin_fw_get_field( $field, true, false ); ?>
</label>






