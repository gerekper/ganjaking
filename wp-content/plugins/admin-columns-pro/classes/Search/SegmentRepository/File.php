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
use ACP\Search\SegmentRepositoryWritable;
use ACP\Search\Storage;
use ACP\Search\Type\SegmentKey;
use ACP\Storage\AbstractDecoderFactory;
use ACP\Storage\Decoder;
use ACP\Storage\Directory;
use ACP\Storage\EncoderFactory;
use ACP\Storage\Serializer;
use DirectoryIterator;
use SplFileInfo;

final class File implements SegmentRepositoryWritable
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
        Serializer $serializer
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

    /**
     * @param ListScreenId|null $list_screen_id
     *
     * @return array
     */
    private function load_segments(ListScreenId $list_screen_id = null): array
    {
        $segments = [];

        /**
         * @var SplFileInfo $file
         */
        foreach ((new DirectoryIterator($this->directory->get_path())) as $file) {
            if ( ! $file->isReadable() ||
                 ! $file->isFile() ||
                 ! $file->getSize() ||
                 $file->getExtension() !== $this->get_file_extension() ||
                 ! str_contains($file->getBasename(), self::SUFFIX)
            ) {
                continue;
            }

            $list_id = str_replace(sprintf('%s.%s', self::SUFFIX, $file->getExtension()), '', $file->getBasename());

            if ( ! ListScreenId::is_valid_id($list_id)) {
                continue;
            }

            $list_id = new ListScreenId($list_id);

            if ($list_screen_id && ! $list_screen_id->equals($list_id)) {
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
                $segments[] = [
                    'segment' => $segment,
                    'file'    => $file->getRealPath(),
                    'list_id' => $list_id,
                ];
            }
        }

        return $segments;
    }

    public function find_all(ListScreenId $list_screen_id = null, Sort $sort = null): SegmentCollection
    {
        if (null === $sort) {
            $sort = new Sort\Name();
        }

        $segments = [];

        foreach ($this->load_segments($list_screen_id) as $segment_data) {
            $segments[] = $segment_data['segment'];
        }

        return $sort->sort(new SegmentCollection($segments));
    }

    public function find_all_personal(
        int $user_id,
        ListScreenId $list_screen_id = null,
        Sort $sort = null
    ): SegmentCollection {
        return new SegmentCollection();
    }

    public function find_all_shared(ListScreenId $list_screen_id = null, Sort $sort = null): SegmentCollection
    {
        return $this->find_all($list_screen_id, $sort);
    }

    /**
     * @throws FileNotWritableException
     * @throws DirectoryNotWritableException
     * @throws FailedToSaveSegmentException
     * @throws FailedToCreateDirectoryException
     */
    public function save(Segment $segment): void
    {
        if ( ! $this->directory->exists()) {
            $this->directory->create();
        }

        if ( ! $this->directory->is_writable()) {
            throw new DirectoryNotWritableException($this->directory->get_path());
        }

        if ($this->find($segment->get_key())) {
            throw FailedToSaveSegmentException::from_duplicate_key($segment->get_key());
        }

        $file = $this->get_file_name($segment->get_list_id());

        $segments = $this->get_segments_from_file($file);
        $segments->add($segment);

        $this->update_file($segments, $file, $segment->get_list_id());
    }

    private function find_segment_data_by_key(SegmentKey $key): ?array
    {
        foreach ($this->load_segments() as $segment_data) {
            if ($key->equals($segment_data['segment']->get_key())) {
                return $segment_data;
            }
        }

        return null;
    }

    /**
     * @throws FileNotWritableException
     */
    public function delete(SegmentKey $key): void
    {
        $segment_data = $this->find_segment_data_by_key($key);

        if ( ! $segment_data) {
            return;
        }

        $file = $segment_data['file'];
        $list_id = $segment_data['list_id'];

        $segments = $this->get_segments_from_file($file);
        $segments->remove($key);

        if ($segments->count() < 1) {
            $this->delete_file($file, $list_id);

            return;
        }

        $this->update_file(
            $segments,
            $file,
            $list_id
        );
    }

    /**
     * @throws FileNotWritableException
     */
    public function delete_all(ListScreenId $list_screen_id): void
    {
        $file = $this->get_file_name(
            $list_screen_id
        );

        if (file_exists($file)) {
            $this->delete_file($file, $list_screen_id);
        }
    }

    /**
     * @throws FileNotWritableException
     */
    public function delete_all_shared(ListScreenId $list_screen_id): void
    {
        $this->delete_all($list_screen_id);
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

    /**
     * @throws FileNotWritableException
     */
    private function delete_file(string $file, ListScreenId $list_screen_id): void
    {
        $this->opcache_invalidate($file);

        $result = unlink($file);

        if ($result === false) {
            throw FileNotWritableException::from_removing_segment($list_screen_id);
        }
    }

    private function get_file_name(ListScreenId $list_screen_id): string
    {
        return sprintf(
            '%s/%s.%s',
            $this->directory->get_path(),
            $list_screen_id . self::SUFFIX,
            $this->get_file_extension()
        );
    }

    private function get_file_extension(): string
    {
        return 'php';
    }

}