<?php
/**
 * Addon Advanced Options Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var YITH_WAPO_Addon $addon
 * @var int $addon_id
 * @var string $addon_type
 * @var YITH_WAPO_Block $block
 * @var int $block_id
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$addon_type = isset( $_GET['addon_type'] ) ? $_GET['addon_type'] : $addon->type; //phpcs:ignore

?>

<div id="tab-advanced-settings" style="display: none;">
	<?php
	$options_configuration = $addon->get_options_configuration_array();
	$default_options       = get_default_configuration_options();

	foreach( $options_configuration as $config_id => $config_options ) {

		$config_options = array_merge( $default_options['parent'], $config_options );

		foreach( $config_options as $config_option_id => &$config_option_values ) {
			if ( 'field' === $config_option_id ) {
				foreach( $config_option_values as &$field_values ){
					$field_values = array_merge( $default_options['field'], $field_values );
				}
			}
		}

		yith_wapo_get_view(
			'addon-editor/addon-field.php',
			array(
				'addon'      => $addon,
				'addon_id'   => $addon_id,
				'addon_type' => $addon_type,
				'config_id'  => $config_id,
				'config_options' => $config_options,
			)
		);

	}
	?>
</div>
