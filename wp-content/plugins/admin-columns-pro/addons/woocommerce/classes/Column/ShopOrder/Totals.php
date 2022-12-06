<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\ConditionalFormat\Formatter\PriceFormatter;
use ACA\WC\Filtering;
use ACA\WC\Settings;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use WC_Order;

/**
 * @since 3.0
 */
class Totals extends AC\Column\Meta
	implements ACP\Filtering\Filterable, ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	/**
	 * @var WC_Order[]
	 */
	private $orders;

	public function __construct() {
		$this->set_type( 'column-wc-order_totals' )
		     ->set_label( __( 'Totals', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function conditional_format(): ?FormattableConfig {
		return new FormattableConfig( new PriceFormatter() );
	}

	public function get_meta_key() {
		switch ( $this->get_setting_total_property() ) {
			case 'total' :
				$key = '_order_total';

				break;
			case 'discount' :
				$key = '_cart_discount';

				break;
			case 'shipping' :
				$key = '_order_shipping';

				break;
			default:
				$key = false;
		}

		return $key;
	}

	public function get_value( $id ) {
		$price = $this->get_raw_value( $id );

		if ( ! $price ) {
			return $this->get_empty_char();
		}

		return wc_price( $this->get_raw_value( $id ), [ 'currency' => $this->get_order( $id )->get_currency() ] );
	}

	public function get_raw_value( $id ) {

		switch ( $this->get_setting_total_property() ) {
			case 'subtotal' :
				$value = $this->get_order( $id )->get_subtotal();

				break;
			case 'discount' :
				$value = $this->get_order( $id )->get_total_discount();

				break;
			case 'refunded' :
				$value = $this->get_order( $id )->get_total_refunded();

				break;
			case 'tax' :
				$value = $this->get_order( $id )->get_total_tax();

				break;
			case 'shipping' :
				$value = $this->get_order( $id )->get_shipping_total();

				break;
			case 'paid' :
				$value = 0;

				if ( $this->get_order( $id )->is_paid() ) {
					$value = $this->get_order( $id )->get_total() - $this->get_order( $id )->get_total_refunded();
				}
				break;
			default :
				$value = $this->get_order( $id )->get_total();
		}

		return $value;
	}

	/**
	 * @param int $id
	 *
	 * @return WC_Order
	 */
	private function get_order( $id ) {
		if ( ! isset( $this->orders[ $id ] ) ) {
			$this->orders[ $id ] = wc_get_order( $id );
		}

		return $this->orders[ $id ];
	}

	/**
	 * @return string|false
	 */
	private function get_setting_total_property() {
		$setting = $this->get_setting( 'order_total_property' );

		if ( ! $setting instanceof Settings\ShopOrder\Totals ) {
			return false;
		}

		return $setting->get_order_total_property();
	}

	public function register_settings() {
		$this->add_setting( new Settings\ShopOrder\Totals( $this ) );
	}

	public function filtering() {
		if ( $this->get_meta_key() ) {
			return new Filtering\Number( $this );
		}

		return new ACP\Filtering\Model\Disabled( $this );
	}

	public function search() {
		if ( ! $this->get_meta_key() ) {
			return false;
		}

		return new ACP\Search\Comparison\Meta\Decimal( $this->get_meta_key(), AC\MetaType::POST );
	}

	public function sorting() {
		if ( $this->get_meta_key() ) {
			return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key() );
		}

		return new ACP\Sorting\Model\Disabled();
	}

}