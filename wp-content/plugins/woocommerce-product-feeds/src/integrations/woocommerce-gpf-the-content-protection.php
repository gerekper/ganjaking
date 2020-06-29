<?php

/**
 * Class WoocommerceGpfTheContentProtection
 *
 * Avoid issues with extensions that abuse the_content filter
 */
class WoocommerceGpfTheContentProtection {

	/**
	 * Add filters to populate and restore postdata.
	 */
	public function run() {
		add_filter( 'woocommerce_gpf_title', array( $this, 'before_processing' ), 10, 2 );
		add_filter( 'woocommerce_gpf_description', array( $this, 'after_processing' ), 10, 3 );
	}

	/**
	 * Setup postdata before we grab info so that plugins that expect it set when the_content filter called still work.
	 *
	 * @param $title string Returned unmodified.
	 * @param $specific_id int Unused.
	 *
	 * @return string
	 */
	public function before_processing( $title, $specific_id ) {
		global $post, $gpf_original_post;
		$gpf_original_post = $post;
		$post              = get_post( $specific_id );
		setup_postdata( $post );

		return $title;
	}

	/**
	 * Restore postdata after the_content has been used.
	 *
	 * @param $description string Return unmodified.
	 * @param $specific_id int Unused.
	 * @param $general_id int Unused.
	 *
	 * @return mixed
	 */
	public function after_processing( $description, $specific_id, $general_id ) {
		global $post, $gpf_original_post;
		$post = $gpf_original_post;
		wp_reset_postdata();

		return $description;
	}
}

