<?php

/**
 * Class YITH_WCMV_Unit_Test_Case_With_Store.
 *
 * This helper class should ONLY be used for unit tests!.
 */
class YITH_WCMV_Unit_Test_Case_With_Store extends WP_UnitTestCase {
    /**
     * @var WC_Product[] Array of products to clean up.
     */
    protected $products = array();

    /**
     * @var WP_Post[] Array of posts to clean up.
     */
    protected $posts = array();

	/**
	 * Run to init unit test
	 */
    public function setUp() {
        parent::setUp();
    }

    /**
     * Clean up after each test. DB changes are reverted in parent::tearDown().
     */
    public function tearDown() {
        parent::tearDown();
    }
}
