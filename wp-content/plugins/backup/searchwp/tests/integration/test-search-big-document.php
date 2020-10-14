<?php

class SWPQuerySearchesTest extends WP_UnitTestCase {
	protected static $factory;
	protected static $post_ids;
	protected static $flag;

	public static function wpSetUpBeforeClass( $factory ) {
		self::$factory = $factory;

		// Generate 1000 sentences of random content.
		$sentences = 1000;
		$content = '';
		for ( $i = 0; $i < $sentences; $i++ ) {
			$content .= self::generate_random_sentence( rand( 5, 15 ) ) . '. ';
		}

		$post_ids[] = $factory->post->create( [
			'post_title' => 'Random document',
			'post_content' => trim( $content ),
		] );

		self::$post_ids      = $post_ids;

		$index = new \SearchWP\Index\Controller();

		// Create a Default Engine.
		$engine_model = json_decode( json_encode( new \SearchWP\Engine( 'default' ) ), true );
		\SearchWP\Settings::update_engines_config( [
			'default' => \SearchWP\Utils::normalize_engine_config( $engine_model ),
		] );

		foreach ( self::$post_ids as $post_id ) {
			$index->add( new \SearchWP\Entry( 'post.post', $post_id ) );
		}
	}

	public static function wpTearDownAfterClass() {
		$index = \SearchWP::$index;
		$index->reset();

		\SearchWP\Settings::update_engines_config( [] );
	}

	public function test_that_one_result_is_returned_from_token_chunk() {
		$results = new SWP_Query( [
			'engine'         => 'default',
			's'              => self::$flag,
			'fields'         => 'ids',
			'posts_per_page' => 1,
		] );

		// That there was 1 result returned.
		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );

		// That the result is in our IDs.
		$this->assertContains( $results->posts[0], self::$post_ids );
	}

	public static function generate_random_string( $length = 10 ) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$string     = '';

		for ( $i = 0; $i < $length; $i++ ) {
			$string .= $characters[ rand( 0, strlen( $characters ) - 1 ) ];
		}

		return $string;
	}

	public static function generate_random_sentence( $words = 10 ) {
		$random_sentence = '';

		for ( $i = 0; $i < $words; $i++ ) {
			$word = self::generate_random_string( rand( 3, 11 ) );

			// We'll set the first word of the last generated sentence to be our flag.
			if ( $i == 0 ) {
				self::$flag = $word;
			}

			$random_sentence .= ' ' . $word;
		}

		return trim( $random_sentence );
	}
}
