<?php

/**
 * Class WoocommerceGpfTheContentProtection
 *
 * Avoid issues with extensions that abuse the_content filter
 */
// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
class WoocommerceGpfTheContentProtection {

	/**
	 * Add filters to populate and restore postdata.
	 */
	public function run() {
		add_action( 'woocommerce_gpf_before_description_generation', array( $this, 'before_processing' ), 10, 1 );
		add_action( 'woocommerce_gpf_after_description_generation', array( $this, 'after_processing' ), 10, 0 );
	}

	/**
	 * Setup postdata before we grab info so that plugins that expect it set when the_content filter called still work.
	 *
	 * @param $specific_id int
	 */
	public function before_processing( $specific_id ) {
		global $post, $gpf_original_post;
		$gpf_original_post = $post;
		$post              = get_post( $specific_id );
		setup_postdata( $post );
	}

	/**
	 * Restore postdata after the_content has been used.
	 *
	 * @return mixed
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function after_processing() {
		global $post, $gpf_original_post;
		$post = $gpf_original_post;
		wp_reset_postdata();
	}
}

