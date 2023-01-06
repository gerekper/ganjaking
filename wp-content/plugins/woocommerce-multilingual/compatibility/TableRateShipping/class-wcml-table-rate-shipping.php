<?php

use WPML\FP\Fns;
use WPML\FP\Logic;
use WPML\FP\Obj;

/**
 * Class WCML_Table_Rate_Shipping
 */
class WCML_Table_Rate_Shipping implements \IWPML_Action {

	/**
	 * @var SitePress
	 */
	public $sitepress;

	/**
	 * @var woocommerce_wpml
	 */
	public $woocommerce_wpml;

	/**
	 * @var wpdb
	 */
	private $wpdb;

	const PRIORITY_BEFORE_DELETE = 5;

	/**
	 * WCML_Table_Rate_Shipping constructor.
	 *
	 * @param SitePress        $sitepress
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param wpdb             $wpdb
	 */
	public function __construct( SitePress $sitepress, woocommerce_wpml $woocommerce_wpml, wpdb $wpdb ) {
		$this->sitepress        = $sitepress;
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->wpdb             = $wpdb;
	}

	public function add_hooks() {

		add_action( 'init', [ $this, 'init' ], 9 );

		if ( ! is_admin() ) {
			add_filter( 'get_the_terms', [ $this, 'shipping_class_id_in_default_language' ], 10, 3 );
			add_filter( 'woocommerce_shipping_table_rate_is_available', [ $this, 'shipping_table_rate_is_available' ], 10, 3 );
		}

		if ( is_admin() ) {
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			if ( Obj::prop( 'shipping_abort_reason', $_POST ) ) {
				// phpcs:enable WordPress.Security.NonceVerification.Missing
				add_filter( 'woocommerce_table_rate_get_shipping_rates', [ $this, 'register_abort_messages' ] );
			}
			add_action( 'wp_ajax_woocommerce_table_rate_delete', [ $this, 'unregister_abort_messages_ajax' ], self::PRIORITY_BEFORE_DELETE );
			add_action( 'delete_product_shipping_class', [ $this, 'unregister_abort_messages_shipping_class' ], self::PRIORITY_BEFORE_DELETE );
		}
		add_filter( 'woocommerce_table_rate_query_rates', [ $this, 'translate_abort_messages' ] );

	}

	/**
	 * Register shipping labels for string translation.
	 */
	public function init() {
		// Register shipping label.
		if (
			isset( $_GET['page'] ) &&
			(
				'shipping_zones' === $_GET['page'] ||
				(
					'wc-settings' === $_GET['page'] &&
					isset( $_GET['tab'] ) &&
					'shipping' === $_GET['tab']
				)
			)
		) {

			$this->show_pointer_info();

			if ( isset( $_POST['shipping_label'] ) &&
				isset( $_POST['woocommerce_table_rate_title'] ) ) {
				do_action( 'wpml_register_single_string', WCML_WC_Shipping::STRINGS_CONTEXT, sanitize_text_field( $_POST['woocommerce_table_rate_title'] ) . '_shipping_method_title', sanitize_text_field( $_POST['woocommerce_table_rate_title'] ) );

				$shipping_labels = array_map( 'wc_clean', $_POST['shipping_label'] );
				foreach ( $shipping_labels as $key => $shipping_label ) {
					$rate_key = isset( $_GET['instance_id'] ) ? 'table_rate' . $_GET['instance_id'] . $_POST['rate_id'][ $key ] : $shipping_label;
					do_action( 'wpml_register_single_string', WCML_WC_Shipping::STRINGS_CONTEXT, $rate_key . '_shipping_method_title', $shipping_label );
				}
			}
		}
	}

	/**
	 * @param WP_Term[] $terms
	 * @param int       $post_id
	 * @param string    $taxonomy
	 *
	 * @return WP_Term[]
	 */
	public function shipping_class_id_in_default_language( $terms, $post_id, $taxonomy ) {
		global $icl_adjust_id_url_filter_off;

		$is_product_object = 'product' === get_post_type( $post_id ) || 'product_variation' === get_post_type( $post_id );
		if ( $terms && $is_product_object && 'product_shipping_class' === $taxonomy ) {

			if ( is_admin() ) {
				$shipp_class_language = $this->woocommerce_wpml->products->get_original_product_language( $post_id );
			} else {
				$shipp_class_language = $this->sitepress->get_default_language();
			}

			$cache_key  = md5( wp_json_encode( $terms ) );
			$cache_key .= ':' . $post_id . $shipp_class_language;

			$cache_group = 'trnsl_shipping_class';
			$cache_terms = wp_cache_get( $cache_key, $cache_group );

			if ( $cache_terms ) {
				return $cache_terms;
			}

			foreach ( $terms as $k => $term ) {

				$shipping_class_id = apply_filters( 'translate_object_id', $term->term_id, 'product_shipping_class', false, $shipp_class_language );

				$icl_adjust_id_url_filter     = $icl_adjust_id_url_filter_off;
				$icl_adjust_id_url_filter_off = true;

				$terms[ $k ] = get_term( $shipping_class_id, 'product_shipping_class' );

				$icl_adjust_id_url_filter_off = $icl_adjust_id_url_filter;
			}

			wp_cache_set( $cache_key, $terms, $cache_group );
		}

		return $terms;
	}

