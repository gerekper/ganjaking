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
	 * Terms will be displayed on current page
	 *
	 * @var array
	 */
	private $termsToDisplay = array();

	/**
	 * Terms levels array
	 *
	 * @var int[]
	 */
	private $termsLevels = array();

	/**
	 * Terms number per page
	 *
	 * @var int
	 */
	private $perPage;

	/**
	 * Current page number
	 *
	 * @var int
	 */
	private $currentPage;

	/**
	 * TermsCollection constructor.
	 *
	 * @param WP_Term[] $terms
	 * @param int $perPage
	 * @param int $currentPage
	 */
	public function __construct( array $terms, $perPage = 20, $currentPage = 1) {
		$this->perPage 	   	  = $perPage;
		$this->currentPage	  = $currentPage;
		$this->sortedTerms 	  = $this->sortTerms($terms);
		$this->termsToDisplay = $this->getTermsToDisplay();


		parent::__construct($this->termsToDisplay);
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
				$this->setTermLevel($term->term_id, 0);

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
	 * Get terms to be displayed on current page
	 *
	 * @return array
	 */
	public function getTermsToDisplay() {

		/**
		 * Get terms offset
		 *
		 * @var WP_Term $firstInList
		 */
		$firstInList     = $this->sortedTerms[$this->getTermsOffset()];
		$sortedAncestors = array();
		if ($this->hasAncestors($firstInList)) {
			$sortedAncestors = $this->getSortedAncestorsList($firstInList->term_id);
		}

		$termsToGetFrom = array_slice($this->sortedTerms, $this->getTermsOffset(), $this->perPage);

		return array_merge($sortedAncestors, $termsToGetFrom);
	}

	/**
	 * Get terms offset
	 *
	 * @return int
	 */
	private function getTermsOffset() {
		return ( $this->currentPage -1 ) * $this->perPage;
	}

	/**
	 * Whether term has ancestors
	 *
	 * @param WP_Term $term
	 *
	 * @return bool
	 */
	private function hasAncestors( WP_Term $term) {
		return 0 !== $term->parent;
	}

	/**
	 * Return ancestors in ascending order
	 *
	 * @param $termId
	 *
	 * @return WP_Term[]
	 */
	private function getSortedAncestorsList( $termId) {
		$sortedAncestors = array();

		$ancestorsIds = get_ancestors($termId, 'product_cat', 'taxonomy');
		$ancestors    = get_terms(array(
			'include' => $ancestorsIds,
			'hide_empty' => false,
			'orderby' => 'parent',
			'order' => 'ASC'
		));

		if ($ancestors) {
			$sortedAncestors = $this->getBranchAsFlatArray(reset($ancestors), $ancestors);
		}

		return $sortedAncestors;
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
				$this->setTermLevel($term->term_id, $level);

				$foundTerms[]  = $term;
				$childrenTerms = $this->walkBranch($term->term_id, $terms, $level + 1);
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
