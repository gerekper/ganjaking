<?php

namespace ACP\RequestHandler;

use AC\Capabilities;
use AC\ListScreenCollection;
use AC\Message;
use AC\Request;
use AC\Type\ListScreenId;
use ACP\Exception\UnserializeException;
use ACP\Migrate\ImportHandler;
use ACP\Migrate\MessageTrait;
use ACP\Nonce;
use ACP\RequestHandler;
use ACP\Storage\Unserializer;

class Import implements RequestHandler
{

    use MessageTrait;

    private $json_unserializer;

    private $import_handler;

    public function __construct(
        Unserializer\JsonUnserializer $json_unserializer,
        ImportHandler $import_handler
    ) {
        $this->json_unserializer = $json_unserializer;
        $this->import_handler = $import_handler;
    }

    public function is_request(Request $request): bool
    {
        return current_user_can(Capabilities::MANAGE) &&
               (new Nonce\ImportFileNonce())->verify($request);
    }

    public function handle(Request $request): void
    {
        if ( ! $this->is_request($request)) {
            return;
        }

        $file_path = $request->get('file_name');

        $file_contents = $file_path && file_exists($file_path)
            ? file_get_contents($file_path)
            : null;

        if (empty($file_contents)) {
            $this->set_message(__('Uploaded file is empty or not readable.', 'codepress-admin-columns'));

            return;
        }

        try {
            $encoded_data = $this->json_unserializer->unserialize($file_contents);
        } catch (UnserializeException $e) {
            $this->set_message(__('Error parsing the uploaded file.', 'codepress-admin-columns'));

            return;
        }

        $id = $request->get('list_id');

        $list_screens = $this->import_handler->handle(
            $encoded_data,
            ListScreenId::is_valid_id($id) ? new ListScreenId($id) : null
        );

        if ( ! $list_screens->count()) {
            $this->set_message(
                __('The uploaded file does not contain any column settings.', 'codepress-admin-columns'),
                Message::WARNING
            );

            return;
        }

        $this->success($list_screens);
    }

    private function success(ListScreenCollection $list_screens): void
    {
        $grouped = [];

        foreach ($list_screens as $list_screen) {
            $grouped[$list_screen->get_label()][] = sprintf(
                '<a href="%s"><strong>%s</strong></a>',
                esc_url((string)$list_screen->get_editor_url()),
                esc_html($list_screen->get_title())
            );
        }

        foreach ($grouped as $label => $links) {
            $message = sprintf(
                __('Successfully imported %s for %s.', 'codepress-admin-columns'),
                ac_helper()->string->enumeration_list($links, 'and'),
                "<strong>" . $label . "</strong>"
            );

            $this->set_message($message, Message::SUCCESS);
        }
    }

}