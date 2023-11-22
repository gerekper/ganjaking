<?php

namespace WCML\Orders\Legacy;

use WPML\FP\Obj;

class Helper {

	/**
	 * Legacy constant for CRUD pages for orders (e.g. edit.php?post_type=shop_order)
	 *
	 * @var string
	 */
	const POST_TYPE_ORDER = 'shop_order';

	/**
	 * Legacy constant for list of orders page (e.g. edit.php?post_type=shop_order)
	 *
	 * @var string
	 */
	const PAGE_ORDER_LIST = 'edit.php';

	/**
	 * Legacy constant for new order page (e.g. post-new.php?post_type=shop_order)
	 *
	 * @var string
	 */
	const PAGE_ORDER_NEW = 'post-new.php';

	/**
	 * Legacy constant for edit order page (e.g. post.php?post=162&action=edit)
	 *
	 * @var string
	 */
	const PAGE_ORDER_EDIT = 'post.php';

	/**
	 * Legacy constant for edit action
	 *
	 * @var string
	 */
	const ACTION_EDIT = 'edit';

	/**
	 * Checks if the post type is the one we expect. If no parameter passed, checking for $_GET['post_type']
	 *
	 * @param string|null $postType
	 * @return bool
	 */
	private static function isPostTypeOrder( $postType = null ) {
		if ( null === $postType ) {
			$postType = Obj::prop( 'post_type', $_GET );
		}
		return self::POST_TYPE_ORDER  === $postType;
	}

	/**
	 * Compared current WP $pagenow with the expected $page
	 *
	 * @param string $page
	 * @return bool
	 */
	private static function isOrderAdminScreen( $page ) {
		global $pagenow;
		return is_admin() && $page === $pagenow;
	}

	/**
	 * Checks if the current screen is an admin screen for Legacy WooCommerce New Order (non-HPOS).
	 *
	 * @return bool
	 */
	public static function isOrderCreateAdminScreen() {
		return self::isOrderAdminScreen( self::PAGE_ORDER_NEW ) && self::isPostTypeOrder();
	}

	/**
	 * Checks if the current screen is an admin screen for Legacy list of WooCommerce orders (non-HPOS).
	 *
	 * @return bool
	 */
	public static function isOrderListAdminScreen() {
		return self::isOrderAdminScreen( self::PAGE_ORDER_LIST ) && self::isPostTypeOrder();
	}

	/**
	 * Checks if the current screen is an admin screen for WooCommerce Legacy Edit Order (non-HPOS).
	 *
	 * @return bool
	 */
	public static function isOrderEditAdminScreen() {
		$isActionEdit = Obj::prop( 'action', $_GET ) === self::ACTION_EDIT;
		$getPost      = Obj::prop( 'post', $_GET );

		return self::isOrderAdminScreen( self::PAGE_ORDER_EDIT ) && $isActionEdit && $getPost && self::isPostTypeOrder( get_post_type( $getPost ) );
	}
}
