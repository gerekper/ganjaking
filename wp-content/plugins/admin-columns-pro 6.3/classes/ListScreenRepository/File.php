<?php

declare(strict_types=1);

namespace ACP\ListScreenRepository;

use AC;
use AC\Exception\MissingListScreenIdException;
use AC\Exception\SourceNotAvailableException;
use AC\ListScreen;
use AC\ListScreenCollection;
use AC\ListScreenRepository\Filter;
use AC\ListScreenRepository\SourceAware;
use AC\OpCacheInvalidateTrait;
use AC\Type\ListScreenId;
use ACP\Exception\DirectoryNotWritableException;
use ACP\Exception\FailedToCreateDirectoryException;
use ACP\Exception\FileNotWritableException;
use ACP\Storage\AbstractDecoderFactory;
use ACP\Storage\Decoder\ListScreenDecoder;
use ACP\Storage\Directory;
use ACP\Storage\EncoderFactory;
use ACP\Storage\Serializer;
use SplFileInfo;

final class File implements AC\ListScreenRepositoryWritable, SourceAware
{

    use OpCacheInvalidateTrait;
    use AC\ListScreenRepository\ListScreenRepositoryTrait;

    private $directory;

    private $decoder_factory;

    private $encoder_factory;

    private $serializer;

    public function __construct(
        Directory $directory,
        EncoderFactory $encoder_factory,
        AbstractDecoderFactory $decoder_factory,
        Serializer $serializer
    ) {
        $this->directory = $directory;
        $this->encoder_factory = $encoder_factory;
        $this->decoder_factory = $decoder_factory;
        $this->serializer = $serializer;
    }

    protected function find_from_source(ListScreenId $id): ?ListScreen
    {
        $list_screens = (new Filter\ListId($id))->filter(
            $this->find_all()
        );

        return $list_screens->get_first() ?: null;
    }

    protected function find_all_from_source(): ListScreenCollection
    {
        $list_screens = new ListScreenCollection();

        foreach ($this->get_files() as $file) {
            $encoded_data = require($file->getRealPath());

            if ( ! $this->decoder_factory->can_create($encoded_data)) {
                continue;
            }

            $decoder = $this->decoder_factory->create($encoded_data);

            if ( ! $decoder instanceof ListScreenDecoder || ! $decoder->has_list_screen()) {
                continue;
            }

            $list_screen = $decoder->get_list_screen();

            $list_screens->add($list_screen);
        }

        return $list_screens;
    }

    protected function find_all_by_key_from_source(string $key): ListScreenCollection
    {
        return (new Filter\ListKey($key))->filter(
            $this->find_all()
        );
    }

    /**
     * @throws FileNotWritableException
     * @throws DirectoryNotWritableException
     * @throws FailedToCreateDirectoryException
     */
    public function save(ListScreen $list_screen): void
    {
        if ( ! $this->directory->exists()) {
            $this->directory->create();
        }

        if ( ! $this->directory->get_info()->isWritable()) {
            throw new DirectoryNotWritableException($this->directory->get_path());
        }

        if ( ! $list_screen->has_id()) {
            throw MissingListScreenIdException::from_saving_list_screen();
        }

        $encoder = $this->encoder_factory
            ->create()
            ->set_list_screen($list_screen);

        $file = $this->create_file_name(
            $this->directory->get_path(),
            $list_screen->get_id()
        );

        $result = file_put_contents(
            $file,
            $this->serializer->serialize($encoder->encode())
        );

        if ($result === false) {
            throw FileNotWritableException::from_saving_list_screen($list_screen);
        }

        $this->opcache_invalidate($file);
    }

    /**
     * @throws FileNotWritableException
     */
    public function delete(ListScreen $list_screen): void
    {
        $file = $this->create_file_name(
            $this->directory->get_path(),
            $list_screen->get_id()
        );

        $this->opcache_invalidate($file);

        $result = unlink($file);

        if ($result === false) {
            throw FileNotWritableException::from_removing_list_screen($list_screen);
        }
    }

    public function get_source(ListScreenId $id = null): string
    {
        if ( ! $this->has_source($id)) {
            throw new SourceNotAvailableException();
        }

        $path = $this->directory->get_path();

        return null === $id
            ? $path
            : $this->create_file_name($path, $id);
    }

    public function has_source(ListScreenId $id = null): bool
    {
        if ( ! $this->directory->exists()) {
            return false;
        }

        return null === $id || $this->exists($id);
    }

    /**
     * @return SplFileInfo[]
     */
    private function get_files(): array
    {
        $files = [];

        if ($this->directory->is_readable()) {
            /** @var SplFileInfo $file */
            foreach ($this->directory->get_iterator() as $file) {
                if ( ! $file->isFile() || ! $file->isReadable() || $file->getSize() === 0) {
                    continue;
                }

                if ($this->get_file_extension() !== $file->getExtension()) {
                    continue;
                }

                $files[] = $file->getFileInfo();
            }
        }

        return $files;
    }

    private function create_file_name(string $path, ListScreenId $id): string
    {
        return sprintf('%s/%s.%s', $path, $id->get_id(), $this->get_file_extension());
    }

    private function get_file_extension(): string
    {
        return 'php';
    }

}