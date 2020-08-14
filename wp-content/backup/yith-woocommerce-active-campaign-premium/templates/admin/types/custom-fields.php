<div class="form-table" data-id="<?php echo esc_attr( $id ) ?>">
    <div class="fields-cointainer">
        <div class="fields-header">
            <a href="#" id="add_field" class="button button-primary"><?php _e( '+ Add New Field', 'yith-woocommerce-active-campaign' ) ?></a>
            <span><?php _e( 'Add new fields from Active Campaign and customise the field name (label)', 'yith-woocommerce-active-campaign' ) ?></span>
        </div>
        <div class="fields-content">
			<?php
			if ( ! empty( $fields_options ) ) {
				$counter = 1;

				foreach ( $fields_options as $field ) {
					$args = array(
						'id'                 => $id,
						'item_id'            => $counter,
						'selected_list'      => $list_id,
						'selected_name'      => $field['name'],
						'selected_merge_var' => isset( $field['merge_var'] ) ? $field['merge_var'] : 'email',
						'removable'          => isset( $field['removable'] ) ? ( 'yes' == $field['removable'] ) : true
					);
					YITH_WCAC_Admin()->print_custom_fields_item( $args );
					$counter ++;
				}
			} else {
				YITH_WCAC_Admin()->print_custom_fields_item( array(
					'id'                 => $id,
					'item_id'            => 1,
					'selected_list'      => $list_id,
					'selected_name'      => '',
					'selected_merge_var' => 'email',
					'removable'          => ( $_REQUEST['tab'] == 'register' ) ? true : false
				) );
			}
			?>
        </div>
    </div>
</div>