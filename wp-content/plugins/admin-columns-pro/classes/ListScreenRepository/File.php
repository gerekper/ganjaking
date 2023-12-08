<?php

declare(strict_types=1);

namespace ACP\ListScreenRepository;

use AC\Exception\MissingListScreenIdException;
use AC\ListScreen;
use AC\ListScreenCollection;
use AC\ListScreenRepository\ListScreenRepositoryTrait;
use AC\ListScreenRepositoryWritable;
use AC\OpCacheInvalidateTrait;
use ACP\Exception\DecoderNotFoundException;
use ACP\Exception\DirectoryNotWritableException;
use ACP\Exception\FailedToCreateDirectoryException;
use ACP\Exception\FailedToSaveSegmentException;
use ACP\Exception\FileNotWritableException;
use ACP\ListScreenPreferences;
use ACP\Search\SegmentCollection;
use ACP\Search\SegmentRepository;
use ACP\Storage;
use ACP\Storage\AbstractDecoderFactory;
use ACP\Storage\Decoder\ListScreenDecoder;
use ACP\Storage\Directory;
use ACP\Storage\EncoderFactory;
use ACP\Storage\Serializer;
use DirectoryIterator;

final class File implements ListScreenRepositoryWritable, SourceAware
{

    use SegmentTrait;
    use ListScreenRepositoryTrait;
    use FilteredListScreenRepositoryTrait;
    use OpCacheInvalidateTrait;

    /**
     * @var ListScreenCollection
     */
    private $list_screens;

    /**
     * @var SourceCollection
     */
    private $sources;

    private $directory;

    private $decoder_factory;

    private $encoder_factory;

    private $serializer;

    public function __construct(
        Directory $directory,
        AbstractDecoderFactory $decoder_factory,
        EncoderFactory $encoder_factory,
        Serializer $serializer,
        SegmentRepository\FileFactory $file_factory
    ) {
        $this->directory = $directory;
        $this->decoder_factory = $decoder_factory;
        $this->encoder_factory = $encoder_factory;
        $this->serializer = $serializer;
        $this->segment_repository = $file_factory->create(
            $directory,
            $decoder_factory,
            $encoder_factory,
            $serializer
        );
    }

    /**
     * @throws FileNotWritableException
     * @throws DirectoryNotWritableException
     * @throws FailedToCreateDirectoryException
     * @throws FailedToSaveSegmentException
     */
    public function save(ListScreen $list_screen): void
    {
        if ( ! $this->directory->exists()) {
            $this->directory->create();
        }

        if ( ! $this->directory->is_writable()) {
            throw new DirectoryNotWritableException($this->directory->get_path());
        }

        if ( ! $list_screen->has_id()) {
            throw MissingListScreenIdException::from_saving_list_screen();
        }

        $encoder = $this->encoder_factory
            ->create()
            ->set_list_screen($list_screen);

        $file = sprintf(
            '%s/%s.%s',
            $this->directory->get_path(),
            $list_screen->get_id(),
            $this->get_file_extension()
        );

        $result = file_put_contents(
            $file,
            $this->serializer->serialize($encoder->encode())
        );

        if ($result === false) {
            throw FileNotWritableException::from_saving_list_screen($list_screen);
        }

        $this->opcache_invalidate($file);

        $segments = $list_screen->get_preference(ListScreenPreferences::SHARED_SEGMENTS);

        if ($segments instanceof SegmentCollection) {
            $this->save_segments($segments, $list_screen->get_id());
        }
    }

    /**
     * @throws FileNotWritableException
     */
    public function delete(ListScreen $list_screen): void
    {
        $this->parse_directory();

        if ( ! $this->sources->contains($list_screen->get_id())) {
            throw FileNotWritableException::from_removing_list_screen($list_screen);
        }

        $path = $this->sources->get($list_screen->get_id());

        $this->opcache_invalidate($path);

        $result = unlink($path);

        if ($result === false) {
            throw FileNotWritableException::from_removing_list_screen($list_screen);
        }

        $this->segment_repository->delete_all($list_screen->get_id());
    }

    protected function get_file_extension(): string
    {
        return 'php';
    }

    protected function find_all_from_source(): ListScreenCollection
    {
        $this->parse_directory();

        return $this->list_screens;
    }

    public function get_sources(): SourceCollection
    {
        $this->parse_directory();

        return $this->sources;
    }

    private function parse_directory(): void
    {
        $this->list_screens = new ListScreenCollection();
        $this->sources = new SourceCollection();

        if ( ! $this->directory->is_readable()) {
            return;
        }

        $iterator = new Storage\FileIterator(
            new DirectoryIterator($this->directory->get_path()),
            $this->get_file_extension()
        );

        foreach ($iterator as $file) {
            $encoded_screen = require($file->getRealPath());

            try {
                $decoder = $this->decoder_factory->create($encoded_screen);
            } catch (DecoderNotFoundException $e) {
                continue;
            }

            if ( ! $decoder instanceof ListScreenDecoder || ! $decoder->has_list_screen()) {
                continue;
            }

            $list_screen = $decoder->get_list_screen();
            $list_screen->set_preference(
                ListScreenPreferences::SHARED_SEGMENTS,
                $this->segment_repository->find_all_shared(
                    $list_screen->get_id()
                )
            );

            $this->list_screens->add($list_screen);
            $this->sources->add($list_screen->get_id(), $file->getRealPath());
        }
    }

}