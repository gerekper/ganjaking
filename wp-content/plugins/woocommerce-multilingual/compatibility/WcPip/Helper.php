<?php

namespace WCML\Compatibility\WcPip;

class Helper {

	/**
	 * @return false|int|string
	 */
	public static function getPipOrderId() {
		$order_id = false;

		if ( isset( $_GET['wc_pip_action'] ) && isset( $_GET['order_id'] ) ) {
			$order_id = $_GET['order_id'];
		} elseif (
			isset( $_POST['action'] ) &&
			(
				$_POST['action'] == 'wc_pip_order_send_email' ||
				$_POST['action'] == 'wc_pip_send_email_packing_list'
			) &&
			isset( $_POST['order_id'] )
		) {
			$order_id = $_POST['order_id'];
		}

		return $order_id;
	}
}
