<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;

wp_enqueue_script( 'wc-enhanced-select' );
extract( $args );

$value = get_post_meta( $post->ID, $id, true );

if ( empty( $value ) ) {
	$value = array(
		array(
			'quantity' => 1,
			'days'     => array(
				array( 'enabled' => 'yes', 'value' => 0 , 'type' => 'discount' ),
				array( 'enabled' => 'yes', 'value' => 0, 'type' => 'discount' ),
				array( 'enabled' => 'yes', 'value' => 0 , 'type' => 'discount' ),
				array( 'enabled' => 'yes', 'value' => 0 , 'type' => 'discount' )
			)
		),
	);
}
$column       = 4;
$header_title = _x( 'Day %d', 'Part of Day 1', 'yith-woocommerce-delivery-date' );
$placeholder = __( 'Discount or Markup %', 'yith-woocommerce-delivery-date');

?>
<div id="<?php echo $id; ?>-container" class="yith-plugin-fw-metabox-field-row">
    <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
    <div class="ywcdd_quantity_table_container">
        <div class="ywcdd_table_wrap">
            <div class="ywcdd_quantity_table_content">
                <table>
                    <thead>
                    <tr>
                        <th class="ywcdd_quantity"><?php _e( 'Quantity', 'yith-woocommerce-delivery-date' ); ?></th>
						<?php
						for ( $i = 0; $i < $column; $i ++ ) {

							?>
                            <th><?php echo sprintf( $header_title, $i + 1 ); ?></th>
							<?php
						}
						?>
                        <th class="ywcdd_remove_row_column">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach ( $value as $i => $table ): ?>
                        <tr>
                            <td class="ywcdd_quantity">
								<?php
								$qyt_args = array(
									'type'  => 'number',
									'id'    => 'ywcdd_quantity_' . $i,
									'name'  => $name . "[$i][quantity]",
									'value' => $table['quantity'],
									'class' => 'ywcdd_quantity_field'
								);

								echo yith_plugin_fw_get_field( $qyt_args, true );
								?>

                            </td>
							<?php
							$days = $table['days'];

							foreach ( $days as $k => $day ) {

								$value_name  = $name . "[$i][days][$k][value]";
								$value_type_name  = $name . "[$i][days][$k][type]";
								$enable_name = $name . "[$i][days][$k][enabled]";

								$onoff_field = array(
									'type'    => 'select',
									'class'   => 'wc-enhanced-select ywcdd_enable_day yith-plugin-fw-select',
									'id'      => 'ywcdd_enable_day_' . $i . '_' . $k,
									'name'    => $enable_name,
									'options' => array(
										'yes' => __( 'Available', 'yith-woocommerce-delivery-date' ),
										'no'  => __( 'Unavailable', 'yith-woocommerce-delivery-date' ),
									),
									'value'   => $day['enabled']
								);

								$disable_class = 'no' == $day['enabled'] ? 'yith-disabled' : '';

								$value_type_field = array(
								        'id' => 'ywcdd_product_day_value_type_'.$i.'_'.$k,
                                        'type' => 'select',
                                        'class' => 'wc-enhanced-select ywcdd_day_value_type yith-plugin-fw-select '.$disable_class,
                                        'name' => $value_type_name,
                                        'options' => array(
                                                'discount' => '-',
                                                'discount_perc' => '-%',
                                            'markup' => '+',
                                            'markup_perc' =>'+%'
                                        ),
                                    'default' => 'discount',
                                    'value' => $day['type']

                                );
								$value_field   = array(
									'type'  => 'number',
									'class' => 'ywcdd_product_day_value ' . $disable_class,
									'id'    => 'ywcdd_product_day_' . $i . '_' . $k,
									'name'  => $value_name,
									'value' => isset( $day['value'] ) ? $day['value'] : '',
									'custom_attributes' => 'min = "0" max="100" step="any"',

								)
								?>
                                <td>
									<?php echo yith_plugin_fw_get_field( $onoff_field, true ); ?>
                                    <div class="yith-plugin-fw-field-wrapper ywcdd_product_quantity_value_wrapper">
									    <?php echo yith_plugin_fw_get_field( $value_type_field, true ); ?>
									    <?php echo yith_plugin_fw_get_field( $value_field, true ); ?>
                                    </div>
                                </td>
								<?php
							}
							?>

                            <td class="ywcdd_remove_row_column">
	                            <?php if( $i > 0):?>
                                <a href="" class="ywcdd_remove_row" title=""><span class="yith-icon icon-trash"></span></a>
                                <?php endif;?>
                            </td>

                        </tr>
					<?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td></td>
                        <td colspan="4">
                            <div class="ywcdd_add_new_row">
                                <button class="button-primary yith-add-button"><?php _e( 'Add row', 'yith-woocommerce-delivery-date' ); ?></button>
                            </div>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <script type="text/template" id="tmpl-ywcdd-product-table-row">
        <tr>
            <td class="ywcdd_quantity">
	            <?php
	            $qyt_args = array(
		            'type'  => 'number',
		            'id'    => 'ywcdd_quantity_{{{data.row}}}',
		            'name'  => $name . "[{{{data.row}}}][quantity]",
		            'value' => 1,
		            'class' => 'ywcdd_quantity_field'
	            );

	            echo yith_plugin_fw_get_field( $qyt_args, true );
	            ?>
            </td>
            <?php
                for( $i=0;$i<4;$i++){
	                $value_name  = $name . "[{{{data.row}}}][days][$i][value]";
	                $value_type_name  = $name . "[{{{data.row}}}][days][$i][type]";
	                $enable_name = $name . "[{{{data.row}}}][days][$i][enabled]";

	                $onoff_field = array(
		                'type'    => 'select',
		                'class'   => 'wc-enhanced-select ywcdd_enable_day yith-plugin-fw-select',
		                'id'      => 'ywcdd_enable_day_{{{data.row}}}_' . $i,
		                'name'    => $enable_name,
		                'options' => array(
			                'yes' => __( 'Available', 'yith-woocommerce-delivery-date' ),
			                'no'  => __( 'Unavailable', 'yith-woocommerce-delivery-date' ),
		                ),
		                'value'   => 'yes'
	                );

	                $value_type_field = array(
		                'id' => 'ywcdd_product_day_value_type_{{{data.row}}}_'.$i,
		                'type' => 'select',
		                'class' => 'wc-enhanced-select ywcdd_day_value_type yith-plugin-fw-select',
		                'name' => $value_type_name,
		                'options' => array(
			                'discount' => '-',
			                'discount_perc' => '-%',
			                'markup' => '+',
			                'markup_perc' =>'+%'
		                ),
		                'default' => 'discount',
		                'value' => $day['type']

	                );
	                $value_field   = array(
		                'type'  => 'number',
		                'class' => 'ywcdd_product_day_value',
		                'id'    => 'ywcdd_product_day_{{{data.row}}}_' . $i,
		                'name'  => $value_name,
		                'custom_attributes' => 'min="0" max="100" step="any"',
		                'value' => '',

	                )
	                ?>
                    <td>
		                <?php echo yith_plugin_fw_get_field( $onoff_field, true ); ?>
                        <div class="yith-plugin-fw-field-wrapper ywcdd_product_quantity_value_wrapper">
                            <?php echo yith_plugin_fw_get_field( $value_type_field, true ); ?>
                            <?php echo yith_plugin_fw_get_field( $value_field, true ); ?>
                        </div>
                    </td>

<?php         }
      ?>
            <td class="ywcdd_remove_row_column"><a href="" class="ywcdd_remove_row" title=""><span class="yith-icon icon-trash"></span></a></td>
        </tr>
    </script>
</div>
