<?php

namespace ACA\YoastSeo\Column\User;

use AC;
use ACA\YoastSeo\Editing;
use ACP;

class DisableReadabilityAnalysis extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-yoast_disable_readability_analysis' )
		     ->set_group( 'yoast-seo' )
		     ->set_label( __( 'Disable readability analysis', 'wordpress-seo' ) );
	}

	public function get_value( $id ) {
		return ac_helper()->icon->yes_or_no( 'on' === $this->get_raw_value( $id ) );
	}

	public function get_meta_key() {
		return 'wpseo_content_analysis_disable';
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Checkmark( $this->get_meta_key() );
	}

	public function editing() {
		return new Editing\Service\User\ToggleOn( $this->get_meta_key() );
	}

}