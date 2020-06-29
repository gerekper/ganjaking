<?php


namespace Premmerce\WooCommercePinterest\Admin\Table\TermRelationsTable;

use \ArrayObject;
use \WP_Term;

/**
 * Class TermsCollection
 *
 * @package Premmerce\WooCommercePinterest\Admin\Table
 *
 * This class is represents terms collection.
 * It used for sorting and iterating terms for using in WP List tables.
 * The main idea behind this class is to encapsulate terms used as items for WP List tables  with methods for handling them.
 */
class TermsCollection extends ArrayObject {

	/**
	 * WP_Term instances array
	 *
	 * @var WP_Term[]
	 */
	private $sortedTerms = array();

	/**
	 * Terms levels array
	 *
	 * @var int[]
	 */
	private $termsLevels = array();

	/**
	 * TermsCollection constructor.
	 *
	 * @param WP_Term[] $terms
	 */
	public function __construct( array $terms) {
		$this->sortedTerms = $this->sortTerms($terms);

		parent::__construct($this->sortedTerms);
	}

	/**
	 * Return number of sorted terms
	 *
	 * @return int
	 */
	public function count() {
		return count($this->sortedTerms);
	}

	/**
	 * Sort terms
	 *
	 * @param WP_Term[] $terms
	 *
	 * @return  WP_Term[]
	 */
	private function sortTerms( array $terms) {
		$sortedTerms = array();

		foreach ($terms as $term) {
			if (! $term->parent) {
				$this->setTermLevel($term->term_taxonomy_id, 0);

				$branch         = $this->getBranchAsFlatArray($term, $terms);
				$filteredBranch = array_filter($branch);
				if ($filteredBranch) {
					$sortedTerms = array_merge($sortedTerms, $filteredBranch);
				}
			}
		}

		return $sortedTerms;
	}

	/**
	 * Return terms tree branch as flat array. Helper function for sorting.
	 *
	 * @param WP_Term $parentTerm
	 * @param WP_Term[] $terms
	 *
	 * @return WP_Term[]
	 */
	private function getBranchAsFlatArray( $parentTerm, array &$terms) {
		$branchTerms = array($parentTerm);

		$childrenTerms = $this->walkBranch($parentTerm->term_id, $terms);
		$branchTerms   = array_merge($branchTerms, $childrenTerms);

		$branchTerms = array_filter($branchTerms);

		return $branchTerms;
	}

	/**
	 * Recursive find children in terms tree. Helper function for sorting.
	 *
	 * @param int $parentId
	 * @param WP_Term[] $terms
	 * @param int $level Term nesting depth level. Root (parent) terms has 0 level.
	 *
	 * @return WP_Term[]|null
	 */
	private function walkBranch( $parentId, &$terms, $level = 1) {
		$foundTerms = array();
		foreach ($terms as $index => $term) {
			if ($term->parent === $parentId) {
				$this->setTermLevel($term->term_taxonomy_id, $level);

				$foundTerms[]  = $term;
				$childrenTerms = $this->walkBranch($term->term_taxonomy_id, $terms, $level + 1);
				if ($childrenTerms) {
					$foundTerms = array_merge($foundTerms, $childrenTerms);
				}
				unset($terms[$index]);
			}
		}

		return $foundTerms;
	}

	/**
	 * Set nesting level of term
	 *
	 * @param int $termId
	 * @param int $level
	 */
	private function setTermLevel( $termId, $level) {
		$this->termsLevels[$termId] = $level;
	}

	/**
	 * Return nesting level of term
	 *
	 * @param int $termId
	 *
	 * @return int
	 */
	public function getTermLevel( $termId) {
		return $this->termsLevels[$termId];
	}
}
