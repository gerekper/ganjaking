<?php

class SearchWP_Source_Post extends WP_UnitTestCase {
	protected static $factory;
	protected static $post_type = 'post' . SEARCHWP_SEPARATOR . 'post';
	protected static $page_type = 'post' . SEARCHWP_SEPARATOR . 'page';
	protected static $post_ids;
	protected static $page_ids;

	public static function wpSetUpBeforeClass( $factory ) {
		self::$factory = $factory;

		$tax_term = $factory->tag->create_and_get( [
			'name' => 'taxtermtest',
		] );

		$post_ids[] = $factory->post->create( [
			'post_title'   => 'This is the attributetitle',
			'post_name'    => 'this-is-the-attributeslug',
			'post_content' => 'This is the contenttest content',
			'post_excerpt' => 'This is the excerpttest content',
			'meta_input'   => [
				'testmetakey'         => 'attributeoptiontest',
				'wildcardmatchkey'    => 'this is a phrase jsadkgha9ajs match test',
				'wildcardmismatchkey' => 'this is a phrase jsadkgha9ajs mismatch test',
			],
			'tags_input' => [
				$tax_term->term_id
			],
		] );

		$factory->comment->create( [
			'comment_post_ID' => $post_ids[0],
			'comment_content' => 'this is a commenttest lipsum lorem',
		] );

		// Create a post that's added to a specific Category.
		$category_term = $factory->category->create_and_get( [
			'name' => 'taxtermcategory',
		] );

		$post_ids[] = $factory->post->create( [
			'post_title'    => 'This is for a ruletest lorem',
			'post_category' => [ $category_term->term_id ],
		] );

		self::$post_ids = $post_ids;

		// Create Pages too.
		$page_ids[] = $factory->post->create( [
			'post_title' => 'This is the parent for the test',
			'post_type'  => 'page',
		] );

		$page_ids[] = $factory->post->create( [
			'post_title'  => 'Child for the parenttest',
			'post_type'   => 'page',
			'post_parent' => $page_ids[0],
		] );

		self::$page_ids = $page_ids;

		$engine_model = json_decode( json_encode( new \SearchWP\Engine( 'default' ) ), true );
		\SearchWP\Settings::update_engines_config( [
			'default' => \SearchWP\Utils::normalize_engine_config( $engine_model ),

			// Create Supplemental Engine with Posts that have no Title attribute.
			'postsnotitle' => [
				'sources'  => [
					'post.post' => [
						// Due to the test suite we have to set a weight of zero to "remove" the Title.
						'attributes' => [ 'title' => 0, 'content' => 1 ],
						'rules'      => [],
						'options'    => [],
					],
				],
			],

			// Create Supplemental Engine with no Posts Source.
			'noposts' => [
				'sources'  => [
					'post.page' => [
						'attributes' => [ 'title' => 300 ],
						'rules'      => [],
						'options'    => [],
					],
				],
			],

			// Create Supplemental Engine with Attribute that has Options.
			'attributeoption' => [
				'sources'  => [
					'post.post' => [
						'attributes' => [
							'title'   => 300,
							'comments' => 1,
							'meta'  => [
								'testmetakey' => 5,
							],
							'taxonomy' => [
								'post_tag' => 4,
							],
						],
						'rules'      => [],
						'options'    => [],
					],
				],
			],

			// Create Supplemental Engine with Attribute that has Options but only a wildcard added.
			'attributeoptionwildcard' => [
				'sources'  => [
					'post.post' => [
						'attributes' => [
							'meta'  => [
								'*' => 5,
							],
						],
						'rules'      => [],
						'options'    => [],
					],
				],
			],

			// Create Supplemental Engine with taxonomy rule.
			'taxinnotin' => [
				'sources'  => [
					'post.post' => [
						'attributes' => [
							'title'   => 300,
							'comments' => 1,
							'taxonomy' => [
								'post_tag' => 4,
							],
						],
						'rules'      => [
							[
								'type'  => 'IN',
								'rules' => [ [
									'option'    => 'category',
									'condition' => 'NOT IN',
									'rule'      => 'taxonomy',
									'value'     => [
										1, // ID of "Uncategorized".
									],
								] ],
							],
						],
						'options'    => [],
					],
				],
			],

			// Create Supplemental Engine with taxonomy rule.
			'taxnotinin' => [
				'sources'  => [
					'post.post' => [
						'attributes' => [
							'title'   => 300,
							'comments' => 1,
							'taxonomy' => [
								'post_tag' => 4,
							],
						],
						'rules'      => [
							[
								'type'  => 'NOT IN',
								'rules' => [ [
									'option'    => 'category',
									'condition' => 'IN',
									'rule'      => 'taxonomy',
									'value'     => [
										1, // ID of "Uncategorized".
									],
								] ],
							],
						],
						'options'    => [],
					],
				],
			],

			// Create Supplemental Engine with taxonomy rule.
			'taxininmulti' => [
				'sources'  => [
					'post.post' => [
						'attributes' => [
							'title'   => 300,
							'comments' => 1,
							'taxonomy' => [
								'post_tag' => 4,
							],
						],
						'rules'      => [
							[
								'type'  => 'IN',
								'rules' => [ [
									'option'    => 'category',
									'condition' => 'IN',
									'rule'      => 'taxonomy',
									'value'     => [
										1, // ID of "Uncategorized".
										$category_term->term_id, // Category we created.
									],
								] ],
							],
						],
						'options'    => [],
					],
				],
			],

			// Create Supplemental Engine with multiple taxonomy rule groups.
			'taxmultirulegroups' => [
				'sources'  => [
					'post.post' => [
						'attributes' => [
							'title'   => 300,
							'comments' => 1,
							'taxonomy' => [
								'post_tag' => 4,
							],
						],
						'rules'      => [
							[
								'type'  => 'IN',
								'rules' => [ [
									'option'    => 'category',
									'condition' => 'IN',
									'rule'      => 'taxonomy',
									'value'     => [
										1, // ID of "Uncategorized".
										$category_term->term_id, // Category we created.
									],
								] ],
							],
							[
								'type'  => 'NOT IN',
								'rules' => [ [
									'option'    => 'post_tag',
									'condition' => 'IN',
									'rule'      => 'taxonomy',
									'value'     => [
										$tax_term->term_id, // Tag we created.
									],
								] ],
							],
						],
						'options'    => [],
					],
				],
			],

			// Create Supplemental Engine with date rule.
			'daterule' => [
				'sources'  => [
					'post.post' => [
						'attributes' => [
							'title'   => 300,
							'comments' => 1,
							'taxonomy' => [
								'post_tag' => 4,
							],
						],
						'rules'      => [
							[
								'type'  => 'IN',
								'rules' => [ [
									'option'    => null,
									'condition' => '<',
									'rule'      => 'published',
									'value'     => strtotime( '1 week ago' ),
								] ],
							],
						],
						'options'    => [],
					],
				],
			],

			// Create Supplemental Engine with date rule.
			'daterulealt' => [
				'sources'  => [
					'post.post' => [
						'attributes' => [
							'title'   => 300,
							'comments' => 1,
							'taxonomy' => [
								'post_tag' => 4,
							],
						],
						'rules'      => [
							[
								'type'  => 'IN',
								'rules' => [ [
									'option'    => null,
									'condition' => '>',
									'rule'      => 'published',
									'value'     => strtotime( '1 month ago' ),
								] ],
							],
						],
						'options'    => [],
					],
				],
			],

			// Create Supplemental Engine with ID rule.
			'idrule' => [
				'sources'  => [
					'post.post' => [
						'attributes' => [
							'title'   => 300,
							'comments' => 1,
							'taxonomy' => [
								'post_tag' => 4,
							],
						],
						'rules'      => [
							[
								'type'  => 'NOT IN',
								'rules' => [ [
									'option'    => null,
									'condition' => 'IN',
									'rule'      => 'post_id',
									'value'     => (string) $post_ids[0],
								] ],
							],
						],
						'options'    => [],
					],
				],
			],

			// Create Supplemental Engine with ID weight transfer.
			'idtransfer' => [
				'sources'  => [
					'post.post' => [
						'attributes' => [
							'title'   => 300,
							'comments' => 1,
							'taxonomy' => [
								'post_tag' => 4,
							],
						],
						'rules'      => [],
						'options'    => [
							'weight_transfer' => [
								'enabled' => true,
								'option'  => 'id',
								'value'   => (string) $post_ids[0],
							],
						],
					],
				],
			],

			// Create Supplemental Engine with parent weight transfer.
			'parenttransfer' => [
				'sources'  => [
					'post.page' => [
						'attributes' => [
							'title'   => 300,
						],
						'rules'      => [],
						'options'    => [
							'weight_transfer' => [
								'enabled' => true,
								'option'  => 'col',
								'value'   => '',
							],
						],
					],
				],
			],
		] );

		foreach ( self::$post_ids as $post_id ) {
			\SearchWP::$index->add(
				new \SearchWP\Entry( self::$post_type, $post_id )
			);
		}

		foreach ( self::$page_ids as $post_id ) {
			\SearchWP::$index->add(
				new \SearchWP\Entry( self::$page_type, $post_id )
			);
		}
	}

