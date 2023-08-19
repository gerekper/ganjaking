<?php

namespace ACP\Search;

use AC\ListScreen;
use AC\ListScreen\Comment;
use AC\ListScreen\Media;
use AC\ListScreen\Post;
use AC\ListScreen\User;
use ACP\ListScreen\MSUser;
use ACP\ListScreen\Taxonomy;

class TableScreenFactory {

	private static $list_screens = [
		Post::class     => TableScreen\Post::class,
		Media::class    => TableScreen\Post::class,
		Comment::class  => TableScreen\Comment::class,
		MSUser::class   => TableScreen\MSUser::class,
		User::class     => TableScreen\User::class,
		Taxonomy::class => TableScreen\Taxonomy::class,
	];

	/**
	 * @param string $list_screen  ListScreen class (FQN)
	 * @param string $table_screen TableScreen class (FQN)
	 */
	public static function register( string $list_screen, string $table_screen ): void {
		self::$list_screens[ $list_screen ] = $table_screen;
	}

	public static function create( ListScreen $list_screen, array $assets ): ?TableScreen {
		$table_screen_reference = self::get_table_screen_reference( $list_screen );

		if ( ! $table_screen_reference ) {
			return null;
		}

		$table_screen = new $table_screen_reference( $list_screen, $assets );

		return $table_screen instanceof TableScreen
			? $table_screen
			: null;
	}

	public static function get_table_screen_reference( ListScreen $list_screen ): ?string {
		foreach ( self::$list_screens as $list_screen_reference => $table_screen_reference ) {
			if ( $list_screen instanceof $list_screen_reference ) {
				return $table_screen_reference;
			}
		}

		return null;
	}

}