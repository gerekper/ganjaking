<?php

if ( ! function_exists( 'yith_wcmbs_user_has_membership_without_subscription' ) ) {
	/**
	 * check if a user has at least one membership with subscription
	 *
	 * @param int $user_id the id of the user; if not setted get current user id
	 *
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

if ( ! function_exists( 'yith_wcmbs_get_membership_statuses' ) ) {

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


if ( ! function_exists( 'yith_wcmbs_get_dates_customer_bought_product' ) ) {
	/**
	 * Checks if a user (by email) has bought an item
	 *
	 * @param int   $user_id
	 * @param int   $product_id
	 * @param array $args
	 *
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

		$limit = isset( $args['limit'] ) ? ( "LIMIT " . $args['limit'] ) : '';

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
		if ( ! empty( $results ) ) {
			foreach ( $results as $r ) {
				$membership_dates[] = $r->post_date;
			}
		}

		$membership_dates = array_unique( $membership_dates );

		if ( ! empty( $membership_dates ) && isset( $args['limit'] ) && $args['limit'] == 1 ) {
			return $membership_dates[0];
		}

		return ! empty( $membership_dates ) ? $membership_dates : false;
	}
}

if ( ! function_exists( 'yith_wcmbs_get_post_term_ids' ) ) {
	function yith_wcmbs_get_post_term_ids( $post_id, $taxonomy, $args = array(), $include_parents = false ) {
		if ( $include_parents ) {
			$args['fields'] = 'all';
			$terms          = wp_get_post_terms( $post_id, $taxonomy, $args );
			$terms_id       = array();
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
			$args['fields'] = 'ids';
			$terms_id       = wp_get_post_terms( $post_id, $taxonomy, $args );
		}

		return $terms_id;
	}
}

if ( ! function_exists( 'yith_wcmbs_get_hierarchicaly_terms' ) ) {
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
		if ( $t && $t2 && ! is_wp_error( $t ) && ! is_wp_error( $t2 ) ) {
			foreach ( $t as $id => $parent ) {
				if ( ! isset( $t2[ $id ] ) ) {
					continue;
				}
				$name        = $t2[ $id ];
				$just_did_it = array( $id );
				while ( $parent != 0 && ! in_array( $parent, $just_did_it ) ) {
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

if ( ! function_exists( 'yith_wcmbs_get_product_credits' ) ) {
	/**
	 * get credits for a product
	 * default 1
	 *
	 * @param int  $product_id
	 * @param bool $check_downloadable if true check if product exists and if it's downloadable (Error return -1)
	 *
	 * @return int
	 */
	function yith_wcmbs_get_product_credits( $product_id = 0, $check_downloadable = false ) {
		if ( ! $product_id ) {
			global $post;
			$product_id = $post instanceof WP_Post ? $post->ID : 0;
		}

		if ( ! $product_id ) {
			return 0;
		}

		if ( $check_downloadable ) {
			$product = wc_get_product( $product_id );
			if ( ! $product || ! $product->is_downloadable() ) {
				return - 1;
			}
		}

		$isset_credits = metadata_exists( 'post', $product_id, '_yith_wcmbs_credits' );
		if ( ! $isset_credits ) {
			return get_option( 'yith-wcmbs-default-credits-for-product', 1 );
		}

		return apply_filters( 'yith_wcmbs_get_product_credits', absint( get_post_meta( $product_id, '_yith_wcmbs_credits', true ) ), $product_id );
	}
}

