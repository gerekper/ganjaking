<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with Follow-Up Emails.
 * Version tested: 4.7.1.
 *
 * @since 0.9
 */
class PLLWC_Follow_Up_Emails {

	/**
	 * Constructor.
	 * Setups actions and filters.
	 *
	 * @since 0.9
	 */
	public function __construct() {
		// Post types.
		add_filter( 'pll_get_post_types', array( $this, 'translate_types' ), 10, 2 );

		// Synchronizations.
		add_filter( 'pll_copy_taxonomies', array( $this, 'copy_taxonomies' ) );
		add_filter( 'pll_copy_post_metas', array( $this, 'copy_post_metas' ), 10, 3 );
		add_filter( 'pll_translate_post_meta', array( $this, 'translate_post_meta' ), 10, 4 );

		// Filter emails.
		add_action( 'parse_query', array( $this, 'parse_query' ), 5 );
		add_filter( 'fue_insert_email_order', array( $this, 'insert_email_order' ) );

		// Email preview.
		if ( 1 === PLL()->options['force_lang'] ) {
			add_filter( 'fue_email_preview_url', array( $this, 'preview_url' ) );
		}

		// Email language.
		add_action( 'fue_before_email_send', array( $this, 'before_email_send' ), 10, 4 );
		add_action( 'fue_after_email_sent', array( PLLWC()->emails, 'after_email' ) );
	}

	/**
	 * Adds the language and translation management for the 'follow_up_email' custom post type.
	 * Hooked to the filter 'pll_get_post_types'.
	 *
	 * @since 0.9
	 *
	 * @param string[] $types List of post type names for which Polylang manages language and translations.
	 * @param bool     $hide  True when displaying the list in Polylang settings.
	 * @return string[] List of post type names for which Polylang manages language and translations.
	 */
	public function translate_types( $types, $hide ) {
		$fue_types = array( 'follow_up_email' );
		return $hide ? array_diff( $types, $fue_types ) : array_merge( $types, $fue_types );
	}

	/**
	 * Synchronizes the Follow-Up emails taxonomies.
	 * Hooked to the filter 'pll_copy_taxonomies'.
	 *
	 * @since 0.9
	 *
	 * @param string[] $taxonomies List of taxonomies to Synchronize.
	 * @return string[] Modified list of taxonomies.
	 */
	public function copy_taxonomies( $taxonomies ) {
		return array_merge( $taxonomies, array( 'follow_up_email_type', 'follow_up_email_campaign' ) );
	}

	/**
	 * Synchronizes the custom fields.
	 * Hooked to the filter 'pll_copy_post_metas'.
	 *
	 * @since 0.9
	 *
	 * @param string[] $keys List of custom fields names.
	 * @param bool     $sync True if it is synchronization, false if it is a copy.
	 * @param int      $from Id of the post from which we copy informations.
	 * @return string[]
	 */
	public function copy_post_metas( $keys, $sync, $from ) {
		if ( 'follow_up_email' === get_post_type( $from ) ) {
			$to_sync = array(
				'_template',
				'_interval_type',
				'_interval_num',
				'_interval_duration',
				'_send_date',
				'_send_date_hour',
				'_send_date_minute',
				'_always_send',
				'_conditions',
				'_send_coupon',
				'_coupon_id',
				'_category_id',
				'_product_id',
				'_meta',
			);

			$to_copy = array(
				'_tracking_on',
				'_tracking',
				'_tracking_code',
			);

			if ( $sync ) {
				$keys = array_merge( $keys, $to_sync );
			} else {
				$keys = array_merge( $keys, $to_copy, $to_sync );
			}
		}
		return $keys;
	}

	/**
	 * Translates some custom fields when synchronizing follow-up emails.
	 * Hooked to the filter 'pll_translate_post_meta'.
	 *
	 * @since 1.0
	 * @param mixed  $value Meta value.
	 * @param string $key   Meta key.
	 * @param string $lang  Language of target.
	 * @param int    $from  Id of the follow-up email from which we copy informations.
	 * @return mixed
	 */
	public function translate_post_meta( $value, $key, $lang, $from ) {
		if ( 'follow_up_email' === get_post_type( $from ) && ! empty( $value ) ) {
			switch ( $key ) {
				case '_category_id':
					// Translate a product category id.
					$tr_value = pll_get_term( $value, $lang );
					$value = $tr_value ? $tr_value : $value;
					break;
				case '_product_id':
					// Translate a product id.
					$data_store = PLLWC_Data_Store::load( 'product_language' );
					$tr_value = $data_store->get( $value, $lang );
					$value = $tr_value ? $tr_value : $value;
					break;
				case '_meta':
					foreach ( $value as $k => $v ) {
						switch ( $k ) {
							case 'excluded_customers_products':
								// Translate an array of product ids.
								if ( ! empty( $v ) ) {
									$value[ $k ] = array();
									$data_store = PLLWC_Data_Store::load( 'product_language' );
									foreach ( $v as $product_id ) {
										if ( $tr_id = $data_store->get( $product_id, $lang ) ) {
											$value[ $k ][] = $tr_id;
										}
									}
								}
								break;
							case 'excluded_categories':
							case 'excluded_customers_categories':
								// Translate an array of product category ids.
								if ( ! empty( $v ) ) {
									$value[ $k ] = array();
									foreach ( $v as $term_id ) {
										if ( $tr_id = pll_get_term( $term_id, $lang ) ) {
											$value[ $k ][] = $tr_id;
										}
									}
								}
								break;
						}
					}
					break;
				case '_conditions':
					foreach ( $value as $k => $v ) {
						switch ( $v['condition'] ) {
							case 'bought_categories':
								// Translate an array of product category ids.
								if ( ! empty( $v['categories'] ) ) {
									$value[ $k ]['categories'] = array();
									foreach ( $v['categories'] as $term_id ) {
										if ( $tr_id = pll_get_term( $term_id, $lang ) ) {
											$value[ $k ]['categories'][] = $tr_id;
										}
									}
								}
								break;
						}
					}
					break;
			}
		}

		return $value;
	}

