<?php
declare( strict_types=1 );

namespace ACA\MLA;

use AC\ThirdParty\MediaLibraryAssistant\WpListTableFactory;
use ACA\MLA\ListTable\MediaLibraryTable;

class ListTableFactory {

	private $wp_list_table_factory;

	public function __construct( WpListTableFactory $wp_list_table_factory ) {
		$this->wp_list_table_factory = $wp_list_table_factory;
	}

	public function create(): MediaLibraryTable {
		return new MediaLibraryTable( $this->wp_list_table_factory->create() );
	}

}