<?php

namespace ACP\Search\RequestHandler;

use AC;
use AC\Exception;
use AC\ListScreenFactory;
use AC\ListScreenRepository\Storage;
use AC\Request;
use AC\Response;
use AC\Type\ListScreenId;
use ACP\Controller;
use ACP\Filtering\ApplyFilter\CacheDuration;
use ACP\Search;
use ACP\Search\Searchable;
use DomainException;

class Comparison extends Controller
{

    /**
     * @var AC\ListScreen;
     */
    protected $list_screen;

    public function __construct(
        Storage $storage,
        Request $request,
        ListScreenFactory $list_screen_factory
    ) {
        parent::__construct($request);

        $id = $request->get('layout');
        $list_key = (string)$request->get('list_screen', '');

        if (ListScreenId::is_valid_id($id)) {
            $this->list_screen = $storage->find(new ListScreenId($id));
        } elseif ($list_key && $list_screen_factory->can_create($list_key)) {
            $this->list_screen = $list_screen_factory->create($list_key);
        }

        if ( ! $this->list_screen instanceof AC\ListScreen) {
            throw Exception\RequestException::parameters_invalid();
        }
    }

    public function get_options_action(): void
    {
        $response = new Response\Json();

        $column = $this->list_screen->get_column_by_name(
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
                $options = $comparison->get_values();
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

                $options = $comparison->get_values(
                    $search_term,
                    (int)$this->request->filter('page', 1, FILTER_SANITIZE_NUMBER_INT)
                );
                $has_more = ! $options->is_last_page();

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