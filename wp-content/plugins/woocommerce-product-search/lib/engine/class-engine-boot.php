<?php
/**
 * class-engine-boot.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 5.0.0
 */

namespace com\itthinx\woocommerce\search\engine;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Engine boot-loader.
 */
class Engine_Boot {

	/**
	 * Loads resources, registers actions.
	 */
	public static function init() {
		require_once 'class-tools.php';
		require_once 'class-matrix.php';
		require_once 'class-engine-settings.php';
		require_once 'class-engine.php';
		require_once 'class-engine-timer.php';
		require_once 'class-engine-stage-settings.php';
		require_once 'class-engine-stage.php';
		require_once 'class-engine-stage-words.php';
		require_once 'class-engine-stage-terms.php';
		require_once 'class-engine-stage-price.php';
		require_once 'class-engine-stage-stock.php';
		require_once 'class-engine-stage-sale.php';
		require_once 'class-engine-stage-rating.php';
		require_once 'class-engine-stage-featured.php';
		require_once 'class-engine-stage-visibility.php';
		require_once 'class-engine-stage-posts.php';
		require_once 'class-engine-stage-pagination.php';
		require_once 'class-engine-stage-synchrotron.php';
	}
}

Engine_Boot::init();
