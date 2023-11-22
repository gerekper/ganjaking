<?php

namespace WCML\Coupons;

use WPML\FP\Obj;

class Helper {

	/**
	 * Constant for CRUD pages for coupons (e.g. edit.php?post_type=shop_coupon)
	 *
	 * @var string
	 */
	const POST_TYPE_COUPON = 'shop_coupon';

	/**
	 * Constant for list of coupons page (e.g. edit.php?post_type=shop_coupon)
	 *
	 * @var string
	 */
	const PAGE_COUPON_LIST = 'edit.php';

	/**
	 * Constant for new coupon page (e.g. post-new.php?post_type=shop_coupon)
	 *
	 * @var string
	 */
	const PAGE_COUPON_NEW = 'post-new.php';

	/**
	 * Constant for edit coupon page (e.g. post.php?post=170&action=edit)
	 *
	 * @var string
	 */
	const PAGE_COUPON_EDIT = 'post.php';

	/**
	 * Constant for edit action
	 *
	 * @var string
	 */
	const ACTION_EDIT = 'edit';

	/**
	 * Checks if the post type is shop coupon. If no parameter passed, checking for $_GET['post_type']
	 *
	 * @param string|null $postType
	 * @return bool
	 */
	private static function isPostTypeCoupon( $postType = null ) {
		if ( null === $postType ) {
			$postType = Obj::prop( 'post_type', $_GET );
		}
		return self::POST_TYPE_COUPON  === $postType;
	}

	/**
	 * Compared current WP $pagenow with the expected $page
	 *
	 * @param string $page
	 * @return bool
	 */
	private static function isCouponAdminScreen( $page ) {
		global $pagenow;
		return is_admin() && $page === $pagenow;
	}

	/**
	 * Checks if the current screen is an admin screen for Legacy WooCommerce New Coupon (non-HPOS).
	 *
	 * @return bool
	 */
	public static function isCouponCreateAdminScreen() {
		return self::isCouponAdminScreen( self::PAGE_COUPON_NEW ) && self::isPostTypeCoupon();
	}

	/**
	 * Checks if the current screen is an admin screen for Legacy list of WooCommerce coupons (non-HPOS).
	 *
	 * @return bool
	 */
	public static function isCouponListAdminScreen() {
		return self::isCouponAdminScreen( self::PAGE_COUPON_LIST ) && self::isPostTypeCoupon();
	}

	/**
	 * Checks if the current screen is an admin screen for WooCommerce Legacy Edit Coupon (non-HPOS).
	 *
	 * @return bool
	 */
	public static function isCouponEditAdminScreen() {
		$isActionEdit = Obj::prop( 'action', $_GET ) === self::ACTION_EDIT;
		$getPost      = Obj::prop( 'post', $_GET );

		return self::isCouponAdminScreen( self::PAGE_COUPON_EDIT ) && $isActionEdit && $getPost && self::isPostTypeCoupon( get_post_type( $getPost ) );
	}
}
