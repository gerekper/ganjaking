<?php
/**
 * WooCommerce Print Invoices/Packing Lists
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Print
 * Invoices/Packing Lists to newer versions in the future. If you wish to
 * customize WooCommerce Print Invoices/Packing Lists for your needs please refer
 * to http://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * PIP Email class
 *
 * Handles sending documents by email tasks
 *
 * @since 3.0.0
 */
class WC_PIP_Emails {


	/**
	 * Email-related hooks
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		// WooCommerce emails processing
		add_filter( 'woocommerce_email_classes',         array( $this, 'email_classes' ) );
		add_action( 'wc_pip_invoice_email_trigger',      array( 'WC_Emails', 'send_transactional_email' ), 10, 10 );
		add_action( 'wc_pip_packing_list_email_trigger', array( 'WC_Emails', 'send_transactional_email' ), 10, 10 );
		add_action( 'wc_pip_pick_list_email_trigger',    array( 'WC_Emails', 'send_transactional_email' ), 10, 10 );
	}


	/**
	 * Add PIP email classes to WC Emails
	 *
	 * @since 3.0.0
	 * @param array $emails
	 * @return array
	 */
	public function email_classes( $emails ) {

		$emails['pip_email_invoice']      = wc_pip()->load_class( '/includes/emails/class-wc-pip-email-invoice.php',      'WC_PIP_Email_Invoice' );
		$emails['pip_email_packing_list'] = wc_pip()->load_class( '/includes/emails/class-wc-pip-email-packing-list.php', 'WC_PIP_Email_Packing_List' );
		$emails['pip_email_pick_list']    = wc_pip()->load_class( '/includes/emails/class-wc-pip-email-pick-list.php',    'WC_PIP_Email_Pick_List' );

		return $emails;
	}


}
