<?php
/**
 * Addon Conditional Logic Template
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

$enable_rules                 = $addon->get_setting( 'enable_rules', 'no', false );
$enable_rules_variations      = $addon->get_setting( 'enable_rules_variations', 'no', false );
$conditional_logic_display    = $addon->get_setting( 'conditional_logic_display', 'show', false );
$conditional_rules_variations = $addon->get_setting( 'conditional_rule_variations', array() );
$conditional_set_conditions   = $addon->get_setting( 'conditional_set_conditions', '0' );
$conditional_logic_display_if = $addon->get_setting( 'conditional_logic_display_if', 'all', false );
$conditional_rule_addon       = (array) $addon->get_setting( 'conditional_rule_addon' );
$conditional_rule_addon_is    = $addon->get_setting( 'conditional_rule_addon_is' );

// translators: String displayed in the conditional logic selector.
$conditional_array  = array( 'empty' => __( 'Select an add-on', 'yith-woocommerce-product-add-ons' ) );

$show_in                      = $block->get_rule( 'show_in', 'all' );
$selected_products            = array();
$selected_categories          = array();
$original_selected_products   = ! empty( $block->get_rule( 'show_in_products' ) ) ? (array) $block->get_rule( 'show_in_products' ) : array();
$original_selected_categories = ! empty( $block->get_rule( 'show_in_categories' ) ) ? (array) $block->get_rule( 'show_in_categories' ) : array();
$has_categories               = 0;

$conditional_addons       = YITH_WAPO()->db->yith_wapo_get_addons_by_block_id( $block_id );
$total_conditional_addons = count( $conditional_addons );
if ( $total_conditional_addons > 0 ) {

    foreach ( $conditional_addons as $key => $conditional_addon ) {
        /**
         * @var YITH_WAPO_Addon $conditional_addon
         */

        // Return HTML Type Add-ons
        if ( str_starts_with( $conditional_addon->get_type(), 'html' ) ) {
            continue;
        }

        $current_addon_id = $conditional_addon->get_id();

        if ( $conditional_addon->get_id() !== $addon_id ) {
            // translators: When the add-on has no title on the conditional logic selector.
            $addon_title = ! empty( $conditional_addon->get_title() ) ? $conditional_addon->get_title() : __( 'Empty title', 'yith-woocommerce-product-add-ons' );
            if ( apply_filters( 'yith_wapo_show_id_in_conditional_addon_title', false ) ) {
                $addon_title = '#' . $conditional_addon->get_id() . ' - ' . $addon_title;
            }

            $conditional_array[$current_addon_id]['label'] = $addon_title;

            $options_total = is_array( $conditional_addon->options ) && isset( array_values( $conditional_addon->options )[0] ) ? count( array_values( $conditional_addon->options )[0] ) : 1;

            for ( $x = 0; $x < $options_total; $x++ ) {
                if ( isset( $conditional_addon->options['label'][ $x ] ) ) {

                    $option_name = $conditional_addon->options['label'][ $x ];
                    if ( apply_filters( 'yith_wapo_reduce_conditional_option_name', true ) && strlen( $option_name ) > 25 ) {
                        $option_name = substr( $option_name, 0, 22 ) . '...';
                    }
                    $conditional_array[$current_addon_id]['options'][ $conditional_addon->id . '-' . $x ] = ' - ' . $option_name;
                }
            }
        }
    }
}



/** Include specific variations to the select2 of the conditional logic */

if ( 'products' === $show_in && ! empty( $original_selected_products ) ) {
    $selected_products = $original_selected_products;
    foreach ( $selected_products as $index => $product_id ) {
        $product = wc_get_product( $product_id );
        if ( $product instanceof WC_Product_Variable ) {
            $variation_ids     = $product->get_children();
            $selected_products = array_merge( $selected_products, $variation_ids );
        }
        $selected_products[ $index ] = $product_id;
    }
}


if ( 'products' === $show_in && ! empty( $original_selected_categories ) ) {
    foreach ( $original_selected_categories as $index => $category_id ) {
        $category = get_term( $category_id, 'product_cat' );

        $product_ids_cat = get_posts(
            array(
                'post_type'   => 'product',
                'numberposts' => -1,
                'post_status' => 'publish',
                'fields'      => 'ids',
                'tax_query'   => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'id',
                        'terms'    => $category_id,
                        'operator' => 'IN',
                    ),
                ),
            )
        );
        foreach ( $product_ids_cat as $index => $product_id ) {
            $product = wc_get_product( $product_id );
            if ( $product instanceof WC_Product_Variable ) {
                $variation_ids     = $product->get_children();
                $selected_products = array_merge( $selected_products, $variation_ids );
            }
        }
    }
}

