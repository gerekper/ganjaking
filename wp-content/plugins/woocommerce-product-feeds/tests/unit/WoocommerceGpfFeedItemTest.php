<?php

class WoocommerceGpfFeedItemTest extends WoocommerceGpfTestAbstract {

	public function setUp() {
		parent::setUp();
		WoocommerceGpfWpMocks::setupMocks();
		WoocommerceGpfWcMocks::setupMocks();
		WoocommerceGpfMocks::setupMocks();
	}

	public function testShippingWeightConversion() {
		$p = $this->setup_simple_product( [ 'weight' => '2' ] );
		$c = new WoocommerceGpfCommon();
		// \WP_Mock::expectFilter( 'woocommerce_gpf_shipping_weight_unit' );
		$i = new WoocommerceGpfFeedItem( $p, $p, 'google', $c );
		$this->assertEquals( 'g', $i->shipping_weight_unit );
		$this->assertEquals( 3, $i->shipping_weight );
	}

	public function testSimpleProduct() {
		$p = $this->setup_simple_product();
		$c = new WoocommerceGpfCommon();
		// FIXME
		// \WP_Mock::expectFilter( 'woocommerce_gpf_image_style', \Mockery::any() );
		// \WP_Mock::expectFilter( 'woocommerce_gpf_description_type' );
		// \WP_Mock::expectFilter( 'woocommerce_gpf_shipping_dimension_unit' );
		$i = new WoocommerceGpfFeedItem( $p, $p, 'google', $c );
		$this->assertEquals( 'google', $i->feed_format );
		$this->assertFalse( $i->is_variation );
		$this->assertEquals( 1, $i->specific_id );
		$this->assertEquals( 1, $i->general_id );
		$this->assertEquals( 7.5, $i->sale_price_ex_tax );
		$this->assertEquals( 9, $i->sale_price_inc_tax );
		$this->assertEquals( 10, $i->regular_price_ex_tax );
		$this->assertEquals( 12, $i->regular_price_inc_tax );
		$this->assertEquals( 7.5, $i->price_ex_tax );
		$this->assertEquals( 9, $i->price_inc_tax );
		$this->assertEquals( 'woocommerce_gpf_1', $i->guid );
		$this->assertEquals( 'woocommerce_gpf_1', $i->item_group_id );
		$this->assertEquals( 'Simple product', $i->title );
		$this->assertEquals( 'Simple product description', $i->description );
		$this->assertEquals( 'http://placehold.it/101/101?text=full', $i->image_link );
		$this->assertEquals( 'http://www.example.com/permalink-for-1', $i->purchase_link );
		$this->assertTrue( $i->is_in_stock );
		$this->assertEquals( 'TESTSKUVALUE', $i->sku );
		$this->assertContains( 'http://placehold.it/201/201?text=full', $i->additional_images );
		$this->assertContains( 'http://placehold.it/301/301?text=full', $i->additional_images );
		$this->assertContains( 'http://placehold.it/401/401?text=full', $i->additional_images );
		$this->assertContains( 'http://placehold.it/501/501?text=full', $i->additional_images );
		$this->assertNotContains( 'http://placehold.it/101/101?text=full', $i->additional_images );
		$this->assertEquals( '1', $i->shipping_weight );
		$this->assertEquals( '5 cm', $i->additional_elements['shipping_width'][0] );
		$this->assertEquals( '5 cm', $i->additional_elements['shipping_length'][0] );
		$this->assertEquals( '5 cm', $i->additional_elements['shipping_height'][0] );
		// [sale_price_start_date] =>
		// [sale_price_end_date] =>
		// FIXME : Test that gallery image files exclude relevant images
		// FIXME : Test that attached image files exclude relevant images
	}

