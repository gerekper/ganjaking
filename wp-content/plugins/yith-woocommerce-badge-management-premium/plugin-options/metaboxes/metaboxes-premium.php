<?php
/**
 * Plugin Metaboxes
 *
 * @package YITH\BadgeManagementPremium\PluginOptions
 */

$metaboxes = array(
	'yith-wcbm-badge-metabox' => include 'product-badge-metabox.php',
	'yith-wcbm-metabox'       => include 'badge-metabox.php',
	'yith-wcbm-badge-rules'   => include 'badge-rule-metabox.php',
);

$metaboxes['yith-wcbm-metabox']['tabs']['badge-options']['fields'] = apply_filters( 'yith_wcbm_badge_metabox_fields', $metaboxes['yith-wcbm-metabox']['tabs']['badge-options']['fields'] );

return $metaboxes;