$selected_products = array_unique( $selected_products );

?>

<div id="tab-conditional-logic" style="display: none;">

    <!-- Option field -->
    <div class="field-wrap">
        <div class="field addon-field-grid">
            <label for="addon-enable-rules"><?php echo esc_html__( 'Set conditions to show or hide this set of options', 'yith-woocommerce-product-add-ons' ); ?></label>
            <?php
            yith_plugin_fw_get_field(
                array(
                    'id'    => 'addon-enable-rules',
                    'name'  => 'addon_enable_rules',
                    'class' => 'enabler',
                    'type'  => 'onoff',
                    'value' => $enable_rules,
                ),
                true
            );
            ?>
            <span class="description">
				<?php echo esc_html__( 'Enable to set rules to hide or show the options.', 'yith-woocommerce-product-add-ons' ); ?>
			</span>
        </div>
    </div>
    <!-- End option field -->

    <!-- Option field -->
    <div class="field-wrap enabled-by-addon-enable-rules">
        <div class="field addon-field-grid">
            <label for="addon-enable-rules-variations"><?php echo esc_html__( 'Show/hide on specific variations', 'yith-woocommerce-product-add-ons' ); ?></label>
            <?php
            yith_plugin_fw_get_field(
                array(
                    'id'    => 'addon-enable-rules-variations',
                    'name'  => 'addon_enable_rules_variations',
                    'class' => 'enabler',
                    'type'  => 'onoff',
                    'value' => $enable_rules_variations,
                ),
                true
            );
            ?>
            <span class="description">
				<?php echo esc_html__( 'Enable if you want to set rules to hide or show the options in specific product variations.', 'yith-woocommerce-product-add-ons' ); ?>
			</span>
        </div>
    </div>
    <!-- End option field -->

    <!-- Option field -->
    <div class="field-wrap enabled-by-addon-enable-rules addon-field-grid" style="display: none;">
        <label for="conditional-logic-rules"><?php echo esc_html__( 'Display rules', 'yith-woocommerce-product-add-ons' ); ?></label>

        <div id="conditional-display-rules">
            <div class="enabled-variations-container">
                <?php
                yith_plugin_fw_get_field(
                    array(
                        'id'      => 'addon-conditional-logic-display',
                        'name'    => 'addon_conditional_logic_display',
                        'type'    => 'select',
                        'class'   => 'wc-enhanced-select show-hide-selector',
                        'value'   => $conditional_logic_display,
                        'options' => array(
                            'show' => esc_html__( 'Show', 'yith-woocommerce-product-add-ons' ),
                            'hide' => esc_html__( 'Hide', 'yith-woocommerce-product-add-ons' ),
                        ),
                    ),
                    true
                );
                ?>

                <div class="enabled-variations-in-container enabled-by-addon-enable-rules-variations">
                    <span class="enabled-variations-in"><?php echo esc_html__( 'in', 'yith-woocommerce-product-add-ons' ); ?></span>
                    <?php
                    yith_plugin_fw_get_field(
                        array(
                            'id'       => 'yith-wapo-conditional-logic-variations',
                            'name'     => 'addon_conditional_rule_variations',
                            'type'     => 'ajax-products',
                            'multiple' => true,
                            'value'    => $conditional_rules_variations,
                            'data'     => apply_filters('yith_wapo_conditional_logic_variation_data', array(
                                'action'       => 'woocommerce_json_search_products_and_variations',
                                'security'     => wp_create_nonce( 'search-products' ),
                                'exclude_type' => implode( ',', array_keys( wc_get_product_types() ) ),
                                'include'      => wp_json_encode( $selected_products ),
                                'placeholder'  => esc_html__( 'Search for a variation...', 'yith-woocommerce-product-add-ons' ),
                                'limit'        => apply_filters( 'yith_wapo_conditional_logic_variations_limit', 30 ),
                            ) ),
                        ),
                        true
                    );
                    ?>
                </div>

                <div class="enabled-variations-set-conditions enabled-by-addon-enable-rules-variations">
                    <?php
                    $set_conditions = '1' === $conditional_set_conditions ? 'yes' : 'no';

                    yith_plugin_fw_get_field(
                        array(
                            'id'    => 'enabled-variations-set-conditions',
                            'name'  => 'addon_conditional_set_conditions',
                            'type'  => 'checkbox',
                            'class' => 'set-conditions checkbox',
                            'value' => $set_conditions,
                        ),
                        true
                    );

                    ?>
                    <label for="enabled-variations-set-conditions" class="enabled-variations-set-conditions"><?php echo esc_html__( 'Set conditions', 'yith-woocommerce-product-add-ons' ); ?></label>
                </div>

            </div>
            <div class="enabled-variations-only-if-container">
                <span class="variations-only-if"><?php echo esc_html__( 'only if', 'yith-woocommerce-product-add-ons' ); ?></span>
                <?php
                yith_plugin_fw_get_field(
                    array(
                        'id'      => 'addon-conditional-logic-display-if',
                        'name'    => 'addon_conditional_logic_display_if',
                        'type'    => 'select',
                        'class'   => 'wc-enhanced-select',
                        'value'   => $conditional_logic_display_if,
                        'options' => array(
                            'all' => esc_html__( 'All of these rules', 'yith-woocommerce-product-add-ons' ),
                            'any' => esc_html__( 'Any of these rules', 'yith-woocommerce-product-add-ons' ),
                        ),
                    ),
                    true
                );
                ?>
                <span><?php echo esc_html__( 'match', 'yith-woocommerce-product-add-ons' ); ?>:</span>
            </div>
        </div>

        <div id="conditional-rules" data-addon-options="<?php echo esc_attr( json_encode( $conditional_array ) ); ?>">
            <?php
            $conditional_rules_count = count( $conditional_rule_addon );
            for ( $y = 0; $y < $conditional_rules_count; $y++ ) :
                $conditional_rule = isset( $conditional_rule_addon[ $y ] ) ? $conditional_rule_addon[ $y ] : '';
                ?>
                <div class="field rule">
                    <?php
                    yith_plugin_fw_get_field(
                        array(
                            'id'      => 'addon-conditional-rule-addon',
                            'name'    => 'addon_conditional_rule_addon[]',
                            'type'    => 'select',
                            'class'   => 'wc-enhanced-select addon-conditional-rule-addon',
                            'placeholder' => __( 'Select an add-on', 'yith-woocommerce-product-add-ons' ),
                            'value'   => $conditional_rule,
                            'options' => $conditional_array,
                        ),
                        true
                    );
                    ?>
                    <span class="is-selection"><?php echo esc_html__( 'is', 'yith-woocommerce-product-add-ons' ); ?></span>
                    <?php
                    // Todo: set the correct options depending on Add-on type selected.
                    yith_plugin_fw_get_field(
                        array(
                            'id'      => 'addon-conditional-rule-addon-is',
                            'name'    => 'addon_conditional_rule_addon_is[]',
                            'class'   => 'wc-enhanced-select addon-conditional-rule-addon-is',
                            'type'    => 'select',
                            // translators: String in the Conditional logic section (select, not select, empty, not empty).
                            'placeholder' => _x( 'Select an option', '[ADMIN] Translate in second person, not infinitive', 'yith-woocommerce-product-add-ons' ),
                            'value'   => isset( $conditional_rule_addon_is[ $y ] ) ? $conditional_rule_addon_is[ $y ] : '',
                            'options' => array(
                                ''             => '',
                                'selected'     => esc_html__( 'Selected', 'yith-woocommerce-product-add-ons' ),
                                'not-selected' => esc_html__( 'Not selected', 'yith-woocommerce-product-add-ons' ),
                                'empty'        => esc_html__( 'Empty', 'yith-woocommerce-product-add-ons' ),
                                'not-empty'    => esc_html__( 'Not empty', 'yith-woocommerce-product-add-ons' ),
                            ),
                        ),
                        true
                    );
                    ?>
                    <img src="<?php echo esc_attr( YITH_WAPO_URL ); ?>/assets/img/delete.png" class="delete-rule" style="width: 8px; height: 10px; padding: 0px 10px; cursor: pointer;">
                </div>
            <?php endfor; ?>
            <div id="add-conditional-rule"><a href="#">+ <?php echo esc_html__( 'Add rule', 'yith-woocommerce-product-add-ons' ); ?></a></div>
        </div>

    </div>
    <!-- End option field -->

</div>