	public function testImageSizeFilter() {
		$p = $this->setup_simple_product();
		$c = new WoocommerceGpfCommon();
		\WP_Mock::onFilter( 'woocommerce_gpf_image_style' )
		        ->with( 'full' )
		        ->reply( 'small' );
		$i = new WoocommerceGpfFeedItem( $p, $p, 'google', $c );
		$this->assertEquals( 'http://placehold.it/101/101?text=small', $i->image_link );
		$this->assertContains( 'http://placehold.it/201/201?text=small', $i->additional_images );
		$this->assertContains( 'http://placehold.it/301/301?text=small', $i->additional_images );
		$this->assertContains( 'http://placehold.it/401/401?text=small', $i->additional_images );
		$this->assertContains( 'http://placehold.it/501/501?text=small', $i->additional_images );
	}

	/**
	 * Test that the image exclusion filters work as expected.
	 */
	public function testImageFilterExclusions() {
		$p = $this->setup_simple_product();
		$c = new WoocommerceGpfCommon();
		$i = new WoocommerceGpfFeedItem( $p, $p, 'google', $c );
		$this->assertContains( 'http://placehold.it/201/201?text=full', $i->additional_images );
		$this->assertContains( 'http://placehold.it/301/301?text=full', $i->additional_images );
		$this->assertContains( 'http://placehold.it/401/401?text=full', $i->additional_images );
		$this->assertContains( 'http://placehold.it/501/501?text=full', $i->additional_images );

		\WP_Mock::onFilter( 'woocommerce_gpf_include_product_gallery_images' )
		        ->with( true )
		        ->reply( false );
		$i = new WoocommerceGpfFeedItem( $p, $p, 'google', $c );
		$this->assertNotContains( 'http://placehold.it/201/201?text=full', $i->additional_images );
		$this->assertNotContains( 'http://placehold.it/301/301?text=full', $i->additional_images );
		$this->assertContains( 'http://placehold.it/401/401?text=full', $i->additional_images );
		$this->assertContains( 'http://placehold.it/501/501?text=full', $i->additional_images );

		\WP_Mock::onFilter( 'woocommerce_gpf_include_attached_images' )
		        ->with( true )
		        ->reply( false );
		$i = new WoocommerceGpfFeedItem( $p, $p, 'google', $c );
		$this->assertEmpty( $i->additional_images );
	}

	public function testDescriptionTypeFilter() {
		$p = $this->setup_simple_product();
		$c = new WoocommerceGpfCommon();
		\WP_Mock::onFilter( 'woocommerce_gpf_description_type' )
		        ->with( 'full' )
		        ->reply( 'short' );
		$i = new WoocommerceGpfFeedItem( $p, $p, 'google', $c );
		$this->assertEquals( 'Simple product short description', $i->description );
	}

	public function testShippingDimensionUnitFilter() {
		$p = $this->setup_simple_product();
		$c = new WoocommerceGpfCommon();
		\WP_Mock::onFilter( 'woocommerce_gpf_shipping_dimension_unit' )
		        ->with( 'cm' )
		        ->reply( 'in' );
		$i = new WoocommerceGpfFeedItem( $p, $p, 'google', $c );
		$this->assertEquals( '5 in', $i->additional_elements['shipping_width'][0] );
		$this->assertEquals( '5 in', $i->additional_elements['shipping_length'][0] );
		$this->assertEquals( '5 in', $i->additional_elements['shipping_height'][0] );
	}

	public function testCalculateValuesForSimpleProduct() {
		$this->setup_simple_product();
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$feed_item = new WoocommerceGpfFeedItem( wc_get_product( 1 ), wc_get_product( 1 ), 'bing', $c );
		$values    = $feed_item->additional_elements;

		// Check that feed-specific values excluded / included.
		$this->assertArrayNotHasKey( 'google_product_category', $values );
		$this->assertArrayHasKey( 'bing_category', $values );
		$this->assertEquals( [ 'Software' ], $values['bing_category'] );

		$feed_item = new WoocommerceGpfFeedItem( wc_get_product( 1 ), wc_get_product( 1 ), 'google', $c );
		$values    = $feed_item->additional_elements;
		$this->assertArrayNotHasKey( 'bing_category', $values );
		$this->assertArrayHasKey( 'google_product_category', $values );
		$this->assertEquals( [ 'Software' ], $values['google_product_category'] );

		// Test that values with store defaults come through.
		$this->assertArrayHasKey( 'custom_label_0', $values );
		$this->assertEquals( [ 'Store-level setting' ], $values['custom_label_0'] );

		// Test that values only set at category level are correct.
		$this->assertArrayHasKey( 'custom_label_1', $values );
		$this->assertEquals( [ 'Category-level setting' ], $values['custom_label_1'] );

		// Test that values overridden at category level are correct.
		$this->assertArrayHasKey( 'custom_label_3', $values );
		$this->assertEquals( [ 'Category-level setting' ], $values['custom_label_3'] );

		// Test that values overridden at category & product level are correct.
		$this->assertArrayHasKey( 'custom_label_4', $values );
		$this->assertEquals( [ 'Product-level setting' ], $values['custom_label_4'] );

		// Test that values overridden at just product level are correct.
		$this->assertArrayHasKey( 'brand', $values );
		$this->assertEquals( [ 'Product-level setting' ], $values['brand'] );

		$this->assertArrayHasKey( 'material', $values );
		$this->assertEquals( [ 'Test meta value' ], $values['material'] );
	}

