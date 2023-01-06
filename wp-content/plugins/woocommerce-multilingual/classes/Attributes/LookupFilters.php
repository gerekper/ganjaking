<?php

namespace WCML\Attributes;

class LookupFilters implements \IWPML_Action {
	
	/** @var \SitePress $sitepress */
	private $sitepress;
	
	/** @var \wpdb $wpdb */
	private $wpdb;
	
	public function __construct( \SitePress $sitepress, \wpdb $wpdb ) {
		$this->sitepress = $sitepress;
		$this->wpdb      = $wpdb;
	}
	
	public function add_hooks() {
			add_filter( 'woocommerce_get_filtered_term_product_counts_query', [ $this, 'adjustAttributeWidgetCount' ] );
	}
	
	/**
	 * @param array $query
	 *
	 * @return array
	 */
	public function adjustAttributeWidgetCount( $query ) {
			
			$query['join'] .= " INNER JOIN {$this->wpdb->prefix}icl_translations AS icl_t ON {$this->wpdb->posts}.ID = icl_t.element_id
								AND icl_t.element_type = 'post_product'";
			
			$query['where'] .= $this->wpdb->prepare(" AND icl_t.language_code = %s", $this->sitepress->get_current_language() );
		
		return $query;
	}
}
