<?php

declare(strict_types=1);

namespace ACP\Search\SegmentRepository;

use AC\ListScreen;
use AC\ListScreenRepository;
use AC\Type\ListScreenId;
use ACP\Search\Entity\Segment;
use ACP\Search\SegmentCollection;
use ACP\Search\SegmentRepository;
use ACP\Search\Type\SegmentKey;
use ACP\Storage\Directory;

final class Storage implements SegmentRepository
{

    use KeyGeneratorTrait;

    private $list_screen_storage;

    private $file_factory;

    private $database_storage;

    public function __construct(
        ListScreenRepository\Storage $list_screen_storage,
        Database $database_storage,
        FileFactory $file_factory
    ) {
        $this->list_screen_storage = $list_screen_storage;
        $this->file_factory = $file_factory;
        $this->database_storage = $database_storage;
    }

    private function sort(SegmentCollection $collection, Sort $sort = null): SegmentCollection
    {
        if ($sort === null) {
            $sort = new SegmentRepository\Sort\Name();
        }

        return $sort->sort($collection);
    }

    public function find_all_global(ListScreenId $list_screen_id = null, Sort $sort = null): SegmentCollection
    {
        $collection = [];

        foreach ($this->get_segment_repositories() as $repository) {
            foreach ($repository->find_all_global($list_screen_id, $sort) as $segment) {
                $collection[] = $segment;
            }
        }

        return $this->sort(new SegmentCollection($collection), $sort);
    }

    /**
     * @return SegmentRepository[]
     */
    public function get_segment_repositories(): array
    {
        $repositories = [
            $this->database_storage,
        ];

        foreach ($this->list_screen_storage->get_repositories() as $repository) {
            if ($repository->has_source()) {
                $repositories[] = $this->file_factory->create(new Directory($repository->get_source()));
            }
        }

        return $repositories;
    }

    public function find(SegmentKey $key): ?Segment
    {
        foreach ($this->get_segment_repositories() as $repository) {
            $segment = $repository->find($key);

            if ($segment) {
                return $segment;
            }
        }

        return null;
    }

    public function find_all(ListScreenId $list_screen_id = null, Sort $sort = null): SegmentCollection
    {
        $collection = [];

        foreach ($this->get_segment_repositories() as $repository) {
            foreach ($repository->find_all($list_screen_id, $sort) as $segment) {
                $collection[] = $segment;
            }
        }

        return $this->sort(new SegmentCollection($collection), $sort);
    }

    public function find_all_by_user(
        int $user_id,
        ListScreenId $list_screen_id = null,
        Sort $sort = null
    ): SegmentCollection {
        $collection = [];

        foreach ($this->get_segment_repositories() as $repository) {
            foreach ($repository->find_all_by_user($user_id, $list_screen_id, $sort) as $segment) {
                $collection[] = $segment;
            }
        }

        return $this->sort(new SegmentCollection($collection), $sort);
    }

    public function create(
        SegmentKey $segment_key,
        ListScreenId $list_screen_id,
        string $name,
        array $url_parameters,
        int $user_id = null
    ): Segment {
        $list_screen = $this->list_screen_storage->find($list_screen_id);

        $repository = $this->database_storage;

        if ($user_id === null && $list_screen) {
            $repository = $this->get_writable_repository_for_list_screen($list_screen);
        }

        return $repository->create(
            $segment_key,
            $list_screen_id,
            $name,
            $url_parameters,
            $user_id
        );
    }

    private function get_writable_repository_for_list_screen(ListScreen $list_screen): SegmentRepository
    {
        $list_screen_repository = $this->list_screen_storage->get_writable_repository($list_screen);

        if ( ! $list_screen_repository instanceof ListScreenRepository\SourceAware) {
            return $this->database_storage;
        }

        return $list_screen_repository->has_source()
            ? $this->file_factory->create(new Directory($list_screen_repository->get_source()))
            : $this->database_storage;
    }

    public function delete(SegmentKey $key): void
    {
        foreach ($this->get_segment_repositories() as $repository) {
            if ($repository->find($key)) {
                $repository->delete($key);
                break;
            }
        }
    }

}