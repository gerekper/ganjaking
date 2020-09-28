<?php
/*
 * Template for Metabox Plan Item Order
 */

?>
    <ul style="width:600px; border-top:1px solid #ccc">
        <li id='yith-wcmbs-plan-item-text'
            class='yith-wcmbs-plan-item-text'><?php _e( 'Add Title', 'yith-woocommerce-membership' ) ?>
            <span class="yith-wcmbs-plan-item-text-description">
                <?php _e( 'Drag and drop this entry into item list', 'yith-woocommerce-membership' ) ?>
            </span>
        </li>
    </ul>
<?php
if ( !empty( $posts ) ) {
    echo "<ul id='yith-wcmbs-plan-item-order-container'>";
    $loop           = 0;
    $plan_post_cats = get_post_meta( $plan_id, '_post-cats', true );
    $plan_prod_cats = get_post_meta( $plan_id, '_product-cats', true );
    $plan_post_tags = get_post_meta( $plan_id, '_post-tags', true );
    $plan_prod_tags = get_post_meta( $plan_id, '_product-tags', true );
    $plan_post_cats = !empty( $plan_post_cats ) ? $plan_post_cats : array();
    $plan_prod_cats = !empty( $plan_prod_cats ) ? $plan_prod_cats : array();
    $plan_post_tags = !empty( $plan_post_tags ) ? $plan_post_tags : array();
    $plan_prod_tags = !empty( $plan_prod_tags ) ? $plan_prod_tags : array();

    $hidden_item_ids = (array)get_post_meta( $plan_id, '_yith_wcmbs_hidden_item_ids', true );

    foreach ( $posts as $value ) {
        if ( is_numeric( $value ) ) {
            $post_id        = absint( $value );
            $post_title     = get_the_title( $post_id );
            $post_type      = get_post_type( $post_id );
            $post_type_icon = '';

            switch ( $post_type ) {
                case 'attachment':
                    $post_type_icon = '<span class="dashicons dashicons-admin-media"></span>';
                    break;
                case 'post':
                    $post_type_icon = '<span class="dashicons dashicons-admin-post"></span>';
                    break;
                case 'page':
                    $post_type_icon = '<span class="dashicons dashicons-admin-page"></span>';
                    break;
                case 'product':
                    $post_type_icon = '<span class="dashicons dashicons-cart"></span>';
                    break;
            }

            $cats_and_tags_array = array();
            if ( $post_type == 'post' ) {
                $post_cats     = yith_wcmbs_get_post_term_ids( $post_id, 'category', array(), true );
                $this_cats_ids = array_intersect( $post_cats, (array)$plan_post_cats );

                if ( !empty( $this_cats_ids ) ) {
                    foreach ( $this_cats_ids as $cat_id ) {
                        $cats_and_tags_array[] = get_cat_name( $cat_id );
                    }
                }

                $post_tags       = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
                $this_tags       = array_intersect( $plan_post_tags, $post_tags );
                $this_tags_names = array();
                if ( !empty( $this_tags ) ) {
                    foreach ( $this_tags as $tag_id ) {
                        $t = get_tag( $tag_id );
                        if ( $t ) {
                            $this_tags_names[] = $t->name;
                        }
                    }
                }
                $cats_and_tags_array = array_merge( $cats_and_tags_array, $this_tags_names );

            } else if ( $post_type == 'product' ) {
                $prod_cats      = yith_wcmbs_get_post_term_ids( $post_id, 'product_cat', array(), true );
                $prod_cat_terms = wp_get_post_terms( $post_id, 'product_cat' );

                $this_cats_ids = array_intersect( $prod_cats, (array)$plan_prod_cats );

                if ( !empty( $this_cats_ids ) ) {
                    foreach ( $this_cats_ids as $cat_id ) {
                        $term                  = get_term_by( 'id', $cat_id, 'product_cat' );
                        $cats_and_tags_array[] = $term->name;
                    }
                }

                $product_tags    = wp_get_post_terms( $post_id, 'product_tag', array( 'fields' => 'ids' ) );
                $this_tags       = array_intersect( $plan_prod_tags, $product_tags );
                $this_tags_names = array();
                if ( !empty( $this_tags ) ) {
                    foreach ( $this_tags as $tag_id ) {
                        $t = get_term( $tag_id, 'product_tag' );
                        if ( $t ) {
                            $this_tags_names[] = $t->name;
                        }
                    }
                }
                $cats_and_tags_array = array_merge( $cats_and_tags_array, $this_tags_names );
            }

            $cats_and_tags    = '';
            $bg_color_classes = array(
                'orange-bg',
                'blue-bg',
                'yellow-bg',
                'green-bg',
                'violet-bg',
            );

            if ( !empty( $cats_and_tags_array ) ) {
                $loop_cat = 0;
                foreach ( $cats_and_tags_array as $label ) {
                    $index       = $loop_cat % ( count( $bg_color_classes ) );
                    $color_class = $bg_color_classes[ $index ];
                    $cats_and_tags .= "<span class='yith-wcmbs-plan-item-cats-and-tags-label {$color_class}'>$label</span>";
                    $loop_cat++;
                }
            }

            $restricted_access_plans = get_post_meta( $post_id, '_yith_wcmbs_restrict_access_plan', true );
            $del_from_plan           = '<span class="dashicons yith-wcmbs-right"></span>';
            if ( empty( $cats_and_tags_array ) && !empty( $restricted_access_plans ) && in_array( $plan_id, $restricted_access_plans ) ) {
                $del_from_plan_title = __( 'Remove item from plan', 'yith-woocommerce-membership' );
                $del_from_plan = "<span class='dashicons dashicons-no-alt yith-wcmbs-delete-from-plan yith-wcmbs-right'
                                data-post-id='$post_id' data-plan-id='$plan_id' title='$del_from_plan_title'></span>";
            }

            $restrict_access_plan_delay = get_post_meta( $post_id, '_yith_wcmbs_plan_delay', true );
            $delay_text                 = '';
            if ( !empty( $restrict_access_plan_delay[ $plan_id ] ) ) {
                $delay_days = $restrict_access_plan_delay[ $plan_id ];
                $delay_text = '<span class="yith-wcmbs-plan-item-delay">';
                $delay_text .= sprintf( _n( 'Delay Time: %s day', 'Delay Time: %s days', $delay_days ), $delay_days );
                $delay_text .= '</span>';

            }

            $hidden_input = "<input type='hidden' name='_yith_wcmbs_plan_items[]' value='{$value}' />";
            $edit_title   = __( 'Edit item', 'yith-woocommerce-membership' );
            $edit_link    = '<a target="_blank" class="yith-wcmbs-edit-post-link yith-wcmbs-right" href="' . get_edit_post_link( $post_id ) . '" title="' . $edit_title . '">' . '<span class="dashicons dashicons-admin-generic"></span>' . '</a>';

            $is_hidden                        = in_array( absint( $value ), $hidden_item_ids );
            $disabled                         = !$is_hidden ? ' disabled ' : '';
            $hidden_item_input                = "<input type='hidden' class='yith_wcmbs_hidden_item_ids' name='_yith_wcmbs_hidden_item_ids[]' value='{$value}' {$disabled} />";
            $hide_item_button_dashicons_class = !$is_hidden ? 'dashicons-visibility' : 'dashicons-hidden';
            $hide_item_button_title           = __( 'Show/hide item to Members', 'yith-woocommerce-membership' );
            $hide_item_button                 = "<span class='dashicons {$hide_item_button_dashicons_class} yith-wcmbs-right yith-wcmbs-hide-show-item' data-post-id='$post_id' title='{$hide_item_button_title}'></span>";

            echo "<li class='yith-wcmbs-plan-item' rel='{$loop}'>{$post_type_icon} {$post_title}{$del_from_plan}{$edit_link}{$hide_item_button}{$delay_text}{$cats_and_tags}{$hidden_input}{$hidden_item_input}</li>";

        } elseif ( is_string( $value ) ) {

            $text_input = "<input type='text' name='_yith_wcmbs_plan_items[]' value='{$value}' />";
            echo "<li class='yith-wcmbs-plan-item-text' rel='{$loop}'>{$text_input}<span class='dashicons dashicons-no-alt close'></span></li>";
        } else {
            continue;
        }
        $loop++;
    }
    echo '</ul>';
}
?>