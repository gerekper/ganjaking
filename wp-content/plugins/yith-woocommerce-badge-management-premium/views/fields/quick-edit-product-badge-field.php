<?php
/**
 * Quick edit product badge field
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Views
 * @since   2.0
 */

?>
<div class="inline-edit-group yith-wcbm-inline-edit-col">
	<span class="title"><?php esc_html_e( 'Badges', 'yith-woocommerce-badges-management' ); ?></span>
	<?php
	$field = yith_plugin_fw_get_field(
		apply_filters( 'yith_wcbm_quick_edit_badge_field', array(
				'type'     => 'select',
				'data'     => array(
					'placeholder' => __( 'Search Badge...', 'yith-woocommerce-badges-management' ),
					'post_type'   => YITH_WCBM_Post_Types::$badge,
					'action'      => 'yith_plugin_fw_json_search_posts',
				),
				'name'     => 'yith_wcbm_quick_badge_ids',
				'id'       => 'yith_wcbm_quick_badge_ids',
				'multiple' => defined( 'YITH_WCBM_PREMIUM' ) && YITH_WCBM_PREMIUM,
				'options'  => array(),
			)
		),
		true,
		false
	);
	?>
</div>
