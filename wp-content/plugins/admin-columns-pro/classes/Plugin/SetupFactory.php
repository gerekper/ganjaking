<?php

namespace ACP\Plugin;

use AC;
use AC\Plugin\InstallCollection;
use AC\Plugin\UpdateCollection;

class SetupFactory extends AC\Plugin\SetupFactory {

	public function create( $type ) {

		switch ( $type ) {
			case self::NETWORK:
				$this->installers = new InstallCollection( [
						new Install\BookmarkTable(),
					]
				);
				$this->updates = new UpdateCollection( [
					new NetworkUpdate\V5000(),
					new NetworkUpdate\V5700(),
				] );

				break;
			case self::SITE:
				$this->installers = new InstallCollection( [
						new Install\BookmarkTable(),
					]
				);
				$this->updates = new UpdateCollection( [
					new Update\V4101(),
					new Update\V4301(),
					new Update\V5000(),
					new Update\V5104(),
					new Update\V5201(),
					new Update\V5300(),
					new Update\V5400(),
					new Update\V5700(),
				] );

				break;
		}

		return parent::create( $type );
	}

}