	public function testCalculatedValuesForCorruptSimpleProduct() {
		$this->setup_simple_product_corrupt_gpf_config();
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$feed_item = new WoocommerceGpfFeedItem( wc_get_product( 1 ), wc_get_product( 1 ), 'bing', $c );
		$values    = $feed_item->additional_elements;
		$this->assertArrayNotHasKey( 'google_product_category', $values );
		$this->assertArrayHasKey( 'bing_category', $values );
		$this->assertEquals( [ 'Software' ], $values['bing_category'] );

		$feed_item = new WoocommerceGpfFeedItem( wc_get_product( 1 ), wc_get_product( 1 ), 'google', $c );
		$values    = $feed_item->additional_elements;
		$this->assertArrayNotHasKey( 'bing_category', $values );
		$this->assertArrayHasKey( 'google_product_category', $values );
		$this->assertEquals( [ 'Software' ], $values['google_product_category'] );

		// Test that values with store defaults come through.
		$this->assertArrayHasKey( 'custom_label_0', $values );
		$this->assertEquals( [ 'Store-level setting' ], $values['custom_label_0'] );

		// Test that values only set at category level are correct.
		$this->assertArrayHasKey( 'custom_label_1', $values );
		$this->assertEquals( [ 'Category-level setting' ], $values['custom_label_1'] );

		// Test that values overridden at category level are correct.
		$this->assertArrayHasKey( 'custom_label_3', $values );
		$this->assertEquals( [ 'Category-level setting' ], $values['custom_label_3'] );
	}

	public function testCalculatePrepopulatedTaxValuesForSimpleProduct() {
		$this->setup_simple_product();
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$feed_item = new WoocommerceGpfFeedItem( wc_get_product( 1 ), wc_get_product( 1 ), 'google', $c );
		$this->assertArrayHasKey( 'delivery_label', $feed_item->additional_elements );
		$this->assertEquals( [ 'Standard parcel' ], $feed_item->additional_elements['delivery_label'] );
	}

	public function testCalculatePrepopulatedFieldValuesForSimpleProduct() {
		$this->setup_simple_product();
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$feed_item = new WoocommerceGpfFeedItem( wc_get_product( 1 ), wc_get_product( 1 ), 'google', $c );
		$this->assertArrayHasKey( 'mpn', $feed_item->additional_elements );
		$this->assertEquals( [ 'TESTSKUVALUE' ], $feed_item->additional_elements['mpn'] );
	}

