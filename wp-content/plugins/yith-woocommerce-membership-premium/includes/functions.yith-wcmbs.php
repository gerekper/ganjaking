<?php

if ( !function_exists( 'yith_wcmbs_user_has_membership_without_subscription' ) ) {
    /**
     * check if a user has at least one membership with subscription
     *
     * @param int $user_id the id of the user; if not setted get current user id
     * @return bool
     * @since 1.0.0
     */
    function yith_wcmbs_user_has_membership_without_subscription( $user_id = 0 ) {
        if ( $user_id == 0 ) {
            $user_id = get_current_user_id();
        }
        $member = YITH_WCMBS_Members()->get_member( $user_id );

        return $member->has_membership_without_subscription();
    }
}

if ( !function_exists( 'yith_wcmbs_get_membership_statuses' ) ) {

    /**
     * Return the list of status available
     *
     * @return array
     * @since 1.0.0
     */

    function yith_wcmbs_get_membership_statuses() {
        $options = array(
            'active'     => __( 'active', 'yith-woocommerce-membership' ),
            'paused'     => __( 'paused', 'yith-woocommerce-membership' ),
            'not_active' => __( 'suspended', 'yith-woocommerce-membership' ),
            'resumed'    => __( 'resumed', 'yith-woocommerce-membership' ),
            'expiring'   => __( 'expiring', 'yith-woocommerce-membership' ),
            'cancelled'  => __( 'cancelled', 'yith-woocommerce-membership' ),
            'expired'    => __( 'expired', 'yith-woocommerce-membership' ),
        );

        return apply_filters( 'yith_wcmbs_membership_statuses', $options );
    }
}


