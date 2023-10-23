<?php
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 4.0.0
 *
 * @var object $addon
 * @var string $addon_type
 * @var int    $index
 */

$show_number_option = $addon->get_option( 'show_number_option', $index, 'empty', false );
$number_limit       = $addon->get_option( 'number_limit', $index, 'no', false );

?>
    <!-- Option field -->
    <div class="field-wrap addon-field-grid">
        <label for="option-show-number-option"><?php
            // translators: [ADMIN] add-on option for Number in Populate Options tab.
            echo esc_html__( 'Show field', 'yith-woocommerce-product-add-ons' ); ?>:</label>
        <div class="field">
            <?php
            yith_plugin_fw_get_field(
                array(
                    'id'      => 'option-show-number-option',
                    'class'   => 'enabler wc-enhanced-select show_number_option',
                    'name'    => 'options[show_number_option][]',
                    'type'    => 'select',
                    'value'   => $show_number_option,
                    'default' => 'empty',
                    'options' => array(
                        // translators: [ADMIN] add-on option for Number in Populate Options tab
                        'empty'      => __( 'Empty by default', 'yith-woocommerce-product-add-ons' ),
                        // translators: [ADMIN] add-on option for Number in Populate Options tab
                        'default'    => __( 'With a default number', 'yith-woocommerce-product-add-ons' ),
                    ),
                ),
                true
            );
            ?>
        </div>
    </div>
    <!-- End option field -->
    <!-- Option field -->
    <div class="field-wrap show-number-option-default enabled-by-show-number-option-default addon-field-grid" style="<?php echo 'default' !== $show_number_option ? 'display:none' : '' ?>">
        <label for="option-default-number"><?php
            // translators: [ADMIN] add-on option for Number in Populate Options tab
            echo esc_html__( 'Default number', 'yith-woocommerce-product-add-ons' ); ?>:</label>
        <div class="field">
            <?php
            yith_plugin_fw_get_field(
                array(
                    'id'    => 'option-default-number'  . $index,
                    'class' => 'enabler default-number',
                    'name'  => 'options[default_number][]',
                    'type'  => 'number',
                    'value' => $addon->get_option( 'default_number', $index, '', false ),
                ),
                true
            );
            ?>
        </div>
    </div>
    <!-- End option field -->

    <!-- Option field -->
    <div class="field-wrap addon-field-grid">
        <label for="option-number-limit-<?php
        // translators: [ADMIN] add-on option for Number in Populate Options tab
        echo esc_html( $index ) ?>"><?php echo esc_html__( 'Set min/max number', 'yith-woocommerce-product-add-ons' ); ?>:</label>
        <div class="field">
            <?php
            yith_plugin_fw_get_field(
                array(
                    'id'    => 'option-number-limit-' . $index,
                    'class' => 'enabler',
                    'name'  => 'options[number_limit][]',
                    'type'  => 'onoff',
                    'value' => $number_limit,
                ),
                true
            );
            ?>
        </div>
    </div>
    <!-- End option field -->

    <!-- Option field -->
    <div class="field-wrap enabled-by-option-number-limit-<?php echo esc_html( $index ) ?> addon-field-grid" style="display: none">
        <label for="option-number-limit"><?php
            // translators: '[ADMIN] add-on option after activating "Set min/max number" for Number in Populate Options tab.
            echo esc_html__( 'Values allowed', 'yith-woocommerce-product-add-ons' ); ?>:</label>
        <div class="option-number-limit">
            <div class="field min-value">
                <small><?php
                    // translators: '[ADMIN] add-on option after activating "Set min/max number" for Number in Populate Options tab.
                    echo esc_html__( 'MIN', 'yith-woocommerce-product-add-ons' ); ?>
                </small>
                <input type="number" name="options[number_limit_min][]" id="option-number-limit-min" value="<?php echo esc_attr( $addon->get_option( 'number_limit_min', $index, '', false ) ); ?>">
            </div>
            <div class="field max-value">
                <small><?php
                    // translators: '[ADMIN] add-on option after activating "Set min/max number" for Number in Populate Options tab.
                    echo esc_html__( 'MAX', 'yith-woocommerce-product-add-ons' ); ?>
                </small>
                <input type="number" name="options[number_limit_max][]" id="option-number-limit-max" value="<?php echo esc_attr( $addon->get_option( 'number_limit_max', $index, '', false ) ); ?>">
            </div>
        </div>
    </div>
    <!-- End option field -->