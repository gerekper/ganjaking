<?php

class WoocommerceGpfCommonTest extends WoocommerceGpfTestAbstract {

	public function setUp() {
		parent::setUp();

		WoocommerceGpfWpMocks::setupMocks();
		WoocommerceGpfWcMocks::setupMocks();
		WoocommerceGpfMocks::setupMocks();

		$this->c = new WoocommerceGpfCommon();
		$this->c->initialise();
	}

	public function fixmeTestFiltersFields() {
		// FIXME
		// \WP_Mock::expectFilter( 'woocommerce_gpf_all_product_fields' );
		// \WP_Mock::expectFilter( 'woocommerce_gpf_feed_types' );
	}

	public function testGetFeedTypes() {
		$types = $this->c->get_feed_types();
		$this->assertInternalType( 'array', $types, 'Feed types not correct type (expected array)' );
		$this->assertArrayHasKey( 'google', $types, "Feed type 'google' not registered" );
		$this->assertArrayHasKey( 'googleinventory', $types, "Feed type 'google' not registered" );
		$this->assertArrayHasKey( 'bing', $types, "Feed type 'google' not registered" );

		$new_types = $types;
		$new_types['new'] = $new_types['google'];
		$c     = new WoocommerceGpfCommon();
		\WP_Mock::onFilter( 'woocommerce_gpf_feed_types' )
		->with( $types )
		->reply( $new_types );
		$c->initialise();
		$types = $c->get_feed_types();
		$this->assertArrayHasKey( 'new', $types, 'New feed type not registered' );
	}

	public function testGetPrepopulateOptions() {
		global $wpdb, $table_prefix;
		// Mocks.
		define( 'MONTH_IN_SECONDS', 2592000 );
		\WP_Mock::userFunction(
			'get_transient',
			array(
				'args' => [ 'woocommerce_gpf_meta_prepopulate_options' ],
				'return' => false,
			)
		);
		$wpdb = Mockery::mock( '\WPDB' );
		$wpdb->shouldReceive( 'get_col' )
			->once()
			->with( "SELECT DISTINCT( wp_postmeta.meta_key )
		          FROM wp_posts
			 LEFT JOIN wp_postmeta
			        ON wp_posts.ID = wp_postmeta.post_id
				 WHERE wp_posts.post_type IN ( 'product', 'product_variation' )" )
			->andReturn( [ 'test_meta', 'other_test_meta' , '_internal_meta' ] );
		\WP_Mock::userFunction(
			'set_transient',
			array(
				'args' => [ 'woocommerce_gpf_meta_prepopulate_options', Mockery::any(), MONTH_IN_SECONDS ],
				'return' => true,
			)
		);
		$options = $this->c->get_prepopulate_options();
		$this->assertInternalType( 'array', $options );
		$this->assertArrayHasKey( 'tax:product_type', $options );
		$this->assertArrayHasKey( 'tax:product_cat', $options );
		$this->assertArrayHasKey( 'tax:product_tag', $options );
		$this->assertArrayHasKey( 'tax:product_shipping_class', $options );
		$this->assertArrayHasKey( 'tax:pa_colour', $options );
		$this->assertArrayHasKey( 'field:sku', $options );
		$this->assertArrayHasKey( 'meta:test_meta', $options );
		$this->assertArrayHasKey( 'meta:other_test_meta', $options );
		$this->assertArrayNotHasKey( 'meta:_internal_meta', $options );
	}

	public function testGetDefaultsForProduct() {
		$this->setup_simple_product();
		// Test that values with no default do not appear.
		$defaults = $this->c->get_defaults_for_product( 1, 'all' );
		$this->assertArrayNotHasKey( 'gender', $defaults );

		// Test that values with store defaults come through.
		$this->assertArrayHasKey( 'custom_label_0', $defaults );
		$this->assertEquals( 'Store-level setting', $defaults['custom_label_0'] );

		// Test that values only set at category level are correct.
		$this->assertArrayHasKey( 'custom_label_1', $defaults );
		$this->assertEquals( 'Category-level setting', $defaults['custom_label_1'] );

		// Test that values overridden at category level are correct.
		$this->assertArrayHasKey( 'custom_label_3', $defaults );
		$this->assertEquals( 'Category-level setting', $defaults['custom_label_3'] );
		$this->assertArrayHasKey( 'custom_label_4', $defaults );
		$this->assertEquals( 'Category-level setting', $defaults['custom_label_4'] );
	}

}
