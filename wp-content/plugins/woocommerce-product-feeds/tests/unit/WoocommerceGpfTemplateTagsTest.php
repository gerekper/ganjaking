<?php

class WoocommerceGpfTemplateTagsTest extends WoocommerceGpfTestAbstract {

	public function setUp() {
		parent::setUp();

		WoocommerceGpfWpMocks::setupMocks();
		WoocommerceGpfWcMocks::setupMocks();
		WoocommerceGpfMocks::setupMocks();

		// Set up a WoocommerceGpfCommon instance.
		$this->c = new WoocommerceGpfCommon();
		$this->c->initialise();

		// Set up a simple product.
		$this->p = $this->setup_simple_product();
		$this->p->mock_set_gpf_config( [
			'gtin'           => '012345678',
			'adwords_labels' => 'label1,label2',
		] );

		// Set up a mock post.
		$this->post            = new MockWpPost();
		$this->post->ID        = $this->p->get_id();
		$this->post->post_type = 'product';

		// Set up a template loader.
		$this->template = new WoocommerceGpfTemplateLoader();

		// Return a mock FeedItem when asked for one for our mock post.
		\WP_Mock::userFunction(
			'woocommerce_gpf_get_feed_item',
			[
				'args' => [ $this->post ],
				'return' => function () {
					$r = new stdClass();
					$r->additional_elements = [
						'gtin'           => [ '123456789' ],
						'adwords_labels' => [ 'label1', 'label2' ],
					];
					return $r;
				},
			]
		);

	}

	/**
	 * Test WoocommerceGpfTemplateTags::get_element_values().
	 */
	public function testGetElementValues() {
		$values = WoocommerceGpfTemplateTags::get_element_values( 'gtin', $this->c, $this->post );
		$this->assertInternalType( 'array', $values );
		$this->assertCount( 1, $values );
		$this->assertEquals( '123456789', $values[0] );
		$values = WoocommerceGpfTemplateTags::get_element_values( 'adwords_labels', $this->c, $this->post );
		$this->assertCount( 2, $values );
		$this->assertEquals( 'label1', $values[0] );
		$this->assertEquals( 'label2', $values[1] );
	}

	/**
	 * Test WoocommerceGpfTemplateTags::show_element() with a single valued
	 * element.
	 */
	public function testShowElementSingle() {
		$this->expectOutputString( <<<OUTPUT
<div class="woocommerce-gpf-element">
	<span class="woocommerce-gpf-element-value">
		123456789
	</span>
</div>

OUTPUT
		);
		WoocommerceGpfTemplateTags::show_element( 'gtin', $this->c, $this->template, $this->post );
	}

	/**
	 * Test WoocommerceGpfTemplateTags::show_element() with a multi-valued
	 * element.
	 */
	public function testShowElementMultiple() {
		$this->expectOutputString( <<<OUTPUT
<div class="woocommerce-gpf-element">
	<span class="woocommerce-gpf-element-value">
		label1, label2
	</span>
</div>

OUTPUT
		);
		WoocommerceGpfTemplateTags::show_element( 'adwords_labels', $this->c, $this->template, $this->post );
	}

	/**
	 * Test WoocommerceGpfTemplateTags::show_element_with_label() with a single
	 * valued element.
	 */
	public function testShowElementWithLabelSingle() {
		$this->expectOutputString( <<<OUTPUT
<div class="woocommerce-gpf-element">
	<span class="woocommerce-gpf-element-label">
		Global Trade Item Number (GTIN)
	</span>:
	<span class="woocommerce-gpf-element-value">
		123456789
	</span>
</div>

OUTPUT
		);
		WoocommerceGpfTemplateTags::show_element_with_label( 'gtin', $this->c, $this->template, $this->post );
	}

	/**
	 * Test WoocommerceGpfTemplateTags::show_element_with_label() with a multi-
	 * valued element.
	 */
	public function testShowElementWithLabelMulti() {
		$this->expectOutputString( <<<OUTPUT
<div class="woocommerce-gpf-element">
	<span class="woocommerce-gpf-element-label">
		Adwords labels
	</span>:
	<span class="woocommerce-gpf-element-value">
		label1, label2
	</span>
</div>

OUTPUT
		);
		WoocommerceGpfTemplateTags::show_element_with_label( 'adwords_labels', $this->c, $this->template, $this->post );
	}

}
