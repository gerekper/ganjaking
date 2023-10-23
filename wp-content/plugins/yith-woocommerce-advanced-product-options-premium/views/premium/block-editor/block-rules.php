<?php
/**
 * Block Rules Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var YITH_WAPO_Block $block
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$show_in                 = $block->get_rule( 'show_in' );

$show_show_in_products   = ( 'categories' !== $show_in && 'all' !== $show_in && '' !== $show_in ) || isset( $_REQUEST['block_rule_show_in'] ) && 'products' === $_REQUEST['block_rule_show_in'];
$show_show_in_categories = 'categories' === $show_in;

$show_exclude_products            = 'all' === $show_in || 'products' === $show_in || 'categories' === $show_in;
$show_exclude_products_products   = $block->get_rule( 'exclude_products' ) === 'yes';
$show_exclude_products_categories = $block->get_rule( 'exclude_products' ) === 'yes';

?>

<div id="block-rules">

	<!-- Option field -->
	<div class="field-wrap">
		<label for="yith-wapo-block-rule-show-in"><?php
            // translators: [ADMIN] Edit block page
            echo esc_html__( 'Show this block of options in', 'yith-woocommerce-product-add-ons' ); ?>:</label>
		<div class="field block-option">
			<?php

                $block_rule_show_in = 'all';

                if ( ! empty( $block->get_rule( 'show_in' ) ) ) {
                    $block_rule_show_in = $block->get_rule( 'show_in' );
                } elseif ( isset( $_REQUEST['block_rule_show_in'] ) && ! empty( $_REQUEST['block_rule_show_in'] ) ) {
                    $block_rule_show_in = $_REQUEST['block_rule_show_in'];
                }


				yith_plugin_fw_get_field(
					array(
						'id'      => 'yith-wapo-block-rule-show-in',
						'name'    => 'block_rule_show_in',
						'type'    => 'select',
						'value'   => $block_rule_show_in,
						'options' => array(
                            // translators: [ADMIN] Edit block page
							'all'      => __( 'All products', 'yith-woocommerce-product-add-ons' ),
                            // translators: [ADMIN] Edit block page
                            'products' => __( 'Specific products or categories', 'yith-woocommerce-product-add-ons' ),
						),
						'default' => 'all',
						'class'   => 'wc-enhanced-select',
					),
					true
				);
				?>
			<span class="description"><?php
                // translators: [ADMIN] Edit block page
                echo esc_html__( 'Choose to show these options in all products or only specific products or categories.', 'yith-woocommerce-product-add-ons' ); ?></span>
		</div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap yith-wapo-block-rule-show-in-products" style="<?php echo $show_show_in_products ? '' : 'display: none;'; ?>">
		<label for="yith-wapo-block-rule-show-in-products">
            <?php
            // translators: [ADMIN] Edit block page.
            echo esc_html__( 'Show in products',  'yith-woocommerce-product-add-ons' ); ?>:</label>
		<div class="field block-option">
			<?php
            $show_in_products = $block->get_rule( 'show_in_products' );

            if ( empty( $show_in_products ) && isset( $_REQUEST['block_rule_show_in_products'] ) ) {
                $show_in_products = $_REQUEST['block_rule_show_in_products'] ?? '';
            }
				yith_plugin_fw_get_field(
					array(
						'id'       => 'yith-wapo-block-rule-show-in-products',
						'name'     => 'block_rule_show_in_products',
						'type'     => 'ajax-products',
						'multiple' => true,
						'value'    => $show_in_products,
						'data'     => array(
							'action'   => 'woocommerce_json_search_products_and_variations',
							'security' => wp_create_nonce( 'search-products' ),
							'limit'    => apply_filters( 'yith_wapo_show_in_products_limit', 30 ),
						),
					),
					true
				);
				?>
			<span class="description"><?php
                // translators: [ADMIN] Edit block page.
                echo esc_html__( 'Choose in which products to show this block.', 'yith-woocommerce-product-add-ons' ); ?></span>
		</div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap yith-wapo-block-rule-show-in-products" style="<?php echo $show_show_in_products ? '' : 'display: none;'; ?>">
		<label for="yith-wapo-block-rule-show-in-categories"><?php
            // translators: [ADMIN] Edit block page
            echo esc_html__( 'Show in categories',  'yith-woocommerce-product-add-ons' ); ?>:</label>
		<div class="field block-option">
			<?php
            $show_in_categories = $block->get_rule( 'show_in_categories' );

            if ( empty( $show_in_categories ) && isset( $_REQUEST['block_rule_show_in_categories'] ) ) {
                $show_in_categories = $_REQUEST['block_rule_show_in_categories'] ?? '';
            }
				yith_plugin_fw_get_field(
					array(
						'id'       => 'yith-wapo-block-rule-show-in-categories',
						'name'     => 'block_rule_show_in_categories',
						'type'     => 'ajax-terms',
						'multiple' => true,
						'value'    => $show_in_categories,
						'data'     => array(
                            // translators: [ADMIN] Edit block page
                            'placeholder' => __( 'Search for categories', 'yith-woocommerce-product-add-ons' ) . '&hellip;',
							'taxonomy'    => 'product_cat',
						),
					),
					true
				);
				?>
			<span class="description">
                <?php
                // translators: [ADMIN] Edit block page
                echo esc_html__( 'Choose in which product categories to show this block.', 'yith-woocommerce-product-add-ons' ); ?></span>
		</div>
	</div>
	<!-- End option field -->

    <?php //TODO: change next fields to a premium view ?>

    <!-- Option field -->
    <div class="field-wrap yith-wapo-block-rule-exclude-products" style="<?php echo $show_exclude_products ? '' : 'display: none;'; ?>">
        <label for="yith-wapo-block-rule-exclude-products">
            <?php
            // translators: '[ADMIN] Edit block page'
            echo esc_html__( 'Exclude products', 'yith-woocommerce-product-add-ons' ); ?>
        </label>
        <div class="field block-option">
            <?php
                yith_plugin_fw_get_field(
                    array(
                        'id'    => 'yith-wapo-block-rule-exclude-products',
                        'name'  => 'block_rule_exclude_products',
                        'type'  => 'onoff',
                        'value' => $block->get_rule( 'exclude_products' ),
                    ),
                    true
                );
            ?>
            <span class="description"><?php
                // translators: [ADMIN] Edit block page
                echo esc_html__( 'Enable if you want to hide these options in some products.', 'yith-woocommerce-product-add-ons' ); ?></span>
        </div>
    </div>
    <!-- End option field -->

    <!-- Option field -->
    <div class="field-wrap yith-wapo-block-rule-exclude-products-products" style="<?php echo $show_exclude_products_products ? '' : 'display: none;'; ?>">
        <label for="yith-wapo-block-rule-exclude-products-products"><?php
            // translators: [ADMIN] Edit block page
            echo esc_html__( 'Hide in products', 'yith-woocommerce-product-add-ons' ); ?>:</label>
        <div class="field block-option">
            <?php
                yith_plugin_fw_get_field(
                    array(
                        'id'       => 'yith-wapo-block-rule-exclude-products-products',
                        'name'     => 'block_rule_exclude_products_products',
                        'type'     => 'ajax-products',
                        'multiple' => true,
                        'value'    => $block->get_rule( 'exclude_products_products' ),
                        'data'     => array(
                            'action'   => 'woocommerce_json_search_products_and_variations',
                            'security' => wp_create_nonce( 'search-products' ),
                        ),
                    ),
                    true
                );
            ?>
            <span class="description"><?php
                // translators: [ADMIN] Edit block page
                echo esc_html__( 'Choose the products to exclude.','yith-woocommerce-product-add-ons' ); ?></span>
        </div>
    </div>
    <!-- End option field -->

    <!-- Option field -->
    <div class="field-wrap yith-wapo-block-rule-exclude-products-categories" style="<?php echo $show_exclude_products_categories ? '' : 'display: none;'; ?>">
        <label for="yith-wapo-block-rule-exclude-products-categories"><?php
            // translators: [ADMIN] Edit block page
            echo esc_html__( 'Hide in categories', 'yith-woocommerce-product-add-ons' ); ?>:</label>
        <div class="field block-option">
            <?php
                yith_plugin_fw_get_field(
                    array(
                        'id'       => 'yith-wapo-block-rule-exclude-products-categories',
                        'name'     => 'block_rule_exclude_products_categories',
                        'type'     => 'ajax-terms',
                        'multiple' => true,
                        'value'    => $block->get_rule( 'exclude_products_categories' ),
                        'data'     => array(
                            // translators: [ADMIN] Edit block page
                            'placeholder' => __( 'Search for categories', 'yith-woocommerce-product-add-ons' ) . '&hellip;',
                            'taxonomy'    => 'product_cat',
                        ),
                    ),
                    true
                );
            ?>
            <span class="description"><?php
                // translators: [ADMIN] Edit block page
                echo esc_html__( 'Choose the categories to exclude.', 'yith-woocommerce-product-add-ons' ); ?></span>
        </div>
    </div>
    <!-- End option field -->

    <!-- Option field -->
    <div class="field-wrap">
        <label for="yith-wapo-block-rule-show-to"><?php
            // translators: [ADMIN] Edit block page
            echo esc_html__( 'Show options to', 'yith-woocommerce-product-add-ons' ); ?>:</label>
        <div class="field block-option">
            <?php

            $block_rule_show_to = 'all';

            if ( ! empty( $block->get_rule( 'show_to' ) ) ) {
                $block_rule_show_to = $block->get_rule( 'show_to' );
            } elseif ( isset( $_REQUEST['block_rule_show_to'] ) && ! empty( $_REQUEST['block_rule_show_to'] ) ) {
                $block_rule_show_to = $_REQUEST['block_rule_show_to'];
            }

                global $wp_roles;
                $show_to_user_roles = array();
            foreach ( $wp_roles->roles as $key => $value ) {
                $show_to_user_roles[ $key ] = $value['name'];
            }

                $show_to_options_array = array(
                    // translators: [ADMIN] Edit block page
                    'all'          => __( 'All users', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Edit block page
                    'guest_users'  => __( 'Only to guest users', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Edit block page
                    'logged_users' => __( 'Only to logged-in users', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Edit block page
                    'user_roles'   => __( 'Only to specified user roles', 'yith-woocommerce-product-add-ons' ),
                );

                if ( function_exists( 'yith_wcmbs_get_plans' ) ) {
                    // translators: [ADMIN] Edit block page
                    $show_to_options_array['membership'] = __( 'Only to users with a membership plan', 'yith-woocommerce-product-add-ons' );
                    $plan_ids                            = yith_wcmbs_get_plans( array( 'fields' => 'ids' ) );
                    $plans                               = array_combine( $plan_ids, array_map( 'get_the_title', $plan_ids ) );
                }

                yith_plugin_fw_get_field(
                    array(
                        'id'      => 'yith-wapo-block-rule-show-to',
                        'name'    => 'block_rule_show_to',
                        'type'    => 'select',
                        'value'   => $block_rule_show_to,
                        'options' => $show_to_options_array,
                        'default' => 'all',
                        'class'   => 'wc-enhanced-select',
                    ),
                    true
                );
            ?>
            <span class="description"><?php
                // translators: [ADMIN] Edit block page
                echo esc_html__( 'Choose to show these options to all users, or only to specific user roles or members of a membership plan.', 'yith-woocommerce-product-add-ons' ); ?></span>
        </div>
    </div>
    <!-- End option field -->

    <!-- Option field -->
    <div class="field-wrap yith-wapo-block-rule-show-to-user-roles" style="<?php echo $block->get_rule( 'show_to' ) === 'user_roles' ? '' : 'display: none;'; ?>">
        <label><?php
            // translators: [ADMIN] Edit block page
            echo esc_html__( 'User roles', 'yith-woocommerce-product-add-ons' ); ?>:</label>
        <div class="field block-option">
            <?php
            yith_plugin_fw_get_field(
                array(
                    'id'      => 'yith-wapo-block-rule-show-to-user-roles',
                    'name'    => 'block_rule_show_to_user_roles',
                    'type'    => 'select-buttons',
                    'value'   => $block->get_rule( 'show_to_user_roles' ),
                    'options' => $show_to_user_roles,
                ),
                true
            );
            ?>
        </div>
    </div>
    <!-- End option field -->

    <?php if ( function_exists( 'yith_wcmbs_get_plans' ) ) : ?>
        <!-- Option field -->
        <div class="field-wrap yith-wapo-block-rule-show-to-membership" style="<?php echo $block->get_rule( 'show_to' ) === 'membership' ? '' : 'display: none;'; ?>">
            <label><?php
                // translators: [ADMIN] Edit block page - When Membership is activated
                echo esc_html__( 'Membership plan', 'yith-woocommerce-product-add-ons' ); ?>:</label>
            <div class="field block-option">
                <?php
                yith_plugin_fw_get_field(
                    array(
                        'id'      => 'yith-wapo-block-rule-show-to-membership',
                        'name'    => 'block_rule_show_to_membership',
                        'type'    => 'select',
                        'value'   => $block->get_rule( 'show_to_membership' ),
                        'options' => $plans,
                        'class'   => 'wc-enhanced-select',
                        // translators: When activate Membership and the option "Show options to:" on Edit Block page is "Only to users with a membership plan" and no membership has been selected yet.
                        'placeholder' => __( 'Search for a membership plan...', 'yith-woocommerce-product-add-ons' )
                    ),
                    true
                );
                ?>
                <span class="description"><?php
                    // translators: description in Block editor for Show options to: when 'membership plan' is selected (YITH Membership activated is necessary).
                    echo esc_html__( 'Choose for which membership plan to show this block.', 'yith-woocommerce-product-add-ons' ); ?>
                </span>
            </div>
        </div>
        <!-- End option field -->
    <?php endif; ?>

    <?php do_action( 'yith_wapo_after_block_rules' , $block ) ?>



</div>
