<?php
// Exit if accessed directly
! defined( 'YITH_POS' ) && exit();

$args = $field;
extract( $field );

$value = !!$value && is_array($value) ? $value : array();
?>
<div id="<?php esc_attr_e( $id ); ?>-container" <?php echo yith_field_deps_data( $args ); ?>
     class="yith-plugin-fw-metabox-field-row">
    <span class="show_product_label"><?php echo __( 'Product to', 'yith-point-of-sale-for-woocommerce' ) ?></span>
    <span class="show_product_select"><?php yith_plugin_fw_get_field( array(
			'id'      => $id . "[type]",
			'name'     => $name . "[type]",
			'class'   => 'wc-enhanced-select',
			'type'    => 'select',
			'label'   => '',
			'options' => array(
				'include' => __( 'Include', 'yith-point-of-sale-for-woocommerce' ),
				'exclude' => __( 'Exclude', 'yith-point-of-sale-for-woocommerce' ),
			),
		    'value' => isset( $value['type'] ) ? $value['type'] : 'include'
		), true, false ); ?>
	</span>
    <span class="show_product_list"><?php yith_plugin_fw_get_field( array(
			'id'       => $id . "[products]",
			'name'     => $name . "[products]",
			'type'     => 'ajax-products',
			'multiple' => true,
			'label'    => '',
			'value'    => isset( $value['products'] ) ? $value['products'] : array()
		), true, false ); ?>
	</span>
</div>