	public function show_pointer_info() {
		$pointer_ui = new WCML_Pointer_UI(
			/* translators: %1$s and %2$s are opening and closing HTML link tag */
			sprintf( __( 'You can translate this method title on the %1$sWPML String Translation page%2$s. Use the search on the top of that page to find the method title string.', 'woocommerce-multilingual' ), '<a href="' . admin_url( 'admin.php?page=' . WPML_ST_FOLDER . '/menu/string-translation.php' ) . '">', '</a>' ),
			WCML_Tracking_Link::getWcmlTableRateShippingDoc(),
			'woocommerce_table_rate_title'
		);

		$pointer_ui->show();

		$pointer_ui = new WCML_Pointer_UI(
			/* translators: %1$s and %2$s are opening and closing HTML link tag */
			sprintf( __( 'You can translate the labels of your table rates on the %1$sWPML String Translation page%2$s. Use the search on the top of that page to find the labels strings.', 'woocommerce-multilingual' ), '<a href="' . admin_url( 'admin.php?page=' . WPML_ST_FOLDER . '/menu/string-translation.php' ) . '">', '</a>' ),
			WCML_Tracking_Link::getWcmlTableRateShippingDoc(),
			'shipping_rates .shipping_label a'
		);

		$pointer_ui->show();
	}

	/**
	 * Register the new rate's shipping abort reasons.
	 *
	 * @param array[] $rates
	 * @return array[]
	 */
	public function register_abort_messages( $rates ) {
		// $registerAbortReason :: array -> void
		$registerAbortReason = function( $rate ) {
			do_action(
				'wpml_register_single_string',
				WCML_WC_Shipping::STRINGS_CONTEXT,
				$this->get_rate_name( $rate['rate_id'] ),
				$rate['rate_abort_reason']
			);
		};

		return wpml_collect( $rates )
			->filter( Obj::prop( 'rate_abort_reason' ) )
			->map( $registerAbortReason )
			->toArray();
	}

	/**
	 * Unregister the deleted rate's shipping abort reasons when deleted via AJAX.
	 */
	public function unregister_abort_messages_ajax() {
		check_ajax_referer( 'delete-rate', 'security' );

		wpml_collect( (array) Obj::prop( 'rate_id', $_POST ) )
			->map( Fns::unary( 'intval' ) )
			->map( [ $this, 'unregister_abort_messages' ] );
	}

	/**
	 * Unregister the deleted rate's shipping abort reasons when the shipping class it's for is deleted.
	 *
	 * @param int $term_id
	 */
	public function unregister_abort_messages_shipping_class( $term_id ) {
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$table = $this->wpdb->prefix . 'woocommerce_shipping_table_rates';
		$query = $this->wpdb->prepare(
			"SELECT rate_id FROM $table WHERE rate_class=%d",
			[ $term_id ]
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		wpml_collect( $this->wpdb->get_col( $query ) )
			->map( [ $this, 'unregister_abort_messages' ] );
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Translate the rate's shipping abort reasons
	 *
	 * @param stdClass[] $rates
	 * @return stdClass[]
	 */
	public function translate_abort_messages( $rates ) {
		// translateAbortReason :: stdClass -> stdClass
		$translateAbortReason = function( $rate ) {
			return Obj::assoc(
				'rate_abort_reason',
				apply_filters(
					'wpml_translate_single_string',
					$rate->rate_abort_reason,
					WCML_WC_Shipping::STRINGS_CONTEXT,
					$this->get_rate_name( $rate->rate_id )
				),
				$rate
			);
		};

		return wpml_collect( $rates )
			->map( Logic::ifElse( Obj::prop( 'rate_abort_reason' ), $translateAbortReason, Fns::identity() ) )
			->toArray();
	}


	/**
	 * Unregister the deleted rate's shipping abort reasons for list of ids
	 *
	 * @param int $rate_id
	 */
	public function unregister_abort_messages( $rate_id ) {
		icl_unregister_string(
			WCML_WC_Shipping::STRINGS_CONTEXT,
			$this->get_rate_name( $rate_id )
		);
	}

	/**
	 * The name for the rate's shipping abort reason
	 *
	 * @param int $rate_id
	 * @return string
	 */
	private function get_rate_name( $rate_id ) {
		return 'table_rate_shipping_abort_reason_' . $rate_id;
	}

	/**
	 * @param bool               $available
	 * @param array              $package
	 * @param WC_Shipping_Method $object
	 *
	 * @return bool
	 */
	public function shipping_table_rate_is_available( $available, $package, $object ) {

		add_filter(
			'option_woocommerce_table_rate_priorities_' . $object->instance_id,
			[ $this, 'filter_table_rate_priorities' ]
		);
		remove_filter(
			'woocommerce_shipping_table_rate_is_available',
			[ $this, 'shipping_table_rate_is_available' ],
			10
		);

		$available = $object->is_available( $package );

		add_filter(
			'woocommerce_shipping_table_rate_is_available',
			[ $this, 'shipping_table_rate_is_available' ],
			10,
			3
		);

		return $available;
	}

	/**
	 * @param array $priorities
	 *
	 * @return array
	 */
	public function filter_table_rate_priorities( $priorities ) {

		foreach ( $priorities as $slug => $priority ) {

			$shipping_class_term = get_term_by( 'slug', $slug, 'product_shipping_class' );
			if ( $shipping_class_term->slug !== $slug ) {
				unset( $priorities[ $slug ] );
				$priorities[ $shipping_class_term->slug ] = $priority;
			}
		}

		return $priorities;
	}
}
