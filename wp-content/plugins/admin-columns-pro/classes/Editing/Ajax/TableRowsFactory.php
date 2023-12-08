<?php

namespace ACP\Editing\Ajax;

use AC;
use AC\ListScreen;
use AC\Request;
use ACP\ListScreen\Comment;
use ACP\ListScreen\Media;
use ACP\ListScreen\MSUser;
use ACP\ListScreen\Post;
use ACP\ListScreen\Taxonomy;
use ACP\ListScreen\User;

final class TableRowsFactory
{

    private static $list_screens = [
        Post::class     => TableRows\Post::class,
        Media::class    => TableRows\Media::class,
        Comment::class  => TableRows\Comment::class,
        User::class     => TableRows\User::class,
        MSUser::class   => TableRows\User::class,
        Taxonomy::class => TableRows\Taxonomy::class,
    ];

    /**
     * @param string $list_screen  ListScreen class (FQN)
     * @param string $table_screen TableScreen class (FQN)
     */
    public static function register(string $list_screen, string $table_screen): void
    {
        self::$list_screens[$list_screen] = $table_screen;
    }

    public static function get_table_rows_reference(ListScreen $list_screen): ?string
    {
        foreach (self::$list_screens as $list_screen_reference => $table_rows_reference) {
            if ($list_screen instanceof $list_screen_reference) {
                return $table_rows_reference;
            }
        }

        return null;
    }

    public static function create(Request $request, AC\ListScreen $list_screen): ?TableRows
    {
        $table_rows_reference = self::get_table_rows_reference($list_screen);

        if ( ! $table_rows_reference) {
            return null;
        }

        $table_rows = new $table_rows_reference($request, $list_screen);

        return $table_rows instanceof TableRows
            ? $table_rows
            : null;
    }

}