if ( !function_exists( 'yith_wcmbs_get_dates_customer_bought_product' ) ) {
    /**
     * Checks if a user (by email) has bought an item
     *
     * @param int   $user_id
     * @param int   $product_id
     * @param array $args
     * @return array|bool array of dates when customer bought the product; return false if customer didn't buy the product
     */
    function yith_wcmbs_get_dates_customer_bought_product( $user_id, $product_id, $args = array() ) {
        global $wpdb;

        $customer_data = array( $user_id );

        if ( $user_id ) {
            $user = get_user_by( 'id', $user_id );

            if ( isset( $user->user_email ) ) {
                $customer_data[] = $user->user_email;
            }
        }

        $customer_data = array_map( 'esc_sql', array_filter( array_unique( $customer_data ) ) );

        if ( sizeof( $customer_data ) == 0 ) {
            return false;
        }

        $limit = isset( $args[ 'limit' ] ) ? ( "LIMIT " . $args[ 'limit' ] ) : '';

        $results = $wpdb->get_results( $wpdb->prepare( "
				SELECT p.post_date FROM {$wpdb->posts} AS p
				INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
				INNER JOIN {$wpdb->prefix}woocommerce_order_items AS i ON p.ID = i.order_id
				INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
				WHERE p.post_status IN ( 'wc-completed', 'wc-processing' )
				AND pm.meta_key IN ( '_billing_email', '_customer_user' )
				AND im.meta_key IN ( '_product_id', '_variation_id' )
				AND im.meta_value = %d
				", $product_id ) . " AND pm.meta_value IN ( '" . implode( "','", $customer_data ) . "' )" . " ORDER BY p.post_date DESC " . $limit );

        $membership_dates = array();
        if ( !empty( $results ) ) {
            foreach ( $results as $r ) {
                $membership_dates[] = $r->post_date;
            }
        }

        $membership_dates = array_unique( $membership_dates );

        if ( !empty( $membership_dates ) && isset( $args[ 'limit' ] ) && $args[ 'limit' ] == 1 ) {
            return $membership_dates[ 0 ];
        }

        return !empty( $membership_dates ) ? $membership_dates : false;
    }
}

if ( !function_exists( 'yith_wcmbs_get_post_term_ids' ) ) {
    function yith_wcmbs_get_post_term_ids( $post_id, $taxonomy, $args = array(), $include_parents = false ) {
        if ( $include_parents ) {
            $args[ 'fields' ] = 'all';
            $terms            = wp_get_post_terms( $post_id, $taxonomy, $args );
            $terms_id         = array();
            foreach ( $terms as $term ) {
                $terms_id[] = $term->term_id;
                $parent     = $term->parent;
                while ( $parent != 0 ) {
                    $parent_term = get_term( $parent, $taxonomy );
                    if ( $parent_term ) {
                        $terms_id[] = $parent;
                        $parent     = $parent_term->parent;
                    }
                }
            }
            $terms_id = array_unique( $terms_id );

        } else {
            $args[ 'fields' ] = 'ids';
            $terms_id         = wp_get_post_terms( $post_id, $taxonomy, $args );
        }

        return $terms_id;
    }
}

if ( !function_exists( 'yith_wcmbs_get_hierarchicaly_terms' ) ) {
    function yith_wcmbs_get_hierarchicaly_terms( $taxonomy ) {

        $t = YITH_WCMBS()->wp->get_terms( array(
                                              'taxonomy'   => $taxonomy,
                                              'hide_empty' => false,
                                              'fields'     => 'id=>parent',
                                          ) );

        $t2 = YITH_WCMBS()->wp->get_terms( array(
                                               'taxonomy'   => $taxonomy,
                                               'hide_empty' => false,
                                               'fields'     => 'id=>name',
                                           ) );

        $terms = array();
        if ( $t && $t2 && !is_wp_error( $t ) && !is_wp_error( $t2 ) ) {
            foreach ( $t as $id => $parent ) {
                if ( !isset( $t2[ $id ] ) ) {
                    continue;
                }
                $name        = $t2[ $id ];
                $just_did_it = array( $id );
                while ( $parent != 0 && !in_array( $parent, $just_did_it ) ) {
                    $parent_name = isset( $t2[ $parent ] ) ? $t2[ $parent ] : '';
                    $name        = $parent_name . ' > ' . $name;

                    $just_did_it[] = $parent;
                    $parent        = isset( $t[ $parent ] ) ? $t[ $parent ] : 0;
                }
                $terms[ $id ] = $name;
            }
        }
        asort( $terms );

        return $terms;
    }
}

if ( !function_exists( 'yith_wcmbs_get_product_credits' ) ) {
    /**
     * get credits for a product
     * default 1
     *
     * @param int  $product_id
     * @param bool $check_downloadable if true check if product exists and if it's downloadable (Error return -1)
     * @return int
     */
    function yith_wcmbs_get_product_credits( $product_id = 0, $check_downloadable = false ) {
        if ( !$product_id ) {
            global $post;
            $product_id = $post instanceof WP_Post ? $post->ID : 0;
        }

        if ( !$product_id ) {
            return 0;
        }

        if ( $check_downloadable ) {
            $product = wc_get_product( $product_id );
            if ( !$product || !$product->is_downloadable() )
                return -1;
        }
        // Default 1
        $isset_credits = metadata_exists( 'post', $product_id, '_yith_wcmbs_credits' );
        if ( !$isset_credits )
            return 1;

        $credits = absint( get_post_meta( $product_id, '_yith_wcmbs_credits', true ) );

        return $credits;
    }
}

if ( !function_exists( 'yith_wcmbs_get_other_custom_post_types' ) ) {
    function yith_wcmbs_get_other_custom_post_types( $return = 'objects' ) {
        $post_types = get_post_types( array(
                                          '_builtin' => false,
                                      ), 'object' );

        $not_allowed_cpts = array(
            'product',
            'product_variation',
            'shop_order',
            'shop_order_refund',
            'shop_coupon',
            'shop_webhook',
            'ywcmbs-membership',
            'yith-wcmbs-thread',
            'yith-wcmbs-plan',
        );
        foreach ( $not_allowed_cpts as $na_cpt ) {
            if ( isset( $post_types[ $na_cpt ] ) ) {
                unset( $post_types[ $na_cpt ] );
            }
        }

        if ( $return === 'id=>name' ) {
            foreach ( $post_types as $id => $obj ) {
                $post_types[ $id ] = isset( $obj->labels->singular_name ) ? $obj->labels->singular_name : $id;
            }
        }

        return $post_types;
    }
}

if ( !function_exists( 'yith_wcmbs_user_has_membership' ) ) {

    /**
     * Return true if user has a membership plan active
     *
     * @param int       $user_id
     * @param int|array $plan_id
     * @return bool|YITH_WCMBS_Membership[]
     */
    function yith_wcmbs_user_has_membership( $user_id = 0, $plan_id = 0 ) {
        $has_membership = false;
        $user_id        = !!$user_id ? $user_id : get_current_user_id();

        $member = YITH_WCMBS_Members()->get_member( $user_id );

        if ( $member->is_valid() ) {
            if ( !$plan_id ) {
                $has_membership = $member->is_member();
            } else {
                if ( is_array( $plan_id ) ) {
                    foreach ( $plan_id as $the_id ) {
                        $has_membership = $member->has_active_plan( absint( $the_id ), false );
                        if ( $has_membership )
                            break;
                    }
                } else {
                    $has_membership = $member->has_active_plan( absint( $plan_id ), false );
                }
            }
        }

        return $has_membership;
    }
}

if ( !function_exists( 'yith_wcmbs_get_alternative_content' ) ) {
    function yith_wcmbs_get_alternative_content( $post_id ) {
        $alternative_content = get_post_meta( $post_id, '_alternative-content', true );
        if ( !$alternative_content ) {
            $alternative_content = get_option( 'yith-wcmbs-default-alternative-content', '' );
        }

        return apply_filters( 'yith_wcmbs_get_alternative_content', $alternative_content, $post_id );
    }
}

if ( !function_exists( 'yith_wcmbs_stylize_content' ) ) {
    function yith_wcmbs_stylize_content( $content ) {
        $content = do_shortcode( $content );
        $content = wptexturize( $content );
        $content = wpautop( $content );
        $content = shortcode_unautop( $content );
        $content = prepend_attachment( $content );
		$content = function_exists( 'wp_filter_content_tags' ) ? wp_filter_content_tags( $content ) : wp_make_content_images_responsive( $content );
        $content = convert_smilies( $content );

        return $content;
    }
}