	public function testCalculateValuesForVariationProduct() {
		$this->setup_variable_product();
		$c = new WoocommerceGpfCommon();
		$c->initialise();

		// Run these tests in legacy description mode.
		// The newer modes are tested elsewhere.
		$settings                                       = WooCommerceGpfMocks::getMockConfigSettings();
		$settings['product_prepopulate']['description'] = 'description:varfull';
		$c->setSettings( $settings );

		$feed_item = new WoocommerceGpfFeedItem( wc_get_product( 3 ), wc_get_product( 2 ), 'google', $c );

		$this->assertEquals( 'Variation description #3', $feed_item->description );

		$values = $feed_item->additional_elements;

		$this->assertArrayHasKey( 'google_product_category', $values );
		$this->assertEquals( [ 'Software' ], $values['google_product_category'] );

		// Test that values with store defaults come through.
		$this->assertArrayHasKey( 'custom_label_0', $values );
		$this->assertEquals( [ 'Store-level setting' ], $values['custom_label_0'] );

		// Test that values only set at category level are correct.
		$this->assertArrayHasKey( 'custom_label_1', $values );
		$this->assertEquals( [ 'Category-level setting' ], $values['custom_label_1'] );

		// Test that values overridden at category level are correct.
		$this->assertArrayHasKey( 'custom_label_3', $values );
		$this->assertEquals( [ 'Category-level setting' ], $values['custom_label_3'] );

		// Test that values overridden at category & product level are correct.
		$this->assertArrayHasKey( 'custom_label_4', $values );
		$this->assertEquals( [ 'Product-level setting' ], $values['custom_label_4'] );

		// Test that values provided at variation level are correct.
		$this->assertArrayHasKey( 'gtin', $values );
		$this->assertEquals( [ 'variation-gtin' ], $values['gtin'] );

		// Test that store defaults overridden at variation level are correct.
		$this->assertArrayHasKey( 'brand', $values );
		$this->assertEquals( [ 'Variation-level setting' ], $values['brand'] );
	}

	public function testCalculatePrepopulatedTaxValuesForVariableProduct() {
		$this->setup_variable_product();
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$feed_item = new WoocommerceGpfFeedItem( wc_get_product( 3 ), wc_get_product( 2 ), 'google', $c );
		$this->assertArrayHasKey( 'delivery_label', $feed_item->additional_elements );
		$this->assertEquals( [ 'Large parcel' ], $feed_item->additional_elements['delivery_label'] );
	}

	public function testCalculatePrepopulatedFieldValuesForVariableProduct() {
		$this->setup_variable_product();
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$feed_item = new WoocommerceGpfFeedItem( wc_get_product( 3 ), wc_get_product( 2 ), 'google', $c );
		$this->assertArrayHasKey( 'mpn', $feed_item->additional_elements );
		$this->assertEquals( [ 'TESTVARIATIONSKUVALUE' ], $feed_item->additional_elements['mpn'] );
	}

	public function testDescriptionPrepopulationOptionsSimple() {

		// Product with main description.
		$p1 = $this->setup_simple_product();
		// Product without main description.
		$p2                      = $this->setup_simple_product();
		$p2->data['description'] = '';
		// Product without short description.
		$p3                            = $this->setup_simple_product();
		$p3->data['short_description'] = '';

		// Get the standard settings.
		$settings = WooCommerceGpfMocks::getMockConfigSettings();

		// description:fullvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:fullvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $p1, $p1, 'google', $c );
		$this->assertEquals( 'Simple product description', $feed_item->description );
		$feed_item = new WoocommerceGpfFeedItem( $p2, $p2, 'google', $c );
		$this->assertEquals( 'Simple product short description', $feed_item->description );

		// description:shortvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:shortvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $p1, $p1, 'google', $c );
		$this->assertEquals( 'Simple product short description', $feed_item->description );
		$feed_item = new WoocommerceGpfFeedItem( $p3, $p3, 'google', $c );
		$this->assertEquals( 'Simple product description', $feed_item->description );

		// description:full
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:full';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $p1, $p1, 'google', $c );
		$this->assertEquals( 'Simple product description', $feed_item->description );
		$feed_item = new WoocommerceGpfFeedItem( $p2, $p2, 'google', $c );
		$this->assertEquals( 'Simple product short description', $feed_item->description );

		// description:short
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:short';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $p1, $p1, 'google', $c );
		$this->assertEquals( 'Simple product short description', $feed_item->description );
		$feed_item = new WoocommerceGpfFeedItem( $p3, $p3, 'google', $c );
		$this->assertEquals( 'Simple product description', $feed_item->description );

		// description:varfull
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varfull';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $p1, $p1, 'google', $c );
		$this->assertEquals( 'Simple product description', $feed_item->description );
		$feed_item = new WoocommerceGpfFeedItem( $p2, $p2, 'google', $c );
		$this->assertEquals( 'Simple product short description', $feed_item->description );

		// description:varshort
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varshort';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $p1, $p1, 'google', $c );
		$this->assertEquals( 'Simple product short description', $feed_item->description );
		$feed_item = new WoocommerceGpfFeedItem( $p3, $p3, 'google', $c );
		$this->assertEquals( 'Simple product description', $feed_item->description );
	}

