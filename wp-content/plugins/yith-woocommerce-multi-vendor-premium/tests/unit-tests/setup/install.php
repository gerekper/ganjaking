<?php

/**
 * Class WC_Tests_Install.
 * @package WooCommerce\Tests\Util
 */
class YITH_WCMV_Tests_Install extends YITH_WCMV_Unit_Test_Case_With_Store {

	/**
	 * Test - create pages.
	 */
	public function test_create_pages() {
		/* === Before Test === */

		// Check if wc_create_page function exists
		if( ! function_exists( 'wc_create_page' ) ){
			include_once dirname( __FILE__ ) . '/admin/wc-admin-functions.php';
		}

		// Delete pages and store the results
		$become_a_vendor_page_id = get_option( 'yith_wpv_become_a_vendor_page_id' );
		$terms_and_conditions_page_id = get_option( 'yith_wpv_terms_and_conditions_page_id' );
		wp_delete_post( get_option( 'yith_wpv_become_a_vendor_page_id' ), true );
		wp_delete_post( get_option( 'yith_wpv_terms_and_conditions_page_id' ), true );

		// Clear options
		delete_option( 'yith_wpv_become_a_vendor_page_id' );
		delete_option( 'yith_wpv_terms_and_conditions_page_id' );

		//Check if option is correctly deleted
		$this->assertFalse( get_option( 'yith_wpv_become_a_vendor_page_id', false ) );
		$this->assertFalse( get_option( 'yith_wpv_terms_and_conditions_page_id', false ) );

		//Check if pages is correctly deleted
		$this->assertEquals( null, get_post( $become_a_vendor_page_id ) );
		$this->assertEquals( null, get_post( $terms_and_conditions_page_id ) );

		YITH_Vendors_Admin_Premium::create_plugins_page();

		//Check for option
		$this->assertGreaterThan( 0, get_option( 'yith_wpv_become_a_vendor_page_id' ) );
		$this->assertGreaterThan( 0, get_option( 'yith_wpv_terms_and_conditions_page_id' ) );

		//Check for posts
		$this->assertInstanceOf( 'WP_Post', get_post( get_option( 'yith_wpv_become_a_vendor_page_id' ), OBJECT ) );
		$this->assertInstanceOf( 'WP_Post', get_post( get_option( 'yith_wpv_terms_and_conditions_page_id' ), OBJECT ) );
	}

	/**
	 * Test - create roles.
	 */
	public function test_create_roles() {
		//Remove vendor role
		remove_role( 'yith_vendor' );

		//Check if the Vendors class retreive the correct vendor role name
		$this->assertEquals( 'yith_vendor', YITH_Vendors()->get_role_name() );

		//Check vendor role doesn't exists
		$this->assertNull( get_role( 'yith_vendor' ) );

		//Install the vendor role and check if exist
		YITH_Vendors()::add_vendor_role();
		$this->assertNotNull( get_role( 'yith_vendor' ) );
	}

	/**
	 * Test - remove roles.
	 */
	public function test_remove_roles() {
		//Check if vendor role exist
		$this->assertNotNull( get_role( 'yith_vendor' ) );

		//Remove Vendor Role
		YITH_Vendors::remove_vendor_role();

		//Check vendor role doesn't exists
		$this->assertNull( get_role( 'yith_vendor' ) );
	}
}