	/**
	 * Removes the language filter for Follow-Up Emails
	 * as the language may not be the desired one on admin.
	 * Hooked to the action 'parse_query'.
	 *
	 * @since 0.9
	 *
	 * @param WP_Query $query WP_Query object.
	 * @return void
	 */
	public function parse_query( $query ) {
		if ( isset( $query->query['post_type'] ) && 'follow_up_email' === $query->query['post_type'] ) {
			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
			}

			// Keep the filter active on the Follow-Up Emails page.
			if ( empty( $screen ) || empty( $screen->base ) || 'toplevel_page_followup-emails' !== $screen->base ) {
				$query->set( 'lang', '' );
			}
		}
	}

	/**
	 * Get the email language from the email order data.
	 *
	 * @since 0.9
	 *
	 * @param array $data Email order data.
	 * @return string|bool
	 */
	protected function get_email_order_language( $data ) {
		if ( ! empty( $data['order_id'] ) ) {
			$data_store = PLLWC_Data_Store::load( 'order_language' );
			return $data_store->get_language( $data['order_id'] );
		} elseif ( ! empty( $data['user_id'] ) ) {
			return get_user_meta( $data['user_id'], 'locale', true );
		}
		return false;
	}

	/**
	 * Make sure that the emails are sent in the correct language.
	 * Hooked to the filter 'fue_insert_email_order'.
	 *
	 * @since 0.9
	 *
	 * @param array $data Email order data.
	 * @return array
	 */
	public function insert_email_order( $data ) {
		$lang = $this->get_email_order_language( $data );

		if ( ! empty( $lang ) && ! empty( $data['email_id'] ) ) {
			if ( doing_action( 'user_register' ) ) {
				// In this case Follow-Up Emails sends each email only once.
				if ( $tr_id = pll_get_post( $data['email_id'], $lang ) ) {
					$args = array(
						'email_id' => $tr_id,
						'user_id'  => $data['user_id'],
					);

					if ( count( Follow_Up_Emails::instance()->scheduler->get_items( $args ) ) === 0 ) {
						$data['email_id'] = $tr_id;
					} else {
						$data['email_id'] = 0;
					}
				}
			} elseif ( doing_action( 'woocommerce_cart_updated' ) ) {
				// In this case Follow-Up Emails sends only the email with the highest priority.
				if ( $tr_id = pll_get_post( $data['email_id'], $lang ) ) {
					$data['email_id'] = $tr_id;
				}
			} elseif ( pll_get_post_language( $data['email_id'] ) !== $lang ) {
				// Otherwise Follow-Up Emails sends all emails in all languages, so let's keep only the right one.
				$data['email_id'] = 0;
			}
		}
		return $data;
	}

	/**
	 * Adds the language information in the preview url.
	 * Hooked to the filter 'fue_email_preview_url'.
	 *
	 * @since 0.9
	 *
	 * @param string $url Preview url.
	 * @return string Modified url.
	 */
	public function preview_url( $url ) {
		$query = wp_parse_url( $url, PHP_URL_QUERY );
		parse_str( $query, $args );

		if ( ! empty( $args['email'] ) && $lang = pll_get_post_language( (int) $args['email'] ) ) {
			if ( ! PLL()->options['hide_default'] || PLL()->options['default_lang'] !== $lang ) {
				// Can't use switch_langage_in_link() due to the lack of slash before the query.
				$url = str_replace( '?', "/{$lang}?", $url );
			}
		}
		return $url;
	}

	/**
	 * Sets the email language.
	 * Hooked to the action 'fue_before_email_send'.
	 *
	 * @since 0.9
	 *
	 * @param string $subject    Email subject.
	 * @param string $message    Email message.
	 * @param string $headers    Email headers.
	 * @param object $queue_item Email queue item.
	 * @return void
	 */
	public function before_email_send( $subject, $message, $headers, $queue_item ) {
		if ( ! empty( $queue_item->email_id ) ) {
			$language = PLL()->model->post->get_language( $queue_item->email_id );
			PLLWC()->emails->set_email_language( $language );
		}
	}
}
