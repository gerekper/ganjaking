<?php
declare(strict_types=1);

namespace ACP\ConditionalFormat\Service;

use AC\Registerable;
use ACP\ConditionalFormat\Settings\ListScreen\HideOnScreenFactory;
use ACP\Settings\ListScreen\HideOnScreenCollection;
use ACP\Type\HideOnScreen\Group;

final class ListScreenSettings implements Registerable
{

    /**
     * @var HideOnScreenFactory
     */
    private $hide_on_screen_factory;

    public function __construct(HideOnScreenFactory $hide_on_screen_factory)
    {
        $this->hide_on_screen_factory = $hide_on_screen_factory;
    }

    public function register(): void
    {
        add_action('acp/admin/settings/hide_on_screen', [$this, 'add_hide_on_screen']);
    }

    public function add_hide_on_screen(HideOnScreenCollection $collection): void
    {
        $collection->add($this->hide_on_screen_factory->create(), new Group(Group::FEATURE), 55);
    }

}