	/**
	 * Test variable product description processing:
	 * - Main product description  ✖ ️
	 * - Short product description ✖ ️
	 * - Variation description     ✖
	 **/
	public function testDescriptionPrepopulationOptionsVariation0() {

		// Setup products.
		$p = $this->setup_variable_product();
		$p->data['description'] = '';
		$p->data['short_description'] = '';
		$v = wc_get_product(3);
		$v->data['description'] = '';

		// Get the standard settings.
		$settings = WooCommerceGpfMocks::getMockConfigSettings();

		// description:fullvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:fullvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "", $feed_item->description );

		// description:shortvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:shortvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "", $feed_item->description );

		// description:full
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:full';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( '', $feed_item->description );

		// description:short
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:short';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( '', $feed_item->description );

		// description:varfull
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varfull';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( '', $feed_item->description );

		// description:varshort
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varshort';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( '', $feed_item->description );
	}

	/**
	 * Test variable product description processing:
	 * - Main product description  ✖ ️
	 * - Short product description ✖ ️
	 * - Variation description     ✔
	 **/
	public function testDescriptionPrepopulationOptionsVariation1() {

		// Setup products.
		$p = $this->setup_variable_product();
		$p->data['description'] = '';
		$p->data['short_description'] = '';
		$v = wc_get_product(3);

		// Get the standard settings.
		$settings = WooCommerceGpfMocks::getMockConfigSettings();

		// description:fullvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:fullvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "Variation description #3", $feed_item->description );

		// description:shortvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:shortvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "Variation description #3", $feed_item->description );

		// description:full
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:full';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( '', $feed_item->description );

		// description:short
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:short';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( '', $feed_item->description );

		// description:varfull
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varfull';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variation description #3', $feed_item->description );

