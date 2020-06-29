<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWCES_Emails' ) ) {

	/**
	 * Implements email functions for YWCES plugin
	 *
	 * @class   YWCES_Emails
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWCES_Emails {

		/**
		 * Single instance of the class
		 *
		 * @var \YWCES_Emails
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWCES_Emails
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self();

			}

			return self::$instance;

		}

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

		}

		/**
		 * Send the coupon mail
		 *
		 * @since   1.0.0
		 *
		 * @param   $mail_body
		 * @param   $mail_subject
		 * @param   $mail_address
		 * @param   $template
		 *
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function send_email( $mail_body, $mail_subject, $mail_address, $template = false ) {

			$mail_args = array(
				'mail_body'    => $mail_body,
				'mail_subject' => $mail_subject,
				'mail_address' => $mail_address,
				'template'     => false,
			);

			return apply_filters( 'send_ywces_mail', $mail_args );

		}

		/**
		 * Set the mail for user registration
		 *
		 * @since   1.0.0
		 *
		 * @param   $user_id
		 * @param   $type
		 * @param   $coupon_code
		 * @param   $args
		 * @param   $test_email
		 * @param   $template
		 * @param   $vendor_id
		 *
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function prepare_coupon_mail( $user_id, $type, $coupon_code, $args = array(), $test_email = false, $template = false, $vendor_id = '' ) {

			$first_name = get_user_meta( $user_id, 'billing_first_name', true );

			if ( $first_name == '' ) {
				$first_name = get_user_meta( $user_id, 'nickname', true );
			}

			$last_name = get_user_meta( $user_id, 'billing_last_name', true );

			if ( $last_name == '' ) {
				$last_name = get_user_meta( $user_id, 'nickname', true );
			}

			if ( ! $test_email ) {

				$user_email = get_user_meta( $user_id, 'billing_email', true );

				if ( $user_email == '' ) {
					$user_info  = get_userdata( $user_id );
					$user_email = $user_info->user_email;
				}

			} else {

				$user_email = $test_email;

			}

			$lang = '';

			if ( class_exists( 'SitePress' ) ) {

				$order_id = 0;

				$query_args = array(
					'post_type'      => wc_get_order_types(),
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'orderby'        => 'ID',
					'order'          => 'DESC',
					'meta_query'     => array(
						array(
							'key'     => '_customer_user',
							'value'   => $user_id,
							'compare' => '='
						)
					)
				);

				$query = new WP_Query( $query_args );

				if ( $query->have_posts() ) {

					while ( $query->have_posts() ) {

						$query->the_post();
						$order_id = $query->post->ID;

					}

				}

				wp_reset_query();
				wp_reset_postdata();

				$order = wc_get_order( $order_id );
				$lang  = yit_get_prop( $order, 'wpml_language' );

				if ( $lang == '' ) {
					$lang = get_user_locale( $user_id );
					$lang = substr( $lang, 0, 2 );
				}

			}

			$mail_body    = $this->get_mail_body( $type, $coupon_code, $first_name, $last_name, $user_email, $args, $lang, $vendor_id );
			$mail_subject = $this->get_subject( $type, $first_name, $last_name, $lang, $vendor_id );

			return $this->send_email( $mail_body, $mail_subject, $user_email, $template );

		}

		/**
		 * Set the mail body
		 *
		 * @since   1.0.0
		 *
		 * @param   $type
		 * @param   $coupon_code
		 * @param   $first_name
		 * @param   $last_name
		 * @param   $user_email
		 * @param   $args
		 * @param   $vendor_id
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_mail_body( $type, $coupon_code, $first_name, $last_name, $user_email, $args = array(), $lang, $vendor_id = '' ) {

			$unsubscribe_text = apply_filters( 'ywces_unsubscribe_link_text', esc_html__( 'If you don\'t want to receive these emails click %s here %s', 'yith-woocommerce-coupon-email-system' ) );
			$unsubscribe_url  = ( wc_get_account_endpoint_url( 'edit-account' ) );

			$coupon       = $this->get_coupon_info( $coupon_code );
			$placeholders = array(
				'{coupon_description}' => $coupon,
				'{site_title}'         => get_option( 'blogname' ),
				'{customer_name}'      => $first_name,
				'{customer_last_name}' => $last_name,
				'{customer_email}'     => $user_email,
				'{vendor_name}'        => apply_filters( 'ywces_get_vendor_name', '', $vendor_id ),
				'{unsubscribe_link}'   => sprintf( $unsubscribe_text, '<a href="' . $unsubscribe_url . '">', '</a>' )
			);

			switch ( $type ) {
				case 'first_purchase':
					$placeholders['{order_date}'] = ucwords( date_i18n( get_option( 'date_format' ), yit_datetime_to_timestamp( $args['order_date'] ) ) );
					break;

				case 'purchases':
					$placeholders['{order_date}']          = ucwords( date_i18n( get_option( 'date_format' ), yit_datetime_to_timestamp( $args['order_date'] ) ) );
					$placeholders['{purchases_threshold}'] = $args['threshold'];
					break;

				case 'spending':
					$placeholders['{order_date}']           = ucwords( date_i18n( get_option( 'date_format' ), yit_datetime_to_timestamp( $args['order_date'] ) ) );
					$placeholders['{spending_threshold}']   = wc_price( $args['threshold'] );
					$placeholders['{customer_money_spent}'] = wc_price( $args['expense'] );
					break;

				case 'product_purchasing':
					$placeholders['{order_date}']        = ucwords( date_i18n( get_option( 'date_format' ), yit_datetime_to_timestamp( $args['order_date'] ) ) );
					$placeholders['{purchased_product}'] = $this->render_mailbody_link( wc_get_product( $args['product'] ), 'product' );
					break;

				case 'last_purchase':
					$placeholders['{days_ago}'] = $args['days_ago'];
					break;

				default:

			}

			$placeholders = apply_filters( 'ywces_mail_placeholders', $placeholders, $user_email );

			$mail_body_opt = 'ywces_mailbody_' . $type . ( apply_filters( 'ywces_set_vendor_id', '', $vendor_id ) );
			$mail_body     = nl2br( apply_filters( 'wpml_translate_single_string', get_option( $mail_body_opt ), 'admin_texts_' . $mail_body_opt, $mail_body_opt, $lang ) );
			$mail_body     = str_replace( array_keys( $placeholders ), $placeholders, $mail_body );

			return $mail_body;

		}

		/**
		 * Set the subject and mail heading
		 *
		 * @since   1.0.0
		 *
		 * @param   $type
		 * @param   $first_name
		 * @param   $last_name
		 * @param   $vendor_id
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_subject( $type, $first_name, $last_name, $lang, $vendor_id = '' ) {

			$find    = array(
				'{site_title}',
				'{customer_name}',
				'{customer_last_name}',
				'{vendor_name}',
			);
			$replace = array(
				get_option( 'blogname' ),
				$first_name,
				$last_name,
				apply_filters( 'ywces_get_vendor_name', '', $vendor_id ),
			);

			$subject_opt = 'ywces_subject_' . $type . ( apply_filters( 'ywces_set_vendor_id', '', $vendor_id ) );
			$subject     = nl2br( apply_filters( 'wpml_translate_single_string', get_option( $subject_opt ), 'admin_texts_' . $subject_opt, $subject_opt, $lang ) );
			$subject     = str_replace( $find, $replace, $subject );

			return $subject;
		}

		/**
		 * Get coupon info
		 *
		 * @since   1.0.0
		 *
		 * @param   $coupon_code
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_coupon_info( $coupon_code ) {

			$result    = '';
			$coupon    = new WC_Coupon( $coupon_code );
			$coupon_id = yit_get_prop( $coupon, 'id' );

			if ( $coupon_id ) {

				$post = get_post( $coupon_id );
				if ( $post ) {

					$amount_suffix = get_woocommerce_currency_symbol();

					if ( function_exists( 'wc_price' ) ) {

						$amount_suffix = null;

					}

					$discount_type = yit_get_prop( $coupon, 'discount_type' );

					if ( $discount_type == 'percent' || $discount_type == 'percent_product' ) {
						$amount_suffix = '%';
					}

					$amount = yit_get_prop( $coupon, 'coupon_amount' );
					if ( $amount_suffix === null ) {
						$amount        = wc_price( $amount );
						$amount_suffix = '';
					}

					$products            = array();
					$products_excluded   = array();
					$categories          = array();
					$categories_excluded = array();

					$product_ids                = yit_get_prop( $coupon, 'product_ids' );
					$exclude_product_ids        = yit_get_prop( $coupon, 'exclude_product_ids' );
					$product_categories         = yit_get_prop( $coupon, 'product_categories' );
					$exclude_product_categories = yit_get_prop( $coupon, 'exclude_product_categories' );
					$minimum_amount             = yit_get_prop( $coupon, 'minimum_amount' );
					$maximum_amount             = yit_get_prop( $coupon, 'maximum_amount' );
					$expiry_date                = yit_get_prop( $coupon, 'expiry_date' );

					if ( count( $product_ids ) > 0 ) {
						foreach ( $product_ids as $product_id ) {
							$product = wc_get_product( $product_id );
							if ( $product ) {
								$products[] = $this->render_mailbody_link( $product, 'product' );
							}
						}
					}

					if ( count( $exclude_product_ids ) > 0 ) {
						foreach ( $exclude_product_ids as $product_id ) {
							$product = wc_get_product( $product_id );
							if ( $product ) {
								$products_excluded[] = $this->render_mailbody_link( $product, 'product' );
							}
						}
					}

					if ( count( $product_categories ) > 0 ) {
						foreach ( $product_categories as $term_id ) {
							$term = get_term_by( 'id', $term_id, 'product_cat' );
							if ( $term ) {
								$categories[] = $this->render_mailbody_link( $term, 'category' );
							}
						}
					}

					if ( count( $exclude_product_categories ) > 0 ) {
						foreach ( $exclude_product_categories as $term_id ) {
							$term = get_term_by( 'id', $term_id, 'product_cat' );
							if ( $term ) {
								$categories_excluded[] = $this->render_mailbody_link( $term, 'category' );
							}
						}
					}

					ob_start();

					$new_line = ( apply_filters( 'ywces_new_line', false ) ? '<br/>' : '' );

					?>

                    <h2>
						<?php echo esc_html__( 'Coupon code: ', 'yith-woocommerce-coupon-email-system' ) . $new_line . yit_get_prop( $coupon, 'code' ); ?>
                    </h2>

					<?php if ( ! empty( $post->post_excerpt ) && apply_filters( 'ywces_coupon_email_show_coupon_description', true ) ) : ?>

                        <i>
							<?php echo $post->post_excerpt; ?>
                        </i>

					<?php endif; ?>

                    <p>
                        <b>
							<?php printf( esc_html__( 'Coupon amount: %s%s off', 'yith-woocommerce-coupon-email-system' ), $amount, $amount_suffix ); ?>
							<?php if ( yit_get_prop( $coupon, 'free_shipping' ) == 'yes' ) : ?>
                                + <?php esc_html_e( 'Free shipping', 'yith-woocommerce-coupon-email-system' ); ?>
                                <br />
							<?php endif; ?>
                        </b>
                        <span>
                            <?php if ( $minimum_amount != '' && $maximum_amount == '' ) : ?>
	                            <?php printf( esc_html__( 'Valid for a minimum purchase of %s', 'yith-woocommerce-coupon-email-system' ), wc_price( $minimum_amount ) ); ?>
                            <?php endif; ?>
                            <?php if ( $minimum_amount == '' && $maximum_amount != '' ) : ?>
	                            <?php printf( esc_html__( 'Valid for a maximum purchase of %s', 'yith-woocommerce-coupon-email-system' ), wc_price( $maximum_amount ) ); ?>
                            <?php endif; ?>
                            <?php if ( $minimum_amount != '' && $maximum_amount != '' ) : ?>
	                            <?php printf( esc_html__( 'Valid for a minimum purchase of %s and a maximum of %s', 'yith-woocommerce-coupon-email-system' ), wc_price( $minimum_amount ), wc_price( $maximum_amount ) ); ?>
                            <?php endif; ?>
                        </span>
                    </p>

					<?php if ( count( $products ) > 0 || count( $categories ) > 0 ) : ?>
                        <p>
                            <b><?php echo esc_html__( 'Valid for:', 'yith-woocommerce-coupon-email-system' ); ?></b>
                            <br />
							<?php if ( count( $products ) > 0 ) : ?>
								<?php printf( esc_html__( 'Following products: %s', 'yith-woocommerce-coupon-email-system' ), implode( ',', $products ) ); ?>
                                <br />
							<?php endif; ?>

							<?php if ( count( $categories ) > 0 ) : ?>
								<?php printf( esc_html__( 'Products of the following categories: %s', 'yith-woocommerce-coupon-email-system' ), implode( ',', $categories ) ); ?>
                                <br />
							<?php endif; ?>

                        </p>
					<?php endif; ?>

					<?php if ( count( $products_excluded ) > 0 || count( $categories_excluded ) > 0 ) : ?>
                        <p>
                            <b><?php echo esc_html__( 'Not valid for:', 'yith-woocommerce-coupon-email-system' ); ?></b>
                            <br />
							<?php if ( count( $products_excluded ) > 0 ): ?>
								<?php printf( esc_html__( 'Following products: %s', 'yith-woocommerce-coupon-email-system' ), implode( ',', $products_excluded ) ) ?>
                                <br />
							<?php endif; ?>

							<?php if ( count( $categories_excluded ) > 0 ): ?>
								<?php printf( esc_html__( 'Products of the following categories: %s', 'yith-woocommerce-coupon-email-system' ), implode( ',', $categories_excluded ) ) ?>
                                <br />
							<?php endif; ?>
                        </p>
					<?php endif; ?>

                    <span>
                        <?php if ( yit_get_prop( $coupon, 'individual_use' ) == 'yes' ) : ?>
                            &bull; <?php esc_html_e( 'This coupon cannot be used in conjunction with other coupons', 'yith-woocommerce-coupon-email-system' ); ?>
                            <br />
                        <?php endif; ?>
						<?php if ( yit_get_prop( $coupon, 'exclude_sale_items' ) == 'yes' ) : ?>
                            &bull; <?php esc_html_e( 'This coupon will not apply to items on sale', 'yith-woocommerce-coupon-email-system' ); ?>
                            <br />
						<?php endif; ?>
						<?php do_action( 'ywces_email_additional_notes', $coupon ) ?>
                    </span>

					<?php if ( $expiry_date != '' ) : ?>
                        <p>
                            <br class="last-br" />
                            <b>
								<?php printf( esc_html__( 'Expiration date: %s', 'yith-woocommerce-coupon-email-system' ), ucwords( date_i18n( get_option( 'date_format' ), yit_datetime_to_timestamp( $expiry_date ) ) ) ); ?>
                            </b>
                        </p>
					<?php endif; ?>

					<?php

					$result = ob_get_clean();

				}

			}

			return $result;

		}

		/**
		 * Renders links for products or categories
		 *
		 * @since   1.0.0
		 *
		 * @param   $object
		 * @param   $type
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function render_mailbody_link( $object, $type ) {
			if ( $type == 'product' ) {

				$url   = esc_url( get_permalink( yit_get_product_id( $object ) ) );
				$title = $object->get_title();

			} else {

				$url   = get_term_link( $object->slug, 'product_cat' );
				$title = esc_html( $object->name );

			}

			return sprintf( '<a href="%s">%s</a>', $url, $title );
		}

	}

	/**
	 * Unique access to instance of YWCES_Emails class
	 *
	 * @return \YWCES_Emails
	 */
	function YWCES_Emails() {
		return YWCES_Emails::get_instance();
	}

}