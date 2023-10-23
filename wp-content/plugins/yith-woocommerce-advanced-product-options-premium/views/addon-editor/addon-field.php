<?php
/**
 * Addon Advanced Options Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 4.0.0
 *
 * @var YITH_WAPO_Addon $addon
 * @var int $addon_id
 * @var string $addon_type
 * @var string $config_id
 * @var array $config_options
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.
?>

<!-- Option field -->
<div class="field-wrap <?php echo ! empty( $config_options['enabled-by'] ) ? 'enabled-by-' . esc_html( $config_options['enabled-by'] ) : ''; ?> <?php echo esc_html( $config_id . '-container' ?? '' ); ?> <?php echo ! empty( $config_options['field-wrap-class'] ) ? esc_attr( $config_options['field-wrap-class'] ) : '' ?>
">
	<div class="field <?php echo esc_html( $config_options['div-class'] ) ?> addon-field-grid">
        <label for="<?php echo esc_html( $config_id ) ?>"><?php echo esc_html( $config_options['title'] );?></label>
        <?php

        $fields = $config_options['field'];
        foreach( $fields as $field ) {
            $custom_message = $field['custom_message'] ?? '';
            ?>
            <div class="field__wrap <?php echo esc_html( $field['div-class'] ); ?> <?php echo ! empty( $custom_message ) ? 'custom-message' : '' ?>">
                <?php
                if ( ! empty( $field['title'] ) ) {
                    ?>
                    <small><?php echo esc_html( $field['title'] ); ?></small>
                    <?php
                }
                yith_plugin_fw_get_field(
                    array(
                        'id'      => $config_id,
                        'name'    => $field['name'],
                        'class'   => $field['class'],
                        'type'    => $field['type'],
                        'min'     => $field['min'],
                        'max'     => $field['max'],
                        'step'    => $field['step'],
                        'value'   => $field['value'],
                        'default' => $field['default'],
                        'options' => $field['options'],
                        'units'   => $field['units'],
                        'custom_message' => $custom_message
                    ),
                    true
                );
                ?>
            </div>
            <?php
        }
        ?>
        <span class="description"><?php echo wp_kses_post( $config_options['description'] ); ?></span>
	</div>
</div>
<!-- End option field -->
