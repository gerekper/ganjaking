<?php
/**
 * WooCommerce Checkout Add-Ons
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Admin\Meta_Boxes;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On;

defined( 'ABSPATH' ) or exit;

/**
 * Abstract Add-On Meta Box Class
 *
 * @since 2.0.0
 */
abstract class Add_On_Meta_Box {


	/** @var Add_On|null the add-on loaded in this meta box */
	protected $add_on;


	/**
	 * Constructs the class.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On|null $add_on the add-on to provide data for the meta box
	 */
	public function __construct( $add_on = null ) {

		if ( $add_on instanceof Add_On ) {
			$this->add_on = $add_on;
		}
	}


	/**
	 * Renders the meta box to the page.
	 *
	 * @since 2.0.0
	 */
	abstract public function render();


	/**
	 * Creates and renders the meta box.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On|null $add_on the add-on to provide data for the meta box
	 */
	public static function create_and_render( $add_on = null ) {

		$meta_box = new static( $add_on );
		$meta_box->render();
	}


}
