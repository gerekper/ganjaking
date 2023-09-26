<?php

declare(strict_types=1);

namespace ACP\Service;

use AC;
use AC\Groups;
use AC\PostTypeRepository;
use AC\Registerable;
use ACP\ListScreenFactory\CommentFactory;
use ACP\ListScreenFactory\MediaFactory;
use ACP\ListScreenFactory\MSSiteFactory;
use ACP\ListScreenFactory\MSUserFactory;
use ACP\ListScreenFactory\PostFactory;
use ACP\ListScreenFactory\TaxonomyFactory;
use ACP\ListScreenFactory\UserFactory;

class ListScreens implements Registerable
{

    public function register(): void
    {
        AC\ListScreenFactory\Aggregate::add(new MSSiteFactory());
        AC\ListScreenFactory\Aggregate::add(new MSUserFactory());
        AC\ListScreenFactory\Aggregate::add(new MediaFactory());
        AC\ListScreenFactory\Aggregate::add(new CommentFactory());
        AC\ListScreenFactory\Aggregate::add(new TaxonomyFactory());
        AC\ListScreenFactory\Aggregate::add(new UserFactory());
        AC\ListScreenFactory\Aggregate::add(new PostFactory(new PostTypeRepository()));

        add_action('ac/list_screen_groups', [$this, 'register_list_screen_groups']);
    }

    public function register_list_screen_groups(Groups $groups): void
    {
        $groups->add('network', __('Network'), 5);
        $groups->add('taxonomy', __('Taxonomy'), 15);
    }

}