		// description:varshort
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varshort';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variation description #3', $feed_item->description );
	}

	/**
	 * Test variable product description processing:
	 * - Main product description  ✖ ️
	 * - Short product description ✔ ️
	 * - Variation description     ✖
	 **/
	public function testDescriptionPrepopulationOptionsVariation2() {

		// Setup products.
		$p = $this->setup_variable_product();
		$p->data['description'] = '';
		$v = wc_get_product(3);
		$v->data['description'] = '';

		// Get the standard settings.
		$settings = WooCommerceGpfMocks::getMockConfigSettings();

		// description:fullvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:fullvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "Variable short description #2", $feed_item->description );

		// description:shortvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:shortvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "Variable short description #2", $feed_item->description );

		// description:full
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:full';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable short description #2', $feed_item->description );

		// description:short
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:short';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable short description #2', $feed_item->description );

		// description:varfull
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varfull';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable short description #2', $feed_item->description );

		// description:varshort
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varshort';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable short description #2', $feed_item->description );
	}

	/**
	 * Test variable product description processing:
	 * - Main product description  ✖ ️
	 * - Short product description ✔ ️
	 * - Variation description     ✔
	 **/
	public function testDescriptionPrepopulationOptionsVariation3() {

		// Setup products.
		$p = $this->setup_variable_product();
		$p->data['description'] = '';
		$v = wc_get_product(3);

		// Get the standard settings.
		$settings = WooCommerceGpfMocks::getMockConfigSettings();

		// description:fullvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:fullvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "Variable short description #2\nVariation description #3", $feed_item->description );

		// description:shortvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:shortvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "Variable short description #2\nVariation description #3", $feed_item->description );

		// description:full
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:full';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable short description #2', $feed_item->description );

		// description:short
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:short';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable short description #2', $feed_item->description );

		// description:varfull
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varfull';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variation description #3', $feed_item->description );

		// description:varshort
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varshort';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variation description #3', $feed_item->description );
	}

	/**
	 * Test variable product description processing:
	 * - Main product description  ✔ ️
	 * - Short product description ✖ ️
	 * - Variation description     ✖
	 **/
	public function testDescriptionPrepopulationOptionsVariation4() {

		// Setup products.
		$p = $this->setup_variable_product();
		$p->data['short_description'] = '';
		$v = wc_get_product(3);
		$v->data['description'] = '';

		// Get the standard settings.
		$settings = WooCommerceGpfMocks::getMockConfigSettings();

		// description:fullvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:fullvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "Variable description #2", $feed_item->description );

		// description:shortvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:shortvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "Variable description #2", $feed_item->description );

		// description:full
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:full';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable description #2', $feed_item->description );

		// description:short
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:short';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable description #2', $feed_item->description );

		// description:varfull
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varfull';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable description #2', $feed_item->description );

		// description:varshort
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varshort';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable description #2', $feed_item->description );
	}

	/**
	 * Test variable product description processing:
	 * - Main product description  ✔ ️
	 * - Short product description ✖ ️
	 * - Variation description     ✔
	 **/
	public function testDescriptionPrepopulationOptionsVariation5() {

		// Setup products.
		$p = $this->setup_variable_product();
		$p->data['short_description'] = '';
		$v = wc_get_product(3);

		// Get the standard settings.
		$settings = WooCommerceGpfMocks::getMockConfigSettings();

		// description:fullvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:fullvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "Variable description #2\nVariation description #3", $feed_item->description );

		// description:shortvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:shortvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "Variable description #2\nVariation description #3", $feed_item->description );

		// description:full
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:full';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable description #2', $feed_item->description );

		// description:short
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:short';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable description #2', $feed_item->description );

		// description:varfull
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varfull';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variation description #3', $feed_item->description );

		// description:varshort
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varshort';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variation description #3', $feed_item->description );
	}

	/**
	 * Test variable product description processing:
	 * - Main product description  ✔ ️
	 * - Short product description ✔ ️
	 * - Variation description     ✖
	 **/
	public function testDescriptionPrepopulationOptionsVariation6() {

		// Setup products.
		$p = $this->setup_variable_product();
		$v = wc_get_product(3);
		$v->data['description'] = '';

		// Get the standard settings.
		$settings = WooCommerceGpfMocks::getMockConfigSettings();

		// description:fullvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:fullvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "Variable description #2", $feed_item->description );

		// description:shortvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:shortvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "Variable short description #2", $feed_item->description );

		// description:full
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:full';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable description #2', $feed_item->description );

		// description:short
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:short';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable short description #2', $feed_item->description );

		// description:varfull
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varfull';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable description #2', $feed_item->description );

		// description:varshort
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varshort';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable short description #2', $feed_item->description );
	}

	/**
	 * Test variable product description processing:
	 * - Main product description  ✔ ️
	 * - Short product description ✔ ️
	 * - Variation description     ✔
	 **/
	public function testDescriptionPrepopulationOptionsVariation7() {

		// Product with main description.
		$p = $this->setup_variable_product();
		$v = wc_get_product(3);

		// Get the standard settings.
		$settings = WooCommerceGpfMocks::getMockConfigSettings();

		// description:fullvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:fullvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "Variable description #2\nVariation description #3", $feed_item->description );

		// description:shortvar
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:shortvar';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( "Variable short description #2\nVariation description #3", $feed_item->description );

		// description:full
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:full';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable description #2', $feed_item->description );

		// description:short
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:short';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variable short description #2', $feed_item->description );

		// description:varfull
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varfull';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variation description #3', $feed_item->description );

		// description:varshort
		$c = new WoocommerceGpfCommon();
		$c->initialise();
		$settings['product_prepopulate']['description'] = 'description:varshort';
		$c->setSettings( $settings );
		$feed_item = new WoocommerceGpfFeedItem( $v, $p, 'google', $c );
		$this->assertEquals( 'Variation description #3', $feed_item->description );
	}
}
