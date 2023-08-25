<?php

declare(strict_types=1);

namespace ACP\Search\RequestHandler;

use AC;
use AC\Capabilities;
use AC\ListScreenRepository;
use AC\Request;
use AC\Response;
use AC\Type\ListScreenId;
use ACP\Controller;
use ACP\Search\Entity;
use ACP\Search\SegmentCollection;
use ACP\Search\SegmentRepository;
use ACP\Search\Type\SegmentKey;

final class Segment extends Controller
{

    private $segment_repository;

    private $list_screen_repository;

    public function __construct(
        ListScreenRepository\Storage $storage,
        Request $request,
        SegmentRepository $segment_repository
    ) {
        parent::__construct($request);

        $this->list_screen_repository = $storage;
        $this->segment_repository = $segment_repository;
    }

    private function get_list_screen(): ?AC\ListScreen
    {
        $id = $this->request->get('layout');

        if ( ! ListScreenId::is_valid_id($id)) {
            return null;
        }

        return $this->list_screen_repository->find(
            new ListScreenId($this->request->get('layout'))
        );
    }

    private function get_segment_response(Entity\Segment $segment, AC\ListScreen $list_screen): array
    {
        $query_string = array_merge($segment->get_url_parameters(), [
            'ac-segment' => (string)$segment->get_key(),
        ]);

        foreach ($query_string as $k => $v) {
            $query_string[$k] = urlencode_deep($v);
        }

        $url = add_query_arg(
            $query_string,
            (string)$list_screen->get_table_url()
        );

        return [
            'key'    => (string)$segment->get_key(),
            'name'   => $segment->get_name(),
            'url'    => $url,
            'global' => $segment->is_global(),
        ];
    }

    public function read_action(): void
    {
        $response = new Response\Json();

        $list_screen = $this->get_list_screen();

        if ( ! $list_screen) {
            $response
                ->set_status_code(400)
                ->error();
        }

        $user_segments = $this->segment_repository->find_all_by_user(
            get_current_user_id(),
            $list_screen->get_id()
        );

        $global_segments = $this->segment_repository->find_all_global(
            $list_screen->get_id()
        );

        $segments = new SegmentCollection(
            array_merge(iterator_to_array($user_segments), iterator_to_array($global_segments))
        );

        $segments = apply_filters(
            'acp/search/segments',
            $segments,
            $list_screen
        );

        $data = [];

        foreach ($segments as $segment) {
            $data[] = $this->get_segment_response($segment, $list_screen);
        }

        $response
            ->set_parameters($data)
            ->success();
    }

    public function create_action(): void
    {
        $response = new Response\Json();

        $list_screen = $this->get_list_screen();

        if ( ! $list_screen) {
            $response
                ->set_status_code(400)
                ->error();
        }

        $data = filter_var_array(
            $this->request->get_parameters()->all(),
            [
                'name'                     => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'query_string'             => FILTER_DEFAULT,
                'whitelisted_query_string' => FILTER_DEFAULT,
                'global'                   => FILTER_SANITIZE_NUMBER_INT,
            ]
        );

        parse_str($data['query_string'], $url_parameters);
        parse_str($data['whitelisted_query_string'], $whitelisted_url_parameters);

        foreach ($whitelisted_url_parameters as $whitelisted_url_parameter => $placeholder) {
            if ( ! isset($url_parameters[$whitelisted_url_parameter])) {
                continue;
            }

            $whitelisted_url_parameters[$whitelisted_url_parameter] = $url_parameters[$whitelisted_url_parameter];
        }

        // Check capability before allowing global segments
        $user_id = $data['global'] && current_user_can(Capabilities::MANAGE)
            ? null
            : get_current_user_id();

        $segment = $this->segment_repository->create(
            $this->segment_repository->generate_key(),
            $list_screen->get_id(),
            (string)$data['name'],
            $whitelisted_url_parameters,
            $user_id
        );

        $response
            ->set_parameters([
                'segment' => $this->get_segment_response($segment, $list_screen),
            ])
            ->success();
    }

    public function delete_action(): void
    {
        $response = new Response\Json();
        $key = $this->request->filter('key', FILTER_SANITIZE_NUMBER_INT);

        if ( ! $key) {
            $response->error();
        }

        $segment_key = new SegmentKey($key);
        $segment = $this->segment_repository->find(new SegmentKey($key));

        if ( ! $segment) {
            $response->error();
        }

        if ( ! current_user_can(Capabilities::MANAGE) && $segment->get_user_id() !== get_current_user_id()) {
            $response->error();
        }

        $this->segment_repository->delete($segment_key);

        $response->success();
    }

}