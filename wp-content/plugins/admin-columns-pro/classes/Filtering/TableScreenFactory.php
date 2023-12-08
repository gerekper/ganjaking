<?php

declare(strict_types=1);

namespace ACP\Filtering;

use AC;
use AC\ListScreen\Comment;
use AC\ListScreen\Media;
use AC\ListScreen\Post;
use AC\ListScreen\User;
use AC\Registerable;
use ACP\ListScreen\MSUser;
use ACP\ListScreen\Taxonomy;

class TableScreenFactory
{

    /**
     * @var Registerable[]
     */
    private static $list_screens = [
        Post::class     => Table\Post::class,
        Media::class    => Table\Post::class,
        Comment::class  => Table\Comment::class,
        MSUser::class   => Table\MsUser::class,
        User::class     => Table\User::class,
        Taxonomy::class => Table\Taxonomy::class,
    ];

    public static function register(string $list_screen_fqn, string $table_factory_fqn): void
    {
        self::$list_screens[$list_screen_fqn] = $table_factory_fqn;
    }

    public function create(AC\ListScreen $list_screen, string $column_name): ?Registerable
    {
        foreach (self::$list_screens as $list_screen_reference => $table_screen_reference) {
            if ($list_screen instanceof $list_screen_reference) {
                return new $table_screen_reference($column_name);
            }
        }

        return null;
    }
}