	public static function wpTearDownAfterClass() {
		$index = \SearchWP::$index;
		$index->reset();

		\SearchWP\Settings::update_engines_config( [] );
	}

	public function test_searching_title() {
		// Test that a title search works.
		$results = new \SWP_Query( [
			's'      => 'attributetitle',
			'fields' => 'ids',
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContains( $results->posts[0], self::$post_ids );

		// Test that a title search returns no results.
		$results = new \SWP_Query( [
			's'      => 'noattributetitle',
			'fields' => 'ids',
		] );

		$this->assertEquals( 0, count( $results->posts ) );

		// Test that an engine with no Title attribute returns no results.
		$query = new \SearchWP\Query( 'attributetitle', [
			'engine' => 'postsnotitle',
			'fields' => 'ids',
		] );

		$this->assertEquals( 0, count( $query->results ) );

		// Test that an engine with no Posts Source returns no results.
		$query = new \SearchWP\Query( 'attributetitle', [
			'engine' => 'noposts',
			'fields' => 'ids',
		] );

		$this->assertEquals( 0, count( $query->results ) );
	}

	public function test_searching_content() {
		// Test that a title search works.
		$results = new \SWP_Query( [
			's'      => 'contenttest',
			'fields' => 'ids',
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContains( $results->posts[0], self::$post_ids );
	}

	public function test_searching_slug() {
		// Test that a title search works.
		$results = new \SWP_Query( [
			's'      => 'attributeslug',
			'fields' => 'ids',
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContains( $results->posts[0], self::$post_ids );
	}

	public function test_searching_excerpt() {
		// Test that a title search works.
		$results = new \SWP_Query( [
			's'      => 'excerpttest',
			'fields' => 'ids',
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContains( $results->posts[0], self::$post_ids );
	}

	public function test_searching_comments() {
		// Test that a title search works.
		$results = new \SWP_Query( [
			'engine' => 'attributeoption',
			's'      => 'commenttest',
			'fields' => 'ids',
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContains( $results->posts[0], self::$post_ids );
	}

	public function test_searching_attribute_option() {
		// Test that a Custom Field search works.
		$results = new \SWP_Query( [
			'engine' => 'attributeoption',
			's'      => 'attributeoptiontest',
			'fields' => 'ids',
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContains( $results->posts[0], self::$post_ids );

		// Test that a Taxonomy search works.
		$results = new \SWP_Query( [
			'engine' => 'attributeoption',
			's'      => 'taxtermtest',
			'fields' => 'ids',
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContains( $results->posts[0], self::$post_ids );
	}

	public function test_searching_meta_wildcard() {
		// Test that a Custom Field search works.
		$results = new \SWP_Query( [
			'engine' => 'attributeoptionwildcard',
			's'      => 'jsadkgha9ajs', // Exact match for meta key that's not added
			'fields' => 'ids',
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContains( $results->posts[0], self::$post_ids );
	}

	public function test_searching_meta_phrase() {
		// Test that a Custom Field search works.
		$results = new \SWP_Query( [
			'engine' => 'attributeoptionwildcard',
			's'      => '"phrase jsadkgha9ajs match"',
			'fields' => 'ids',
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContains( $results->posts[0], self::$post_ids );
	}

	public function test_taxonomy_rule_group_in_rule_not_in() {
		// Test that searching for a term in an Uncategorized post is not returned.
		$results = new \SWP_Query( [
			'engine' => 'taxinnotin',
			's'      => 'attributetitle',
			'fields' => 'ids',
		] );

		$this->assertTrue( empty( $results->posts ) );

		// Test that searching for a term NOT in an Uncategorized post is returned.
		$results = new \SWP_Query( [
			'engine' => 'taxinnotin',
			's'      => 'ruletest',
			'fields' => 'ids',
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContains( $results->posts[0], self::$post_ids );
	}

	public function test_taxonomy_rule_group_not_in_rule_in() {
		// Test that searching for a term in an Uncategorized post is not returned.
		$results = new \SWP_Query( [
			'engine' => 'taxnotinin',
			's'      => 'attributetitle',
			'fields' => 'ids',
		] );

		$this->assertTrue( empty( $results->posts ) );

		// Test that searching for a term NOT in an Uncategorized post is returned.
		$results = new \SWP_Query( [
			'engine' => 'taxnotinin',
			's'      => 'ruletest',
			'fields' => 'ids',
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContains( $results->posts[0], self::$post_ids );
	}

	public function test_taxonomy_rule_group_in_multi_in() {
		// Test that searching for a term in a post with required Categories.
		$results = new \SWP_Query( [
			'engine' => 'taxininmulti',
			's'      => 'ruletest',
			'fields' => 'ids',
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContains( $results->posts[0], self::$post_ids );
	}

	public function test_taxonomy_multi_rule_groups() {
		// Search for one term in an excluded taxonomy, and another term in an included taxonomy
		$results = new \SWP_Query( [
			'engine' => 'taxmultirulegroups',
			's'      => 'attributetitle ruletest', // attributetitle is excluded by tag.
			'fields' => 'ids',
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContains( $results->posts[0], self::$post_ids );

		// Run a similar search but this time for the known excluded Tag.
		$results = new \SWP_Query( [
			'engine' => 'taxmultirulegroups',
			's'      => 'attributetitle', // attributetitle is excluded by tag.
			'fields' => 'ids',
		] );

		$this->assertTrue( empty( $results->posts ) );
	}

	public function test_date_rule() {
		$results = new \SWP_Query( [
			'engine' => 'daterule',
			's'      => 'attributetitle',
			'fields' => 'ids',
		] );

		// Post was publised now(), engine restricts to older than 1 week.
		$this->assertTrue( empty( $results->posts ) );

		$results = new \SWP_Query( [
			'engine' => 'daterulealt',
			's'      => 'attributetitle',
			'fields' => 'ids',
		] );

		// Post was publised now(), engine restricts to newer than 1 month.
		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContains( $results->posts[0], self::$post_ids );
	}

	public function test_post_id_rule() {
		$results = new \SWP_Query( [
			'engine' => 'idrule',
			's'      => 'attributetitle',
			'fields' => 'ids',
		] );

		// Post was publised now(), engine restricts to older than 1 week.
		$this->assertTrue( empty( $results->posts ) );
	}

	public function test_id_weight_transfer() {
		$results = new \SWP_Query( [
			'engine' => 'idtransfer',
			's'      => 'ruletest',
			'fields' => 'ids',
		] );

		// Searched for the 2nd post we created, but expect that the weight
		// was transferred to the first post we created.
		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertEquals( $results->posts[0], self::$post_ids[0] );
	}

	public function test_parent_weight_transfer() {
		$results = new \SWP_Query( [
			'engine' => 'parenttransfer',
			's'      => 'parenttest',
			'fields' => 'ids',
		] );

		// Searched for a term that is in a child Page, expect that only the parent is returned.
		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertEquals( $results->posts[0], self::$page_ids[0] );
	}
}
