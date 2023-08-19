<?php

namespace ACA\YoastSeo\Service;

use AC;
use AC\Registerable;

final class ColumnGroups implements Registerable {

	public function register(): void
    {
		add_action( 'ac/column_groups', [ $this, 'register_column_groups' ] );
	}

	public function register_column_groups( AC\Groups $groups ) {
		$groups->add( 'yoast-seo', 'Yoast SEO', 25 );
	}

}