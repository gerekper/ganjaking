<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Awesome Icon Admin View
 *
 * @package YITH
 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
 * @since 1.0.0
 */




if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( isset( $field['label'] ) ) {
	$field['title'] = $field['label'];
}

$default_field = array(
	'id'    => '',
	'title' => isset( $field['name'] ) ? $field['name'] : '',
	'desc'  => '',
);
$field         = wp_parse_args( $field, $default_field );


?>



<div id="<?php echo esc_attr( $field['id'] ); ?>-container" <?php echo yith_field_deps_data( $field ); //phpcs:ignore ?> class="yith-plugin-fw-metabox-field-row">
	<?php
	// get the select field to choose how method use

	$select_field = array(
		'id'      => $field['id'] . '_select_icon_mode',
		'name'    => 'yit_metaboxes[' . $field['id'] . '][select]',
		'value'   => isset( $field['value']['select'] ) ? $field['value']['select'] : 'icon',
		'type'    => 'select',
		'options' => array(
			'icon'   => __( 'Theme Icon', 'yith-woocommerce-popup' ),
			'upload' => __( 'Custom Icon', 'yith-woocommerce-popup' ),
			'none'   => __( 'None', 'yith-woocommerce-popup' ),
		),
		'default' => 'icon',
	);
	yith_plugin_fw_get_field( $select_field, true );

	// get the icon field to show the icon list

	$icon_field = array(
		'type'         => 'icons',
		'id'           => $field['id'] . '_icon',
		'name'         => 'yit_metaboxes[' . $field['id'] . '][icon]',
		'value'        => isset( $field['value']['icon'] ) ? ypop_map_old_icon_with_new( $field['value']['icon'] ) : '',
		'filter_icons' => YITH_YPOP_SLUG,
		'default'      => 'retina-the-essentials-082',
	);
	yith_plugin_fw_get_field( $icon_field, true );


	// get the upload button to custom icon
	$button_field = array(
		'type'  => 'upload',
		'id'    => $field['id'] . '_custom',
		'name'  => 'yit_metaboxes[' . $field['id'] . '][custom]',
		'value' => isset( $field['value']['custom'] ) ? $field['value']['custom'] : '',

	);
	yith_plugin_fw_get_field( $button_field, true );

	?>
	<div class="clear"></div>
</div>

<script>

	jQuery(document).ready( function($){

		$('#_submit_button_icon_select_icon_mode').on('change', function(){

			var t       = $(this),
				container = t.parents('.yith-plugin-fw-iconlist-field-wrapper' ),
				value = t.val(),
				icon_wrapper = container.find( '.yith-plugin-fw-icons-field-wrapper' ),
				custom_wrapper = container.find( '.yith-plugin-fw-upload-field-wrapper' );


			if( 'icon' == value ){
				icon_wrapper.show();
				custom_wrapper.hide();

			}else if ( 'upload' == value ){
				icon_wrapper.hide();
				custom_wrapper.show();
			}else{
				icon_wrapper.hide();
				custom_wrapper.hide();
			}
		});

		$('#_submit_button_icon_select_icon_mode').trigger('change');


	});

</script>
