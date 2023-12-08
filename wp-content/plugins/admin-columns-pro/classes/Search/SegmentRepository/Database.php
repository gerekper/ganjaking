<?php

declare(strict_types=1);

namespace ACP\Search\SegmentRepository;

use AC\Type\ListScreenId;
use ACP\Exception\FailedToSaveSegmentException;
use ACP\Search\Entity\Segment;
use ACP\Search\SegmentCollection;
use ACP\Search\SegmentRepositoryWritable;
use ACP\Search\Storage;
use ACP\Search\Type\SegmentKey;
use DateTime;

final class Database implements SegmentRepositoryWritable
{

    use KeyGeneratorTrait;

    private $table;

    public function __construct(Storage\Table\Segment $table)
    {
        $this->table = $table;
    }

    public function find(SegmentKey $key): ?Segment
    {
        global $wpdb;

        $sql = "
			SELECT *
			FROM `" . $this->table->get_name() . "`
			WHERE `" . $this->table::KEY . "` = %s
		";

        $result = $wpdb->get_row(
            $wpdb->prepare($sql, (string)$key),
            ARRAY_A
        );

        if ( ! $result) {
            return null;
        }

        return $this->create_segment_from_row($result);
    }

    private function create_segment_from_row(array $row): Segment
    {
        $user_id = $row[$this->table::USER_ID]
            ? (int)$row[$this->table::USER_ID]
            : null;

        return new Segment(
            new SegmentKey((string)$row[$this->table::KEY]),
            $row[$this->table::NAME],
            unserialize(
                $row[$this->table::URL_PARAMETERS],
                [
                    'allowed_classes' => false,
                ]
            ),
            new ListScreenId($row[$this->table::LIST_SCREEN_ID]),
            $user_id,
            new DateTime($row[$this->table::DATE_CREATED])
        );
    }

    private function fetch_results(
        ListScreenId $list_screen_id = null,
        int $user_id = null,
        Sort $sort = null
    ): SegmentCollection {
        global $wpdb;

        if ($sort === null) {
            $sort = new Sort\Name();
        }

        $sql = "
			SELECT * 
			FROM " . $this->table->get_name() . "
			WHERE 1=1
		";

        if ($list_screen_id) {
            $sql .= $wpdb->prepare("\nAND `" . $this->table::LIST_SCREEN_ID . "` = %s", (string)$list_screen_id);
        }

        if ($user_id !== null) {
            $sql .= $wpdb->prepare("\nAND `" . $this->table::USER_ID . "` = %d", $user_id);
        }

        $rows = [];

        foreach ($wpdb->get_results($sql, ARRAY_A) as $row) {
            $rows[$row[$this->table::KEY]] = $this->create_segment_from_row($row);
        }

        return $sort->sort(new SegmentCollection($rows));
    }

    public function find_all(ListScreenId $list_screen_id = null, Sort $sort = null): SegmentCollection
    {
        return $this->fetch_results(
            $list_screen_id,
            null,
            $sort
        );
    }

    public function find_all_personal(
        int $user_id,
        ListScreenId $list_screen_id = null,
        Sort $sort = null
    ): SegmentCollection {
        return $this->fetch_results(
            $list_screen_id,
            $user_id,
            $sort
        );
    }

    public function find_all_shared(ListScreenId $list_screen_id = null, Sort $sort = null): SegmentCollection
    {
        return $this->fetch_results(
            $list_screen_id,
            0,
            $sort
        );
    }

    /**
     * @throws FailedToSaveSegmentException
     */
    public function save(Segment $segment): void
    {
        global $wpdb;

        if ($this->find($segment->get_key()) !== null) {
            throw FailedToSaveSegmentException::from_duplicate_key($segment->get_key());
        }

        $inserted = $wpdb->insert(
            $this->table->get_name(),
            [
                $this->table::KEY => (string)$segment->get_key(),
                $this->table::LIST_SCREEN_ID => (string)$segment->get_list_id(),
                $this->table::USER_ID => $segment->has_user_id() ? $segment->get_user_id() : 0,
                $this->table::NAME => $segment->get_name(),
                $this->table::URL_PARAMETERS => serialize($segment->get_url_parameters()),
                $this->table::DATE_CREATED => (new DateTime())->format($this->table->get_timestamp_format()),
            ],
            [
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
            ]
        );

        if ($inserted !== 1) {
            throw new FailedToSaveSegmentException();
        }
    }

    public function delete(SegmentKey $key): void
    {
        global $wpdb;

        $wpdb->delete(
            $this->table->get_name(),
            [
                $this->table::KEY => (string)$key,
            ],
            [
                '%s',
            ]
        );
    }

    public function delete_all(ListScreenId $list_screen_id): void
    {
        global $wpdb;

        $wpdb->delete(
            $this->table->get_name(),
            [
                $this->table::LIST_SCREEN_ID => (string)$list_screen_id,
            ],
            [
                '%s',
            ]
        );
    }

    public function delete_all_shared(ListScreenId $list_screen_id): void
    {
        global $wpdb;

        $wpdb->delete(
            $this->table->get_name(),
            [
                $this->table::LIST_SCREEN_ID => (string)$list_screen_id,
                $this->table::USER_ID        => 0,
            ],
            [
                '%s',
                '%d',
            ]
        );
    }

}