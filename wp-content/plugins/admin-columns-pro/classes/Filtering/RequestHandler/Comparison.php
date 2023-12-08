<?php

declare(strict_types=1);

namespace ACP\Filtering\RequestHandler;

use AC;
use AC\ListScreenRepository\Storage;
use AC\Request;
use AC\RequestAjaxHandler;
use AC\Response;
use AC\Type\ListScreenId;
use ACP\Filtering\ApplyFilter\CacheDuration;
use ACP\Filtering\OptionsFactory;
use ACP\Search;
use ACP\Search\Searchable;
use DomainException;

class Comparison implements RequestAjaxHandler
{

    private $request;

    private $storage;

    private $options_factory;

    public function __construct(Request $request, Storage $storage, OptionsFactory $options_factory)
    {
        $this->request = $request;
        $this->storage = $storage;
        $this->options_factory = $options_factory;
    }

    public function handle(): void
    {
        check_ajax_referer('ac-ajax');

        $response = new Response\Json();

        $list_id = $this->request->get('layout');

        if ( ! ListScreenId::is_valid_id($list_id)) {
            $response->error();
        }

        $list_screen = $this->storage->find(new ListScreenId($list_id));

        if ( ! $list_screen || ! $list_screen->is_user_allowed(wp_get_current_user())) {
            $response->error();
        }

        $column = $list_screen->get_column_by_name(
            (string)$this->request->filter('column', null, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        );

        if ( ! $column instanceof Searchable) {
            $response->error();
        }

        $comparison = $column->search();

        switch (true) {
            case $comparison instanceof Search\Comparison\RemoteValues :
                $response->set_header(
                    'Cache-Control',
                    'max-age=' . $this->get_cache_duraction_in_seconds($comparison)
                );
                $options = $this->options_factory->create_by_remote($comparison);
                $has_more = false;

                break;
            case $comparison instanceof Search\Comparison\SearchableValues :
                $search_term = $this->request->filter('searchterm', '');

                if ('' === $search_term) {
                    $response->set_header(
                        'Cache-Control',
                        'max-age=' . $this->get_cache_duraction_in_seconds($comparison)
                    );
                }

                $paginated = $comparison->get_values(
                    $search_term,
                    (int)$this->request->filter('page', 1, FILTER_SANITIZE_NUMBER_INT)
                );

                $options = $this->options_factory->create_by_searchable($comparison, $paginated);

                $has_more = ! $paginated->is_last_page();

                break;
            default :
                throw new DomainException('Invalid Comparison type found.');
        }

        $select = new AC\Helper\Select\Response($options, $has_more);

        $response
            ->set_parameters($select())
            ->success();
    }

    private function get_cache_duraction_in_seconds(Search\Comparison $comparison): int
    {
        return (new CacheDuration($comparison))->apply_filters(300);
    }

}