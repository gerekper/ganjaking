<?php

declare(strict_types=1);

namespace ACP\RequestHandler\Ajax;

use AC;
use AC\Capabilities;
use AC\ListScreenRepository\Storage;
use AC\Request;
use AC\RequestAjaxHandler;
use AC\Response;
use AC\Storage\ListScreenOrder;
use AC\Type\ListScreenId;
use ACP\Admin\Encoder;
use ACP\ListScreenFactory\PrototypeFactory;
use ACP\ListScreenRepository\Template;
use ACP\Search\SegmentRepository\KeyGeneratorTrait;
use ACP\Storage\EncoderFactory;

class ListScreenCreate implements RequestAjaxHandler
{

    use KeyGeneratorTrait;

    private $list_screen_factory;

    private $storage;

    private $order_storage;

    private $template_repository;

    private $encoder_factory;

    public function __construct(
        Storage $storage,
        PrototypeFactory $list_screen_factory,
        Template $template_repository,
        ListScreenOrder $order_storage,
        EncoderFactory $encoder
    ) {
        $this->list_screen_factory = $list_screen_factory;
        $this->storage = $storage;
        $this->order_storage = $order_storage;
        $this->template_repository = $template_repository;
        $this->encoder_factory = $encoder;
    }

    public function handle(): void
    {
        if ( ! current_user_can(Capabilities::MANAGE)) {
            return;
        }

        $request = new Request();
        $response = new Response\Json();

        if ( ! (new AC\Nonce\Ajax())->verify($request)) {
            $response->error();
        }

        $list_key = $request->get('list_key');

        if ( ! $this->list_screen_factory->can_create($list_key)) {
            return;
        }

        $title = $request->get('title');

        if ( ! $title) {
            $response->set_message(__('Name can not be empty.', 'codepress-admin-columns'))
                     ->error();
        }

        $list_id = $request->get('list_id');

        // Create a Copy
        if (ListScreenId::is_valid_id($list_id)) {
            $list_id = new ListScreenId($list_id);

            $list_screen_from = $this->template_repository->exists($list_id)
                ? $this->template_repository->find($list_id)
                : $this->storage->find($list_id);

            if ( ! $list_screen_from) {
                $response
                    ->set_message('Invalid list screen source.')
                    ->error();
            }

            $list_screen = $this->list_screen_factory->create_from_list_screen(
                $list_screen_from,
                [
                    'title' => $title,
                ]
            );
        } else {
            $list_screen = $this->list_screen_factory->create(
                $list_key,
                [
                    'list_id' => ListScreenId::generate()->get_id(),
                    'title'   => $title,
                ]
            );
        }

        $this->storage->save($list_screen);
        $this->order_storage->add($list_screen->get_key(), (string)$list_screen->get_id());

        $response->set_parameters(
            (new Encoder($list_screen))->encode()
        )->success();
    }

}