if ( ! function_exists( 'yith_wcmbs_get_other_custom_post_types' ) ) {
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

if ( ! function_exists( 'yith_wcmbs_user_has_membership' ) ) {

	/**
	 * Return true if user has a membership plan active
	 *
	 * @param int       $user_id
	 * @param int|array $plan_id
	 *
	 * @return bool|YITH_WCMBS_Membership[]
	 */
	function yith_wcmbs_user_has_membership( $user_id = 0, $plan_id = 0 ) {
		$has_membership = false;
		$user_id        = ! ! $user_id ? $user_id : get_current_user_id();

		$member = YITH_WCMBS_Members()->get_member( $user_id );

		if ( $member->is_valid() ) {
			if ( ! $plan_id ) {
				$has_membership = $member->is_member();
			} else {
				if ( is_array( $plan_id ) ) {
					foreach ( $plan_id as $the_id ) {
						$has_membership = $member->has_active_plan( absint( $the_id ), false );
						if ( $has_membership ) {
							break;
						}
					}
				} else {
					$has_membership = $member->has_active_plan( absint( $plan_id ), false );
				}
			}
		}

		return $has_membership;
	}
}

if ( ! function_exists( 'yith_wcmbs_get_alternative_content' ) ) {
	/**
	 * Retrieve the alternative content.
	 *
	 * @param int         $post_id         The post ID.
	 * @param string|null $default_content The default post-content. Useful since this pass through the 'the_content' filter.
	 *
	 * @return string
	 * @since 1.11.0 Added $default_content param.
	 */
	function yith_wcmbs_get_alternative_content( $post_id, $default_content = null ) {
		$alternative_content    = '';
		$alternative_content_id = false;
		$mode                   = metadata_exists( 'post', $post_id, '_alternative-content-mode' ) ? get_post_meta( $post_id, '_alternative-content-mode', true ) : 'set';
		if ( 'set' === $mode ) {
			$alternative_content = get_post_meta( $post_id, '_alternative-content', true );
		} else {
			$alternative_content_id = absint( get_post_meta( $post_id, '_alternative-content-id', true ) );
			if ( ! $alternative_content_id || YITH_WCMBS_Post_Types::$alternative_contents !== get_post_type( $alternative_content_id ) || 'publish' !== get_post_status( $alternative_content_id ) ) {
				$alternative_content_id = false;
			}
		}

		// Let's get the default values if the alternative content is not set
		if ( ! $alternative_content && ! $alternative_content_id ) {
			$default_mode = yith_wcmbs_settings()->get_option( 'yith-wcmbs-default-alternative-content-mode' );
			if ( 'set' === $default_mode ) {
				$alternative_content = yith_wcmbs_settings()->get_option( 'yith-wcmbs-default-alternative-content' );
			} else {
				$alternative_content_id = yith_wcmbs_settings()->get_option( 'yith-wcmbs-default-alternative-content-id' );
				if ( ! $alternative_content_id || YITH_WCMBS_Post_Types::$alternative_contents !== get_post_type( $alternative_content_id ) || 'publish' !== get_post_status( $alternative_content_id ) ) {
					$alternative_content_id = false;
				}
			}
		}

		if ( $alternative_content_id ) {
			$post                = get_post( $alternative_content_id );
			$alternative_content = $post->post_content;
		}

		$guest_content = yith_wcmbs_get_guest_content( $post_id, $default_content );
		if ( $guest_content ) {
			$alternative_content = str_replace( '<!--yith_wcmbs_alternative_content-->', $alternative_content, $guest_content );
		} else {
			$alternative_content = ! ! $alternative_content ? $alternative_content : __( 'You cannot access this content!', 'yith-woocommerce-membership' );
		}

		return apply_filters( 'yith_wcmbs_get_alternative_content', $alternative_content, $post_id );
	}
}

if ( ! function_exists( 'yith_wcmbs_get_guest_content' ) ) {
	/**
	 * Retrieve the guest content.
	 *
	 * @param int         $post_id         The post ID.
	 * @param string|null $default_content The default post-content. Useful since this pass through the 'the_content' filter.
	 *
	 * @return string
	 * @since 1.11.0 Added $default_content param.
	 */
	function yith_wcmbs_get_guest_content( $post_id, $default_content = null ) {
		$content = '';
		if ( yith_wcmbs_settings()->is_alternative_content_enabled() ) {
			if ( apply_filters( 'yith_wcmbs_get_guest_content_use_short_description_for_products', true, $post_id ) && 'product' === get_post_type( $post_id ) ) {
				$product      = wc_get_product( $post_id );
				$post_content = ! ! $product ? $product->get_short_description() : '';
			} else {
				$post = get_post( $post_id );
				if ( ! is_null( $default_content ) ) {
					$post_content = $default_content;
				} else {
					$post_content = ! ! $post ? $post->post_content : '';
				}
			}

			$has_block = has_block( 'yith/wcmbs-members-only-content-start', $post_content );

			$post_content = do_shortcode( $post_content );
			$post_content = do_blocks( $post_content );

			if ( preg_match( '/<!--yith_wcmbs_members_only_content_start(.*?)?-->/', $post_content, $matches ) ) {
				$content = explode( $matches[0], $post_content, 2 );
				$content = $content[0];

				if ( $has_block ) {
					// Remove the block delimiters.
					$content = preg_replace( '/<!-- \/?wp:yith\/wcmbs-members-only-content-start(.*?)?-->/', '', $content );
				}
			}
		}

		return $content;
	}
}

if ( ! function_exists( 'yith_wcmbs_is_default_alternative_content_set' ) ) {
	function yith_wcmbs_is_default_alternative_content_set() {
		static $alternative_content = null;
		if ( is_null( $alternative_content ) ) {
			$default_mode = yith_wcmbs_settings()->get_option( 'yith-wcmbs-default-alternative-content-mode' );
			if ( 'set' === $default_mode ) {
				$alternative_content = yith_wcmbs_settings()->get_option( 'yith-wcmbs-default-alternative-content' );
			} else {
				$alternative_content = yith_wcmbs_settings()->get_option( 'yith-wcmbs-default-alternative-content-id' );
				if ( ! $alternative_content || YITH_WCMBS_Post_Types::$alternative_contents !== get_post_type( $alternative_content ) || 'publish' !== get_post_status( $alternative_content ) ) {
					$alternative_content = false;
				}
			}
		}

		return ! ! $alternative_content;
	}
}

if ( ! function_exists( 'yith_wcmbs_stylize_content' ) ) {
	function yith_wcmbs_stylize_content( $content ) {
		global $wp_embed;
		// Apply the [embed] shortcode before.
		$content = ! ! $wp_embed && is_callable( array( $wp_embed, 'run_shortcode' ) ) ? $wp_embed->run_shortcode( $content ) : $content;

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

if ( ! function_exists( 'yith_wcmbs_get_view' ) ) {
	/**
	 * print a view
	 *
	 * @param string $view The view.
	 * @param array  $args The arguments.
	 *
	 * @since 1.4.0
	 */
	function yith_wcmbs_get_view( $view, $args = array() ) {
		$view_path = trailingslashit( YITH_WCMBS_VIEWS_PATH ) . $view;

		extract( $args );
		if ( file_exists( $view_path ) ) {
			include $view_path;
		}
	}
}

if ( ! function_exists( 'yith_wcmbs_get_specific_items_for_plan' ) ) {
	/**
	 * Retrieve specific items (products, pages or posts) set for a plan
	 *
	 * @param int    $plan_id   The plan ID.
	 * @param string $post_type The post type
	 *
	 * @return int[]
	 * @since 1.4.0
	 */
	function yith_wcmbs_get_specific_items_for_plan( $plan_id, $post_type ) {
		$items = get_posts(
			array(
				'posts_per_page' => - 1,
				'post_type'      => $post_type,
				'fields'         => 'ids',
				'meta_key'       => '_yith_wcmbs_restrict_access_plan',
				'meta_value'     => serialize( (string) $plan_id ),
				'meta_compare'   => 'LIKE',
			)
		);

		return ! ! $items ? $items : array();
	}
}

if ( ! function_exists( 'yith_wcmbs_get_plans' ) ) {
	/**
	 * Return the list of plans.
	 *
	 * @param array $args
	 *
	 * @return mixed|void
	 */
	function yith_wcmbs_get_plans( $args = array() ) {

		$defaults = array(
			'posts_per_page'             => - 1,
			'offset'                     => 0,
			'orderby'                    => 'date',
			'order'                      => 'DESC',
			'include'                    => '',
			'exclude'                    => '',
			'meta_key'                   => '',
			'meta_value'                 => '',
			'post_type'                  => YITH_WCMBS_Post_Types::$plan,
			'post_status'                => 'publish',
			'fields'                     => 'ids',
			'yith_wcmbs_suppress_filter' => true,
			'lang'                       => false   // support for Polylang
		);

		$args = wp_parse_args( $args, $defaults );

		$return_plans = 'plans' === $args['fields'];

		if ( $return_plans ) {
			$args['fields'] = 'ids';
		}

		$plans = get_posts( $args );

		if ( $return_plans ) {
			$plans = array_filter( array_map( 'yith_wcmbs_get_plan', $plans ) );
		}

		return apply_filters( 'yith_wcmbs_get_plans', $plans );
	}
}

if ( ! function_exists( 'yith_wcmbs_transient_enabled' ) ) {
	/**
	 * Return the list of plans.
	 *
	 * @param array $args
	 *
	 * @return mixed|void
	 * @since 1.4.0
	 */
	function yith_wcmbs_transient_enabled() {
		return defined( 'YITH_WCMBS_TRANSIENT_ENABLED' ) ? YITH_WCMBS_TRANSIENT_ENABLED : true;
	}
}

if ( ! function_exists( 'yith_wcmbs_get_membership' ) ) {
	/**
	 * Get the membership
	 *
	 * @param int|YITH_WCMBS_Membership $membership The Membership.
	 *
	 * @return false|YITH_WCMBS_Membership
	 * @since 1.4.0
	 */
	function yith_wcmbs_get_membership( $membership ) {
		$membership = $membership instanceof YITH_WCMBS_Membership ? $membership : new YITH_WCMBS_Membership( $membership );

		return $membership->is_valid() ? $membership : false;
	}
}

if ( ! function_exists( 'yith_wcmbs_get_memberships' ) ) {
	/**
	 * Retrieve memberships
	 *
	 * @param array $args
	 *
	 * @return array|int[]|YITH_WCMBS_Membership[]
	 */
	function yith_wcmbs_get_memberships( $args = array() ) {
		return YITH_WCMBS_Membership_Helper()->get_memberships( $args );
	}
}

if ( ! function_exists( 'yith_wcmbs_update_plans_meta_for_post' ) ) {
	/**
	 * Update the plans meta fora specific Post
	 *
	 * @param int         $post_id The post ID.
	 * @param int[]|false $plans   The array of plan IDs.
	 *
	 * @since 1.4.0
	 */
	function yith_wcmbs_update_plans_meta_for_post( $post_id, $plans ) {
		$plans = ! ! $plans && is_array( $plans ) ? array_filter( array_map( 'absint', $plans ) ) : false;
		if ( $plans ) {
			// Store the value as a serialized array of strings (instead of int).
			// This will prevent issues with queries to search through the serialize.
			$plans = array_map( 'strval', $plans );
			update_post_meta( $post_id, '_yith_wcmbs_restrict_access_plan', $plans );
		} else {
			delete_post_meta( $post_id, '_yith_wcmbs_restrict_access_plan' );
		}
	}
}

if ( ! function_exists( 'yith_wcmbs_get_plans_meta_for_post' ) ) {
	/**
	 * Retrieve the plans meta fora specific Post
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return int[] The array of the plan IDs.
	 * @since 1.4.0
	 */
	function yith_wcmbs_get_plans_meta_for_post( $post_id ) {
		$plans = get_post_meta( $post_id, '_yith_wcmbs_restrict_access_plan', true );
		$plans = ! ! $plans ? array_filter( array_map( 'absint', $plans ) ) : array();

		return $plans;
	}
}

if ( ! function_exists( 'yith_wcmbs_get_user_membership_discount' ) ) {
	/**
	 * Get the user membership discount
	 *
	 * @param bool|int $user_id
	 *
	 * @return int
	 * @since 1.4.0
	 */
	function yith_wcmbs_get_user_membership_discount( $user_id = false ) {
		static $discounts = array( 0 => 0 ); // guests will not have any discount
		$user_id = ! ! $user_id ? $user_id : get_current_user_id();
		if ( ! isset( $discounts[ $user_id ] ) ) {
			$discounts[ $user_id ] = false;
			$active_memberships    = yith_wcmbs_get_memberships( array(
																	 'user'        => $user_id,
																	 'active_only' => true,
																	 'return'      => 'memberships',
																 ) );

			$membership_discounts = array_map( function ( $membership ) {
				return $membership->has_discount() ? $membership->get_discount() : 0;
			}, $active_memberships );

			$membership_discounts  = array_filter( $membership_discounts );
			$discounts[ $user_id ] = ! ! $membership_discounts ? min( absint( max( $membership_discounts ) ), 100 ) : 0;
		}

		return $discounts[ $user_id ];
	}
}

if ( ! function_exists( 'yith_wcmbs_sanitize_protected_links' ) ) {
	/**
	 * Sanitize protected links removing empty ones
	 *
	 * @param array $links The links
	 *
	 * @return array
	 * @since 1.4.0
	 */
	function yith_wcmbs_sanitize_protected_links( $links ) {
		$links = is_array( $links ) ? $links : array();

		$links = array_filter( $links, function ( $link ) {
			return isset( $link['name'], $link['link'], $link['membership'] ) && ! ! $link['link'];
		} );

		$links = array_map( function ( $link ) {
			$link['name'] = ! ! $link['name'] ? $link['name'] : esc_attr_x( 'Download', 'Default download text for protected files', 'yith-woocommerce-membership' );

			return $link;
		}, $links );

		return $links;
	}
}

if ( ! function_exists( 'yith_wcmbs_get_protected_links' ) ) {
	/**
	 * Sanitize protected links removing empty ones
	 *
	 * @param int $post_id The post ID
	 *
	 * @return array
	 * @since 1.4.0
	 */
	function yith_wcmbs_get_protected_links( $post_id ) {
		$post_id         = absint( $post_id );
		$protected_links = array();
		if ( $post_id ) {
			$enabled = metadata_exists( 'post', $post_id, '_protected-files-enabled' ) ? get_post_meta( $post_id, '_protected-files-enabled', true ) : 'yes';
			if ( yith_plugin_fw_is_true( $enabled ) ) {
				$protected_links = get_post_meta( $post_id, '_yith_wcmbs_protected_links', true );
				$protected_links = yith_wcmbs_sanitize_protected_links( $protected_links );
			}
		}

		return $protected_links;
	}
}

if ( ! function_exists( 'yith_wcmbs_get_plans_including_all_posts' ) ) {
	/**
	 * Get plans that includes all posts/products
	 *
	 * @param string $post_type The post type.
	 * @param array  $args      Array of arguments
	 *
	 * @return array
	 * @since 1.4.0
	 */
	function yith_wcmbs_get_plans_including_all_posts( $post_type = 'post', $args = array() ) {
		$key = "_{$post_type}s_to_include";

		$args['meta_key']   = $key;
		$args['meta_value'] = 'all';

		$plans = yith_wcmbs_get_plans( $args );
		if ( $plans ) {
			$meta_query = array( 'relation' => 'OR' );
			foreach ( $plans as $plan_id ) {
				$meta_query[] = array(
					'key'     => '_linked-plans',
					'value'   => serialize( (string) $plan_id ),
					'compare' => 'LIKE',
				);
			}

			$parent_linked = yith_wcmbs_get_plans( array( 'meta_query' => $meta_query ) );

			if ( $parent_linked ) {
				$plans = array_unique( array_merge( $plans, $parent_linked ) );
			}
		}

		return $plans;
	}
}

if ( ! function_exists( 'yith_wcmbs_has_any_plan_with_all_posts_included' ) ) {
	/**
	 * Has some plan all posts/products included?
	 *
	 * @param string $post_type The post type
	 *
	 * @return bool
	 */
	function yith_wcmbs_has_any_plan_with_all_posts_included( $post_type = 'post' ) {
		static $plans = array();

		if ( ! isset( $plans[ $post_type ] ) ) {
			$plans[ $post_type ] = ! ! yith_wcmbs_get_plans_including_all_posts( $post_type, array( 'posts_per_page' => 1 ) );
		}

		return $plans[ $post_type ];
	}
}

if ( ! function_exists( 'yith_wcmbs_has_full_access' ) ) {
	/**
	 * Has full access?
	 *
	 * @param bool|int $user_id
	 *
	 * @return bool
	 */
	function yith_wcmbs_has_full_access( $user_id = false ) {
		$user_id = ! ! $user_id ? $user_id : get_current_user_id();

		$access = user_can( $user_id, 'create_users' );

		return apply_filters( 'yith_wcmbs_has_full_access', $access, $user_id );
	}
}

if ( ! function_exists( 'yith_wcmbs_has_full_access_to_all_posts' ) ) {
	/**
	 * Has full access to all posts?
	 *
	 * @param string   $post_type
	 * @param bool|int $user_id
	 *
	 * @return bool
	 */
	function yith_wcmbs_has_full_access_to_all_posts( $post_type = 'post', $user_id = false ) {
		static $full_access = array();
		$user_id = ! ! $user_id ? $user_id : get_current_user_id();

		if ( ! isset( $full_access[ $user_id ] ) ) {
			$access = yith_wcmbs_has_full_access( $user_id );
			if ( ! $access ) {
				$plans_including_all_posts = yith_wcmbs_get_plans_including_all_posts( $post_type );

				if ( $plans_including_all_posts && yith_wcmbs_user_has_membership( $user_id, $plans_including_all_posts ) ) {
					$access = true;
				}
			}

			$full_access[ $user_id ] = $access;

		}

		return $full_access[ $user_id ];
	}
}

if ( ! function_exists( 'yith_wcmbs_is_downloadable_product' ) ) {
	/**
	 * is this product downloadable?
	 *
	 * @param WC_Product $product
	 *
	 * @return bool
	 * @since 1.4.0
	 */
	function yith_wcmbs_is_downloadable_product( $product ) {
		$product      = wc_get_product( $product );
		$downloadable = false;
		if ( $product ) {
			if ( $product->is_type( 'variable' ) ) {
				$variations = $product->get_children();
				if ( ! empty( $variations ) ) {
					foreach ( $variations as $variation ) {
						$variation = wc_get_product( $variation );
						if ( $variation->is_downloadable() ) {
							$downloadable = true;
							break;
						}
					}
				}
			} else {
				$downloadable = $product->is_downloadable();
			}
		}

		return $downloadable;
	}
}


if ( ! function_exists( 'yith_wcmbs_is_blog_page' ) ) {

	/**
	 * is blog page?
	 *
	 * @return void
	 */
	function yith_wcmbs_is_blog_page() {
		global $wp_query;

		$queried_id = isset( $wp_query, $wp_query->queried_object, $wp_query->queried_object->ID ) ? absint( $wp_query->queried_object->ID ) : false;

		return $queried_id && $queried_id === absint( get_option( 'page_for_posts' ) );
	}

}

if ( ! function_exists( 'yith_wcmbs_get_term_taxonomy_ids' ) ) {

	/**
	 * Retrieve the term_taxonomy_ids from term_ids.
	 *
	 * @param array  $term_ids The term ids.
	 * @param string $taxonomy The taxonomy.
	 *
	 * @return array
	 * @since 1.4.9
	 */
	function yith_wcmbs_get_term_taxonomy_ids( $term_ids, $taxonomy ) {
		if ( ! $term_ids ) {
			return array();
		}
		$args = array(
			'get'                    => 'all',
			'number'                 => 0,
			'taxonomy'               => $taxonomy,
			'include'                => $term_ids,
			'update_term_meta_cache' => false,
			'orderby'                => 'none',
		);

		$term_query = new WP_Term_Query();
		$term_list  = $term_query->query( $args );

		return wp_list_pluck( $term_list, 'term_taxonomy_id' );
	}
}

if ( ! function_exists( 'yith_wcmbs_late_enqueue_assets' ) ) {
	/**
	 * Late enqueue scripts and styles.
	 *
	 * @param array $pages The page types (membership, widget, etc...).
	 *
	 * @since 1.4.11
	 */
	function yith_wcmbs_late_enqueue_assets( $pages = array() ) {
		YITH_WCMBS_Frontend()->late_enqueue_assets( $pages );
	}
}

if ( ! function_exists( 'yith_wcmbs_late_enqueue_assets' ) ) {
	/**
	 * Late enqueue scripts and styles.
	 *
	 * @param array $pages The page types (membership, widget, etc...).
	 *
	 * @since 1.4.11
	 */
	function yith_wcmbs_late_enqueue_assets( $pages = array() ) {
		YITH_WCMBS_Frontend()->late_enqueue_assets( $pages );
	}
}

if ( ! function_exists( 'yith_wcmbs_local_strtotime' ) ) {
	/**
	 * Return a timestamp adding the local timezone offset.
	 * Example:
	 * Now it's 01 Oct 01:00 UTC+2 = 30 Set 23:00 UTC
	 * The function will return the timestamp of 01 Oct 01:00 UTC
	 *
	 * @param string   $datetime       Datetime string (you can use the same param of strtotime).
	 * @param int|null $base_timestamp The base timestamp.
	 *
	 * @return int
	 * @since 1.18.0
	 */
	function yith_wcmbs_local_strtotime( string $datetime = 'now', int $base_timestamp = null ): int {
		$time = is_null( $base_timestamp ) ? strtotime( $datetime ) : strtotime( $datetime, $base_timestamp );

		return $time + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
	}
}

if ( ! function_exists( 'yith_wcmbs_local_strtotime_midnight_to_utc' ) ) {
	/**
	 * Return UTC timestamp of the local day midnight.
	 * Example:
	 * Now it's 01 Oct 01:00 UTC+2 = 30 Set 23:00 UTC
	 * The function will return the timestamp of 01 Oct 00:00 UTC+2 = 30 Set 22:00 UTC
	 *
	 * @param string   $datetime       Datetime string (you can use the same param of strtotime).
	 * @param int|null $base_timestamp The base timestamp.
	 *
	 * @return int
	 * @since 1.18.0
	 */
	function yith_wcmbs_local_strtotime_midnight_to_utc( string $datetime = 'now', int $base_timestamp = null ): int {
		$utc_midnight = strtotime( 'midnight', yith_wcmbs_local_strtotime( $datetime, $base_timestamp ) );

		return $utc_midnight - (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
	}
}

if ( ! function_exists( 'yith_wcmbs_date' ) ) {
	/**
	 * Format a date.
	 *
	 * @param int    $timestamp The timestamp to be formatted.
	 * @param string $format    The date format.
	 * @param bool   $gmt       GTM flag.
	 *
	 * @return string
	 * @since 1.18.0
	 */
	function yith_wcmbs_date( int $timestamp, string $format = '', bool $gmt = true ): string {
		$format = ! ! $format ? $format : wc_date_format();
		if ( $gmt ) {
			$timestamp += (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		}

		return date_i18n( $format, $timestamp );
	}
}
