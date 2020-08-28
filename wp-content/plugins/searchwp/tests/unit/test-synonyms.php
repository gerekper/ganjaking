<?php

class SearchWP_Synonym_Test extends WP_UnitTestCase {
	protected static $query;
	protected static $synonyms;

	public function setUp() {
		parent::setUp();

		self::$query    = new \SearchWP\Query( '' );
		self::$synonyms = new \SearchWP\Logic\Synonyms();

		add_filter( 'searchwp\synonyms', function( $synonyms ) {
			return [ [
				'sources'  => 'lu',
				'synonyms' => 'lungs',
				'replace'  => true,
			], [
				'sources'  => 'seo',
				'synonyms' => 'search engine optimization',
				'replace'  => true,
			], [
				'sources'  => '"quoted phrase"',
				'synonyms' => 'phrase synonym',
				'replace'  => false,
			], [
				'sources'  => 'balpha, "bbeta bgamma"',
				'synonyms' => 'bomega',
				'replace'  => true,
			], [
				'sources'  => 'ccompound ssource',
				'synonyms' => 'compound synonym',
				'replace'  => true,
			], [
				'sources'  => 'cccompound sssource',
				'synonyms' => 'compound synonym',
				'replace'  => true,
			],[
				'sources'  => 'sssource',
				'synonyms' => 'ssourcesynonym',
				'replace'  => true,
			] ];
		} );
	}

	public function test_that_quoted_mismatch_does_not_apply() {
		add_filter( 'searchwp\query\logic\phrase', '__return_true' );
		add_filter( 'searchwp\synonyms\strict', '__return_true' );

		$this->assertEquals(
			self::$synonyms->apply( '"seo balpha"', self::$query ),
			'"seo balpha"',
			'Search query has quoted phrase without match'
		);

		remove_filter( 'searchwp\query\logic\phrase', '__return_true' );
		remove_filter( 'searchwp\synonyms\strict', '__return_true' );
	}

	public function test_that_matches_use_word_boundaries() {
		$this->assertEquals(
			self::$synonyms->apply( 'lu', self::$query ),
			'lungs',
			'Search query matches synonym source exactly'
		);

		$this->assertEquals(
			self::$synonyms->apply( 'alu', self::$query ),
			'alu',
			'Search query partially matches synonym source'
		);
	}

	public function test_that_one_synonym_is_replaced_in_query_with_multiple_words() {
		$this->assertEquals(
			self::$synonyms->apply( 'alpha seo beta', self::$query ),
			'alpha search engine optimization beta'
		);
	}

	public function test_that_quoted_phrase_replacement_applies() {
		$this->assertEquals(
			self::$synonyms->apply( 'quoted phrase', self::$query ),
			'quoted phrase phrase synonym',
			'Quoted source exact match'
		);

		$this->assertEquals(
			self::$synonyms->apply( 'quoted phrase coffee', self::$query ),
			'quoted phrase phrase synonym coffee',
			'Quoted source in part match'
		);
	}

	public function test_that_multiple_sources_apply() {
		$this->assertEquals(
			self::$synonyms->apply( 'balpha', self::$query ),
			'bomega',
			'Multiple source single word exact match'
		);

		$this->assertEquals(
			self::$synonyms->apply( 'bbeta bgamma', self::$query ),
			'bomega',
			'Multiple source quoted phrase source exact match'
		);

		$this->assertEquals(
			self::$synonyms->apply( 'bbeta', self::$query ),
			'bbeta',
			'Multiple source quoted phrase source mismatch'
		);
	}

	public function test_that_compound_source_is_considered() {
		$this->assertEquals(
			self::$synonyms->apply( 'ccompound ssource', self::$query ),
			'compound synonym',
			'Compound source replaced with compound synonym'
		);

		$this->assertEquals(
			self::$synonyms->apply( 'ccompound ssources', self::$query ),
			'ccompound ssources',
			'Suffixed compound source not replaced by compound synonym'
		);

		$this->assertEquals(
			self::$synonyms->apply( 'cccompound ssource', self::$query ),
			'cccompound ssource',
			'Prefixed compound source not replaced by compound synonym'
		);

		$this->assertEquals(
			self::$synonyms->apply( 'alpha ccompound ssource omega', self::$query ),
			'alpha compound synonym omega',
			'Compound source (flanked) replaced with compound synonym'
		);

		$this->assertEquals(
			self::$synonyms->apply( 'ccompound', self::$query ),
			'ccompound',
			'Single token from compound source is not replaced'
		);

		$this->assertEquals(
			self::$synonyms->apply( 'sssource', self::$query ),
			'ssourcesynonym',
			'Single token from compound source is replaced'
		);
	}
}
