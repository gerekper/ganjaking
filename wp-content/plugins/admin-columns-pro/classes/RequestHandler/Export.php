<?php

namespace ACP\RequestHandler;

use AC;
use AC\Capabilities;
use AC\ListScreenCollection;
use AC\ListScreenRepository\Storage;
use AC\Type\ListScreenId;
use ACP\ListScreenPreferences;
use ACP\Migrate\Export\ResponseFactory;
use ACP\Nonce\ExportNonce;
use ACP\RequestHandler;
use ACP\Search\Entity;
use ACP\Search\SegmentCollection;

final class Export implements RequestHandler
{

    private $storage;

    private $response_factory;

    public function __construct(
        Storage $storage,
        ResponseFactory $response_factory
    ) {
        $this->storage = $storage;
        $this->response_factory = $response_factory;
    }

    public function handle(AC\Request $request): void
    {
        if ( ! (new ExportNonce())->verify($request)) {
            return;
        }

        if ( ! current_user_can(Capabilities::MANAGE)) {
            return;
        }

        $data = (object)filter_input_array(INPUT_POST, [
            'list_screen_ids' => [
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'flags'  => FILTER_REQUIRE_ARRAY,
            ],
            'segments'        => [
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'flags'  => FILTER_REQUIRE_ARRAY,
            ],
        ]);

        if (empty($data->list_screen_ids)) {
            return;
        }

        $list_screens = $this->get_list_screens_from_request($data->list_screen_ids);

        foreach ($list_screens as $list_screen) {
            $segments = [];

            /* @var Entity\Segment $segment */
            foreach ($list_screen->get_preference(ListScreenPreferences::SHARED_SEGMENTS) as $segment) {
                if (in_array((string)$segment->get_key(), $data->segments, true)) {
                    $segments[] = $segment;
                }
            }

            $list_screen->set_preference(ListScreenPreferences::SHARED_SEGMENTS, new SegmentCollection($segments));
        }

        $response = $this->response_factory->create(
            $list_screens
        );

        $response->send();
    }

    protected function get_list_screens_from_request(array $ids): ListScreenCollection
    {
        $list_screens = new ListScreenCollection();

        foreach ($ids as $id) {
            $list_screen = $this->storage->find(new ListScreenId($id));

            if ($list_screen) {
                $list_screens->add($list_screen);
            }
        }

        return $list_screens;
    }

}