<?php
declare( strict_types=1 );

namespace ACP\Service;

use AC\Groups;
use AC\ListScreenFactory;
use AC\Registerable;
use ACP\ListScreenFactory\CommentFactory;
use ACP\ListScreenFactory\MediaFactory;
use ACP\ListScreenFactory\MSSiteFactory;
use ACP\ListScreenFactory\MSUserFactory;
use ACP\ListScreenFactory\PostFactory;
use ACP\ListScreenFactory\TaxonomyFactory;
use ACP\ListScreenFactory\UserFactory;

class ListScreens implements Registerable {

	public function register() {
		ListScreenFactory::add( new MSSiteFactory() );
		ListScreenFactory::add( new MSUserFactory() );
		ListScreenFactory::add( new PostFactory() );
		ListScreenFactory::add( new MediaFactory() );
		ListScreenFactory::add( new CommentFactory() );
		ListScreenFactory::add( new TaxonomyFactory() );
		ListScreenFactory::add( new UserFactory() );

		add_action( 'ac/list_screen_groups', [ $this, 'register_list_screen_groups' ] );
	}

	public function register_list_screen_groups( Groups $groups ): void {
		$groups->add( 'network', __( 'Network' ), 5 );
		$groups->add( 'taxonomy', __( 'Taxonomy' ), 15 );
	}

}