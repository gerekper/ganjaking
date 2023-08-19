<?php

namespace ACA\WC\Column\ShopCoupon;

use AC;
use ACP;
use WC_Coupon;

/**
 * @since 2.0
 */
class UsedBy extends AC\Column
	implements ACP\Export\Exportable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	public function __construct() {
		$this->set_type( 'column-wc-coupon_user' );
		$this->set_label( __( 'Used By', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	public function get_raw_value( $id ) {
		$coupon = new WC_Coupon( $id );
		$users = $coupon->get_used_by();

		if ( ! $users ) {
			return [];
		}

		return $users;
	}

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public function get_value( $id ) {
		$users = $this->get_raw_value( $id );

		if ( empty( $users ) ) {
			return $this->get_empty_char();
		}

		$values = [];

		foreach ( $users as $user ) {
			if ( is_numeric( $user ) ) {

				$user = get_userdata( $user );

				if ( $user ) {
					$values[] = ac_helper()->html->link( get_edit_user_link( $user->ID ), ac_helper()->user->get_display_name( $user ) );
				}
			} else if ( is_email( $user ) ) {
				$values[] = ac_helper()->html->link( 'mailto:' . $user, $user, [ 'tooltip' => __( 'Not a registered user', 'codepress-admin-columns' ) ] );
			}
		}

		return ac_helper()->html->more( $values, 5 );
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

}