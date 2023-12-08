<?php
declare(strict_types=1);

namespace ACP\ConditionalFormat\Service;

use AC\ListScreen;
use AC\Registerable;
use ACP\ConditionalFormat\RulesRepositoryFactory;

final class Storage implements Registerable
{

    /**
     * @var RulesRepositoryFactory
     */
    private $rules_repository_factory;

    public function __construct(RulesRepositoryFactory $rules_repository_factory)
    {
        $this->rules_repository_factory = $rules_repository_factory;
    }

    public function register(): void
    {
        add_action('acp/list_screen/deleted', [$this, 'list_screen_deleted']);
    }

    public function list_screen_deleted(ListScreen $list_screen): void
    {
        $this->rules_repository_factory->create($list_screen->get_id())->remove_for_all_users();
    }

}