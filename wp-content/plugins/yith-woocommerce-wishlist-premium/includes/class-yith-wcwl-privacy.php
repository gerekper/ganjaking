<?php
/**
 * Privacy class; added to let customer export personal data
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Privacy' ) ) {
	/**
	 * YITH WCWL Exporter
	 *
	 * @since 2.2.2
	 */
	class YITH_WCWL_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * Constructor method
		 *
		 * @since 2.2.2
		 */
		public function __construct() {

			parent::__construct( 'YITH WooCommerce Wishlist' );

			// set up wishlist data exporter.
			add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ) );

			// set up wishlist data eraser.
			add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
		}

		/**
		 * Retrieves privacy example text for wishlist plugin
		 *
		 * @param string $section Section of the message to retrieve.
		 *
		 * @return string Privacy message
		 * @since 2.2.2
		 */
		public function get_privacy_message( $section ) {
			$content = '';

			switch ( $section ) {
				case 'collect_and_store':
					$content = '<p>' . __( 'While you visit our site, we’ll track:', 'yith-woocommerce-wishlist' ) . '</p>' .
								'<ul>' .
								'<li>' . __( 'Products you’ve added to the wishlist: we’ll use this to show you and other users your favourite products, and to create targeted email campaigns.', 'yith-woocommerce-wishlist' ) . '</li>' .
								'<li>' . __( 'Wishlists you’ve created: we’ll keep track of the wishlists you create, and make them visible to the store staff', 'yith-woocommerce-wishlist' ) . '</li>' .
								'</ul>' .
								'<p>' . __( 'We’ll also use cookies to keep track of wishlist contents while you’re browsing our site.', 'yith-woocommerce-wishlist' ) . '</p>';
					break;
				case 'has_access':
					$content = '<p>' . __( 'Members of our team have access to the information you provide us with. For example, both Administrators and Shop Managers can access:', 'yith-woocommerce-wishlist' ) . '</p>' .
								'<ul>' .
								'<li>' . __( 'Wishlist details, such as products added, date of addition, name and privacy settings of your wishlists', 'yith-woocommerce-wishlist' ) . '</li>' .
								'</ul>' .
								'<p>' . __( 'Our team members have access to this information to offer you better deals for the products you love.', 'yith-woocommerce-wishlist' ) . '</p>';
					break;
				case 'share':
				case 'payments':
				default:
					break;
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_privacy_policy_content
			 *
			 * Filter the content of the privacy policy.
			 *
			 * @param string $content Privacy policy content
			 * @param string $section Privacy policy section
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcwl_privacy_policy_content', $content, $section );
		}

		/**
		 * Register exporters for wishlist plugin
		 *
		 * @param array $exporters Array of currently registered exporters.
		 * @return array Array of filtered exporters
		 * @since 2.2.2
		 */
		public function register_exporter( $exporters ) {
			$exporters['yith_wcwl_exporter'] = array(
				'exporter_friendly_name' => __( 'Customer wishlists', 'yith-woocommerce-wishlist' ),
				'callback'               => array( $this, 'wishlist_data_exporter' ),
			);

			return $exporters;
		}

		/**
		 * Register eraser for wishlist plugin
		 *
		 * @param array $erasers Array of currently registered erasers.
		 * @return array Array of filtered erasers
		 * @since 2.2.2
		 */
		public function register_eraser( $erasers ) {
			$erasers['yith_wcwl_eraser'] = array(
				'eraser_friendly_name' => __( 'Customer wishlists', 'yith-woocommerce-wishlist' ),
				'callback'             => array( $this, 'wishlist_data_eraser' ),
			);

			return $erasers;
		}

		/**
		 * Export user wishlists (only available for authenticated users' wishlist)
		 *
		 * @param string $email_address Email of the users that requested export.
		 * @param int    $page Current page processed.
		 * @return array Array of data to export
		 * @since 2.2.2
		 */
		public function wishlist_data_exporter( $email_address, $page ) {
			$done           = true;
			$page           = (int) $page;
			$offset         = 10 * ( $page - 1 );
			$user           = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.
			$data_to_export = array();

			if ( $user instanceof WP_User ) {
				$wishlists = YITH_WCWL()->get_wishlists(
					array(
						'limit'   => 10,
						'offset'  => $offset,
						'user_id' => $user->ID,
						'orderby' => 'ID',
						'order'   => 'ASC',
					)
				);

				if ( 0 < count( $wishlists ) ) {
					foreach ( $wishlists as $wishlist ) {
						$data_to_export[] = array(
							'group_id'    => 'yith_wcwl_wishlist',
							'group_label' => __( 'Wishlists', 'yith-woocommerce-wishlist' ),
							'item_id'     => 'wishlist-' . $wishlist->get_id(),
							'data'        => $this->get_wishlist_personal_data( $wishlist ),
						);
					}
					$done = 10 > count( $wishlists );
				} else {
					$done = true;
				}
			}

			return array(
				'data' => $data_to_export,
				'done' => $done,
			);
		}

		/**
		 * Deletes user wishlists (only available for authenticated users' wishlist)
		 *
		 * @param string $email_address Email of the users that requested export.
		 * @param int    $page Current page processed.
		 * @return array Result of the operation
		 * @since 2.2.2
		 */
		public function wishlist_data_eraser( $email_address, $page ) {
			global $wpdb;

			$page     = (int) $page;
			$offset   = 10 * ( $page - 1 );
			$user     = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.
			$response = array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);

			if ( ! $user instanceof WP_User ) {
				return $response;
			}

			$wishlists = YITH_WCWL()->get_wishlists(
				array(
					'limit'   => 10,
					'offset'  => $offset,
					'user_id' => $user->ID,
					'orderby' => 'ID',
					'order'   => 'ASC',
				)
			);

			if ( 0 < count( $wishlists ) ) {
				foreach ( $wishlists as $wishlist ) {
					/**
					 * APPLY_FILTERS: yith_wcwl_privacy_erase_wishlist_personal_data
					 *
					 * Filter whether to delete the personal data from the wishlist.
					 *
					 * @param bool               $condition Whether to delete personal data or not
					 * @param YITH_WCWL_Wishlist $wishlist  Wishlist object
					 *
					 * @return bool
					 */
					if ( apply_filters( 'yith_wcwl_privacy_erase_wishlist_personal_data', true, $wishlist ) ) {
						/**
						 * DO_ACTION: yith_wcwl_privacy_before_remove_wishlist_personal_data
						 *
						 * Allows to fire some action before deleting the personal data from the wishlist.
						 *
						 * @param YITH_WCWL_Wishlist $wishlist Wishlist object
						 */
						do_action( 'yith_wcwl_privacy_before_remove_wishlist_personal_data', $wishlist );

						$wishlist->delete();

						/**
						 * DO_ACTION: yith_wcwl_privacy_remove_wishlist_personal_data
						 *
						 * Allows to fire some action when deleting the personal data from the wishlist.
						 *
						 * @param YITH_WCWL_Wishlist $wishlist Wishlist object
						 */
						do_action( 'yith_wcwl_privacy_remove_wishlist_personal_data', $wishlist );

						/* Translators: %s Order number. */
						$response['messages'][]    = sprintf( __( 'Removed wishlist %s.', 'yith-woocommerce-wishlist' ), $wishlist->get_token() );
						$response['items_removed'] = true;
					} else {
						/* Translators: %s Order number. */
						$response['messages'][]     = sprintf( __( 'Wishlist %s has been retained.', 'yith-woocommerce-wishlist' ), $wishlist->get_token() );
						$response['items_retained'] = true;
					}
				}
				$response['done'] = 10 > count( $wishlists );
			} else {
				$response['done'] = true;
			}

			return $response;
		}

		/**
		 * Retrieves data to export for each user's wishlist
		 *
		 * @param \YITH_WCWL_Wishlist $wishlist Wishlist.
		 * @return array Data to export
		 * @since 2.2.2
		 */
		protected function get_wishlist_personal_data( $wishlist ) {
			$personal_data = array();

			/**
			 * APPLY_FILTERS: yith_wcwl_privacy_export_wishlist_personal_data_props
			 *
			 * Filter the personal data props to export from the wishlist.
			 *
			 * @param array              $data_to_export Personal data to export
			 * @param YITH_WCWL_Wishlist $wishlist       Wishlist object
			 *
			 * @return array
			 */
			$props_to_export = apply_filters(
				'yith_wcwl_privacy_export_wishlist_personal_data_props',
				array(
					'wishlist_token'   => __( 'Token', 'yith-woocommerce-wishlist' ),
					'wishlist_url'     => __( 'Wishlist URL', 'yith-woocommerce-wishlist' ),
					'wishlist_name'    => __( 'Title', 'yith-woocommerce-wishlist' ),
					'dateadded'        => _x( 'Created on', 'date when wishlist was created', 'yith-woocommerce-wishlist' ),
					'wishlist_privacy' => __( 'Visibility', 'yith-woocommerce-wishlist' ),
					'items'            => __( 'Items added', 'yith-woocommerce-wishlist' ),
				),
				$wishlist
			);

			foreach ( $props_to_export as $prop => $name ) {
				$value = '';

				switch ( $prop ) {
					case 'items':
						$item_names = array();
						$items      = $wishlist->get_items();

						foreach ( $items as $item ) {
							$product = $item->get_product();

							if ( ! $product ) {
								continue;
							}

							$item_name = $product->get_name() . ' x ' . $item['quantity'];

							if ( $item->get_date_added() ) {
								$item_name .= ' (on: ' . $item->get_date_added() . ')';
							}

							$item_names[] = $item_name;
						}

						$value = implode( ', ', $item_names );
						break;
					case 'wishlist_url':
						$wishlist_url = $wishlist->get_url();

						$value = sprintf( '<a href="%1$s">%1$s</a>', $wishlist_url );
						break;
					case 'wishlist_name':
						$wishlist_name = $wishlist->get_formatted_name();

						$value = $wishlist_name ? $wishlist_name : get_option( 'yith_wcwl_wishlist_title' );
						break;
					case 'dateadded':
						$value = $wishlist->get_date_added();
						break;
					case 'wishlist_privacy':
						$value = $wishlist->get_formatted_privacy();
						break;
					default:
						if ( isset( $wishlist[ $prop ] ) ) {
							$value = $wishlist[ $prop ];
						}
						break;
				}

				/**
				 * APPLY_FILTERS: yith_wcwl_privacy_export_wishlist_personal_data_prop
				 *
				 * Filter the personal data value to export from the wishlist.
				 *
				 * @param string             $value    Value to export
				 * @param string             $prop     Prop data to export
				 * @param YITH_WCWL_Wishlist $wishlist Wishlist object
				 *
				 * @return string
				 */
				$value = apply_filters( 'yith_wcwl_privacy_export_wishlist_personal_data_prop', $value, $prop, $wishlist );

				if ( $value ) {
					$personal_data[] = array(
						'name'  => $name,
						'value' => $value,
					);
				}
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_privacy_export_wishlist_personal_data
			 *
			 * Filter the personal data to export from the wishlist.
			 *
			 * @param array              $personal_data Personal data to export
			 * @param YITH_WCWL_Wishlist $wishlist      Wishlist object
			 *
			 * @return array
			 */
			$personal_data = apply_filters( 'yith_wcwl_privacy_export_wishlist_personal_data', $personal_data, $wishlist );

			return $personal_data;
		}
	}
}
