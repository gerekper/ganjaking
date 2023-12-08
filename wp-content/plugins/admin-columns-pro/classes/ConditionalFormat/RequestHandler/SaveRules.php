<?php

declare(strict_types=1);

namespace ACP\ConditionalFormat\RequestHandler;

use AC\Nonce;
use AC\Request;
use AC\RequestAjaxHandler;
use AC\Type\ListScreenId;
use AC\Type\UserId;
use ACP\ConditionalFormat\Entity\Rule;
use ACP\ConditionalFormat\RuleCollection;
use ACP\ConditionalFormat\RulesRepositoryFactory;
use ACP\ConditionalFormat\Type\Format;

class SaveRules implements RequestAjaxHandler
{

    /**
     * @var RulesRepositoryFactory
     */
    private $rules_repository_factory;

    public function __construct(RulesRepositoryFactory $rules_repository_factory)
    {
        $this->rules_repository_factory = $rules_repository_factory;
    }

    public function handle(): void
    {
        $request = new Request();

        if ( ! (new Nonce\Ajax())->verify($request)) {
            wp_send_json_error();
        }

        $rules = new RuleCollection();

        foreach (json_decode($request->get('rules', ''), true) as $rule) {
            $rules->add(
                new Rule(
                    $rule['column_name'],
                    new Format($rule['format']),
                    $rule['operator'],
                    $rule['fact'] ?? null
                )
            );
        }

        $rules_repository = $this->rules_repository_factory->create(
            new ListScreenId($request->get('list_id'))
        );

        $rules_repository->save(
            new UserId(get_current_user_id()),
            $rules
        );

        wp_send_json_success();
    }

}