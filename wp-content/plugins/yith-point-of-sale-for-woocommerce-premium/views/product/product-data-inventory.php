<?php
/**
 * @var bool $multistock_enabled
 * @var string  $is_enabled is enabled? Values: yes | no
 * @var array $multistock
 */
$stores = yith_pos_get_stores( array( 'fields' => 'stores' ) );
$loop   = isset( $loop ) ? $loop : false;
?>
<div class="yith-pos-stock-management yith-plugin-ui">
    <h3><?php _e( 'POS Inventory', 'yith-point-of-sale-for-woocommerce' ) ?></h3>
	    <?php if( ! $multistock_enabled ): ?>
		<div class="multistock_info"><?php
			printf( '%s <a href="%s">%s</a>', __('To enable the multistock, it is necessary activate the option in ','yith-point-of-sale-for-woocommerce'), get_admin_url( null, 'admin.php?page=yith_pos_panel&tab=settings' ),__('YITH > Point of Sale > Customization > Enable multistock','yith-point-of-sale-for-woocommerce') ) ?></div>
	<?php else: ?>
        <div class="form-field menu_order_field ">
            <label for="menu_order"><?php _e( 'Enable Multi Stock in POS', 'yith-point-of-sale-for-woocommerce' ) ?></label>
			<?php echo yith_plugin_fw_get_field( array(
				                                     'type'  => 'onoff',
				                                     'id'    => $loop !== false ? "_yith_pos_multistock_enabled-{$loop}" : '_yith_pos_multistock_enabled',
				                                     'name'  => $loop !== false ? "_yith_pos_multistock_enabled[{$loop}]" : '_yith_pos_multistock_enabled',
				                                     'class' => 'yith-pos-product-multistock-enabled',
				                                     'value' => $is_enabled,
			                                     ) ) ?>
        </div>
	<?php if ( $stores ) : ?>
        <div class="yith-pos-product-multi-stock" data-list='<?php echo json_encode( $multistock ) ?>'>
            <div class="form-field menu_order_field ">
                <label for="menu_order"><?php _e( 'In store', 'yith-point-of-sale-for-woocommerce' ) ?></label>
                <div class="yith-pos-multistock-options">

					<?php
					$i = 0;
					if ( $multistock != '' ):
						foreach ( $multistock as $key => $single_store_stock ): ?>
                            <div class="yith-pos-group">
								<?php

								$name_store = $loop !== false ? "_yith_pos_multistock[{$loop}][{$i}][store]" : "_yith_pos_multistock[{$i}][store]";
								$name_stock = $loop !== false ? "_yith_pos_multistock[{$loop}][{$i}][stock]" : "_yith_pos_multistock[{$i}][stock]";
								$i ++;
								?>
                                <span class="store">
                                <select class="wc-enhanced-select" name="<?php echo $name_store ?>">
                                    <option value="0"><?php _e( "Select Store", 'yith-point-of-sale-for-woocommerce' ) ?></option>
								<?php foreach ( $stores as $store ):
									$selected = ( $key === $store->get_id() );
									?>
                                    <option value="<?php echo $store->get_id() ?>" <?php selected( $selected, true ) ?>><?php echo $store->get_name() ?></option>
								<?php endforeach; ?>
                            </select>
                                </span>
                                <span><?php _e( 'set a stock of: ', 'yith-point-of-sale-for-woocommerce' ) ?></span>
                                <span class="stock"><input name="<?php echo $name_stock ?>" type="number" min="0"
                                                           step="1"
                                                           value="<?php echo $single_store_stock ?>"/></span>
                                <span><?php _e( 'units', 'yith-point-of-sale-for-woocommerce' ) ?></span>
                                <span><i class="yith-icon yith-icon-trash"></i></span>
                            </div>
						<?php
						endforeach;
					endif;
					?>

                </div>
                <div class="add-new-row">
                    <a href="" class="clone-stock-group" data-max="<?php echo count( $stores ) ?>" data-loop="<?php echo $loop !== false ? $loop : '' ?>"><?php _e( '+ manage stock for another store', 'yith-point-of-sale-for-woocommerce' ) ?></a>
                    <script type="text/template" id="tmpl-yith-pos-stock-manager<?php echo $loop !== false ? $loop : '' ?>">
						<?php
						$name_store = $loop !== false ? "_yith_pos_multistock[{$loop}][{{data.id}}][store]" : "_yith_pos_multistock[{{data.id}}][store]";
						$name_stock = $loop !== false ? "_yith_pos_multistock[{$loop}][{{data.id}}][stock]" : "_yith_pos_multistock[{{data.id}}][stock]";
						?>
                        <div class="yith-pos-group" data-id="{{data.id}}">
                  <span class="store">
                       <select class="wc-enhanced-select" name="<?php echo $name_store ?>">
                           <option value="0"><?php _e( "Select Store", 'yith-point-of-sale-for-woocommerce' ) ?></option>
                           <?php foreach ( $stores as $store ): ?>
                               <option value="<?php echo $store->get_id() ?>"><?php echo $store->get_name() ?></option>
                           <?php endforeach; ?>
                       </select>
                   </span>
                            <span>set a stock of: </span>
                            <span class="stock"><input name="<?php echo $name_stock ?>" type="number" min="0" step="1" value=""></span>
                            <span>units</span>
                            <span><i class="yith-icon yith-icon-trash"></i></span>
                        </div>
                    </script>
                </div>
            </div>
        </div>

	<?php else: ?>
        <p><?php _e( 'No Store found!', 'yith-point-of-sale-for-woocommerce' ) ?></p>
	<?php endif ?>
		<?php endif ?>
</div>
