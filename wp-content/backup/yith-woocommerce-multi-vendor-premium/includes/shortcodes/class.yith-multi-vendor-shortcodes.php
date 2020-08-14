<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WPV_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Multi_Vendor_Shortcodes
 * @package    Yithemes
 * @since      Version 2.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Multi_Vendor_Shortcodes' ) ) {
	/**
	 * Class YITH_Multi_Vendor_Shortcodes
	 *
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 */
	class YITH_Multi_Vendor_Shortcodes {

		/**
		 * Add Shortcodes
		 *
		 * @return void
		 * @since  1.7
		 * @author andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function load() {
			/* === Support for YITH WooCommerce Customize My Account Page === */
			add_filter( 'yith_wcmap_is_my_account_page', 'YITH_Multi_Vendor_Shortcodes::is_my_account_page', 15 );

			$shortcodes = array(
				'yith_wcmv_list'            => 'YITH_Multi_Vendor_Shortcodes::vendors_list',
				'yith_wcmv_become_a_vendor' => 'YITH_Multi_Vendor_Shortcodes::become_a_vendor',
				'yith_wcmv_vendor_name'     => 'YITH_Multi_Vendor_Shortcodes::vendor_name',
				'yith_wcmv_vendor_products' => 'YITH_Multi_Vendor_Shortcodes::vendor_products'
			);

			foreach ( $shortcodes as $shortcode => $callback ) {
				add_shortcode( $shortcode, $callback );
			}
		}

		/**
		 * Print vendors list shortcodes
		 *
		 * @param array $sc_args The Shortcode args
		 *
		 * @return mixed ob_get_clean();
		 * @since  1.7
		 * @author andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function vendors_list( $sc_args = array() ) {
			$default = array(
				'per_page'                => - 1,
				'hide_no_products_vendor' => 'false',
				'show_description'        => 'false',
				'description_lenght'      => 40,
				'vendor_image'            => 'store',
				'orderby'                 => 'name',
				//Allowed values: 'name', 'slug', 'term_group', 'term_id', 'id', 'description'
				'order'                   => 'ASC',
				//Allowed values: ASC, DESC,
				'include'                 => array(),
			);

			if ( isset( $sc_args['hide_no_products_vendor'] ) ) {
				$sc_args['hide_no_products_vendor'] = 'true' == $sc_args['hide_no_products_vendor'];
			}

			$sc_args      = wp_parse_args( $sc_args, $default );
			$vendors_args = array(
				'enabled_selling' => true,
				'order'           => $sc_args['order'],
				'orderby'         => $sc_args['orderby'],
				'include'         => $sc_args['include'],
			);
			$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
			$per_page     = intval( $sc_args['per_page'] );
			$total        = ceil( count( YITH_Vendors()->get_vendors( array(
					'enabled_selling' => true,
					'fields'          => 'ids'
				) ) ) / $per_page );
			$per_page     = - 1 == $per_page ? 0 : $per_page;

			if ( ! empty( $sc_args['per_page'] ) ) {
				$pagination_args = array(
					'pagination' => array(
						'offset' => ( $paged - 1 ) * absint( $sc_args['per_page'] ),
						'number' => $per_page,
						'type'   => 'list'
					)
				);
				$vendors_args    = array_merge( $vendors_args, $pagination_args );
			}

			$vendors = YITH_Vendors()->get_vendors( apply_filters( 'yith_wcmv_vendor_list_shortcode_args', $vendors_args ) );

			if ( empty( $vendors ) ) {
				return false;
			}

			$args = array(
				'vendors'          => $vendors,
				'paginate'         => array(
					'current' => $paged,
					'total'   => $total,
				),
				'show_total_sales' => 'yes' == get_option( 'yith_wpv_vendor_total_sales' ) ? true : false,
				'sc_args'          => $sc_args,
				'icons'            => yith_wcmv_get_font_awesome_icons(),
				'socials_list'     => YITH_Vendors()->get_social_fields(),
			);
			ob_start();
			yith_wcpv_get_template( 'vendors-list', $args, 'shortcodes' );

			return ob_get_clean();
		}

		/*
		 * Show vendor name
		 *
		 * @param array $sc_args The Shortcode args
		 *
		 * @return void
		 * @since  2.2.3
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function become_a_vendor( $sc_args = array() ) {
			$user   = wp_get_current_user();
			$vendor = yith_get_vendor( $user->ID, 'user' );

			$is_customer_or_subscriber = in_array( 'subscriber', $user->roles ) || in_array( 'customer', $user->roles ) || ( count( array_intersect( apply_filters( 'yith_wcmv_custom_role_to_access_to_become_a_vendor_form', array() ), $user->roles ) ) > 0 );
			$have_no_roles             = empty( $user->roles );
			$can_create_vendor_account = true;

			if ( ! $vendor->is_valid() && ( $is_customer_or_subscriber || $have_no_roles ) ) {
				$can_create_vendor_account = true;
			}

			if ( $vendor->is_super_user() ) {
				$can_create_vendor_account = true;
			}

			ob_start();

			if ( is_user_logged_in() && $can_create_vendor_account ) {
				$become_a_vendor_label = sprintf( "%s %s", esc_attr_x( 'Become a', '[part of:] Become a vendor', 'yith-woocommerce-product-vendors' ), YITH_Vendors()->get_singular_label( 'strtolower' ) );
				$args                  = array(
					'is_vat_require'                  => YITH_Vendors()->is_vat_require(),
					'is_terms_and_conditions_require' => YITH_Vendors()->is_terms_and_conditions_require(),
					'is_paypal_email_required'        => YITH_Vendors()->is_paypal_email_required(),
					'is_paypal_email_enabled'         => YITH_Vendors()->is_paypal_email_enabled(),
					'become_a_vendor_label'           => apply_filters( 'yith_wcmv_become_a_vendor_button_label', $become_a_vendor_label )
				);
				yith_wcpv_get_template( 'become-a-vendor', $args, 'shortcodes' );
			} else {
				if ( apply_filters( 'yith_wcmv_skip_show_my_account_in_become_a_vendor_shortcode', false ) ) {
					do_action( 'yith_wcmv_show_alternative_content_for_vendors_in_become_a_vendor_shortcode' );
				} else {
					echo do_shortcode( '[woocommerce_my_account]' );
				}

			}

			return ob_get_clean();
		}

		/*
		 * Show vendor name
		 *
		 * @param array $sc_args The Shortcode args
		 *
		 * @return void
		 * @since  2.2.3
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function vendor_name( $sc_args = array() ) {
			$default = array(
				'show_by'  => 'vendor',
				'value'    => 0,
				'type'     => 'link',
				'category' => '',
			);

			$sc_args = wp_parse_args( $sc_args, $default );

			$vendor = yith_get_vendor( $sc_args['value'], $sc_args['show_by'] );

			ob_start();

			if ( $vendor->is_valid() ) {
				$use_link = 'link' == $sc_args['type'];
				?>
				<span class="by-vendor-name">
		            <?php if ( $use_link ) : ?>
				<?php $vendor_url = ! empty( $sc_args['category'] ) ? add_query_arg( array( 'product_cat' => $sc_args['category'] ), $vendor->get_url() ) : $vendor->get_url(); ?>
				    <a class="by-vendor-name-link" href="<?php echo $vendor_url ?>">
				    <?php endif; ?>

					<?php echo $vendor->name ?>

					<?php if ( $use_link ) : ?>
				        </a>
			    <?php endif; ?>
				</span>
				<br>
				<?php
			}

			return ob_get_clean();
		}

		/**
		 * Check if current page is the become a vendor page
		 *
		 * @return  bool  true if the current page is the become a vendor page, false otherwise
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    3.3.2
		 */
		public static function is_become_a_vendor_page() {
			return is_page( get_option( 'yith_wpv_become_a_vendor_page_id' ) );
		}

		/**
		 * Check if current page is the become a vendor page
		 * if yes, set it like My Account
		 *
		 * Support for YITH WooCommerce Customize My Account Page
		 *
		 * @return  bool  true if the current page is the become a vendor page, false otherwise
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    3.3.2
		 */
		public static function is_my_account_page( $is_my_account_page ) {
			return self::is_become_a_vendor_page() ? true : $is_my_account_page;
		}

		/*
		 * Show vendor name
		 *
		 * @param array $sc_args The Shortcode args
		 *
		 * @return void
		 * @since  2.2.3
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function vendor_products( $sc_args = array() ) {
			$default_vendor_id = 0;

			if ( is_singular( 'product' ) ) {
				$vendor            = yith_get_vendor( 'current', 'product' );
				$default_vendor_id = $vendor->is_valid() ? $vendor->id : $default_vendor_id;
			}

			$default = array( 'vendor_id' => $default_vendor_id );

			$sc_args = wp_parse_args( $sc_args, $default );

			if ( empty( $sc_args['vendor_id'] ) ) {
				return false;
			}

			$vendor = yith_get_vendor( $sc_args['vendor_id'], 'vendor' );
			ob_start();

			if ( $vendor->is_valid() ) {
				$products = $vendor->get_products();
				if ( ! empty( $products ) ) {
					$extra_args = '';
					foreach ( $sc_args as $sc_att => $sc_value ) {
						$extra_args .= sprintf( ' %s="%s"', $sc_att, $sc_value );
					}

					$shortcode = sprintf( '[products ids="%s"%s]', implode( ',', $products ), $extra_args );

					echo do_shortcode( $shortcode );
				}
			}

			return ob_get_clean();
		}
	}
}
