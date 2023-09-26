<?php

declare(strict_types=1);

namespace ACP\Search\SegmentRepository;

use AC\OpCacheInvalidateTrait;
use AC\Type\ListScreenId;
use ACP\Exception\DirectoryNotWritableException;
use ACP\Exception\FailedToCreateDirectoryException;
use ACP\Exception\FailedToSaveSegmentException;
use ACP\Exception\FileNotWritableException;
use ACP\Search\Entity\Segment;
use ACP\Search\SegmentCollection;
use ACP\Search\SegmentRepository;
use ACP\Search\Storage;
use ACP\Search\Type\SegmentKey;
use ACP\Storage\AbstractDecoderFactory;
use ACP\Storage\Decoder;
use ACP\Storage\Directory;
use ACP\Storage\EncoderFactory;
use ACP\Storage\Serializer;
use SplFileInfo;

final class File implements SegmentRepository
{

    use OpCacheInvalidateTrait;
    use KeyGeneratorTrait;

    private const SUFFIX = '_segments';

    private $directory;

    private $decoder_factory;

    private $encoder_factory;

    private $serializer;

    public function __construct(
        Directory $directory,
        AbstractDecoderFactory $decoder_factory,
        EncoderFactory $encoder_factory,
        Serializer\PhpSerializer\File $serializer
    ) {
        $this->directory = $directory;
        $this->decoder_factory = $decoder_factory;
        $this->encoder_factory = $encoder_factory;
        $this->serializer = $serializer;
    }

    public function find(SegmentKey $key): ?Segment
    {
        foreach ($this->find_all(null, new Sort\Nullable()) as $segment) {
            if ($key->equals($segment->get_key())) {
                return $segment;
            }
        }

        return null;
    }

    public function find_all(ListScreenId $list_screen_id = null, Sort $sort = null): SegmentCollection
    {
        if (null === $sort) {
            $sort = new Sort\Name();
        }

        $segments = [];

        /** @var SplFileInfo $file */
        foreach ($this->directory->get_iterator() as $file) {
            if ( ! $file->isReadable() ||
                 ! $file->isFile() ||
                 ! $file->getSize() ||
                 ! str_contains($file->getBasename(), self::SUFFIX) ||
                 $file->getExtension() !== $this->get_file_extension() ||
                 ($list_screen_id !== null && 0 !== strpos($file->getBasename(), (string)$list_screen_id))
            ) {
                continue;
            }

            $encoded_data = require($file->getRealPath());

            if ( ! $this->decoder_factory->can_create($encoded_data)) {
                continue;
            }

            $decoder = $this->decoder_factory->create($encoded_data);

            if ( ! $decoder instanceof Decoder\SegmentsDecoder || ! $decoder->has_segments()) {
                continue;
            }

            foreach ($decoder->get_segments() as $segment) {
                $segments[] = $segment;
            }
        }

        return $sort->sort(new SegmentCollection($segments));
    }

    public function find_all_by_user(
        int $user_id,
        ListScreenId $list_screen_id = null,
        Sort $sort = null
    ): SegmentCollection {
        return new SegmentCollection([]);
    }

    public function find_all_global(ListScreenId $list_screen_id = null, Sort $sort = null): SegmentCollection
    {
        return $this->find_all($list_screen_id, $sort);
    }

    /**
     * @throws FailedToSaveSegmentException
     * @throws FailedToCreateDirectoryException
     * @throws DirectoryNotWritableException
     * @throws FileNotWritableException
     */
    public function create(
        SegmentKey $segment_key,
        ListScreenId $list_screen_id,
        string $name,
        array $url_parameters,
        int $user_id = null
    ): Segment {
        if ( ! $this->directory->exists()) {
            $this->directory->create();
        }

        if ( ! $this->directory->get_info()->isWritable()) {
            throw new DirectoryNotWritableException($this->directory->get_path());
        }

        if ($this->find($segment_key)) {
            throw  FailedToSaveSegmentException::from_duplicate_key($segment_key);
        }

        $file = $this->create_file_name($list_screen_id);

        $segment = new Segment(
            $segment_key,
            $list_screen_id,
            $name,
            $url_parameters,
            $user_id
        );

        $segments = $this->get_segments_from_file($file);
        $segments->add($segment);

        $this->update_file($segments, $file, $list_screen_id);

        return $segment;
    }

    /**
     * @throws FileNotWritableException
     */
    public function delete(SegmentKey $key): void
    {
        $segment = $this->find($key);

        if ( ! $segment) {
            return;
        }

        $file = $this->create_file_name($segment->get_list_screen_id());

        $segments = $this->get_segments_from_file($file);
        $segments->remove($key);

        $this->update_file(
            $segments,
            $file,
            $segment->get_list_screen_id()
        );
    }

    private function get_segments_from_file(string $file): SegmentCollection
    {
        $segments = new SegmentCollection();
        $info = new SplFileInfo($file);

        if ($info->isFile()) {
            $encoded_data = require($info->getRealPath());

            if (is_array($encoded_data) && $this->decoder_factory->can_create($encoded_data)) {
                $decoder = $this->decoder_factory->create($encoded_data);

                if ($decoder instanceof Decoder\SegmentsDecoder && $decoder->has_segments()) {
                    $segments = $decoder->get_segments();
                }
            }
        }

        return $segments;
    }

    /**
     * @throws FileNotWritableException
     */
    private function update_file(SegmentCollection $segments, string $file, ListScreenId $list_screen_id): void
    {
        $encoded_data = $this->encoder_factory
            ->create()
            ->set_segments($segments)
            ->encode();

        $result = file_put_contents(
            $file,
            $this->serializer->serialize($encoded_data)
        );

        if ($result === false) {
            throw FileNotWritableException::from_saving_segment($list_screen_id);
        }

        $this->opcache_invalidate($file);
    }

    private function create_file_name(ListScreenId $id): string
    {
        return sprintf(
            '%s/%s%s.%s',
            $this->directory->get_path(),
            $id,
            self::SUFFIX,
            $this->get_file_extension()
        );
    }

    private function get_file_extension(): string
    {
        return 'php';
    }

}