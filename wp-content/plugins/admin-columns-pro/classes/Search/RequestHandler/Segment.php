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
use ACP\ListScreenPreferences;
use ACP\Search\Entity;
use ACP\Search\SegmentCollection;
use ACP\Search\SegmentRepository\Database;
use ACP\Search\Type\SegmentKey;
use Exception;

final class Segment extends Controller
{

    private $segment_repository;

    private $list_screen_repository;

    public function __construct(
        ListScreenRepository\Storage $storage,
        Request $request,
        Database $segment_repository
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
            'userId' => $segment->has_user_id() ? $segment->get_user_id() : null,
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

        $user_segments = $this->segment_repository->find_all_personal(
            get_current_user_id(),
            $list_screen->get_id()
        );

        $shared_segments = $list_screen->get_preference(ListScreenPreferences::SHARED_SEGMENTS);

        $segments = new SegmentCollection(
            array_merge(iterator_to_array($user_segments, false), iterator_to_array($shared_segments, false))
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
            $response->set_status_code(400)
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

        $segment = new Entity\Segment(
            $this->segment_repository->generate_key(),
            (string)$data['name'],
            $whitelisted_url_parameters,
            $list_screen->get_id(),
            $user_id
        );

        try {
            if ($user_id) {
                $this->segment_repository->save($segment);
            } else {
                if ($list_screen->is_read_only()) {
                    $response->set_status_code(400)
                             ->error();
                }

                /** @var SegmentCollection $segments */
                $segments = $list_screen->get_preference(ListScreenPreferences::SHARED_SEGMENTS);
                $segments->add($segment);

                $this->list_screen_repository->save($list_screen);
            }
        } catch (Exception $e) {
            $response->set_status_code(500)
                     ->set_message($e->getMessage())
                     ->error();
        }

        $response
            ->set_parameters([
                'segment' => $this->get_segment_response($segment, $list_screen),
            ])
            ->success();
    }

    public function delete_action(): void
    {
        $response = new Response\Json();

        $segment_key_input = $this->request->filter('key');
        $is_shared_input = (int)$this->request->filter('user_id', 0);
        $list_screen = $this->get_list_screen();

        if ( ! $segment_key_input || ! $list_screen) {
            $response->error();
        }

        $segment_key = new SegmentKey($segment_key_input);

        if ($is_shared_input === 0) {
            if ( ! current_user_can(Capabilities::MANAGE) || $list_screen->is_read_only()) {
                $response->error();
            }

            $segments = $list_screen->get_preference(ListScreenPreferences::SHARED_SEGMENTS);
            $segments->remove($segment_key);

            $this->list_screen_repository->save($list_screen);
        } else {
            $segment = $this->segment_repository->find($segment_key);

            if ( ! $segment || ! $segment->has_user_id() || $segment->get_user_id() !== get_current_user_id()) {
                $response->error();
            }

            $this->segment_repository->delete($segment->get_key());
        }

        $response->success();
    }

}