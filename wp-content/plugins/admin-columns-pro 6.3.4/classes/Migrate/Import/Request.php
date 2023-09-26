<?php

declare(strict_types=1);

namespace ACP\Migrate\Import;

use AC\Capabilities;
use AC\ListScreenCollection;
use AC\ListScreenRepository\Storage;
use AC\Message;
use AC\Registerable;
use ACP\Exception\FailedToSaveSegmentException;
use ACP\Exception\UnserializeException;
use ACP\Migrate\MessageTrait;
use ACP\Search\SegmentRepository;
use ACP\Storage\AbstractDecoderFactory;
use ACP\Storage\Decoder\ListScreenDecoder;
use ACP\Storage\Decoder\SegmentsDecoder;
use ACP\Storage\Unserializer;
use Exception;

final class Request implements Registerable
{

    use MessageTrait;

    public const ACTION = 'acp-import';
    public const NONCE_NAME = 'acp_import_nonce';

    private $storage;

    private $segment_repository;

    private $decoder_factory;

    private $json_unserializer;

    public function __construct(
        Storage $storage,
        SegmentRepository $segment_repository,
        AbstractDecoderFactory $decoder_factory,
        Unserializer\JsonUnserializer $json_unserializer
    ) {
        $this->storage = $storage;
        $this->segment_repository = $segment_repository;
        $this->decoder_factory = $decoder_factory;
        $this->json_unserializer = $json_unserializer;
    }

    public function register(): void
    {
        add_action('admin_init', [$this, 'handle_request']);
    }

    private function is_request(): bool
    {
        $data = filter_input_array(INPUT_POST, [
            'action'         => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            self::NONCE_NAME => FILTER_DEFAULT,
        ]);

        if ( ! isset($data['action']) || $data['action'] !== self::ACTION) {
            return false;
        }

        if ( ! wp_verify_nonce($data[self::NONCE_NAME], $data['action'])) {
            return false;
        }

        if ( ! current_user_can(Capabilities::MANAGE)) {
            return false;
        }

        if ( ! isset($_FILES['import']['name'], $_FILES['import']['tmp_name'])) {
            return false;
        }

        return true;
    }

    /**
     * @throws FailedToSaveSegmentException
     */
    public function handle_request(): void
    {
        if ( ! $this->is_request()) {
            return;
        }

        $extension = pathinfo($_FILES['import']['name'], PATHINFO_EXTENSION);

        if ($extension !== 'json') {
            $this->set_message(
                sprintf(
                    __('Uploaded file does not have a %s extension.', 'codepress-admin-columns'),
                    '.json'
                )
            );

            return;
        }

        $file_contents = file_get_contents($_FILES['import']['tmp_name']);

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

        $list_screens = new ListScreenCollection();
        $errors = [];

        foreach ($encoded_data as $encoded_item) {
            $decoder = $this->decoder_factory->create($encoded_item);

            if ($decoder instanceof ListScreenDecoder && $decoder->has_list_screen()) {
                $list_screen = $decoder->get_list_screen();

                try {
                    $this->storage->save($list_screen);
                } catch (Exception $e) {
                    $errors[] = sprintf(
                        __('Columns settings with id %s could not be saved.', 'codepress-admin-columns'),
                        $list_screen->get_id()->get_id()
                    );
                    continue;
                }

                $list_screens->add($list_screen);

                if ($decoder instanceof SegmentsDecoder && $decoder->has_segments()) {
                    $segments = $decoder->get_segments();

                    foreach ($segments as $segment) {
                        try {
                            $this->segment_repository->create(
                                $segment->get_key(),
                                $segment->get_list_screen_id(),
                                $segment->get_name(),
                                $segment->get_url_parameters()
                            );
                        } catch (Exception $e) {
                            $errors[] = sprintf(
                                __('Segment "%s" for %s could not be saved.', 'codepress-admin-columns'),
                                $segment->get_name(),
                                $list_screen->get_label()
                            );
                            continue;
                        }
                    }
                }
            }
        }

        if (empty($errors) && ! $list_screens->count()) {
            $this->set_message(
                __('The uploaded file does not contain any column settings.', 'codepress-admin-columns'),
                Message::WARNING
            );

            return;
        }

        $this->success($list_screens);

        foreach ($errors as $error) {
            $this->set_message($error, Message::WARNING);
        }
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
                ac_helper()->string->enumeration_list($links, 'and') . ' ' . _n(
                    'set',
                    'sets',
                    count($links),
                    'codepress-admin-columns'
                ),
                "<strong>" . $label . "</strong>"
            );

            $this->set_message($message, Message::SUCCESS);
        }
    }

}