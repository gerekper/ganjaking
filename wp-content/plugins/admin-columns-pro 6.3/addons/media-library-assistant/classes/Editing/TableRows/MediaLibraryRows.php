<?php
declare( strict_types=1 );

namespace ACA\MLA\Editing\TableRows;

use AC\ThirdParty\MediaLibraryAssistant\WpListTableFactory;
use ACP\Editing\Ajax\TableRows;

class MediaLibraryRows extends TableRows {

	public function register(): void
    {
		add_action( 'mla_list_table_prepare_items', [ $this, 'handle_request' ] );

		// Triggers hook above
		( new WpListTableFactory() )->create();
	}

}