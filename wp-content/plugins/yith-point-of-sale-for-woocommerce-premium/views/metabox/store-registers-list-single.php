<?php
global $register, $register_id;
$default_toggle_class = is_numeric( $register_id ) ? 'yith-pos-settings-box--closed' : '';
$field_name_prefix    = "register-{$register_id}_";
$options              = yith_pos_get_register_options( $register );
?>


<div class="yith-pos-store-register yith-pos-settings-box <?php echo $default_toggle_class ?>" data-field-name-prefix="<?php echo $field_name_prefix ?>" data-register-id="<?php echo $register_id ?>">
    <div class="yith-pos-settings-box__header">
        <span class="yith-pos-settings-box__title"><?php echo $register->get_name( 'edit' ) ?></span>
        <span class="yith-pos-settings-box__toggle"><span class="dashicons dashicons-arrow-up-alt2"></span></span>
        <span class="yith-pos-settings-box__enabled">
                        <?php
                        yith_plugin_fw_get_field( array(
	                                                  'id'    => 'yith-pos-store-register-enabled-' . $register_id,
	                                                  'class' => 'yith-pos-register-toggle-enabled',
	                                                  'type'  => 'onoff',
	                                                  'value' => $register->get_enabled( 'edit' ),
	                                                  'data'  => array(
		                                                  'register-id' => $register->get_id(),
		                                                  'security'    => wp_create_nonce( 'register-toggle-enabled' )
	                                                  )
                                                  ), true, false )
                        ?>
                    </span>
    </div>
    <div class="yith-pos-settings-box__content">
		<?php foreach ( $options as $option_key => $option ): ?>
			<?php
			$label             = isset( $option[ 'label' ] ) ? $option[ 'label' ] : '';
			$extra_class       = ! empty( $option[ 'required' ] ) ? 'yith-plugin-fw--required' : '';
			$getter            = 'get_' . $option_key;
			$default           = isset( $option[ 'std' ] ) ? $option[ 'std' ] : '';
			$value             = is_callable( array( $register, $getter ) ) ? $register->$getter( 'edit' ) : $default;
			$container_class   = 'yith-pos-store-register__' . $option_key . '-field-container';
			$option[ 'value' ] = $value;
			$option[ 'id' ]    = $field_name_prefix . $option_key;
			$option[ 'name' ]  = $option[ 'id' ];

			if ( isset( $option[ 'deps' ], $option[ 'deps' ][ 'id' ] ) ) {
				$option[ 'deps' ][ 'id' ] = "register-{$register_id}" . $option[ 'deps' ][ 'id' ];
			}
			if ( isset( $option[ 'type' ] ) && 'hidden' === $option[ 'type' ] ) {
				$extra_class .= ' yith-pos-settings-box__content__row--hidden ';
			}
			$container_class .= ' ' . $extra_class;
			?>
            <div class="yith-pos-settings-box__content__row <?php echo $container_class ?>" <?php echo yith_field_deps_data( $option ); ?>>
                <div class="yith-pos-settings-box__content__row__label"><?php echo $label ?></div>
                <div class="yith-pos-settings-box__content__row__field">
					<?php yith_plugin_fw_get_field( $option, true, false ); ?>

					<?php if ( isset( $option[ 'desc' ] ) ): ?>
                        <span class="description"><?php echo $option[ 'desc' ] ?></span>
					<?php endif; ?>
                </div>

            </div>
		<?php endforeach; ?>
        <div class="yith-pos-settings-box__actions">
            <span class="yith-pos-register-delete yith-pos-big-button button-secondary yith-remove-button"><?php _e( 'Delete', 'yith-point-of-sale-for-woocommerce' ) ?></span>
            <span class="yith-pos-register-update yith-pos-big-button yith-update-button"><?php _e( 'Update', 'yith-point-of-sale-for-woocommerce' ) ?></span>
            <span class="yith-pos-register-create yith-pos-big-button yith-save-button"><?php _e( 'Create', 'yith-point-of-sale-for-woocommerce' ) ?></span>
        </div>
    </div>
</div>